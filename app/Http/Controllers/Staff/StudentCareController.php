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

    public function manageExitApplication(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
            'action' => 'required',
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

        $studentExit->status = $request->action;

        if($studentExit->save()){
            $student = Student::find($studentExit->student_id);

            $pdf = new Pdf();
            $exitApplication = $pdf->generateExitApplication($academicSession, $student->id, $studentExit->id);
            $studentExit->file = $exitApplication;
            $studentExit->save();

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'Your exit application has been '.$request->action;

            $mail = new NotificationMail($senderName, $message, $receiverName, $exitApplication);
            Mail::to($student->email)->send($mail);
            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'attachment' => $exitApplication,
                'status' => 0
            ]);


            alert()->success('Success', 'Application '.$request->action)->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
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
            Mail::to($student->email)->send($mail);
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
            Mail::to($student->email)->send($mail);
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
