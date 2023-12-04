<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

use Log;
use SweetAlert;
use Mail;
use Alert;
use Carbon\Carbon;
use Paystack;

use App\Libraries\Google\Google;
use App\Libraries\Pdf\Pdf;

use App\Mail\ApplicationPayment;
use App\Mail\StudentActivated;
use App\Mail\TransactionMail;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Student;
use App\Models\Payment;
use App\Models\SessionSetting;
use App\Models\Staff;
use App\Models\Partner;
use App\Models\AcademicLevel;
use App\Models\Programme;
use App\Models\Session;
use App\Models\Faculty;
use App\Models\Department;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function dataResponse($message, $data = null, $status = "success", $statusCode = null)
    {
        if (!$statusCode) {
            if ($status == "error")
                $statusCode = Response::HTTP_BAD_REQUEST;
            else
                $statusCode = Response::HTTP_OK;
        }

        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
    
    public function processPaystackPayment($paymentDetails){
        $sessionSetting = SessionSetting::first();
        $academicSession = $sessionSetting['academic_session'];
        $applicationSession = $sessionSetting['application_session'];
        $admissionSession = $sessionSetting['admission_session'];


        log::info("Processing payment:" . json_encode($paymentDetails));
        //get active editions
        $email = $paymentDetails['data']['metadata']['email'];
        $applicationId = $paymentDetails['data']['metadata']['application_id'];
        $studentId = $paymentDetails['data']['metadata']['student_id'];
        $paymentId = $paymentDetails['data']['metadata']['payment_id'];
        $paymentGateway = $paymentDetails['data']['metadata']['payment_gateway'];
        $amount = $paymentDetails['data']['metadata']['amount'];
        $txRef = $paymentDetails['data']['metadata']['reference'];
        $reference = $paymentDetails['data']['reference'];
        $session = $paymentDetails['data']['metadata']['academic_session'];


        if(!empty($txRef)){
            if($existTx = Transaction::where('reference', $txRef)->where('status',  null)->first()){
                $existTx->reference = $reference;
                $existTx->status = 1;
                $existTx->payment_method = $paymentGateway;
                $existTx->save();
                
                return true;
            }
        }

        //check if payment have been added
        if(Transaction::where('reference', $reference)->where('status', 1)->first()){
            return true;
        }

       $payment = Payment::with('programme')->where('id', $paymentId)->first();

       //Create new transaction
       $transaction = Transaction::create([
            'user_id' => !empty($applicationId)?$applicationId:null,
            'student_id' => !empty($studentId)?$studentId:null,
            'payment_id' => $paymentId,
            'amount_payed' => $amount,
            'payment_method' => $paymentGateway,
            'reference' => $reference,
            'session' => $session,
            'status' => 1
        ]);

       return true;
    }

    public function processRavePayment($paymentDetails){
        $sessionSetting = SessionSetting::first();
        $academicSession = $sessionSetting['academic_session'];
        $applicationSession = $sessionSetting['application_session'];
        $admissionSession = $sessionSetting['admission_session'];


        log::info("Processing payment:" . json_encode($paymentDetails));
        //get active editions
        $email = $paymentDetails['data']['meta']['email'];
        $applicationId = !empty($paymentDetails['data']['meta']['application_id'])?$paymentDetails['data']['meta']['application_id']:null;
        $studentId = !empty($paymentDetails['data']['meta']['student_id'])?$paymentDetails['data']['meta']['student_id']:null;
        $paymentId = $paymentDetails['data']['meta']['payment_id'];
        $paymentGateway = $paymentDetails['data']['meta']['payment_gateway'];
        $amount = $paymentDetails['data']['meta']['amount'];
        $txRef = $paymentDetails['data']['meta']['reference'];
        $reference = $paymentDetails['data']['meta']['reference'];
        $session = $paymentDetails['data']['meta']['academic_session'];


        if(!empty($txRef)){
            if($existTx = Transaction::where('reference', $txRef)->where('status',  null)->first()){
                $existTx->reference = $reference;
                $existTx->status = 1;
                $existTx->payment_method = $paymentGateway;
                $existTx->save();
                
                return true;
            }
        }

        //check if payment have been added
        if(Transaction::where('reference', $reference)->where('status', 1)->first()){
            return true;
        }

       $payment = Payment::with('programme')->where('id', $paymentId)->first();

       //Create new transaction
       $transaction = Transaction::create([
            'user_id' => !empty($applicationId)?$applicationId:null,
            'student_id' => !empty($studentId)?$studentId:null,
            'payment_id' => $paymentId,
            'amount_payed' => $amount,
            'payment_method' => $paymentGateway,
            'reference' => $reference,
            'session' => $session,
            'status' => 1
        ]);

       return true;
    }


    public function generateAccessCode () {
        $applicationAccessCode = "";
        $current = $this->generateRandomString();
        $isExist = User::where('passcode', $current)->get();
        if(!($isExist->count() > 0)) {
            $applicationAccessCode = $current;
            return $applicationAccessCode;
        } else {
            return $this->generateUserCode();
        }           
    }

    public function getPaystackAmount($amount){
        $paystackAmount =  (((1.5/100) * $amount)+10500);
        
        if(($paystackAmount) > 200000){
            $paymentAmount = $amount + 200000 + 5000;
        }else if($amount < 250000){
            $paymentAmount = $amount + $paystackAmount + 5000;
        }else{
            $paymentAmount = $amount + $paystackAmount + 5000;
        }

        $paymentAmount = $amount + 50000;

        return $paymentAmount;
    }
    
    public function getRaveAmount($amount){
        $paystackAmount =  (((1.4/100) * $amount)+5000);

        // $paymentAmount = $amount + $paystackAmount + 5000;
        $paymentAmount = $amount + 5000;

        return $paymentAmount/100;
    }

    public function generateReferralCode() {
        $referralCode = "";
        $current = $this->generateRandomString();
        $isExist = Staff::where('referral_code', $current)->get();
        $isExistPartner = Partner::where('referral_code', $current)->get();
        if(!($isExist->count() > 0) && !($isExistPartner->count() > 0)) {
            $referralCode = $current;
            return $referralCode;
        } else {
            return $this->generateReferralCode();
        }           
    }

    public function getReferralId($referralCode) {
        $isExistStaff = Staff::where('referral_code', $referralCode)->first();
        // if($isExistStaff){
        //     return $isExistStaff->id;
        // }else{
        
        $isExistPartner = Partner::where('referral_code', $referralCode)->first();
        if($isExistPartner){
            return $isExistPartner->id;
        }
        
        return null;
    }

    public function getSingleApplicant($studentIdCode, $path){
        $student = User::with('programme', 'programme.department', 'programme.department.faculty', 'transactions')->where('application_number', $studentIdCode)->first();
        if(!$student){
            alert()->info('Record not found', '')->persistent('Close');
            return redirect()->back();
        }

        $studentId = $student->id;

        $levels = AcademicLevel::get();
        $programmes = Programme::get();
        $departments = Department::get();
        $faculties = Faculty::get();
        $sessions = Session::orderBy('id', 'DESC')->get();

        $transactions = Transaction::where('user_id', $student->id)->where('payment_id', '!=', 0)->orderBy('id', 'DESC')->get();
        $filteredTransactions = [];
        foreach ($transactions as $transaction) {
            $paymentType = !empty($transaction->paymentType)?$transaction->paymentType->type:Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
            $session = $transaction->session;
            $totalPaid = $transaction->amount_payed;
            $paymentId = $transaction->payment_id;
        
            if(isset($filteredTransactions[$paymentType][$session])) {
                $filteredTransactions[$paymentType][$session]['totalPaid'] += $totalPaid;
            } else {
                $filteredTransactions[$paymentType][$session] = [
                    'id' => $paymentId,
                    'paymentType' => $paymentType,
                    'totalPaid' => $totalPaid,
                    'session' => $session,
                ];
            }
        }

        return view($path, [
            'transactions' => $filteredTransactions,
            'applicant' => $student,
            'levels' => $levels,
            'programmes' => $programmes,
            'departments' => $departments,
            'faculties' => $faculties,
            'sessions' => $sessions   
        ]);
    }

    public function getSingleStudent($studentIdCode, $path){

        $student = Student::with('programme', 'transactions', 'applicant')->where('matric_number', $studentIdCode)->first();
        if(!$student){
            alert()->info('Record not found', '')->persistent('Close');
            return redirect()->back();
        }
        $studentId = $student->id;
        $levelId = $student->level_id;

        $levels = AcademicLevel::orderBy('id', 'DESC')->get();
        $programmes = Programme::get();
        $departments = Department::get();
        $faculties = Faculty::get();
        $sessions = Session::orderBy('id', 'DESC')->get();

        $transactions = Transaction::with('paymentType')->where('student_id', $studentId)->orderBy('id', 'DESC')->get();

        $filteredTransactions = [];
        foreach ($transactions as $transaction) {
            $paymentType = !empty($transaction->paymentType) ? $transaction->paymentType->type : Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
            $session = $transaction->session;
            $totalPaid = $transaction->status == 1 ? $transaction->amount_payed : 0; // Fixed the condition and property name
            
            // Using the payment_id as the array key for easy access
            $paymentId = $transaction->payment_id;
        
            if (isset($filteredTransactions[$paymentType][$session])) {
                $filteredTransactions[$paymentType][$session]['totalPaid'] += $totalPaid;
            } else {
                $filteredTransactions[$paymentType][$session] = [
                    'id' => $paymentId,
                    'paymentType' => $paymentType,
                    'totalPaid' => $totalPaid,
                    'session' => $session,
                ];
            }
        }
        
        
        // foreach ($filteredTransactions as &$paymentType) {
        //     usort($paymentType, 'sortBySession');
        // }
        

        $schoolPayment = Payment::with('structures')
            ->where('type', Payment::PAYMENT_TYPE_SCHOOL)
            ->where('programme_id', $student->programme_id)
            ->where('level_id', $levelId)
            ->where('academic_session', $student->academic_session)
            ->first();

        if(!$schoolPayment){
            alert()->info('Programme info missing, contact administrator', '')->persistent('Close');
            return redirect()->back();
        }
        $schoolPaymentId = $schoolPayment->id;
        $schoolAmount = $schoolPayment->structures->sum('amount');
        $schoolPaymentTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $schoolPaymentId)->where('session', $student->academic_session)->where('status', 1)->get();

        $passTuitionPayment = false;
        $fullTuitionPayment = false;
        $passEightyTuition = false;
        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.4){
            $passTuitionPayment = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.7){
            $passEightyTuition = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') >= $schoolAmount){
            $passEightyTuition = true;
            $fullTuitionPayment = true;
        }

        return view($path, [
            'path' => $path,
            'transactions' => $filteredTransactions,
            'payment' => $schoolPayment,
            'passTuition' => $passTuitionPayment,
            'fullTuitionPayment' => $fullTuitionPayment,
            'passEightyTuition' => $passEightyTuition,
            'student' => $student,
            'levels' => $levels,
            'programmes' => $programmes,
            'departments' => $departments,
            'faculties' => $faculties,
            'sessions' => $sessions,
            'allTxs' => $transactions,  
        ]);

    }

    //generate clean strings
    public function generateRandomString($length = 8) {
        $characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getPreviousAcademicYear($session)
    {
        list($startYear, $endYear) = explode('/', $session);
    
        $startAcademicYear = Carbon::createFromDate($startYear, 1, 1)->subYear()->format('Y');
        $endAcademicYear = Carbon::createFromDate($endYear, 1, 1)->subYear()->format('Y');
    
        return $startAcademicYear . '/' . $endAcademicYear;
    }

    public function checkSchoolFees($student, $academicSession, $levelId)
    {
        $studentId = $student->id;
        $applicantId = $student->user_id;
        $applicant = User::find($applicantId);
        $applicationType = $applicant->application_type;

        $type = Payment::PAYMENT_TYPE_SCHOOL;

        if($applicationType != 'UTME' && ($student->level_id == 2|| $student->level_id == 3)){
            $type = Payment::PAYMENT_TYPE_SCHOOL_DE;
        }

        $schoolPayment = Payment::with('structures')
            ->where('type', $type)
            ->where('programme_id', $student->programme_id)
            ->where('level_id', $levelId)
            ->where('academic_session', $academicSession)
            ->first();

        if(!$schoolPayment){
            $data = new \stdClass();
            $data->status = 'record_not_found';

            return $data;
        }

        $schoolPaymentId = $schoolPayment->id;
        $schoolAmount = $schoolPayment->structures->sum('amount');
        $schoolPaymentTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $schoolPaymentId)->where('session', $academicSession)->where('status', 1)->get();

        $studentPendingTransactions = Transaction::with('paymentType')->where('student_id', $studentId)->where('payment_id', $schoolPaymentId)->where('status', null)->get();

        $passTuitionPayment = false;
        $fullTuitionPayment = false;
        $passEightyTuition = false;
        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.4){
            $passTuitionPayment = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.8){
            $passTuitionPayment = true;
            $passEightyTuition = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') >= $schoolAmount){
            $passTuitionPayment = true;
            $passEightyTuition = true;
            $fullTuitionPayment = true;
        }

        $data = new \stdClass();
        $data->status = 'success';
        $data->passTuitionPayment = $passTuitionPayment;
        $data->passEightyTuition = $passEightyTuition;
        $data->fullTuitionPayment = $fullTuitionPayment;
        $data->schoolPaymentTransaction = $schoolPaymentTransaction;
        $data->schoolPayment = $schoolPayment;
        $data->studentPendingTransactions = $studentPendingTransactions;

        return $data;
    }
    

    public function generateMatricAndEmail($student){
        if(!$student->is_active && empty($student->matric_number)){
            $sessionSetting = SessionSetting::first();
            $admissionSession = $sessionSetting->admission_session;

            $programme = Programme::with('students', 'department', 'department.faculty')->where('id', $student->programme_id)->first();
            $codeNumber = $programme->code_number;
            $deptCode = $programme->department->code;
            $facultyCode = $programme->department->faculty->code;
            $programmeCode = $programme->code;
            $code = $deptCode.$programmeCode;

            $accessCode = $student->applicant->passcode;
            $studentPreviousEmail = $student->email;

            $name = $student->applicant->lastname.' '.$student->applicant->othernames;
            $nameParts = explode(' ', $student->applicant->othernames);
            $firstName = $nameParts[0];
            $studentEmail = strtolower(str_replace(' ', '', $student->applicant->lastname.'.'.$firstName.'@st.tau.edu.ng'));


            $newMatric = empty($programme->matric_last_number)? ($programme->students->count() + 20) + 1 : $programme->matric_last_number + 1;
            $matricNumber = substr($admissionSession, 2, 2).'/'.$facultyCode.$code.sprintf("%03d", $newMatric);

            $google = new Google();
            $createStudentEmail = $google->createUser($studentEmail, $student->applicant->othernames, $student->applicant->lastname, $accessCode);

            $student->email = $studentEmail;
            $student->matric_number = $matricNumber;
            $student->is_active = true;
            $student->save();

            $programme->matric_last_number = $newMatric;
            $programme->save();

            
            Mail::to($studentPreviousEmail)->send(new StudentActivated($student));

            return true;
        }
    }

    public  function sortBySession($a, $b) {
        return strcmp($a['session'], $b['session']);
    }

    public function creditStudentWallet($studentId, $amount){
        $student = Student::find($studentId);

        $studentBalance = $student->amount_balance;
        $studentNewBalance = $studentBalance + $amount;
        $student->amount_balance = $studentNewBalance;

        if($student->update()){
            return true;
        }
        return false;
    }

    public function billStudent($transactionData){

        $student = Student::with('applicant')->where('id', $transactionData->student_id)->first();

       //Create new transaction
        $transaction = Transaction::create([
            'student_id' => $transactionData->student_id,
            'payment_id' => $transactionData->payment_id,
            'amount_payed' => $transactionData->amount,
            'payment_method' => $transactionData->payment_gateway,
            'reference' => $transactionData->reference,
            'session' => $transactionData->academic_session,
            'status' => 1
        ]);

        $studentBalance = $student->amount_balance;
        $studentNewBalance = $studentBalance - $transactionData->amount;
        $student->amount_balance = $studentNewBalance;
        $student->update();

        if($student && !empty($transactionData->student_id)){
            $pdf = new Pdf();
            $invoice = $pdf->generateTransactionInvoice($transactionData->academic_session, $transactionData->student_id, $transactionData->payment_id, 'single');
                    
            $data = new \stdClass();
            $data->lastname = $student->applicant->lastname;
            $data->othernames = $student->applicant->othernames;
            $data->amount = $transactionData->amount;
            $data->invoice = $invoice;
            
            Mail::to($student->email)->send(new TransactionMail($data));   
        }

        alert()->success('Good Job', 'Payment successful')->persistent('Close');
        return redirect($transactionData->redirect_path);
    }
}
