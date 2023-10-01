<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Staff;
use App\Models\User as Applicant;
use App\Models\Student;

use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;
use KingFlamez\Rave\Facades\Rave as Flutterwave;


class StudentController extends Controller
{

    public function onBoarding(Request $request){
        return view('student.onBoarding');
    }
    
    public function index(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $acceptancePayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)->where('academic_session', $academicSession)->first();
        $acceptancePaymentId = $acceptancePayment->id;
        $acceptanceTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $acceptancePaymentId)->where('status', 1)->first();

        $totalExpenditure = Transaction::where('student_id', $studentId)->where('status', 1)->where('payment_method', 'Wallet')->sum('amount_payed');
        $totalDeposit = Transaction::where('student_id', $studentId)->where('status', 1)->where('payment_id', 0)->sum('amount_payed');


        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);

        if(!$acceptanceTransaction && $student->level_id == 1){
            return view('student.acceptanceFee', [
                'payment' => $acceptancePayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        return view('student.home', [
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'totalExpenditure' => $totalExpenditure,
            'totalDeposit' => $totalDeposit
        ]);
    }

    public function profile(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        return view('student.profile', [
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function saveBioData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dob' => 'required',
            'religion' => 'required',
            'gender' => 'required',
            'marital_status' => 'required',
            'nationality' => 'required',
            'state' => 'required',
            'lga' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();
        $applicant = Applicant::find($student->user_id);

        
        if(!empty($request->dob) && $request->dob != $applicant->dob){
            $applicant->dob = $request->dob;
        }

        if(!empty($request->religion) && $request->religion != $applicant->religion){
            $applicant->religion = $request->religion;
        }

        if(!empty($request->gender) && $request->gender != $applicant->gender){
            $applicant->gender = $request->gender;
        }

        if(!empty($request->marital_status) && $request->marital_status != $applicant->marital_status){
            $applicant->marital_status = $request->marital_status;
        }

        if(!empty($request->nationality) && $request->nationality != $applicant->nationality){
            $applicant->nationality = $request->nationality;
        }

        if(!empty($request->state) && $request->state != $applicant->state_of_origin){
            $applicant->state = $request->state;
        }

        if(!empty($request->lga) && $request->lga != $applicant->lga){
            $applicant->lga = $request->lga;
        }

        if($applicant->save()){
            alert()->success('Changes Saved', 'Bio data saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
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

        $student = Auth::guard('student')->user();

        if(\Hash::check($request->old_password, Auth::guard('student')->user()->password)){
            if($request->password == $request->confirm_password){
                $student->password = bcrypt($request->password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }
        }else{
            alert()->error('Oops', 'Wrong old password, Try again with the right one')->persistent('Close');
            return redirect()->back();
        }

        if($student->update()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function makePayment(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $paymentId = $request->payment_id;
        $redirectLocation = 'student/walletTransactions';
        $amount = $request->amount * 100;

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

        if(strtolower($paymentGateway) == 'wallet') {
            Log::info("Wallet Amount ****************: ". round($amount));

            $studentBalance = $student->amount_balance;
            if($studentBalance < ($amount +20000)){
                $message = 'Insufficient funds, please top up your wallet or use another payment method.';
                alert()->info('Opps!', $message)->persistent('Close');
                return redirect()->back();
            }
            $transactionData = new \stdClass();
            $transactionData->amount = $amount;
            $transactionData->email = $student->email;
            $transactionData->payment_id = $paymentId;
            $transactionData->student_id = $studentId;
            $transactionData->reference = $reference;
            $transactionData->academic_session = $student->academic_session;
            $transactionData->redirect_path = $redirectLocation;
            $transactionData->payment_gateway = $paymentGateway;

            return $this->billStudent($transactionData);
        }

        $message = 'Invalid Payment Gateway';
        alert()->info('Opps!', $message)->persistent('Close');
        return redirect()->back();
    }
    
    public function transactions(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $transactions = Transaction::where('student_id', $studentId)->where('payment_id', '!=', 0)->orderBy('id', 'DESC')->get();

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }
        
        return view('student.transactions', [
            'transactions' => $transactions,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function walletTransactions(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $transactions = Transaction::where('student_id', $studentId)
        ->where(function($query) {
            $query->where('payment_id', 0)
                ->orWhere('payment_method', 'Wallet');
        })
        ->orderBy('id', 'DESC')
        ->get();


        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }
        
        return view('student.walletTransactions', [
            'transactions' => $transactions,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function mentor(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];


        $mentorId  = $student->mentor_id;
        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        $mentor = Staff::with('faculty', 'acad_department')->where('id', $mentorId)->first();

        return view('student.mentor', [
            'mentor' => $mentor,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function exits(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];


        $mentorId  = $student->mentor_id;
        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        return view('student.exits', [
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        ]);
    }

    public function getStudent(Request $request) {
        $matricNumber = $request->matric_number;

        $student = Student::where('matric_number', $matricNumber)
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->first();

        if(!$student){
            return response()->json(['status' => 'record_not_found']);
        }

        return response()->json(['status' => 'record_found', 'student' => $student]);
    }

    public function saveStudentDetails(Request $request){

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'email' => 'required',
            'lastname' => 'required',
            'othernames' => 'required',
            'password' => 'required',
            'confirm_password' => 'required'
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        
        if(!$student = Student::with('applicant')->where('id', $request->student_id)->first()){
            alert()->error('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        if(empty(strpos($request->email, env('SCHOOL_DOMAIN')))) {
            alert()->error('Error', 'Invalid student email, your student email must contain @'.env('SCHOOL_DOMAIN'))->persistent('Close');
            return redirect()->back();
        }

        if($student->onboard_status){
            alert()->info('Oops!', 'You have been onboarded successfully, kindly login to your portal with your details')->persistent('Close');
            return redirect()->back();
        }

        $applicantId = $student->user_id;
        if(!$applicant = Applicant::find($applicantId)){
            alert()->error('Oops!', 'Student application data mismatch')->persistent('Close');
            return redirect()->back();
        }
        
        $applicant->lastname = $applicant->lastname;
        $applicant->othernames = $applicant->othernames;
        $applicant->update();

        if($request->password == $request->confirm_password){
            $student->password = bcrypt($request->password);
        }else{
            alert()->error('Oops!', 'Password mismatch')->persistent('Close');
            return redirect()->back();
        }

        $student->email = $request->email;
        $student->onboard_status = true;

        if($student->update()) {
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
