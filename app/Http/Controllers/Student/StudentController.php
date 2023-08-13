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

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;


class StudentController extends Controller
{
    
    public function index(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];

        $acceptancePayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)->first();
        $acceptancePaymentId = $acceptancePayment->id;
        $acceptanceTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $acceptancePaymentId)->where('status', 1)->first();


        $paymentCheck = $this->checkSchoolFees($student);

        if(!$acceptanceTransaction){
            return view('student.acceptanceFee', [
                'payment' => $acceptancePayment
            ]);
        }

        if(!$paymentCheck->schoolPaymentTransaction){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition
            ]);
        }

        return view('student.home', [
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function makePayment(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $paymentId = $request->payment_id;

        if(!$payment = Payment::with('structures')->where('id', $paymentId)->first()){
            alert()->error('Oops', 'Invalid Payment Initialization, contact ICT')->persistent('Close');
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

        $paymentCheck = $this->checkSchoolFees($student);
        if(!$paymentCheck->schoolPaymentTransaction){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition
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

    public function mentor(){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;

        $mentorId  = $student->mentor_id;
        $paymentCheck = $this->checkSchoolFees($student);
        if(!$paymentCheck->schoolPaymentTransaction){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition
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
}
