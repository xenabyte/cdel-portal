<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Staff;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\StudentExit;
use App\Models\Programme;
use App\Models\AcademicLevel;
use App\Models\Notification;

use App\Mail\NotificationMail;
use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudentCareController extends Controller
{
    public function studentExits(){
        $exitApplications = StudentExit::where('status', 'pending')->orderBy('id', 'DESC')->limit(100)->get(); 

        return view('staff.studentExits', [
            'exitApplications' => $exitApplications
        ]);
    }

    public function getExitApplication(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$studentExit = StudentExit::find($request->exit_id)) {
            alert()->error('Oops!', 'Student exit applicattion record not found')->persistent('Close');
            return redirect()->back();
        }

        return redirect(asset($studentExit->file));
    }

    public function manageExitApplication(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
            'action' => 'required',
            'role' => 'required|in:HOD,student care',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$studentExit = StudentExit::find($request->exit_id)) {
            alert()->error('Oops!', 'Student exit application record not found')->persistent('Close');
            return redirect()->back();
        }

        if ($request->role === 'student care' && !$studentExit->is_hod_approved) {
            alert()->error('Not Allowed', 'HOD must approve this application first')->persistent('Close');
            return redirect()->back();
        }

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];
        $staff = Auth::guard('staff')->user();

        if ($request->role === 'student care') {
            $studentExit->managed_by = $staff->id;
            $studentExit->status = $request->action;
        }

        if ($request->role === 'HOD') {
            $hodStatus = $request->action === 'approved';
            $studentExit->is_hod_approved = $hodStatus;
            $studentExit->is_hod_approved_date = Carbon::now();
            if (!$hodStatus) {
                $studentExit->status = $request->action;
            }
        }

        if ($studentExit->save()) {
            $student = Student::find($studentExit->student_id);

            $pdf = new Pdf();
            $exitApplication = $pdf->generateExitApplication($academicSession, $student->id, $studentExit->id);
            $studentExit->file = $exitApplication;
            $studentExit->save();

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
            $message = 'Your exit application has been ' . $request->action . ' by ' . $request->role;

            if (env('SEND_MAIL')) {
                Mail::to($student->email)->send(new NotificationMail($senderName, $message, $receiverName, $exitApplication));
            }

            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'attachment' => $exitApplication,
                'status' => 0,
            ]);

            alert()->success('Success', 'Application ' . $request->action)->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function bulkManageExitApplications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exit_ids' => 'required|array',
            'action' => 'required',
            'role' => 'required|in:HOD,student care',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];
        $exitIds = $request->exit_ids;
        $staff = Auth::guard('staff')->user();
        $isHod = $request->role === 'HOD';
        $processed = 0;
        $skipped = 0;

        foreach ($exitIds as $exitId) {
            if (!$studentExit = StudentExit::find($exitId)) {
                $skipped++;
                continue;
            }

            // Skip if student care tries to manage without HOD approval
            if (!$isHod && !$studentExit->is_hod_approved) {
                $skipped++;
                continue;
            }

            if ($isHod) {
                $hodStatus = $request->action === 'approved';
                $studentExit->is_hod_approved = $hodStatus;
                $studentExit->is_hod_approved_date = Carbon::now();
                if (!$hodStatus) {
                    $studentExit->status = $request->action;
                }
            }

            if ($request->role === 'student care') {
                $studentExit->managed_by = $staff->id;
                $studentExit->status = $request->action;
            }

            if ($studentExit->save()) {
                $student = Student::find($studentExit->student_id);
                $pdf = new Pdf();
                $exitApplication = $pdf->generateExitApplication($academicSession, $student->id, $studentExit->id);

                $studentExit->file = $exitApplication;
                $studentExit->save();

                $senderName = env('SCHOOL_NAME');
                $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
                $message = 'Your exit application has been ' . $request->action . ' by ' . $request->role;

                if (env('SEND_MAIL')) {
                    Mail::to($student->email)->send(new NotificationMail($senderName, $message, $receiverName, $exitApplication));
                }

                Notification::create([
                    'student_id' => $student->id,
                    'description' => $message,
                    'attachment' => $exitApplication,
                    'status' => 0,
                ]);

                $processed++;
            }
        }

        alert()->success('Success', "$processed applications {$request->action}. $skipped skipped.")->persistent('Close');
        return redirect()->back();
    }

    public function verifyStudentExits(Request $request){

        return view('staff.verifyStudentExit');

    }

    public function verifyStudentExit(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$studentExit = StudentExit::find($request->exit_id)) {
            alert()->error('Oops!', 'Student exit applicattion record not found')->persistent('Close');
            return redirect()->back();
        }

        $student = Student::find($studentExit->student_id);

        return view('staff.verifyStudentExit', [
            'studentExit' => $studentExit,
            'student' => $student
        ]);
    }

    public function enterSchool(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$studentExit = StudentExit::find($request->exit_id)) {
            alert()->error('Oops!', 'Student exit applicattion record not found')->persistent('Close');
            return redirect()->back();
        }

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $studentExit->return_at = Carbon::now();

        if($studentExit->save()){
            $student = Student::find($studentExit->student_id);

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'You are welcome back to school.';

            $mail = new NotificationMail($senderName, $message, $receiverName);
            if(env('SEND_MAIL')){
                Mail::to($student->email)->send($mail);
            }
            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'status' => 0
            ]);

            alert()->success('Success', '')->persistent('Close');
            return redirect()->back();
        }
    }

    public function leftSchool(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$studentExit = StudentExit::find($request->exit_id)) {
            alert()->error('Oops!', 'Student exit applicattion record not found')->persistent('Close');
            return redirect()->back();
        }

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $studentExit->exited_at = Carbon::now();

        if($studentExit->save()){
            $student = Student::find($studentExit->student_id);

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'This is to notify you that you have been exited from the university campus. Safe Trip';

            $mail = new NotificationMail($senderName, $message, $receiverName);
            if(env('SEND_MAIL')){
                Mail::to($student->email)->send($mail);
            }
            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'status' => 0
            ]);

            alert()->success('Success', '')->persistent('Close');
            return redirect()->back();
        }
    }
}
