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
}
