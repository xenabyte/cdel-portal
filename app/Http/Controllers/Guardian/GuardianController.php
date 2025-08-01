<?php

namespace App\Http\Controllers\Guardian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\Student;
use App\Models\AcademicLevel;
use App\Models\Session;
use App\Models\CourseRegistration;
use App\Models\Payment;
use App\Models\ResultApprovalStatus;
use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\RoomType;
use App\Models\ProgrammeCategory;

use Paystack;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

use App\Libraries\Result\Result;
use App\Libraries\Pdf\Pdf;
use App\Libraries\Bandwidth\Bandwidth;
use App\Libraries\Monnify\Monnify;
use App\Libraries\Paygate\Paygate;
use App\Libraries\Google\Google;


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class GuardianController extends Controller
{
    //

    public function index(Request $request){
        $guardian = Auth::guard('guardian')->user();

        if(empty($guardian->change_password)){
            return view('guardian.changePassword');
        }

        return view('guardian.home');
    }

    public function students(Request $request){

        $guardian = Auth::guard('guardian')->user();

        if(empty($guardian->change_password)){
            return view('guardian.changePassword');
        }

        return view('guardian.students');
    }

    public function profile(Request $request){

        return view('guardian.profile');
    }

    
    public function studentProfile(Request $request, $slug){
        $student = Student::withTrashed()->with('applicant', 'applicant.utmes', 'programme', 'transactions')->where('slug', $slug)->first();
        $academicLevels = AcademicLevel::orderBy('id', 'desc')->get();
        $sessions = Session::orderBy('id', 'desc')->get();
        $student->schoolFeeDetails = $this->checkSchoolFees($student);
        $student->accomondationDetails = $this->checkAccomondationStatus($student);

        return view('guardian.studentProfile', [
            'student' => $student,
            'academicLevels' => $academicLevels,
            'sessions' => $sessions
        ]);
    }

    public function makePayment(Request $request)
    {
        $studentId = $request->student_id;
        $student = Student::with('applicant')->where('id', $studentId)->first();
        $transaction = Transaction::find($request->transaction_id);

        $paymentId = $request->payment_id;
        $redirectLocation = url()->previous();
        $amount = $request->amount * 100;
        $transaction = Transaction::find($request->transaction_id);
        $paymentType = $paymentId == 0 ? Payment::PAYMENT_TYPE_WALLET_DEPOSIT : 'Other Fee';
        $suspensionId = $request->suspension_id;
        $summerCourses = null;
        $reference = $this->generatePaymentReference($paymentType);

        if($paymentId > 0){
            if(!$payment = Payment::with('structures')->where('id', $paymentId)->first()){
                alert()->error('Oops', 'Invalid Payment Initialization, contact ICT')->persistent('Close');
                return redirect()->back();
            }
            $redirectLocation = 'student/transactions';
            $amount = $request->amount;

            if($request->has('amountGeneral')){
                $amount = $request->amountGeneral*100;
            }

            $reference = $this->generatePaymentReference($payment->type);

            $paymentClass = new Payment();
            $paymentType = $paymentClass->classifyPaymentType($payment->type);
        }


        if(strtolower($paymentType) == "accomondation") {
            $validator = Validator::make($request->all(), [
                'campus' => 'required',
                'type_id' => 'required',
            ]);
        
            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return redirect()->back();
            }
        
            $campus = $request->campus;
            $typeId = $request->type_id;
        
            $roomType = RoomType::find($typeId);
            $amount = $roomType->amount;

            if(!$roomType) {
                alert()->error('Error', 'Selected room type not found.')->persistent('Close');
                return redirect()->back();
            }
        }

        $paymentGateway = $request->paymentGateway;

        // Determine which additional data to use
        $additionalData = !empty($hostelMeta) ? $hostelMeta : (!empty($summerCourses) ? $summerCourses : null);

        if (!$transaction) {
            // Create new transaction
            $transaction = Transaction::create([
                'student_id' => $studentId,
                'payment_id' => $paymentId,
                'amount_payed' => $amount,
                'payment_method' => $paymentGateway,
                'reference' => $reference,
                'session' => $student->academic_session,
                'plan_id' => !empty($bandwidthPlan) ? $bandwidthPlan->id : null,
                'additional_data' => $additionalData
            ]);
        }

        $meta = [
            "student_id" => $studentId,
            "payment_id" => $paymentId,
            "payment_gateway" => $paymentGateway,
            "reference" => $reference,
            "academic_session" => $student->academic_session,
            "redirect_path" => $redirectLocation,
            "payment_Type" => $paymentType,
            "amount" => $amount
        ];
        
        if (!empty($bandwidthPlan)) {
            $meta['plan_id'] = $bandwidthPlan->id;
        }
        
        if (!empty($suspensionId)) {
            $meta['suspension_id'] = $suspensionId;
        }

        if (
            in_array($student->programme_category_id, [
                ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::PGD),
                ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::MASTER),
                ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::DOCTORATE)
            ])
        ) {
            $paymentType = "PG Tuition fee";
        }

        if($transaction){
            $transaction->reference = $reference;
            $transaction->save();
        }

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
                    "payment_Type" => $paymentType,
                    "suspension_id" => $suspensionId,
                    "plan_id" => !empty($bandwidthPlan)?$bandwidthPlan->id:null,
                    "additionalData" => $additionalData
                ),
            );

            return Paystack::getAuthorizationUrl($data)->redirectNow();
        }

        if(strtolower($paymentGateway) == 'rave') {
            Log::info("Flutterwave Amount ****************: ". round($this->getRaveAmount($amount)));

            $reference = Flutterwave::generateReference();
            if($transaction){
                $transaction->reference = $reference;
                $transaction->save();
            }

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
                    "payment_Type" => $paymentType,
                    "suspension_id" => $suspensionId,
                    "plan_id" => !empty($bandwidthPlan)?$bandwidthPlan->id:null,
                    "additionalData" => $additionalData,
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

        if(strtolower($paymentGateway) == 'upperlink') {
            Log::info("Upperlink Amount ****************: ". round($this->getUpperlinkAmount($amount)));

            $data = array(
                "amount" => round($this->getUpperlinkAmount($amount)/100),
                "phone" => $student->applicant->phone_number,
                "city" => "Lagos",
                "address" =>  strip_tags($student->applicant->address),
                "email" => $student->email,
                "payGateRef" => $reference,
                "merchantId" => env('UPPERLINK_REF'),
                "countryCode" =>  "NG",
                "currency" => "NGN",
                "logoUrl" => env('SCHOOL_LOGO'),
                "firstName" => $student->applicant->othernames,
                "lastName" => $student->applicant->lastname,
                "redirectUrl" => env("UPPERLINK_REDIRECT_URL"),
                "accountCode" => BankAccount::getBankAccountCode($paymentType)->upperlinkAccountCode,
                "meta" => json_encode($meta),
            );

            $paygate = new Paygate();
            $paymentData = $paygate->initializeTransaction($data);
            if($paymentData['code'] != "200"){
                Log::info($paymentData);
                $message = 'Payment Gateway not available, try again.';
                alert()->info('Opps!', $message)->persistent('Close');
                return redirect()->back();
            }

            $paymentUrl = $paymentData['data']['checkOutUrl'];

            return redirect($paymentUrl);
        }

        if(strtolower($paymentGateway) ==  "monnify"){
            $now = Carbon::now();
            $future = $now->addHours(48);
            $invoiceExpire = $future->format('Y-m-d H:i:s');
            $monnifyAmount = $this->getMonnifyAmount($amount);


            $monnifyPaymentdata = array(
                'amount' => ceil($monnifyAmount/100),
                'invoiceReference' => $transaction->reference,
                'description' =>  !empty($payment) ? $payment->title : $paymentType,
                'currencyCode' => "NGN",
                'contractCode' => env('MONNIFY_CONTRACT_CODE'),
                'customerEmail' => $student->email,
                'customerName' => $student->applicant->lastname. ' '.$student->applicant->othernames,
                'expiryDate' => $invoiceExpire,
                'paymentMethods' => ["CARD","ACCOUNT_TRANSFER","USSD","PHONE_NUMBER"],
                'redirectUrl'=> env("MONNIFY_REDIRECT_URL"),
                'metaData' => $meta,
                // 'incomeSplitConfig' => [
                //     [
                //         'subAccountCode' => BankAccount::getBankAccountCode($paymentType)->monnifyAccountCode,
                //         'feePercentage' => 100,
                //         'splitPercentage' => 100,
                //         'feeBearer' => true,
                //     ]
                // ]
            );

            $monnify = new Monnify();
            $createInvoice = $monnify->initiateInvoice($monnifyPaymentdata);
            $checkoutUrl = $createInvoice->responseBody->checkoutUrl;
            $paymentGatewayRef = $createInvoice->responseBody->transactionReference;

            $transaction->checkout_url = $checkoutUrl;
            $transaction->payment_gateway_ref = $paymentGatewayRef;
            $transaction->save();

            return redirect($checkoutUrl);
        }

        $message = 'Invalid Payment Gateway';
        alert()->info('Opps!', $message)->persistent('Close');
        return redirect()->back();
    }

    public function generateResult(Request $request){
        $validator = Validator::make($request->all(), [
            'semester' => 'required',
            'session' => 'required',
            'level_id' => 'required',
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentId = $request->student_id;

        $semester = $request->semester;
        $academicSession = $request->session;
        $levelId = $request->level_id;
        $academicLevel = AcademicLevel::find($levelId);
        $level = $academicLevel->level;

        $courseRegs = CourseRegistration::with('course')
        ->where('student_id', $studentId)
        ->where('academic_session', $academicSession)
        ->where('level_id', $levelId)
        ->where('result_approval_id',  ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))
        ->whereHas('course', function ($query) use ($semester) {
            $query->where('semester', $semester);
        })
        ->get();

        if(!$courseRegs->count() > 0) {
            alert()->info('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        $student = Student::find($studentId);

        $checkStudentPayment = $this->checkSchoolFees($student, $academicSession, $levelId);
        if($checkStudentPayment->status != 'success'){
            alert()->error('Oops!', 'Something went wrong with School fees')->persistent('Close');
            return redirect()->back();
        }

        $passTuition = $checkStudentPayment->passTuitionPayment;
        $fullTuitionPayment = $checkStudentPayment->fullTuitionPayment;
        $passEightyTuition = $checkStudentPayment->passEightyTuition;

        if($semester == 1 && !$passTuition){
            alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 50% of school fees for '.$academicSession.' acaddemic session')->persistent('Close');
            return redirect()->back();
        }

        if($semester == 2 && !$fullTuitionPayment){
            alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 100% of school fees for '.$academicSession.' acaddemic session')->persistent('Close');
            return redirect()->back();
        }

        $pdf = new Pdf();
        $examResult = $pdf->generateExamResult($studentId, $academicSession, $semester, $level);

        return redirect(asset($examResult));
    }


    public function updatePassword (Request $request) {

        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'confirm_password' => 'required'
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $guardian = Auth::guard('guardian')->user();

        if($request->has('case')){
            if($request->password == $request->confirm_password){
                $guardian->password = bcrypt($request->password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }
            $guardian->change_password = true;
        }else{
            if(!empty($request->old_password)){
                alert()->error('Oops!', 'Old password is required')->persistent('Close');
                return redirect()->back();
            }

            if(\Hash::check($request->old_password, Auth::guard('guardian')->user()->password)){
                if($request->password == $request->confirm_password){
                    $guardian->password = bcrypt($request->password);
                }else{
                    alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                    return redirect()->back();
                }
            }else{
                alert()->error('Oops', 'Wrong old password, Try again with the right one')->persistent('Close');
                return redirect()->back();
            }
        }

        if($guardian->update()) {
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
