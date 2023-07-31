<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Programme;
use App\Models\Transaction;
use App\Models\User as Applicant;
use App\Models\Olevel;
use App\Models\Guardian;
use App\Models\NextOfKin;
use App\Models\Payment;

use App\Mail\ApplicationMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;

class ApplicationController extends Controller
{
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

    public function index(Request $request)
    {
        $userId = Auth::guard('user')->user()->id;
        $globalData = $request->input('global_data');
        $applicationSession = $globalData->sessionSetting['application_session'];
        
        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('id', $userId)->first();

        $applicationPayment = Payment::with('structures')->where('type', 'Application Fee')->first();
        $paymentId = $applicationPayment->id;
        $transaction = Transaction::where('user_id', $applicant->id)->where('session', $applicationSession)->where('payment_id', $paymentId)->where('status', 1)->first();

        if(!$transaction){
            return view('user.auth.register', [
                'programmes' => $this->programmes,
                'applicant' => $applicant,
                'payment' => $applicationPayment
            ]);
        }

        if(strtolower($applicant->status) == 'admitted'){
            alert()->success('Congratulation', 'You have been admitted, proceed to student portal, check our mail for more information')->persistent('Close');
            return view('student.auth.login');
        }

        $percent = 1;
        if(!empty($applicant->lastname)){
            $percent = $percent + 1;
        }
        if(!empty($applicant->programme)){
            $percent = $percent + 1;
        }
        if(!empty($applicant->guardian)){
            $percent = $percent + 1;
        }
        if(count($applicant->olevels) > 4 && $applicant->sitting_no != 0){
            $percent = $percent + 1;
        }
        if(!empty($applicant->olevel_1)){
            $percent = $percent + 1;
        }
        if(!empty($applicant->subject_id)){
            $percent = $percent + 1;
        }

        $percent = round(($percent/7)*100);

        return view('user.application', [
            'applicant' => $applicant,
            'percent' => $percent,
        ]);
    }

    //applicant
    public function showRegistrationForm(Request $request)
    {
        $payment = Payment::with('structures')->where('type', 'Application Fee')->first();

        return view('user.auth.register', [
            'programmes' => $this->programmes,
            'payment' => $payment
        ]);
    }
  
    public function register(Request $request)
    {
        $userId = $request->user_id;
        $globalData = $request->input('global_data');
        $applicationSession = $globalData->sessionSetting['application_session'];
        $applicationPayment = Payment::with('structures')->where('type', 'Application Fee')->first();
        $paymentId = $applicationPayment->id;

        if(!$request->has('user_id')){
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,academic_session,' . $applicationSession,
                'lastname' => 'required',
                'programme_id' => 'required',
                'phone_number' => 'required',
                'othernames' => 'required',
                'paymentGateway' => 'required',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'programme_id' => 'required',
                'paymentGateway' => 'required',
            ]);
        }

        if($applicant = Applicant::where('email', $request->email)->where('academic_session', $applicationSession)->first()){
            $transaction = Transaction::where('user_id', $applicant->id)->where('session', $applicationSession)->where('payment_id', $paymentId)->where('status', 1)->first();

            if(!$transaction){
                return view('user.auth.register', [
                    'programmes' => $this->programmes,
                    'applicant' => $applicant,
                    'payment' => $applicationPayment
                ]);
            }
        }

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $paymentGateway = $request->paymentGateway;
        if(strtolower($paymentGateway) != 'paystack' && strtolower($paymentGateway) != 'banktransfer') {
            alert()->error('Oops', 'Gateway not available')->persistent('Close');
            return redirect()->back();
        }

        $programmeId = $request->programme_id;
        $programmeApplied = Programme::where('id', $programmeId)->first();
        

        $accessCode = $this->generateAccessCode();
        $amount = $applicationPayment->structures->sum('amount');

        if($request->has('user_id')) {
            //do something
            $applicant = Applicant::where('id', $userId)->first();
            if(!$applicant){
                $message = 'Invalid application';
                alert()->info('Oops!', $message)->persistent('Close');
                return redirect()->back();
            }
        }else{
            $newApplicant = ([
                'email' => $request->email,
                'lastname' => $request->lastname,
                'programme_id' => $programmeApplied->id,
                'phone_number' => $request->phone_number,
                'othernames' => $request->othernames,
                'password' => Hash::make($accessCode),
                'passcode' => $accessCode,
                'academic_session' => $applicationSession,
            ]);
    
            $applicant = Applicant::create($newApplicant);
            $code = $programmeApplied->code;
            $applicationNumber = substr($applicationSession, 2, 2).'/'.$code.'/'.sprintf("%03d", $applicant->id);
            $applicant->application_number = $applicationNumber;
            $applicant->save();

            Mail::to($request->email)->send(new ApplicationMail($applicant));
        }

        if(strtolower($paymentGateway) == 'paystack') {
            $data = array(
                "amount" => $this->getPaystackAmount($amount),
                "email" => $applicant->email,
                "currency" => "NGN",
                "metadata" => array(
                    "amount" => $amount,
                    "email" => $applicant->email,
                    "application_id" => $applicant->id,
                    "student_id" => null,
                    "payment_id" => $paymentId,
                    'payment_gateway' => $paymentGateway,
                    'reference' => null,
                ),
            );

            return Paystack::getAuthorizationUrl($data)->redirectNow();
        }

        if(strtolower($paymentGateway) == 'banktransfer'){
            $bankName = env('BANK_NAME');
            $bankAccountNo = env('BANK_ACCOUNT_NUMBER');
            $bankAccountName = env('BANK_ACCOUNT_NAME');

            $userData = Applicant::with('programme')->where('id', $applicant->id);
            $userData->bank_name = $bankName;
            $userData->bank_account_name = $bankAccountName;
            $userData->bank_account_no = $bankAccountNo;
            
            //create email to sennd bank details

            $message = 'Kindly proceed to your email to complete application';
            alert()->info('Oops!', $message)->persistent('Close');
            return redirect()->back();
        }
    
    }

    public function programmeById($id) {

        $programme = Programme::with('generalPayments', 'generalPayments.structures')->where('id', $id)->first();
        return $programme;
    }
      
}
