<?php

namespace App\Http\Controllers\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Partner;
use App\Models\User as Applicant;
use App\Models\Programme;
use App\Models\AcademicLevel;
use App\Models\Student;


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class PartnerController extends Controller
{
    //

    public function index(Request $request){
        $partner = Auth::guard('partner')->user();

        if(!$partner->status){
            return view('partner.approval',);
        }

        return view('partner.home');
    }

    public function transactions(Request $request){

        return view('partner.transactions');
    }

    public function applicants(Request $request)
    {
        $globalData = $request->input('global_data');
        $partner = Auth::guard('partner')->user();

        if (!$partner->status) {
            return view('partner.approval');
        }

        $allApplicants = collect();

        foreach ($globalData->sessionSettings as $programmeCategoryId => $setting) {
            $applicationSession = $setting->application_session ?? null;

            if ($applicationSession) {
                $applicants = Applicant::where('academic_session', $applicationSession)
                    ->where('partner_id', $partner->id)
                    ->get();

                $allApplicants = $allApplicants->merge($applicants);
            }
        }

        // Remove duplicates if necessary
        $allApplicants = $allApplicants->unique('id')->values();

        return view('partner.applicants', [
            'applicants' => $allApplicants,
        ]);
    }

    public function applicantWithSession(Request $request){
        $applicants = Applicant::with('programme', 'olevels', 'guardian')->where('academic_session', $request->session)->get();
        $partner = Auth::guard('partner')->user();

        if(!$partner->status){
            return view('partner.approval',);
        }

        return view('partner.applicants', [
            'applicants' => $applicants,
            'session' => $request->session
        ]);
    }

    public function applicant(Request $request, $slug){
        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('slug', $slug)->first();
        $programmes = Programme::get(); //Programme::where('category_id', $applicant->programme->category_id)->get();
        $levels = AcademicLevel::get();
        $partner = Auth::guard('partner')->user();

        if(!$partner->status){
            return view('partner.approval',);
        }
        
        return view('partner.applicant', [
            'applicant' => $applicant,
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function profile(Request $request){
        $partner = Auth::guard('partner')->user();
        if(!$partner->status){
            return view('partner.approval',);
        }

        return view('partner.profile');
    }

    public function students(Request $request)
    {
        $partner = Auth::guard('partner')->user();
        $partnerId = $partner->id;
        $globalData = $request->input('global_data');

        $allStudents = collect();

        foreach ($globalData->sessionSettings as $programmeCategoryId => $setting) {
            $admissionSession = $setting->admission_session ?? null;

            if ($admissionSession) {
                $students = Student::with('applicant', 'programme')
                    ->where('academic_session', $admissionSession)
                    ->whereHas('applicant', function ($query) use ($admissionSession, $partnerId) {
                        $query->where('academic_session', $admissionSession)
                            ->where('partner_id', $partnerId);
                    })
                    ->get();

                $allStudents = $allStudents->merge($students);
            }
        }

        $allStudents = $allStudents->unique('id')->values(); // Ensure no duplicates

        return view('partner.students', [
            'students' => $allStudents,
        ]);
    }

    public function student(Request $request, $slug){
        $student = Student::with('applicant', 'applicant.utmes', 'programme')->where('slug', $slug)->first();

        return view('partner.student', [
            'student' => $student
        ]);
    }

    public function updatePassword (Request $request) {

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required',
            'confirm_password' => 'required'
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $staff = Auth::guard('staff')->user();


        if(\Hash::check($request->old_password, Auth::guard('staff')->user()->password)){
            if($request->password == $request->confirm_password){
                $staff->password = bcrypt($request->password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }
        }else{
            alert()->error('Oops', 'Wrong old password, Try again with the right one')->persistent('Close');
            return redirect()->back();
        }

        if($staff->update()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

}
