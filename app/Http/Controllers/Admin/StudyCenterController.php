<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


use App\Models\Student;
use App\Models\Center;
use App\Models\Notification;

use App\Mail\NotificationMail;
use App\Mail\StudyCenterOnboardingMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudyCenterController extends Controller
{
    //

    public function studyCenters(){
        $studyCenters = Center::all();

        return view('admin.studyCenters', [
           'studyCenters' => $studyCenters
        ]);
    }

    public function addStudyCenter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'center_name' => 'required|string|max:255|unique:centers,center_name',
            'address' => 'required|string',
            'email' => 'required|email|unique:centers,email',
            'phone_number' => 'required|digits:11',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Failed', $validator->errors()->first())->persistent('Close');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $password = $this->generateRandomString();

        $center = Center::create([
            'name' => $request->name,
            'center_name' => $request->center_name,
            'address' => $request->address,
            'email' => $request->email,
            'password' => Hash::make($password),
            'phone_number' => $request->phone_number,
            'slug' => Str::slug($request->center_name),
        ]);

        if($center){
            //send a notification to center Administrator
            $center->view_password = $password;

            $senderName = env('SCHOOL_NAME');

            $mail = new StudyCenterOnboardingMail($senderName, $center);
            Mail::to($request->email)->send($mail);

            alert()->success('Success', 'Study Center Created Successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function updateStudyCenter(Request $request)
    {
        $id = $request->center_id;

        $validator = Validator::make($request->all(), [
            'center_id' => 'required',
            'name' => 'required|string|max:255',
            'center_name' => 'required|string|max:255|unique:centers,center_name,' . $id,
            'address' => 'required|string',
            'email' => 'required|email|unique:centers,email,' . $id,
            'phone_number' => 'required|digits:11',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Failed', $validator->errors()->first())->persistent('Close');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $center = Center::find($id);

        if (!$center) {
            alert()->error('Not Found', 'Study Center not found')->persistent('Close');
            return redirect()->back();
        }

        $center->update([
            'name' => $request->name,
            'center_name' => $request->center_name,
            'address' => $request->address,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'slug' => Str::slug($request->center_name),
        ]);

        alert()->success('Success', 'Study Center Updated Successfully')->persistent('Close');
        return redirect()->back();
    }

    public function deleteStudyCenter(Request $request)
    {

        $id = $request->center_id;

        $validator = Validator::make($request->all(), [
            'center_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Failed', $validator->errors()->first())->persistent('Close');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $center = Center::find($id);

        if (!$center) {
            alert()->error('Not Found', 'Study Center not found')->persistent('Close');
            return redirect()->back();
        }

        if($center->delete()){
            alert()->success('Success', 'Study Center Deleted Successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function assignStudyCenter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'center_id' => 'required|exists:centers,id',
            'student_id' => 'required|exists:students,id',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Failed', $validator->errors()->first())->persistent('Close');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $student = Student::find($request->student_id);

        if (!$student) {
            alert()->error('Not Found', 'Student not found')->persistent('Close');
            return redirect()->back();
        }

        $priorCenter = $student->center; // Get previous center before updating

        // Update Student's Study Center
        $student->center_id = $request->center_id;
        
        if ($student->save()) {
            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
            $newCenter = Center::find($request->center_id);
            $newCenterAdminEmail = $newCenter->email;
            $newCenterName = $newCenter->center_name;

            // Notify Student
            $studentMessage = "Dear $receiverName,  
            You have been successfully assigned to the study center: $newCenterName.  
            Please check your email for further details regarding your classes and schedule.  

            Best regards,  
            $senderName Team";

            Mail::to($student->email)->send(new NotificationMail($senderName, $studentMessage, $receiverName));

            Notification::create([
                'student_id' => $student->id,
                'description' => "Assigned to $newCenterName",
                'status' => 0
            ]);

            // Notify New Study Center Admin
            $newCenterMessage = "Dear $newCenterName Admin,  
            A new student, $receiverName, has been assigned to your study center.  
            Kindly ensure they receive all necessary guidance and information.  

            Best regards,  
            $senderName Team";

            Mail::to($newCenterAdminEmail)->send(new NotificationMail($senderName, $newCenterMessage, $newCenterName));

            Notification::create([
                'center_id' => $newCenter->id,
                'description' => "New student ($receiverName) assigned to $newCenterName",
                'status' => 0
            ]);

            // **Notify Prior Study Center if Student Had One**
            if ($priorCenter) {
                $priorCenterAdminEmail = $priorCenter->email;
                $priorCenterName = $priorCenter->center_name;

                $priorCenterMessage = "Dear $priorCenterName Admin,  
                Please be informed that the student $receiverName has been reassigned to another study center ($newCenterName).  

                Best regards,  
                $senderName Team";

                Mail::to($priorCenterAdminEmail)->send(new NotificationMail($senderName, $priorCenterMessage, $priorCenterName));

                Notification::create([
                    'center_id' => $priorCenter->id,
                    'description' => "Student ($receiverName) reassigned to $newCenterName",
                    'status' => 0
                ]);
            }

            alert()->success('Success', 'Student reassigned successfully, both centers notified')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }
}
