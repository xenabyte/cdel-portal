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

        $student = Student::find($studentId);
        $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
        $phoneNumber = $student->applicant->phone_number;

        if (!$student = Student::find($studentId)) {
            alert()->error('Oops!', 'Student record not found')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, $request->url);
        }

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
            $attachment = $request->file('attachment')->move('uploads/communication', $imageUrl);
        }


        if ($type !== 'sms') {
            $mail = new NotificationMail($senderName, $message, $receiverName);
            if (!empty($ccEmail)) {
                Mail::to($student->email)->cc($ccEmail)->send($mail);
            }else{
                Mail::to($student->email)->send($mail);
            }
        }

        if($type != 'email'){
            Sms::sendSms($message, $phoneNumber);
            if (!empty($ccPhone)) {
                Sms::sendSms($ccPhone, $phoneNumber);
            }
        }

        alert()->success('Oops!', 'Message Sent')->persistent('Close');
        return $this->getSingleStudent($student->matric_number, $request->url);
    }
}
