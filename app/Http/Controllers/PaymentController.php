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
use App\Models\ProgrammeCategory;

use App\Mail\ApplicationPayment;
use App\Mail\StudentActivated;
use App\Mail\TransactionMail;

use App\Libraries\Google\Google;
use App\Libraries\Pdf\Pdf;
use App\Libraries\Paygate\Paygate;
use App\Libraries\Bandwidth\Bandwidth;
use App\Libraries\Monnify\Monnify;


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
    public function verifyPayment(){
        Log::info("**********************Paystack Verifying Payment**********************");

        try {
            $paymentDetails = Paystack::getPaymentData();
            $reference = $paymentDetails['data']['metadata']['reference'] ?? null;

            if (!$reference) {
                return $this->genericVerificationResponse(
                    'Missing payment reference',
                    false,
                    false,
                    '/'
                );
            }

            return $this->handleGenericPaymentVerification('paystack', $reference, '/');
        } catch (\Exception $e) {
            Log::error($e);

            return $this->genericVerificationResponse(
                'An error occurred while verifying payment',
                false,
                false,
                '/'
            );
        }
    }

    public function raveWebhook(Request $request){
        Log::info("**********************Flutterwave Webhook**********************");

        // Verify the webhook came from Flutterwave
        $verified = Flutterwave::verifyWebhook();
        Log::info(json_encode($verified));

        // Proceed only if it's a successful charge
        if (
            $verified &&
            $request->event === 'charge.completed' &&
            isset($request->data['status']) &&
            $request->data['status'] === 'successful'
        ) {
            $reference = $request->data['tx_ref'] ?? $request->data['reference'] ?? null;

            if ($reference) {
                return $this->handleGenericPaymentVerification('flutterwave', $reference, '/', true);
            }

            Log::warning('[FLUTTERWAVE] Webhook received without valid reference', ['data' => $request->all()]);
        }

        return response()->json(['status' => 'ignored'], 200);
    }

    public function paystackWebhook(Request $request){
        try {
            $webhookData = $request->all();
            Log::info('Paystack Webhook Received: ' . json_encode($webhookData));

            if (!isset($webhookData['event']) || $webhookData['event'] !== 'charge.success') {
                return response()->json(['status' => 'ignored'], 200);
            }

            $reference = $webhookData['data']['reference'] ?? null;

            if (!$reference) {
                Log::warning('Paystack webhook missing reference.');
                return response()->json(['status' => 'invalid reference'], 400);
            }

            // Optional: Remove or reduce delay in production
            // sleep(300); // Delay only for dev/debugging

            return $this->handleGenericPaymentVerification('paystack', $reference, '/', true);
        } catch (\Throwable $e) {
            Log::error('Paystack Webhook Error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }


    public function monnifyWebhook(Request $request)
    {
        sleep(30);

        $eventData = $request->all();
        Log::info("Monnify Webhook Received: " . json_encode($eventData));
        $paymentReference = $eventData['eventData']['paymentReference'] ?? null;

        if (!$paymentReference) {
            Log::error('Missing paymentReference in Monnify webhook.', $eventData);
            return response()->json(['status' => 'error'], 400);
        }

        Log::info("Monnify Webhook Received for Reference: {$paymentReference}");

        return $this->handleGenericPaymentVerification('monnify', $paymentReference, '/', true);
    }

    public function upperlinkVerifyPayment(Request $request, $paymentReference = null, $redirectPath = '/', $returnAsJson = false){
        $ref = $paymentReference ?? $request->query('paymentReference') ?? ($_GET['reference'] ?? null);
        return $this->handleGenericPaymentVerification('upperlink', $ref, $redirectPath, $returnAsJson);
    }

    public function monnifyVerifyPayment(Request $request){
        $ref = $request->query('paymentReference');
        return $this->handleGenericPaymentVerification('monnify', $ref, '/');
    }

    public function upperlinkWebhook(Request $request){
        $ref = $request->input('reference');
        return $this->handleGenericPaymentVerification('upperlink', $ref, '/', true);
    }

    private function handleGenericPaymentVerification($gateway, $reference, $redirectPath = '/', $returnAsJson = false){

        Log::info("********** Verifying Payment via $gateway **********", ['reference' => $reference]);

        $programmeCategories = ProgrammeCategory::with('academicSessionSetting')->get();

        if (empty($reference)) {
            return $this->genericVerificationResponse(
                'Payment reference is missing',
                $returnAsJson,
                false,
                $redirectPath
            );
        }

        // Step 1: Verify transaction from the gateway
        $paymentDetails = null;
        $paymentStatus = null;
        $paymentData = [];

        if ($gateway === 'monnify') {
            $transaction = Transaction::where('reference', $reference)->first();

            if (!$transaction) {
                alert()->error('Error', 'Transaction not found');
                return redirect($redirectPath);
            }
            $paymentGatewayRef = $transaction->payment_gateway_ref;

            $monnify = new Monnify();
            $verifyInvoice = $monnify->verifyInvoice($paymentGatewayRef);

            if (!$verifyInvoice || !$verifyInvoice->requestSuccessful) {
                return $this->genericVerificationResponse(
                    $verifyInvoice->responseMessage ?? 'Unable to verify Monnify payment',
                    $returnAsJson,
                    false,
                    $redirectPath
                );
            }

            $paymentStatus = $verifyInvoice->responseBody->paymentStatus ?? null;
            $paymentData = (array) ($verifyInvoice->responseBody->metaData ?? []);
            $paymentDetails = $verifyInvoice->responseBody;
        }

        if ($gateway === 'upperlink') {
            $paygate = new PayGate();
            $response = $paygate->verifyTransaction($reference);

            if (!isset($response['transactionStatus']) || $response['transactionStatus'] !== '00') {
                return $this->genericVerificationResponse(
                    'Upperlink transaction verification failed',
                    $returnAsJson,
                    false,
                    $redirectPath
                );
            }

            $paymentStatus = 'PAID';
            $paymentData = json_decode($response['meta'] ?? '{}', true);
            $paymentDetails = (object) $response;
        }

        if ($paymentStatus !== 'PAID') {
            return $this->genericVerificationResponse(
                'Payment not successful',
                $returnAsJson,
                false,
                $redirectPath
            );
        }

        // Step 2: Process metadata and student/payment details
        $paymentId = $paymentData['payment_id'] ?? null;
        $studentId = $paymentData['student_id'] ?? null;
        $redirectPath = $paymentData['redirect_path'] ?? '/';
        $paymentGateway = $paymentData['payment_gateway'] ?? $gateway;
        $session = $paymentData['academic_session'] ?? null;
        $amount = $gateway === 'monnify' ? ($paymentData['amount'] ?? 0) : (($paymentDetails->amount ?? 0) * 100);
        $txRef = $paymentData['reference'] ?? $reference;

        $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
        $payment = null;
        if ($paymentId) {
            $payment = Payment::find($paymentId);
            $paymentType = $payment->type ?? $paymentType;
        }

        $student = Student::with('applicant', 'programme')->find($studentId);

        // Step 3: Process and credit the payment
        switch ($gateway) {
            case 'monnify':
                $processed = $this->processMonnifyPayment($paymentDetails);
                break;

            case 'upperlink':
                $processed = $this->processUpperLinkPayment((array) $paymentDetails);
                break;

            case 'flutterwave':
                $processed = $this->processFlutterwavePayment((array) $paymentDetails);
                break;

            case 'paystack':
                $processed = $this->processPaystackPayment((array) $paymentDetails);
                break;

            default:
                $processed = false; // or throw an exception
                break;
        }

        if (!$processed) {
            return $this->genericVerificationResponse(
                'Payment verification failed during processing.',
                $returnAsJson,
                false,
                $redirectPath,
                $paymentType,
                $student,
                $programmeCategories,
                $payment
            );
        }

        // Step 4: Post-payment handling
        return $this->handlePostPaymentProcessing([
            'txRef' => $txRef,
            'paymentId' => $paymentId,
            'studentId' => $studentId,
            'paymentType' => $paymentType,
            'amount' => $amount,
            'session' => $session,
            'student' => $student,
            'paymentGateway' => $paymentGateway,
            'redirectPath' => $redirectPath,
            'returnAsJson' => $returnAsJson,
            'programmeCategories' => $programmeCategories,
            'payment' => $payment,
            'applicantData' => $paymentDetails,
            'generateInvoice' => true,
            'sendEmail' => env('SEND_MAIL'),
        ]);
    }

    protected function handlePostPaymentProcessing(array $data){
        $txRef = $data['txRef'] ?? null;
        $paymentType = $data['paymentType'] ?? null;
        $paymentId = $data['paymentId'] ?? null;
        $studentId = $data['studentId'] ?? null;
        $amount = $data['amount'] ?? null;
        $session = $data['session'] ?? null;
        $redirectPath = $data['redirectPath'] ?? '/';
        $returnAsJson = $data['returnAsJson'] ?? false;
        $generateInvoice = $data['generateInvoice'] ?? true;
        $sendEmail = $data['sendEmail'] ?? true;
        $paymentGateway = $data['paymentGateway'] ?? null;

        $student = $data['student'] ?? null;
        $payment = $data['payment'] ?? null;
        $applicantData = $data['applicantData'] ?? null;
        $programmeCategories = $data['programmeCategories'] ?? [];

        $transaction = Transaction::where('reference', $txRef)->first();
        $creditResult = true;

        switch ($paymentType) {
            case Payment::PAYMENT_TYPE_WALLET_DEPOSIT:
                $creditResult = $this->creditStudentWallet($transaction);
                break;

            case Payment::PAYMENT_TYPE_ACCOMONDATION:
                $creditResult = $this->creditAccommodation($transaction);
                break;

            case Payment::PAYMENT_TYPE_SUMMER_COURSE_REGISTRATION:
                $creditResult = $this->creditStudentSummerCourseReg($transaction);
                break;

            case Payment::PAYMENT_TYPE_BANDWIDTH:
                $creditResult = $this->creditBandwidth($transaction, $amount);
                break;

            case Payment::PAYMENT_TYPE_INTRA_TRANSFER_APPLICATION:
                $creditResult = $this->initChangeProgramme($transaction, $studentId);
                break;

            case Payment::PAYMENT_TYPE_SCHOOL:
            case Payment::PAYMENT_TYPE_SCHOOL_DE:
                $this->generateMatricAndEmail($student);
                break;

            case Payment::PAYMENT_TYPE_GENERAL_APPLICATION:
            case Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION:
                $this->createApplicant($applicantData, $paymentGateway);
                break;
        }

        if (is_string($creditResult)) {
            return $this->genericVerificationResponse($creditResult, $returnAsJson, false, $redirectPath, $paymentType, $student, $programmeCategories, $payment);
        } elseif (!$creditResult) {
            Log::info("Unable to credit: $paymentType | Amount: $amount | Ref: $txRef");
        }

        if ($sendEmail && $student && isset($student->email)) {
            $pdf = new Pdf();
            $invoice = $pdf->generateTransactionInvoice($session, $studentId, $paymentId, 'single');

            $mailData = new \stdClass();
            $mailData->lastname = $student->applicant->lastname ?? '';
            $mailData->othernames = $student->applicant->othernames ?? '';
            $mailData->amount = $amount;
            $mailData->invoice = $invoice;

            if (env('SEND_MAIL')) {
                Mail::to($student->email)->send(new TransactionMail($mailData));
            }
        }

        return $this->genericVerificationResponse('Payment successful', $returnAsJson, true, $redirectPath, $paymentType, $student, $programmeCategories, $payment);
    }

    private function genericVerificationResponse($message, $asJson, $success, $redirectPath, $paymentType = null,  $programmeCategories = [], $payment = null){
        if ($asJson) {
            return [
                'status' => $success ? 'success' : 'error',
                'message' => $message
            ];
        }

        if ($success) {
            alert()->success('Good Job', $message)->persistent('Close');
        } else {
            alert()->error('Oops', $message)->persistent('Close');
        }

        // For application-based payments, return view
        if (
            !$success &&
            in_array($paymentType, [Payment::PAYMENT_TYPE_GENERAL_APPLICATION, Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION])
        ) {
            return view($redirectPath, [
                'programmes' => $this->programmes,
                'payment' => $payment,
                'programmeCategories' => $programmeCategories
            ]);
        }

        return redirect($redirectPath);
    }

    
    public function callback (Request $request) {  


        // dd($request->all());
        

        // File path where you want to create the new file
        $filePath = "example.txt";

        // Write content to the file using file_put_contents
        file_put_contents($filePath, $request->all());

        // alert()->success('Good Job', 'Payment is successful')->persistent('Close');
        return $this->dataResponse('Payment is successful!', $request->all());
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


    /**
     * Retrieve all successful transactions from rave
     * @return void
     */
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
