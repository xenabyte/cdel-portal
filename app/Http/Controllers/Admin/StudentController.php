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

use App\Models\Staff;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\Guardian;

use App\Mail\NotificationMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudentController extends Controller
{
    //

    public function resendGuardianOnboarding(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$student = Student::with('applicant')->where('id', $request->student_id)->first()){
            alert()->error('Oops!', 'Student record not found')->persistent('Close');
            return redirect()->back();
        }

        if(!$applicant = Applicant::where('id', $student->user_id)->first()){
            alert()->error('Oops!', 'Applicant record not found')->persistent('Close');
            return redirect()->back();
        }

        $guardianId = $applicant->guardian_id;
        $guardian = Guardian::find($guardianId);
        if(!$guardian = Guardian::where('id', $guardianId)->first()){
            alert()->error('Oops!', 'Guardian record not found')->persistent('Close');
            return redirect()->back();
        }

        $accessCode = $this->generateAccessCode();
        $guardian->password = Hash::make($accessCode);
        $guardian->passcode = $accessCode;

        if($guardian->save()){
            $sendGuardianEmail =  $this->sendGuardianOnboardingMail($student);

            alert()->success('Success', 'Guardian onboarding email sent')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function generateStudentReferrerCode() {
        
        $students = Student::all();

        foreach ($students as $student) {
            $student->referral_code = $this->generateReferralCode(10);
            $student->save();
        }

        return $students;
    }
}
