<?php

namespace App\Http\Controllers\Center;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;



use App\Models\Student;
use App\Models\Center;
use App\Models\Notification;
use App\Models\AcademicLevel;
use App\Models\Session;

use App\Mail\NotificationMail;
use App\Mail\StudyCenterOnboardingMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudyCenterController extends Controller
{

    public function index(Request $request){
        $center = Auth::guard('center')->user();

        if(empty($center->change_password)){
            return view('center.changePassword');
        }

        return view('center.home');
    }

    public function students(Request $request){

        $center = Auth::guard('center')->user();

        if(empty($center->change_password)){
            return view('center.changePassword');
        }

        return view('center.students');
    }

    public function profile(Request $request){

        return view('center.profile');
    }


    public function studentProfile(Request $request, $slug){
        $student = Student::withTrashed()->with('applicant', 'applicant.utmes', 'programme', 'transactions')->where('slug', $slug)->first();
        $academicLevels = AcademicLevel::orderBy('id', 'desc')->get();
        $sessions = Session::orderBy('id', 'desc')->get();
        $student->schoolFeeDetails = $this->checkSchoolFees($student);

        return view('center.studentProfile', [
            'student' => $student,
            'academicLevels' => $academicLevels,
            'sessions' => $sessions
        ]);
    }

    public function updatePassword (Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required'
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $center = Auth::guard('center')->user();

        if($request->has('case')){
            if($request->password == $request->confirm_password){
                $center->password = bcrypt($request->password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }
            $center->change_password = true;
        }else{
            if(!empty($request->old_password)){
                alert()->error('Oops!', 'Old password is required')->persistent('Close');
                return redirect()->back();
            }

            if(\Hash::check($request->old_password, Auth::guard('center')->user()->password)){
                if($request->password == $request->confirm_password){
                    $center->password = bcrypt($request->password);
                }else{
                    alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                    return redirect()->back();
                }
            }else{
                alert()->error('Oops', 'Wrong old password, Try again with the right one')->persistent('Close');
                return redirect()->back();
            }
        }

        if($center->update()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }
}