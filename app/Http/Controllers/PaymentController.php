<?php

namespace App\Http\Controllers;

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
use App\Models\Payment;

use App\Mail\ApplicationPayment;

use Paystack;
use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    //

    protected $admissionSettings;
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

    //verify payment with card
    public function verifyPayment()
    {
        Log::info("**********************Verifying Payment**********************");
        try{
            $paymentDetails = Paystack::getPaymentData();
            $paymentId = $paymentDetails['data']['metadata']['payment_id'];
            $payment = Payment::where('id', $paymentId)->first();
            $paymentType = $payment->type;
            

            if($paymentDetails['status'] == true){
                if($this->processPayment($paymentDetails)){
                    alert()->success('Good Job', 'Payment successful')->persistent('Close');
                    if($paymentType == Transaction::APPLICATION){
                        return view('user.auth.register', [
                            'programmes' => $this->programmes,
                            'payment' => $payment
                        ]);
                    }else{
                        return redirect('student/transaction');
                    }
                }else{
                    alert()->info('oops!!!', 'Something happpened, contact administrator')->persistent('Close');
                    if($paymentType == Transaction::APPLICATION){
                        return view('user.auth.register', [
                            'programmes' => $this->programmes,
                            'payment' => $payment
                        ]);
                    }
                }

            }

            alert()->error('Error', 'Payment not successful')->persistent('Close');
            if($paymentType == Transaction::APPLICATION){
                return view('user.auth.register', [
                    'programmes' => $this->programmes,
                    'payment' => $payment
                ]);
            }

        }catch(\Exception $e) {
            Log::error($e);
        }
    }

    public function paystackWebhook (Request $request) {   
        try {
            $webhookData = $request->all();
            log::info(json_encode($webhookData));
            $event = $webhookData['event'];
            sleep(300);
            return false;
            if($event == "charge.success"){
                return $this->processPayment($webhookData);
            }
          
        }
        catch (ValidationException $e) {
          Log::info(json_encode($e));
        }
    }

}
