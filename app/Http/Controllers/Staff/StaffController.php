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


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StaffController extends Controller
{
    //

    public function index(Request $request){

        return view('staff.home');
    }

    public function mentee(Request $request){

        return view('staff.mentee');
    }

    public function courses(Request $request){

        return view('staff.courses');
    }

    public function reffs(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];
        $referalCode = $staff->referral_code;

        $applicants = Applicant::with('student')->where('referrer', $referalCode)->where('academic_session', $applicationSession)->get();


        return view('staff.reffs', [
            'applicants' => $applicants,
        ]);
    }

    public function getAllReffs(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $referalCode = $staff->referral_code;

        $applicants = Applicant::with('student')->where('referrer', $referalCode)->get();


        return view('staff.reffs', [
            'applicants' => $applicants,
        ]);
    }

    public function applicant(Request $request, $slug){
        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('slug', $slug)->first();
        
        return view('staff.applicant', [
            'applicant' => $applicant,
        ]);
    }

    public function applicantWithSession(Request $request){
        $applicants = Applicant::with('programme', 'olevels', 'guardian', 'student')->where('academic_session', $request->session)->get();
        
        return view('staff.reffs', [
            'applicants' => $applicants
        ]);
    }

    public function student(Request $request, $slug){
        $student = Student::with('applicant', 'applicant.utmes', 'programme', 'transactions')->where('slug', $slug)->first();

        return view('staff.student', [
            'student' => $student
        ]);
    }

    public function courseAllocation(Request $request){

        return view('staff.courseAllocation');
    }

    public function roleAllocation(Request $request){

        return view('staff.roleAllocation');
    }

    
}
