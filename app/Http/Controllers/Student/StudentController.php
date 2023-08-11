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

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;


class StudentController extends Controller
{
    //

    public function index(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];

        $acceptancePayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)->first();
        $acceptancePaymentId = $acceptancePayment->id;
        $acceptanceTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $acceptancePaymentId)->where('status', 1)->first();

        $schoolPayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_SCHOOL)->where('programme_id', $student->programme_id)->where('level_id', $levelId)->first();
        if(!$schoolPayment){
            alert()->info('Programme info missing, contact administrator', '')->persistent('Close');
            return redirect()->back();
        }
        $schoolPaymentId = $schoolPayment->id;
        $schoolAmount = $schoolPayment->structures->sum('amount');
        $schoolPaymentTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $schoolPaymentId)->where('session', $student->academic_session)->where('status', 1)->first();

        $passTuitionPayment = false;
        $fullTuitionPayment = false;
        $passEightyTuition = false;
        if($schoolPaymentTransaction && $schoolPaymentTransaction->amount_payed > $schoolAmount * 0.4){
            $passTuitionPayment = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->amount_payed > $schoolAmount * 0.8){
            $passEightyTuition = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->amount_payed >= $schoolAmount){
            $fullTuitionPayment = true;
        }

        if(!$acceptanceTransaction){
            return view('student.acceptanceFee', [
                'payment' => $acceptancePayment
            ]);
        }

        if(!$schoolPaymentTransaction){
            return view('student.schoolFee', [
                'payment' => $schoolPayment,
                'passTuition' => $passTuitionPayment,
                'fullTuitionPayment' => $fullTuitionPayment,
                'passEightyTuition' => $passEightyTuition
            ]);
        }

        return view('student.home');
    }

    public function makePayment(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $paymentId = $request->payment_id;

        if(!$payment = Payment::with('structures')->where('id', $paymentId)->first()){
            alert()->error('Oops', 'Invalid Payment Initialization, contact ICT ')->persistent('Close');
            return redirect()->back();
        }

        $paymentGateway = $request->paymentGateway;
        if(strtolower($paymentGateway) != 'paystack' && strtolower($paymentGateway) != 'banktransfer') {
            alert()->error('Oops', 'Gateway not available')->persistent('Close');
            return redirect()->back();
        }


        $accessCode = $this->generateAccessCode();
        $amount = $request->amount;

        Log::info(" Amount ****************: ". round($this->getPaystackAmount($amount)));

        if(strtolower($paymentGateway) == 'paystack') {
            $data = array(
                "amount" => round($this->getPaystackAmount($amount)),
                "email" => $student->email,
                "currency" => "NGN",
                "metadata" => array(
                    "amount" => $amount,
                    "email" => $student->email,
                    "application_id" => $studentId,
                    "student_id" => $studentId,
                    "payment_id" => $paymentId,
                    "payment_gateway" => $paymentGateway,
                    "reference" => null,
                    "academic_session" => $admissionSession
                ),
            );

            return Paystack::getAuthorizationUrl($data)->redirectNow();
        }

        // if(strtolower($paymentGateway) == 'banktransfer'){
            
        //     $userData = new \stdClass();
        //     $userData->lastname = $applicant->lastname;
        //     $userData->othernames = $applicant->othernames;
        //     $userData->application_id = $applicant->application_number;
        //     $userData->amount = $this->getPaystackAmount($amount);
            
        //     //create email to sennd bank details
        //     Mail::to($request->email)->send(new BankDetailsMail($userData));

        //     $message = 'Kindly proceed to your email to complete application';
        //     alert()->info('Nice Work!', $message)->persistent('Close');
        //     return redirect()->back();
        // }

        $message = 'Invalid Payment Gateway';
        alert()->info('Nice Work!', $message)->persistent('Close');
        return redirect()->back();
    }

    public function transactions()
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $transactions = Transaction::where('student_id', $studentId)->orderBy('id', 'DESC')->get();

        $schoolPayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_SCHOOL)->where('programme_id', $student->programme_id)->where('level_id', $levelId)->first();
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
        
        return view('student.transactions', [
            'transactions' => $transactions,
            'payment' => $schoolPayment,
            'passTuition' => $passTuitionPayment,
            'fullTuitionPayment' => $fullTuitionPayment,
            'passEightyTuition' => $passEightyTuition
        ]);
    }
}
