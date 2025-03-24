<?php

namespace App\Http\Controllers\Center;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


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
        $student->schoolFeeDetails = $this->checkSchoolFees($student, $student->academic_session, $student->level_id);

        return view('center.studentProfile', [
            'student' => $student,
            'academicLevels' => $academicLevels,
            'sessions' => $sessions
        ]);
    }
}