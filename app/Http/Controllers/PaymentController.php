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
use League\Csv\Reader;

use App\Models\Programme;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Plan;
use App\Models\SessionSetting;

use App\Mail\ApplicationPayment;
use App\Mail\StudentActivated;
use App\Mail\TransactionMail;

use App\Libraries\Google\Google;
use App\Libraries\Pdf\Pdf;
use App\Libraries\Paygate\Paygate;
use App\Libraries\Bandwidth\Bandwidth;


use App\Mail\ApplicationMail;
use App\Mail\BankDetailsMail;

use Paystack;
use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

use KingFlamez\Rave\Facades\Rave as Flutterwave;

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
        Log::info("**********************Paystack Verifying Payment**********************");
        try{
            $paymentDetails = Paystack::getPaymentData();
            $paymentId = $paymentDetails['data']['metadata']['payment_id'];
            $studentId = $paymentDetails['data']['metadata']['student_id'];
            $redirectPath = $paymentDetails['data']['metadata']['redirect_path'];

            $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
            if($paymentId > 0){
                $payment = Payment::where('id', $paymentId)->first();
                $paymentType = $payment->type;
            }

            $student = Student::with('applicant', 'programme')->where('id', $studentId)->first();
            $session = $paymentDetails['data']['metadata']['academic_session'];
            $amount = $paymentDetails['data']['metadata']['amount'];


            if($paymentDetails['status'] == true){
                if($this->processPaystackPayment($paymentDetails)){
                    if($student && !empty($studentId)){
                        $pdf = new Pdf();
                        $invoice = $pdf->generateTransactionInvoice($session, $studentId, $paymentId, 'single');
                                
                        $data = new \stdClass();
                        $data->lastname = $student->applicant->lastname;
                        $data->othernames = $student->applicant->othernames;
                        $data->amount = $amount;
                        $data->invoice = $invoice;
                        
                        Mail::to($student->email)->send(new TransactionMail($data));
                        if($paymentType == Payment::PAYMENT_TYPE_WALLET_DEPOSIT){
                            $creditStudent = $this->creditStudentWallet($studentId, $amount);
                            if(!$creditStudent){
                                Log::info("**********************Unable to credit student**********************: ". $amount .' - '.$student);
                            }
                        }
                    }

                    alert()->success('Good Job', 'Payment successful')->persistent('Close');
                    if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                        $applicantData = $paymentDetails;
                        $this->createApplicant($applicantData);
                        return view($redirectPath, [ 
                            'programmes' => $this->programmes,
                            'payment' => $payment
                        ]);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                        return redirect($redirectPath);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_SCHOOL || $paymentType == Payment::PAYMENT_TYPE_SCHOOL_DE){
                        $this->generateMatricAndEmail($student);
                        return redirect($redirectPath);
                    }else{
                        return redirect($redirectPath);
                    }
                }else{
                    alert()->info('oops!!!', 'Something happpened, contact administrator')->persistent('Close');
                    if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                        return view($redirectPath, [
                            'programmes' => $this->programmes,
                            'payment' => $payment
                        ]);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                        return redirect($redirectPath);
                    }else{
                        return redirect($redirectPath);
                    }
                }

            }

            alert()->error('Error', 'Payment not successful')->persistent('Close');
            if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                return view($redirectPath, [
                    'programmes' => $this->programmes,
                    'payment' => $payment
                ]);
            }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                return redirect($redirectPath);
            }else{
                return redirect($redirectPath);
            }
            

        }catch(\Exception $e) {
            Log::error($e);
        }
    }

    public function raveVerifyPayment(){
        Log::info("**********************Flutterwave Verifying Payment**********************");

        try{
            $status = request()->status;

            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $paymentDetails = Flutterwave::verifyTransaction($transactionID);
            // dd($paymentDetails);

            $paymentId = $paymentDetails['data']['meta']['payment_id'];
            $studentId = !empty($paymentDetails['data']['meta']['student_id'])?$paymentDetails['data']['meta']['student_id']:null;
            $redirectPath = $paymentDetails['data']['meta']['redirect_path'];
            $txRef = $paymentDetails['data']['meta']['reference'];


            $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
            if($paymentId > 0){
                $payment = Payment::where('id', $paymentId)->first();
                $paymentType = $payment->type;
            }

            $student = Student::with('applicant', 'programme')->where('id', $studentId)->first();
            $amount = $paymentDetails['data']['meta']['amount'];
            $session = $paymentDetails['data']['meta']['academic_session'];
            
            if($paymentDetails['status'] == 'success'){
                if($this->processRavePayment($paymentDetails)){
                    
                    if($student && !empty($studentId)){
                        $pdf = new Pdf();
                        $invoice = $pdf->generateTransactionInvoice($session, $studentId, $paymentId, 'single');
                                
                        $data = new \stdClass();
                        $data->lastname = $student->applicant->lastname;
                        $data->othernames = $student->applicant->othernames;
                        $data->amount = $amount;
                        $data->invoice = $invoice;
                        
                        Mail::to($student->email)->send(new TransactionMail($data));

                        if($paymentType == Payment::PAYMENT_TYPE_WALLET_DEPOSIT){
                            $creditStudent = $this->creditStudentWallet($studentId, $amount);
                            if(!$creditStudent){
                                Log::info("**********************Unable to credit student wallet**********************: ". $amount .' - '.$student);
                            }
                        }  
                        
                        if($paymentType == Payment::PAYMENT_TYPE_BANDWIDTH){
                            $transaction = Transaction::where('reference', $txRef)->first();
                            $creditStudent = $this->creditBandwidth($transaction, $amount);

                            if(!$creditStudent){
                                Log::info("**********************Unable to credit student bandwidth**********************: ". $amount .' - '.$student);
                            }
                        }
                    }
                    alert()->success('Good Job', 'Payment successful')->persistent('Close');
                    if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                        $applicantData = $paymentDetails;
                        $this->createApplicant($applicantData);
                        return view($redirectPath, [
                            'programmes' => $this->programmes,
                            'payment' => $payment
                        ]);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                        return redirect($redirectPath);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_SCHOOL || $paymentType == Payment::PAYMENT_TYPE_SCHOOL_DE){
                        $this->generateMatricAndEmail($student);
                        return redirect($redirectPath);
                    }else{
                        return redirect($redirectPath);
                    }
                }else{
                    alert()->info('oops!!!', 'Something happpened, contact administrator')->persistent('Close');
                    if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                        return view($redirectPath, [
                            'programmes' => $this->programmes,
                            'payment' => $payment
                        ]);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                        return redirect($redirectPath);
                    }else{
                        return redirect($redirectPath);
                    }
                }

            }

            alert()->error('Error', 'Payment not successful')->persistent('Close');
            if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                return view($redirectPath, [
                    'programmes' => $this->programmes,
                    'payment' => $payment
                ]);
            }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                return redirect($redirectPath);
            }else{
                return redirect($redirectPath);
            }
            

        }catch(\Exception $e) {
            Log::error($e);
        }
    }

    public function upperlinkVerifyPayment($paymentReference = null){
        Log::info("**********************Upperlink Verifying Payment**********************");
        $ref = null;
        if (isset($_GET['reference'])) {
            $ref = $_GET['reference'];
        }
        
        if(!empty($paymentReference)){
            $ref = $paymentReference;
        }

        $redirectPath = '/';

        if(empty($ref)){
            $redirectPath = 'student/transactions';
            alert()->info('oops!!!', 'Something happpened, contact administrator')->persistent('Close');
            return redirect($redirectPath);
        }

        $upperLinkPayGate = new PayGate;
        $paymentDetails =$upperLinkPayGate->verifyTransaction($ref);

        if(isset($paymentDetails['transactionStatus']) && $paymentDetails['transactionStatus'] == '00'){

            $data = $paymentDetails['meta'];
            $paymentData = json_decode($data, true);

            $paymentId = $paymentData['payment_id'];
            $studentId = !empty($paymentData['student_id'])?$paymentData['student_id']:null;
            $redirectPath = $paymentData['redirect_path'];
            $txRef = $paymentData['reference'];


            $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
            if($paymentId > 0){
                $payment = Payment::where('id', $paymentId)->first();
                $paymentType = $payment->type;
            }

            $student = Student::with('applicant', 'programme')->where('id', $studentId)->first();
            $amount = $paymentDetails['amount']*100;
            $session = $paymentData['academic_session'];
            
            if($this->processUpperLinkPayment($paymentDetails)){

                if($student && !empty($studentId)){
                    $pdf = new Pdf();
                    $invoice = $pdf->generateTransactionInvoice($session, $studentId, $paymentId, 'single');
                            
                    $data = new \stdClass();
                    $data->lastname = $student->applicant->lastname;
                    $data->othernames = $student->applicant->othernames;
                    $data->amount = $amount;
                    $data->invoice = $invoice;
                    
                    Mail::to($student->email)->send(new TransactionMail($data));

                    if($paymentType == Payment::PAYMENT_TYPE_WALLET_DEPOSIT){
                        $creditStudent = $this->creditStudentWallet($studentId, $amount);
                        if(!$creditStudent){
                            Log::info("**********************Unable to credit student wallet**********************: ". $amount .' - '.$student);
                        }
                    }  
                    
                    if($paymentType == Payment::PAYMENT_TYPE_BANDWIDTH){
                        $transaction = Transaction::where('reference', $txRef)->first();
                        $creditStudent = $this->creditBandwidth($transaction, $amount);

                        if(!$creditStudent){
                            Log::info("**********************Unable to credit student bandwidth**********************: ". $amount .' - '.$student);
                        }
                    }
                }
                alert()->success('Good Job', 'Payment successful')->persistent('Close');
                if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                    $applicantData = $paymentData;
                    $this->createApplicant($applicantData);
                    return view($redirectPath, [
                        'programmes' => $this->programmes,
                        'payment' => $payment
                    ]);
                }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                    return redirect($redirectPath);
                }elseif($paymentType == Payment::PAYMENT_TYPE_SCHOOL || $paymentType == Payment::PAYMENT_TYPE_SCHOOL_DE){
                    $this->generateMatricAndEmail($student);
                    return redirect($redirectPath);
                }else{
                    return redirect($redirectPath);
                }
            }else{
                alert()->info('oops!!!', 'Something happpened, contact administrator')->persistent('Close');
                if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                    return view($redirectPath, [
                        'programmes' => $this->programmes,
                        'payment' => $payment
                    ]);
                }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                    return redirect($redirectPath);
                }else{
                    return redirect($redirectPath);
                }
            }
        }

        alert()->error('Error', 'Payment not successful')->persistent('Close');
        return redirect($redirectPath);
   
    }

    public function paystackWebhook (Request $request) {   
        try {
            $webhookData = $request->all();
            log::info(json_encode($webhookData));
            $event = $webhookData['event'];
            sleep(300);
            return false;
            if($event == "charge.success"){
                return $this->verifyPayment($webhookData);
            }
          
        }
        catch (ValidationException $e) {
          Log::info(json_encode($e));
        }
    }

    /**
     * Receives Flutterwave webhook
     * @return void
     */
    public function raveWebhook(Request $request){
        Log::info("**********************Flutterwave Webhook**********************");
        $verified = Flutterwave::verifyWebhook();

        if ($verified && $request->event == 'charge.completed' && $request->data->status == 'successful') {
            $verificationData = Flutterwave::verifyPayment($request->data['id']);
            $paymentDetails = Flutterwave::verifyTransaction($request->data['id']);
            if ($verificationData['status'] === 'success') {
            // process for successful charge
                $paymentId = $paymentDetails['data']['meta']['payment_id'];
                $studentId = !empty($paymentDetails['data']['meta']['student_id'])?$paymentDetails['data']['meta']['student_id']:null;
                $redirectPath = $paymentDetails['data']['meta']['redirect_path'];

                $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
                if($paymentId > 0){
                    $payment = Payment::where('id', $paymentId)->first();
                    $paymentType = $payment->type;
                }

                $student = Student::with('applicant', 'programme')->where('id', $studentId)->first();
                $amount = $paymentDetails['data']['meta']['amount'];
                $session = $paymentDetails['data']['meta']['academic_session'];
                
                if($paymentDetails['status'] == 'success'){
                    if($this->processRavePayment($paymentDetails)){

                        if($student && !empty($studentId)){
                            $pdf = new Pdf();
                            $invoice = $pdf->generateTransactionInvoice($session, $studentId, $paymentId, 'single');
                                    
                            $data = new \stdClass();
                            $data->lastname = $student->applicant->lastname;
                            $data->othernames = $student->applicant->othernames;
                            $data->amount = $amount;
                            $data->invoice = $invoice;
                            
                            Mail::to($student->email)->send(new TransactionMail($data));

                            if($paymentType == Payment::PAYMENT_TYPE_WALLET_DEPOSIT){
                                $creditStudent = $this->creditStudentWallet($studentId, $amount);
                                if(!$creditStudent){
                                    Log::info("**********************Unable to credit student**********************: ". $amount .' - '.$student);
                                }
                            }     
                        }

                        if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                            $applicantData = $paymentDetails;
                            $this->createApplicant($applicantData);
                            return true;
                        }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                            return true;
                        }elseif($paymentType == Payment::PAYMENT_TYPE_SCHOOL || $paymentType == Payment::PAYMENT_TYPE_SCHOOL_DE){
                            $this->generateMatricAndEmail($student);
                            return true;
                        }else{
                            return true;
                        }
                    }else{
                        if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                            return false;
                        }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                            return false;
                        }else{
                            return false;
                        }
                    }

                }

                if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                    return false;
                }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                    return false;
                }else{
                    return false;
                }

            }
        }
    }

    // private function generateMatricAndEmail($student){
    //     if(!$student->is_active && empty($student->matric_number)){
    //         $sessionSetting = SessionSetting::first();
    //         $admissionSession = $sessionSetting->admission_session;

    //         $programme = Programme::with('students', 'department', 'department.faculty')->where('id', $student->programme_id)->first();
    //         $codeNumber = $programme->code_number;
    //         $deptCode = $programme->department->code;
    //         $facultyCode = $programme->department->faculty->code;
    //         $programmeCode = $programme->code;
    //         $code = $deptCode.$programmeCode;

    //         $accessCode = $student->applicant->passcode;
    //         $studentPreviousEmail = $student->email;

    //         $name = $student->applicant->lastname.' '.$student->applicant->othernames;
    //         $nameParts = explode(' ', $student->applicant->othernames);
    //         $firstName = $nameParts[0];
    //         $studentEmail = strtolower($student->applicant->lastname.'.'.$firstName.'@st.tau.edu.ng');

    //         $newMatric = empty($programme->matric_last_number)? ($programme->students->count() + 20) + 1 : $programme->matric_last_number + 1;
    //         $matricNumber = substr($admissionSession, 2, 2).'/'.$facultyCode.$code.sprintf("%03d", $newMatric);

    //         $google = new Google();
    //         $createStudentEmail = $google->createUser($studentEmail, $student->applicant->othernames, $student->applicant->lastname, $accessCode);

    //         $student->email = $studentEmail;
    //         $student->matric_number = $matricNumber;
    //         $student->is_active = true;
    //         $student->save();

    //         $programme->matric_last_number = $newMatric;
    //         $programme->save();

            
    //         Mail::to($studentPreviousEmail)->send(new StudentActivated($student));

    //         return true;
    //     }
    // }
    public function callback (Request $request) {  


        // dd($request->all());
        

        // File path where you want to create the new file
        $filePath = "example.txt";

        // Write content to the file using file_put_contents
        file_put_contents($filePath, $request->all());

        // alert()->success('Good Job', 'Payment is successful')->persistent('Close');
        return $this->dataResponse('Payment is successful!', $request->all());
    }


    public function monnifyWebhook(Request $request){
        try {
            // Process webhook data
            $responseData = json_decode(json_encode($request->eventData));

            // Extract payment information
            $paymentReference = $responseData->paymentReference;
            $paymentStatus = $responseData->paymentStatus;
            $amountPaid = $responseData->amountPaid;

            Log::info('*****************Monnify Webhook ****************');
            Log::info('paymentReference: ' . $paymentReference);

            if ($paymentStatus == "PAID") {
                if (!$transaction = Transaction::where('reference', $paymentReference)->where('status', 0)->first()) {
                    return $this->dataResponse('Transaction not found', null, 'error');
                }

                $monnifyAmount = $this->getMonnifyAmount($transaction->amount_payed);
                if ($paymentReference == $transaction->reference) {
                    $newAmount = $amountPaid * 100;
                    $amountDiff = round(($newAmount - $monnifyAmount) / 100);

                    if ($amountDiff >= 0) {
                        // Credit bandwidth and update transaction status if the payment is valid
                        $result = $this->creditBandwidth($transaction, $amountPaid);
                        if($result){
                            return $this->dataResponse('Account Credited', $creditStudent);
                        }
                        return $this->dataResponse('Account not credited', null, 'error');
                    }
                    Log::info('Difference in amount: ' . $amountDiff);
                    return $this->dataResponse('Difference in amount: ' . $amountDiff, null, 'error');
                }
                return $this->dataResponse('invalid Transaction', null, 'error');
            }
        } catch (ValidationException $e) {
            return $this->dataResponse($this->getMissingParams($e), null, 'error');
        }
    }

     
    public function requeryUpperlinkPayment(Request $request){
        
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$transaction = Transaction::find($request->transaction_id)){
            alert()->error('Oops', 'Invalid Session ')->persistent('Close');
            return redirect()->back();
        }

        $paymentReference = $transaction->reference;

        return $this->upperlinkVerifyPayment($paymentReference);
    }


    public function getAllRave(){
        $data = [
            'page' => 1,
            'status' => 'SUCCESSFUL'
        ];
    
        // $data is optional
        $transfers = Flutterwave::transfers()->fetchAll($data);
    
        dd($transfers);
    }
}
