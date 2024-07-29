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

use App\Models\Faculty;
use App\Models\Programme;
use App\Models\Transaction;
use App\Models\User as Applicant;
use App\Models\Olevel;
use App\Models\Guardian;
use App\Models\NextOfKin;
use App\Models\AcademicLevel;
use App\Models\Student;


use App\Mail\ApplicationMail;
use App\Mail\AdmissionMail;

use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;


class AdmissionController extends Controller
{
    //

    protected $programmes;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->programmes = Programme::get();
    }

    public function applicants(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['application_session'];
        $applicants = Applicant::where('academic_session', $academicSession)->orderBy('status', 'DESC')->get();
        $programmes = Programme::get(); 
        $levels = AcademicLevel::get();

        return view('staff.applicants', [
            'applicants' => $applicants,
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function applicantWithSession(Request $request){
        $applicants = Applicant::with('programme', 'olevels', 'guardian')->where('academic_session', $request->session)->orderBy('status', 'DESC')->get();
        $programmes = Programme::get(); 
        $levels = AcademicLevel::get();


        return view('staff.applicants', [
            'applicants' => $applicants,
            'session' => $request->session,
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function applicant(Request $request, $slug){
        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('slug', $slug)->first();
        $programmes = Programme::get(); //Programme::where('category_id', $applicant->programme->category_id)->get();
        $levels = AcademicLevel::get();
        
        return view('staff.applicant', [
            'applicant' => $applicant,
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function matriculants(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['application_session'];

        $matriculants = Applicant::with('student')->where('academic_session', $academicSession)->where('status', 'Admitted')->get();

        return view('staff.matriculants', [
            'matriculants' => $matriculants,
        ]);
    }

    public function manageAdmission(Request $request){
        if(strtolower($request->status) == 'admitted'){
            $validator = Validator::make($request->all(), [
                'applicant_id' => 'required',
                'programme_id' => 'required',
                'level_id' => 'required',
                'status' => 'required',
                'batch' => 'required',
            ]);


            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return redirect()->back();
            }
        }
        

        if(!$applicant = Applicant::with('programme')->where('id', $request->applicant_id)->first()){
            alert()->error('Oops', 'Invalid Applicant Information')->persistent('Close');
            return redirect()->back();
        }

        $globalData = $request->input('global_data');
        $applicationSession = $globalData->sessionSetting['application_session'];
        $admissionSession = $globalData->sessionSetting['admission_session'];

        $applicantId = $applicant->id;
        $status = $request->status;

        if(strtolower($status) == 'admitted'){
            $programmeId = $request->programme_id;
            $programme = Programme::with('department', 'department.faculty')->where('id', $programmeId)->first();
            $parts = explode("/", $admissionSession);
            $entryYear = $parts[1];

            $accessCode = $applicant->passcode;
            $email = $applicant->email;
            $name = $applicant->lastname.' '.$applicant->othernames;
            $nameParts = explode(' ', $applicant->othernames);
            $firstName = $nameParts[0];  

            //create student records
            $studentId = Student::create([
                'slug' => $applicant->slug,
                'email' => $applicant->email,
                'password' => bcrypt($accessCode),
                'passcode' => $accessCode,
                'user_id' => $applicantId,
                'academic_session' => $admissionSession,
                'level_id' => $request->level_id,
                'faculty_id' => $programme->department->faculty->id,
                'department_id' => $programme->department->id,
                'programme_id' => $programme->id,
                'entry_year' => $entryYear,
                'batch' => $request->batch
            ])->id;

            //create an email with tau letter heading 
            $pdf = new Pdf();
            $admissionLetter = $pdf->generateAdmissionLetter($applicant->slug);

            $student = Student::with('programme', 'applicant')->where('id', $studentId)->first();
            $student->admission_letter = $admissionLetter;
            $student->save();

            //In the email, create and provide student portal login information
            Mail::to($student->email)->send(new AdmissionMail($student));

        }

        $applicant->status = $status;

        if(strtolower($status) == 'reverse_admission'){
            $student = Student::where('user_id', $applicantId)->first();
            unlink($student->admission_letter);
            $student->forceDelete();

            $applicant->status = 'submitted';
        }

        if($applicant->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function students(Request $request){
        $globalData = $request->input('global_data');
        $applicationSession = $globalData->sessionSetting['application_session'];
        $admissionSession = $globalData->sessionSetting['admission_session'];

        $students = Student::with('applicant', 'programme')
        ->where('academic_session', $admissionSession)
        ->whereHas('applicant', function ($query) use ($admissionSession) {
            $query->where('academic_session', $admissionSession);
        })
        ->get();

        return view('staff.students', [
            'students' => $students
        ]);
    }

    public function student(Request $request, $slug){
        $student = Student::with('applicant', 'applicant.utmes', 'programme')->where('slug', $slug)->first();

        return view('staff.student', [
            'student' => $student
        ]);
    }

    public function allStudents(){

        $students = Student::
            with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->get();
        
        return view('staff.allStudents', [
            'students' => $students
        ]);
    }
}
