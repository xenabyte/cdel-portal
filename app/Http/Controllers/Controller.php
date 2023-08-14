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

use Log;

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

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    public function processPayment($paymentDetails){
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
        $paystackAmount =  (((1.5/100) * $amount)+5);
        
        if(($paystackAmount) > 200000){
            $paymentAmount = $amount + 200000 + 5000;
        }else if($amount < 250000){
            $paymentAmount = $amount + $paystackAmount + 5000;
        }else{
            $paymentAmount = $amount + $paystackAmount + 5000;
        }

        return $paymentAmount;
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

    public function getSingleApplicant($studentIdCode, $path){
        $student = User::with('programme', 'transactions')->where('application_number', $studentIdCode)->first();
        if(!$student){
            alert()->info('Record not found', '')->persistent('Close');
            return redirect()->back();
        }

        $studentId = $student->id;

        $levels = AcademicLevel::get();
        $programmes = Programme::get();
        $sessions = Session::orderBy('id', 'DESC')->get();

        $transactions = Transaction::where('user_id', $studentId)->orderBy('id', 'DESC')->get();

        return view($path, [
            'transactions' => $transactions,
            'applicant' => $student,
            'levels' => $levels,
            'programmes' => $programmes,
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

        $levels = AcademicLevel::get();
        $programmes = Programme::get();
        $sessions = Session::orderBy('id', 'DESC')->get();

        $transactions = Transaction::where('student_id', $studentId)->orderBy('id', 'DESC')->get();

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
            'transactions' => $transactions,
            'payment' => $schoolPayment,
            'passTuition' => $passTuitionPayment,
            'fullTuitionPayment' => $fullTuitionPayment,
            'passEightyTuition' => $passEightyTuition,
            'student' => $student,
            'levels' => $levels,
            'programmes' => $programmes,
            'sessions' => $sessions   
        ]);

    }

    //generate clean strings
    public function generateRandomString($length = 6) {
        $characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ';
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

        $schoolPayment = Payment::with('structures')
            ->where('type', Payment::PAYMENT_TYPE_SCHOOL)
            ->where('programme_id', $student->programme_id)
            ->where('level_id', $levelId)
            ->where('academic_session', $academicSession)
            ->first();

        if(!$schoolPayment){
            alert()->info('Programme info missing, contact administrator', '')->persistent('Close');
            return redirect()->back();
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
        $data->passTuitionPayment = $passTuitionPayment;
        $data->passEightyTuition = $passEightyTuition;
        $data->fullTuitionPayment = $fullTuitionPayment;
        $data->schoolPaymentTransaction = $schoolPaymentTransaction;
        $data->schoolPayment = $schoolPayment;
        $data->studentPendingTransactions = $studentPendingTransactions;

        return $data;
    }
    
}
