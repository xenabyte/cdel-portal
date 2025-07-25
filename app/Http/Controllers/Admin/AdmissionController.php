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

use App\Models\Faculty;
use App\Models\Programme;
use App\Models\Transaction;
use App\Models\User as Applicant;
use App\Models\Olevel;
use App\Models\Guardian;
use App\Models\NextOfKin;
use App\Models\AcademicLevel;
use App\Models\Student;
use App\Models\Payment;
use App\Models\ProgrammeCategory;


use App\Mail\ApplicationMail;
use App\Mail\AdmissionMail;

use App\Libraries\Pdf\Pdf;
use App\Libraries\Sms\Sms;

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

    public function applicants(Request $request, $programmeCategory){

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $applicationSession = $programmeCategory->academicSessionSetting->application_session ?? null;
        if (!$applicationSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $applicants = Applicant::where('programme_category_id', $programmeCategoryId)
        ->where(function($query) {
            $query->where('status', 'submitted')
                  ->orWhereNull('status');
        })
        ->where('academic_session', $applicationSession)
        ->orderBy('status', 'DESC')
        ->get();
        
        $programmes = Programme::get(); 
        $levels = AcademicLevel::get();

        return view('admin.applicants', [
            'applicants' => $applicants,
            'programmes' => $programmes,
            'levels' => $levels,
            'programmeCategory' => $programmeCategory
        ]);
    }

    public function matriculants(Request $request, $programmeCategory){

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;
        if (!$academicSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $matriculants = Applicant::with('student')
        ->where('academic_session', $academicSession)
        ->where('status', 'Admitted')
        ->where('programme_category_id', $programmeCategoryId)
        ->get();

        return view('admin.matriculants', [
            'matriculants' => $matriculants,
            'programmeCategory' => $programmeCategory
        ]);
    }

    public function applicantWithSession(Request $request){
        $applicants = Applicant::with('programme', 'olevels', 'guardian')->where('academic_session', $request->session)->orderBy('status', 'DESC')->get();
        $programmes = Programme::get(); 
        $levels = AcademicLevel::get();
        
        return view('admin.applicants', [
            'applicants' => $applicants,
            'session' => $request->session,
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function applicant(Request $request, $slug){
        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('slug', $slug)->first();
        $programmes = Programme::get(); //where('category_id', $applicant->programme->category_id)->
        $levels = AcademicLevel::get();
        
        return view('admin.applicant', [
            'applicant' => $applicant,
            'programmes' => $programmes,
            'levels' => $levels
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

        if(strtolower($request->status) == 'advanceStudies'){
            $validator = Validator::make($request->all(), [
                'advanceStudiesProgrammeType' => 'required',
            ]);


            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return redirect()->back();
            }
        }

        if(!$applicant = Applicant::with('programme', 'programmeCategory')->where('id', $request->applicant_id)->first()){
            alert()->error('Oops', 'Invalid Applicant Information')->persistent('Close');
            return redirect()->back();
        }

        $programmeCategoryId = $applicant->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $admissionSession = $programmeCategory->academicSessionSetting->admission_session ?? null;
        if (!$admissionSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $applicantId = $applicant->id;
        $status = $request->status;

        $applicant->status = $status;

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

            $studentFromPrevSession = Student::where('email', $applicant->email)->where('academic_session', '!=', $admissionSession)->first();
            if ($studentFromPrevSession) {
                $studentFromPrevSession->email = $email.time();
                $studentFromPrevSession->slug = $studentFromPrevSession->slug.time();
                $studentFromPrevSession->update();
            }

            $student = Student::where('email', $email)->where('academic_session', $admissionSession)->first();
            if($student){
                $studentId = $student->id;
            }else{
                $studentId = Student::create([
                    'slug' => $applicant->slug.'_'.time(),
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
                    'batch' => $request->batch,
                    'programme_category_id' => $applicant->programmeCategory->id,
                ])->id;
            }


            //create an email with tau letter heading 
            $pdf = new Pdf();
            $admissionLetter = $pdf->generateAdmissionLetter($studentId);

            $student = Student::with('programme', 'applicant')->where('id', $studentId)->first();
            $student->admission_letter = $admissionLetter;
            $student->save();
            if(env('SEND_MAIL')){
                Mail::to($student->email)->send(new AdmissionMail($student));
            }
            $smsInstance = new Sms();
            $file = asset($admissionLetter);
            $fileUrl = $this->shortURL($file);
            $message = "Congratulations, Dear ".$name." you have been granted admission to ".env('SCHOOL_NAME')." Welcome to our community of scholars, where your journey towards excellence begins. Download your admission letter using this link: ".$fileUrl;
            $phoneNumber= $student->applicant->phone_number;
            $smsInstance->sendSms($message, $phoneNumber);
        }

        if(strtolower($status) == 'advanceStudies'){
            $advanceStudiesProgrammeType = $request('advanceStudiesProgrammeType');
        }


        if(strtolower($status) == 'reverse_admission'){
            $student = Student::where('user_id', $applicantId)->first();

            if (file_exists($student->admission_letter)) {
                unlink($student->admission_letter);
            } 

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

    public function students(Request $request, $programmeCategory) {
        $programmes = Programme::get();

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;
        
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;
        $admissionSession = $programmeCategory->academicSessionSetting->admission_session ?? null;
        $applicationSession = $programmeCategory->academicSessionSetting->application_session ?? null;
        if (!$academicSession || !$admissionSession || !$applicationSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $students = Student::with('applicant', 'programme')
            ->where('academic_session', $admissionSession)
            ->where('programme_category_id', $programmeCategoryId)
            ->whereHas('applicant', function ($query) use ($admissionSession) {
                $query->where('academic_session', $admissionSession);
            })
            ->get();
    
        foreach ($students as $student) {

            $acceptancePaymentTypeId = Payment::where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)
            ->where('academic_session', $applicationSession)
            ->where('programme_id', $student->programme_id)
            ->where('programme_category_id', $student->programme_category_id)
            ->value('id');

            if(!$acceptancePaymentTypeId){
                $acceptancePaymentTypeId = Payment::where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)
                ->where('academic_session', $applicationSession)
                ->where('programme_category_id', $student->programme_category_id)
                ->value('id');
            }

            $acceptanceFee = Transaction::where('student_id', $student->id)
                ->where('payment_id', $acceptancePaymentTypeId)
                ->where('session', $admissionSession)
                ->where('status', 1)
                ->first();
    
            $student->acceptanceFeeStatus = $acceptanceFee ? true : false;
        }
    
        return view('admin.students', [
            'students' => $students,
            'programmes' => $programmes,
            'programmeCategory' => $programmeCategory
        ]);
    }
    

    public function student(Request $request, $slug){
        $student = Student::with('applicant', 'applicant.utmes', 'programme')->where('slug', $slug)->first();

        return view('admin.student', [
            'student' => $student
        ]);
    }

    public function generateAdmissionLetter(Request $request){
        $validator = Validator::make($request->all(), [
            'applicant_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        
        if(!$applicant = Applicant::with('programme')->where('id', $request->applicant_id)->first()){
            alert()->error('Oops', 'Invalid Applicant Information')->persistent('Close');
            return redirect()->back();
        }

        $student = Student::with('programme', 'applicant')->where('user_id', $applicant->id)->first();

        $pdf = new Pdf();
        $admissionLetter = $pdf->generateAdmissionLetter($student->id);

        if($admissionLetter){
            $student->admission_letter = $admissionLetter;
            $student->save();
    
            if(env('SEND_MAIL')){
                //In the email, create and provide student portal login information
                Mail::to($student->email)->send(new AdmissionMail($student));
            }
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateApplicant(Request $request){
        $validator = Validator::make($request->all(), [
            'lastname' => 'required|string|max:255',
            'othernames' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:15',
            'dob' => 'required|date',
            'gender' => 'required|string|in:Male,Female',
            'address' => 'required|string|max:255',
            'programme_id' => 'required|exists:programmes,id', 
            'sitting_no' => 'required|integer',
            'jamb_reg_no' => 'nullable|string|max:20'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        // Find the applicant by ID
        $applicant = Applicant::findOrFail($request->user_id); 

        // Update applicant data
        $applicant->lastname = $request->input('lastname');
        $applicant->othernames = $request->input('othernames');
        $applicant->email = $request->input('email');
        $applicant->phone_number = $request->input('phone_number');
        $applicant->dob = $request->input('dob');
        $applicant->gender = $request->input('gender');
        $applicant->address = $request->input('address');
        $applicant->programme_id = $request->input('programme_id');
        $applicant->sitting_no = $request->input('sitting_no');
        $applicant->jamb_reg_no = $request->input('jamb_reg_no');

        $applicant->save();

        alert()->success('Changes Saved', '')->persistent('Close');
        return redirect()->back();
    }

    public function createNewApplicant(Request $request){

        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $applicationSession = $programmeCategory->academicSessionSetting->application_session ?? null;
        
        if (!$applicationSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users,email,NULL,id,academic_session,' . $applicationSession,
            'lastname' => 'required',
            'password' => 'required|confirmed',
            'phone_number' => 'required',
            'othernames' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        
        $applicationType = $request->input('applicationType');

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->lastname .' '. $request->othernames)));

        $applicationPayment = Payment::with('structures')->where('academic_session', $applicationSession)->where('type', Payment::PAYMENT_TYPE_GENERAL_APPLICATION)->first();
        $interApplicationPayment = Payment::with('structures')->where('academic_session', $applicationSession)->where('type', Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION)->first();

        $payment = $applicationPayment;
        if($applicationType == 'Inter Transfer Application'){
            $payment = $interApplicationPayment;
        }
        $amount = $payment->structures->sum('amount');

        $newApplicant = ([
            'slug' => $slug,
            'email' => strtolower($request->email),
            'lastname' => ucwords($request->lastname),
            'phone_number' => $request->phone_number,
            'othernames' => ucwords($request->othernames),
            'password' => Hash::make($request->password),
            'passcode' => $request->password,
            'academic_session' => $applicationSession,
            'application_type' => $applicationType,
            'programme_category_id' => $request->programme_category_id,
        ]);


        $reference = $this->generateAccessCode();
        if($applicant = Applicant::create($newApplicant)){
            $applicationNumber = env('SCHOOL_CODE').'/'.substr($applicationSession, 0, 4).sprintf("%03d", ($applicant->id + env('APPLICATION_STARTING_NUMBER')));
            $applicant->application_number = $applicationNumber;
            $applicant->save();

            
            $transaction = Transaction::create([
                'payment_id' =>  $payment->id,
                'user_id' => $applicant->id,
                'amount_payed' => $amount,
                'payment_method' => 'Manual',
                'reference' => $reference,
                'session' => $applicationSession,
                'status' => 1
            ]);

            alert()->success('User Created', '')->persistent('Close');
            return redirect()->back();

        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

}
