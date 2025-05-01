<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use App\Models\Student;
use App\Models\StudentSuspension;
use App\Models\StudentExpulsion;
use App\Models\Notification;

use App\Mail\NotificationMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudentDisciplinaryController extends Controller
{
    //

    /**
     * Expel a student (permanent removal).
     */
    
    public function expel(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'reason' => 'required',
            'student_id' => 'required|exists:students,id',
            'file' => 'nullable'
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        // Find the student
        $student = Student::find($request->student_id);
        if (!$student) {
            alert()->error('Oops', 'Student record not found.')->persistent('Close');
            return redirect()->back();
        }

        // Check if already expelled
        if (StudentExpulsion::where('student_id', $student->id)->exists()) {
            alert()->error('Action Denied', 'This student has already been expelled.')->persistent('Close');
            return redirect()->back();
        }

        $fileUrl = null;
        if(!empty($request->file)){
            $dir = 'uploads/student/student_disciplinary/';
            $filename = 'suspension_' . $student->id . '_' . time() . '.' . $request->file('file')->getClientOriginalExtension();
            $request->file('file')->move(public_path($dir), $filename);
            $fileUrl = $dir . $filename;
        }

        // Create expulsion record
        StudentExpulsion::create([
            'student_id' => $student->id,
            'reason' => $request->reason,
            'start_date' => Carbon::now(),
            'academic_session' => $student->academic_session,
            'file' => $fileUrl
        ]);

        // Update student academic status
        $student->academic_status = 'Expelled';
        $student->is_rusticated = true;
        $student->save();

        // Send notification
        $senderName = env('SCHOOL_NAME', 'University Admin');
        $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
        $message = "We regret to inform you that you have been permanently expelled from the university due to the following reason: {$request->reason}.";
        if(env('SEND_MAIL')){
            Mail::to($student->email)->send(new NotificationMail($senderName, $message, $receiverName, $fileUrl));
        }
        Notification::create([
            'student_id' => $student->id,
            'description' => $message,
            'attachment' => $fileUrl,
            'status' => 0,
        ]);

        alert()->success('Success', 'Student has been successfully expelled.')->persistent('Close');
        return redirect()->back();
    }


    public function suspend(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
            'student_id' => 'required|exists:students,id',
            'file' => 'nullable|file' 
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        // Find the student
        $student = Student::find($request->student_id);
        if (!$student) {
            alert()->error('Oops', 'Student record not found.')->persistent('Close');
            return redirect()->back();
        }

        // Check for active suspension (allow multiple suspensions but prevent overlapping ones)
        $activeSuspension = StudentSuspension::where('student_id', $student->id)
            ->whereNull('end_date')
            ->exists();

        if ($activeSuspension) {
            alert()->error('Action Denied', 'This student is already under suspension.')->persistent('Close');
            return redirect()->back();
        }

        // Handle file upload
        $fileUrl = null;
        if ($request->hasFile('file')) {
            $dir = 'uploads/student/student_disciplinary/';
            $filename = 'suspension_' . $student->id . '_' . time() . '.' . $request->file('file')->getClientOriginalExtension();
            $request->file('file')->move(public_path($dir), $filename);
            $fileUrl = $dir . $filename;
        }

        // Create suspension record
        StudentSuspension::create([
            'slug' => md5($student->slug.time()),
            'student_id' => $student->id,
            'reason' => $request->reason,
            'start_date' => Carbon::now(),
            'academic_session' => $student->academic_session,
            'file' => $fileUrl
        ]);

        // Update student academic status
        $student->academic_status = 'Suspended';
        $student->save();

        // Send notification
        $senderName = env('SCHOOL_NAME', 'University Admin');
        $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
        $message = "You have been temporarily suspended from the university due to the following reason: {$request->reason}.";

        Mail::to($student->email)->send(new NotificationMail($senderName, $message, $receiverName, $fileUrl));

        Notification::create([
            'student_id' => $student->id,
            'description' => $message,
            'attachment' => $fileUrl,
            'status' => 0,
        ]);

        alert()->success('Success', 'Student has been successfully suspended.')->persistent('Close');
        return redirect()->back();
    }


    public function recall(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'suspension_id' => 'nullable',
            'expulsion_id' => 'nullable'
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        // Find the student
        $student = Student::find($request->student_id);
        if (!$student) {
            alert()->error('Oops', 'Student record not found.')->persistent('Close');
            return redirect()->back();
        }

        // Check if student is expelled
        if(!empty($request->expulsion_id)) {
            $expulsion = StudentExpulsion::where('id', $request->expulsion_id)->first();
            if ($expulsion) {
                $expulsion->delete();
                $student->academic_status = 'Good Standing'; // Reset status
                $student->is_rusticated = false;
                $student->save();

                // Send notification for recall
                $this->sendRecallNotification($student, 'expulsion');

                alert()->success('Success', 'Student expulsion has been revoked.')->persistent('Close');
                return redirect()->back();
            }
        }

        if(!empty($request->suspension_id)) {
            // Check if student is suspended
            $suspension = StudentSuspension::where('id', $request->suspension_id)
                ->whereNull('end_date')
                ->orWhere('end_date', '>=', now())
                ->first();

            if ($suspension) {
                $suspension->end_date = Carbon::now();
                $student->academic_status = 'Good Standing'; // Reset status
                $student->save();
                $suspension->save();

                // Send notification for recall
                $this->sendRecallNotification($student, 'suspension');

                alert()->success('Success', 'Student suspension has been lifted.')->persistent('Close');
                return redirect()->back();
            }
        }

        alert()->error('Action Denied', 'No active expulsion or suspension found.')->persistent('Close');
        return redirect()->back();
    }

    /**
     * Send recall notification.
     */
    private function sendRecallNotification($student, $type)
    {
        $senderName = env('SCHOOL_NAME', 'University Admin');
        $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
        $message = "Dear $receiverName, your $type has been officially lifted. You are now reinstated to Good Standing. Please contact the administration for any further details.";

        if(env('SEND_MAIL')){
            Mail::to($student->email)->send(new NotificationMail($senderName, $message, $receiverName));
        }
        
        Notification::create([
            'student_id' => $student->id,
            'description' => $message,
            'status' => 0,
        ]);
    }

    public function viewSuspension($slug){

        $suspension = StudentSuspension::with('student')->where('slug', $slug)->first();

        return view('admin.viewSuspension', [
            'suspension' => $suspension
        ]);

    }


    public function manageSuspension(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'suspension_id' => 'nullable|string',
            'court_affidavit' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'undertaking_letter' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'traditional_ruler_reference' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'ps_reference' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'admin_comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $suspension = StudentSuspension::find($request->suspension_id);
        
        if (!$suspension) {
            alert()->error('Error', 'Suspension record not found.')->persistent('Close');
            return redirect()->back();
        }

        // Handle file uploads
        $fields = ['court_affidavit', 'undertaking_letter', 'traditional_ruler_reference', 'ps_reference'];
        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // Delete existing file if any
                if ($suspension->$field) {
                    @unlink(public_path('uploads/' . $suspension->$field));
                }
                // Store new file
                $file = $request->file($field);
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $suspension->$field = $filename;
            }
        }

        $suspension->admin_comment = !empty($request->admin_comment)?$request->admin_comment:null;
        $suspension->status = !empty($request->status)?$request->status:null;

        // Update fields        
        if($suspension->save()){
            alert()->success('Success', 'Suspension details updated successfully.')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error', 'Failed to update suspension details.')->persistent('Close');
        return redirect()->back();
    }

}
