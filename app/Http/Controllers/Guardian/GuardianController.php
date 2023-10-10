<?php

namespace App\Http\Controllers\Guardian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Guardian;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\Programme;
use App\Models\AcademicLevel;
use App\Models\Session;
use App\Models\Course;
use App\Models\Notification;
use App\Models\GradeScale;
use App\Models\CourseRegistration;
use App\Models\Role;
use App\Models\StaffRole;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\LevelAdviser;
use App\Models\Payment;
use App\Models\ResultApprovalStatus;
use App\Models\Transaction;

use Paystack;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

use App\Libraries\Result\Result;
use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class GuardianController extends Controller
{
    //

    public function index(Request $request){

        return view('guardian.home');
    }

    public function students(Request $request){

        return view('guardian.students');
    }

    public function profile(Request $request){

        return view('guardian.profile');
    }

    
    public function studentProfile(Request $request, $slug){
        $student = Student::with('applicant', 'applicant.utmes', 'programme', 'transactions')->where('slug', $slug)->first();
        $academicLevels = AcademicLevel::orderBy('id', 'desc')->get();
        $sessions = Session::orderBy('id', 'desc')->get();
        $student->schoolFeeDetails = $this->checkSchoolFees($student, $student->academic_session, $student->level_id);

        return view('guardian.studentProfile', [
            'student' => $student,
            'academicLevels' => $academicLevels,
            'sessions' => $sessions
        ]);
    }

    public function makePayment(Request $request)
    {
        $studentId = $request->student_id;
        $student = Student::with('applicant')->where('id', $studentId)->first();

        $globalData = $request->input('global_data');
        $paymentId = $request->payment_id;
        $amount = $request->amount*100;
        $redirectLocation = 'guardian/home';
        
        if($paymentId > 0){
            if(!$payment = Payment::with('structures')->where('id', $paymentId)->first()){
                alert()->error('Oops', 'Invalid Payment Initialization, contact ICT')->persistent('Close');
                return redirect()->back();
            }
            $redirectLocation = 'student/transactions';
            $amount = $request->amount;
        }
        

        $paymentGateway = $request->paymentGateway;

        $reference = $this->generateAccessCode();

        if(strtolower($paymentGateway) == 'paystack') {
            Log::info("Paystack Amount ****************: ". round($this->getPaystackAmount($amount)));

            $data = array(
                "amount" => round($this->getPaystackAmount($amount)),
                "email" => $student->email,
                "currency" => "NGN",
                "metadata" => array(
                    "amount" => $amount,
                    "email" => $student->email,
                    "application_id" => null,
                    "student_id" => $studentId,
                    "payment_id" => $paymentId,
                    "payment_gateway" => $paymentGateway,
                    "reference" => null,
                    "academic_session" => $student->academic_session,
                    "redirect_path" => $redirectLocation,
                ),
            );

            return Paystack::getAuthorizationUrl($data)->redirectNow();
        }

        if(strtolower($paymentGateway) == 'rave') {
            Log::info("Flutterwave Amount ****************: ". round($this->getRaveAmount($amount)));

            $reference = Flutterwave::generateReference();

            $data = array(
                "payment_options" => "card,banktransfer",
                "amount" => round($this->getRaveAmount($amount)),
                "tx_ref" => $reference,
                "redirect_url" => env("FLW_REDIRECT_URL"),
                "email" => $student->email,
                "currency" => "NGN",
                "customer" => [
                    "email" => $student->email,
                    "phone_number" => $student->applicant->phone_number,
                    "name" => $student->applicant->lastname.' '.$student->applicant->othernames,
                ],
                "meta" => array(
                    "amount" => $amount,
                    "email" => $student->email,
                    "application_id" => null,
                    "student_id" => $studentId,
                    "payment_id" => $paymentId,
                    "payment_gateway" => $paymentGateway,
                    "reference" => $reference,
                    "academic_session" => $student->academic_session,
                    "redirect_path" => $redirectLocation,
                ),
                "customizations" => array(
                    "title" => env('SCHOOL_NAME'),
                    "logo" => env('SCHOOL_LOGO'),
                ),
            );

            $payment = Flutterwave::initializePayment($data);

            if ($payment['status'] !== 'success') {
                $message = 'Flutterwave Gateway is down, try again.';
                alert()->info('Opps!', $message)->persistent('Close');
                return redirect()->back();
            }

            return redirect($payment['data']['link']);
        }

        $message = 'Invalid Payment Gateway';
        alert()->info('Opps!', $message)->persistent('Close');
        return redirect()->back();
    }

    public function generateResult(Request $request){
        $validator = Validator::make($request->all(), [
            'semester' => 'required',
            'session' => 'required',
            'level_id' => 'required',
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentId = $request->student_id;
        $student = Student::find($studentId);
        $globalData = $request->input('global_data');

        $semester = $request->semester;
        $academicSession = $request->session;
        $levelId = $request->level_id;
        $academicLevel = AcademicLevel::find($levelId);
        $level = $academicLevel->level;

        $courseRegs = CourseRegistration::with('course')
        ->where('student_id', $studentId)
        ->where('academic_session', $academicSession)
        ->where('level_id', $levelId)
        ->where('result_approval_id',  ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))
        ->whereHas('course', function ($query) use ($semester) {
            $query->where('semester', $semester);
        })
        ->get();

        if(!$courseRegs->count() > 0) {
            alert()->info('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        $checkStudentPayment = $this->checkSchoolFees($student, $academicSession, $levelId);
        if($checkStudentPayment->status != 'success'){
            alert()->error('Oops!', 'Something went wrong with School fees')->persistent('Close');
            return redirect()->back();
        }

        $passTuition = $checkStudentPayment->passTuitionPayment;
        $fullTuitionPayment = $checkStudentPayment->fullTuitionPayment;
        $passEightyTuition = $checkStudentPayment->passEightyTuition;

        if($semester == 1 && !$passTuition){
            alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 50% of school fees for '.$academicSession.' acaddemic session')->persistent('Close');
            return redirect()->back();
        }

        if($semester == 2 && !$fullTuitionPayment){
            alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 100% of school fees for '.$academicSession.' acaddemic session')->persistent('Close');
            return redirect()->back();
        }

        $pdf = new Pdf();
        $examResult = $pdf->generateExamResult($studentId, $academicSession, $semester, $level);

        return redirect(asset($examResult));
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

    public function generateInvoice (Request $request){
        $validator = Validator::make($request->all(), [
            'session' => 'required',
            'student_id' => 'required',
            'payment_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $session = $request->session;
        $studentId = $request->student_id;
        $paymentId = $request->payment_id;

        $pdf = new Pdf();
        $invoice = $pdf->generateTransactionInvoice($session, $studentId, $paymentId);

        return redirect(asset($invoice));

    }
}
