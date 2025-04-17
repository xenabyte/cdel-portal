<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\GlobalSetting as Setting;
use App\Models\Admin;
use App\Models\Guardian;
use App\Models\Notification;
use App\Models\Student;

use App\Mail\NotificationMail;

use App\Libraries\Sms\Sms;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class CommunicationController extends Controller
{
    //
    public function messageStudent(){
        return view('admin.messageStudent');
    }

    public function messageAllStudent(){
        return view('admin.messageAllStudent');
    }

    public function getStudent(Request $request){
        $validator = Validator::make($request->all(), [
            'reg_number' => 'required',
            'type' => 'required',
            'url' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentIdCode = $request->reg_number;
        if($request->type == 'Student'){
            return $this->getSingleStudent($studentIdCode, $request->url);
        }
    }

    public function sendStudentMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'message' => 'required',
            'studentId' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentId = $request->studentId;

        if (!$student = Student::find($studentId)) {
            alert()->error('Oops!', 'Student record not found')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, $request->url);
        }

        $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
        $phoneNumber = $student->applicant->phone_number;


        $type = $request->type;
        $message = $request->message;
        $copyGuardian = $request->copy_guardian;
        $senderName = env('SCHOOL_NAME');
        $guardianEmail = null;
        $guardianPhone = null;
        $ccEmail = null;
        $ccPhone = null;

        if($copyGuardian){
            $guardianEmail = $student->applicant->guardian->email;
            $guardianPhone = $student->applicant->guardian->phone_number;
            if(!empty($guardianEmail) && filter_var($guardianEmail, FILTER_VALIDATE_EMAIL)){
                $ccEmail = $guardianEmail;
            }
            if(!empty($guardianPhone) && (strtolower($guardianPhone) != 'null')){
                $ccPhone = $guardianPhone;
            }
        }

        $attachmentUrl = null;
        if($request->has('attachment')) {
            $attachmentUrl = 'uploads/communication/'.time().'.'.$request->file('attachment')->getClientOriginalExtension();
            $attachment = $request->file('attachment')->move('uploads/communication', $attachmentUrl);
        }


        if ($type !== 'sms') {
            $mail = new NotificationMail($senderName, $message, $receiverName, $attachmentUrl);
            if(env('SEND_MAIL')){
                if (!empty($ccEmail)) {
                    Mail::to($student->email)->cc($ccEmail)->send($mail);
                }else{
                    Mail::to($student->email)->send($mail);
                }
            }
        }

        $smsInstance = new Sms();
        if($type != 'email'){
            $smsInstance->sendSms($message, $phoneNumber);
            if (!empty($ccPhone)) {
                $smsInstance->sendSms($message, $ccPhone);
            }
        }

        if($copyGuardian){
            Notification::create([
                'guardian_id' => $student->applicant->guardian->id,
                'description' => $message,
                'attachment' => $attachmentUrl,
                'status' => 0
            ]);
        }

        Notification::create([
            'student_id' => $student->id,
            'description' => $message,
            'attachment' => $attachmentUrl,
            'status' => 0
        ]);

        alert()->success('Good Job!', 'Message Sent')->persistent('Close');
        return $this->getSingleStudent($student->matric_number, $request->url);
    }

    public function sendParentMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'type' => 'required',
            'message' => 'required',
            'guardianId' => 'required',
            'studentId' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentId = $request->studentId;
        $student = Student::find($studentId);

        if (!$student = Student::find($studentId)) {
            alert()->error('Oops!', 'Student record not found')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, $request->url);
        }

        $guardianId = $request->guardianId;
        $guardian = Guardian::find($guardianId);

        if(!$guardian = Guardian::find($guardianId)) {
            alert()->error('Oops!', 'Guardian record not found')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, $request->url);
        }

        $receiverName = $guardian->name;
        $phoneNumber = $guardian->phone_number;
        $receiverEmail = $guardian->email;

        $type = $request->type;
        $message = $request->message;
        $senderName = env('SCHOOL_NAME');

        $attachmentUrl = null;
        if($request->has('attachment')) {
            $attachmentUrl = 'uploads/communication/'.time().'.'.$request->file('attachment')->getClientOriginalExtension();
            $attachment = $request->file('attachment')->move('uploads/communication', $attachmentUrl);
        }

        if ($type !== 'sms') {
            $mail = new NotificationMail($senderName, $message, $receiverName, $attachmentUrl);
            if(env('SEND_MAIL')){
                Mail::to($receiverEmail)->send($mail);
            }
        }

        $smsInstance = new Sms();
        if($type != 'email'){
            $smsInstance->sendSms($message, $phoneNumber);
        }

        Notification::create([
            'guardian_id' => $guardianId,
            'description' => $message,
            'attachment' => $attachmentUrl,
            'status' => 0
        ]);

        alert()->success('Good Job!', 'Message Sent')->persistent('Close');
        return $this->getSingleStudent($student->matric_number, $request->url);
    }
}
