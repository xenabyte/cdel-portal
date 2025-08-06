<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProgrammeCategory;
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
use App\Models\StudentMovement;
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
        $exitApplications = StudentExit::where('status', 'pending')->orderBy('id', 'DESC')->get(); 
        return view('admin.studentExits', [
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
            alert()->error('Oops!', 'Student exit application record not found')->persistent('Close');
            return redirect()->back();
        }

        // return redirect(asset($studentExit->file));

        $student = Student::find($studentExit->student_id);

        return view('admin.verifyStudentExit', [
            'studentExit' => $studentExit,
            'student' => $student
        ]);
    }

    public function manageExitApplication(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
            'action' => 'required',
        ]);

        $studentExit = StudentExit::find($request->exit_id);
        $student = Student::find($studentExit->student_id);
        $programmeCateogoryId = $student->programme_category_id;
    
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCateogoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return view('admin.verifyStudentExit', [
                'studentExit' => $studentExit,
                'student' => $student
            ]);
        }

        $studentExit->status = $request->action;

        if($studentExit->save()){
            
            $pdf = new Pdf();
            $exitApplication = $pdf->generateExitApplication($academicSession, $student->id, $studentExit->id);
            $studentExit->file = $exitApplication;
            $studentExit->save();

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'Your exit application has been '.$request->action;

            $mail = new NotificationMail($senderName, $message, $receiverName, $exitApplication);
            if(env('SEND_MAIL')){
                Mail::to($student->email)->send($mail);
            }
            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'attachment' => $exitApplication,
                'status' => 0
            ]);

            alert()->success('Success', 'Application '.$request->action)->persistent('Close');
            return view('admin.verifyStudentExit', [
                'studentExit' => $studentExit,
                'student' => $student
            ]);
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return view('admin.verifyStudentExit', [
            'studentExit' => $studentExit,
            'student' => $student
        ]);
    }

    public function bulkManageExitApplications(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_ids' => 'required|array',
            'action' => 'required',
        ]);
    
        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
    
        $exitIds = $request->exit_ids;
    
        foreach ($exitIds as $exitId) {
            if (!$studentExit = StudentExit::find($exitId)) {
                continue; // Skip if record not found
            }
    
            $studentExit->status = $request->action;
            if ($studentExit->save()) {
                $student = Student::find($studentExit->student_id);

                $programmeCategoryId = $student->programme_category_id;
                $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
                $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;


                if (!$programmeCategoryId || !isset($academicSession)) {
                    continue;
                }

                
                
                $pdf = new Pdf();
                $exitApplication = $pdf->generateExitApplication($academicSession, $student->id, $studentExit->id);
                $studentExit->file = $exitApplication;
                $studentExit->save();
    
                $senderName = env('SCHOOL_NAME');
                $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
                $message = 'Your exit application has been ' . $request->action;
    
                $mail = new NotificationMail($senderName, $message, $receiverName, $exitApplication);
                if(env('SEND_MAIL')){
                    Mail::to($student->email)->send($mail);
                }
                
                Notification::create([
                    'student_id' => $student->id,
                    'description' => $message,
                    'attachment' => $exitApplication,
                    'status' => 0
                ]);
            }
        }
    
        alert()->success('Success', 'Applications ' . $request->action)->persistent('Close');
        return redirect()->back();
    }
    

    public function verifyStudentExits(Request $request){

        return view('admin.verifyStudentExit');
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

        return view('admin.verifyStudentExit', [
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

        $studentExit->return_at = Carbon::now();

        $student = Student::find($studentExit->student_id);

        if($studentExit->save()){
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
            return view('admin.verifyStudentExit', [
                'studentExit' => $studentExit,
                'student' => $student
            ]);
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return view('admin.verifyStudentExit', [
            'studentExit' => $studentExit,
            'student' => $student
        ]);
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

        $studentExit->exited_at = Carbon::now();

        $student = Student::find($studentExit->student_id);

        if($studentExit->save()){

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
            return view('admin.verifyStudentExit', [
                'studentExit' => $studentExit,
                'student' => $student
            ]);
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return view('admin.verifyStudentExit', [
            'studentExit' => $studentExit,
            'student' => $student
        ]);
    }

    public function studentMovements(){
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', ProgrammeCategory::UNDERGRADUATE)->first();
        $students = Student::where('programme_category_id', $programmeCategory->id)->where('is_active', true)->where('is_passed_out', false)->where('is_rusticated', false)->get();

        return view('admin.studentMovements', [
            'programmeCategory' => $programmeCategory,
            'students' => $students
        ]);
    }

    public function getStudentMovement(Request $request){

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Student::find($request->student_id);
        if(!$student) {
            alert()->error('Error', 'Invalid Student')->persistent('Close');
            return redirect()->back();
        }

        $exitApplications = StudentExit::where('student_id', $student->id)->orderBy('id', 'DESC')->get(); 

        return view('admin.studentMovements', [
            'student' => $student,
            'exitApplications' => $exitApplications
        ]);
    }

    public function createMovement(Request $request){

        $request->validate([
            'student_id' => 'required|exists:students,id',
            'movement_type' => 'required|in:entry,exit',
            'movement_time' => 'required|date',
            'reason' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
        ]);

        StudentMovement::create($request->all());

        $student = Student::find($request->student_id);
        $exitApplications = StudentExit::where('student_id', $student->id)->orderBy('id', 'DESC')->get(); 


        alert()->success('Success', 'Movement recorded successfully')->persistent('Close');

       return view('admin.studentMovements', [
            'student' => $student,
            'exitApplications' => $exitApplications
        ]);
    }
}
