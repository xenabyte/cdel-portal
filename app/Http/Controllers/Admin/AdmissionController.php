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

use App\Mail\ApplicationMail;

use App\Libraries\Pdf\Pdf;
use App\Libraries\Google\Google;

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
        $applicants = Applicant::where('academic_session', $academicSession)->get();

        return view('admin.applicants', [
            'applicants' => $applicants,
        ]);
    }

    public function applicantWithSession(Request $request){
        $applicants = Applicant::with('programme', 'olevels', 'guardian')->where('academic_session', $request->session)->get();
        
        return view('admin.applicants', [
            'applicants' => $applicants
        ]);
    }

    public function applicant(Request $request, $slug){
        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('slug', $slug)->first();
        $programmes = Programme::get();
        $levels = AcademicLevel::get();
        
        return view('admin.applicant', [
            'applicant' => $applicant,
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function manageAdmission(Request $request){
        $validator = Validator::make($request->all(), [
            'applicant_id' => 'required',
            'programme_id' => 'required',
            'level' => 'required',
            'status' => 'required'
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$applicant = Applicant::with('programme')->where('id', $request->applicant_id)->first()){
            alert()->error('Oops', 'Invalid Applicant Information')->persistent('Close');
            return redirect()->back();
        }

        $applicantId = $applicant->id;
        $programmeId = $request->programme_id;
        $programme = Programme::find($programmeId);
        $codeNumber = $programme->code_number;
        $code = $programme->code;
        $matricNumber = $codeNumber.'/'.$code.$

        $status = $request->status;
        $accessCode = $applicant->passcode;
        $email = $applicant->email;
        $name = $applicant->lastname.' '.$applicant->othernames;
        

        if(strtolower($status) == 'admitted'){
            //create an email with tpa letter heading 
            $pdf = new Pdf();
            $admissionLetter = $pdf->generateAdmissionLetter($applicant->slug);
            $applicant->admission_letter = $admissionLetter;
            $google = new Google();
            $createStudentEmail = $google->createUser($email, $applicant->firstname, $applicant->lastname, $accessCode);
            //create student records
            $studentId = Student::create([
                'user_id' => $applicantId,
                'email' => $email,
                'password' => bcrypt($accessCode),
                'student_id' => $studentId,
            ])->id;



            //In the email, create and provide student portal login information
        }

        $applicant->status = $status;

        if($applicant->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    
}
