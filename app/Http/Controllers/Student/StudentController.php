<?php

namespace App\Http\Controllers\Student;

use App\Models\ProgrammeCategory;
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
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\StudentExit;
use App\Models\Plan;
use App\Models\StudentCourseRegistration;
use App\Models\Programme;
use App\Models\BankAccount;
use App\Models\ProgrammeChangeRequest;

use App\Libraries\Pdf\Pdf;
use App\Libraries\Bandwidth\Bandwidth;
use App\Libraries\Monnify\Monnify;
use App\Libraries\Paygate\Paygate;
use App\Libraries\Google\Google;

use App\Models\Hostel;
use App\Models\RoomType;
use App\Models\RoomBedSpace;
use App\Models\Room;
use App\Models\Allocation;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;
use KingFlamez\Rave\Facades\Rave as Flutterwave;
use OneSignal;



class StudentController extends Controller
{

    public function onBoarding(Request $request){
        return view('student.onBoarding');
    }
    
    public function index(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;
        $applicationSession = $student->programmeCategory->academicSessionSetting->application_session;
        $admissionSession = $student->programmeCategory->academicSessionSetting->admission_session;


        $acceptancePayment = Payment::where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)
        ->where('academic_session', $admissionSession)
        ->where('programme_id', $student->programme_id)
        ->where('programme_category_id', $student->programme_category_id)
        ->first();

        if(!$acceptancePayment){
            $acceptancePayment = Payment::where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)
            ->where('academic_session', $admissionSession)
            ->where('programme_category_id', $student->programme_category_id)
            ->first();
        }

        $acceptancePaymentId = $acceptancePayment->id;
        $acceptanceTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $acceptancePaymentId)->where('status', 1)->first();

        $totalExpenditure = Transaction::where('student_id', $studentId)->where('status', 1)->where('payment_method', 'Wallet')->sum('amount_payed');
        $totalDeposit = Transaction::where('student_id', $studentId)->where('status', 1)->where('payment_id', 0)->sum('amount_payed');

        $paymentCheck = $this->checkSchoolFees($student);

         // if ((($levelId == 1 && strtolower($applicationType) == 'utme') || 
        //     ($levelId == 2 && strtolower($applicationType) != 'utme')) && 
        //     ($student->clearance_status != 1)) {
        //     return view('student.clearance', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        if(!$acceptanceTransaction && $student->is_active == 0){
            return view('student.acceptanceFee', [
                'payment' => $acceptancePayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }
        

        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        // if($student->is_active  && empty($student->image)){
        //     return view('student.updateImage', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        // $google = new Google();
        // $google->addMemberToGroup($student->email, env('GOOGLE_STUDENT_GROUP'));
      
        return view('student.home', [
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'totalExpenditure' => $totalExpenditure,
            'schoolPaymentTransaction' => $paymentCheck->schoolPaymentTransaction,
            'totalDeposit' => $totalDeposit
        ]);
    }

    public function profile(Request $request){
        $student = Auth::guard('student')->user();
        $levelId = $student->level_id;
        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $paymentCheck = $this->checkSchoolFees($student);

        return view('student.profile', [
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function saveBioData(Request $request){
        $validator = Validator::make($request->all(), [
            'dob' => 'nullable',
            'religion' => 'nullable',
            'gender' => 'nullable',
            'marital_status' => 'nullable',
            'nationality' => 'nullable',
            'state' => 'nullable',
            'lga' => 'nullable',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();
        $applicant = Applicant::find($student->user_id);

        
        if(!empty($request->dob) && $request->dob != $applicant->dob){
            $applicant->dob = $request->dob;
        }

        if(!empty($request->religion) && $request->religion != $applicant->religion){
            $applicant->religion = $request->religion;
        }

        if(!empty($request->gender) && $request->gender != $applicant->gender){
            $applicant->gender = $request->gender;
        }

        if(!empty($request->marital_status) && $request->marital_status != $applicant->marital_status){
            $applicant->marital_status = $request->marital_status;
        }

        if(!empty($request->nationality) && $request->nationality != $applicant->nationality){
            $applicant->nationality = $request->nationality;
        }

        if(!empty($request->state) && $request->state != $applicant->state_of_origin){
            $applicant->state = $request->state;
        }

        if(!empty($request->lga) && $request->lga != $applicant->lga){
            $applicant->lga = $request->lga;
        }

        if(!empty($request->signature) && $request->signature != $applicant->signature){
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $student->slug.'-signature')));

            $imageUrl = 'uploads/student/signature/'.$slug.'.'.$request->file('signature')->getClientOriginalExtension();
            $image = $request->file('signature')->move('uploads/student/signature/', $imageUrl);

            $student->signature = $imageUrl;
            $student->save();
        }

        if($applicant->save()){
            alert()->success('Changes Saved', 'Bio data saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updatePassword (Request $request) {

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required',
            'confirm_password' => 'required'
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();

        if(\Hash::check($request->old_password, Auth::guard('student')->user()->password)){
            if($request->password == $request->confirm_password){
                $student->password = bcrypt($request->password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }
        }else{
            alert()->error('Oops', 'Wrong old password, Try again with the right one')->persistent('Close');
            return redirect()->back();
        }

        if($student->update()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function makePayment(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;
        $paymentId = $request->payment_id;
        $redirectLocation = 'student/home';
        $amount = $request->amount * 100;
        $transaction = Transaction::find($request->transaction_id);
        $paymentType = 'Other Fee';
        $suspensionId = $request->suspension_id;
        $summerCourses = null;
        $reference = $this->generatePaymentReference("General Fee");

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


        $bandwidthPlan = !empty($request->plan_id)?Plan::find($request->plan_id):null;
        if(!empty($bandwidthPlan)){
            $amount = $bandwidthPlan->amount;
            $redirectLocation = 'student/purchaseBandwidth';
        }

        if(strtolower($paymentType) == strtolower(Payment::PAYMENT_TYPE_ACCOMONDATION)) {
            $validator = Validator::make($request->all(), [
                'campus' => 'required',
                'hostel_id' => 'required',
                'type_id' => 'required',
                'room_id' => 'required',
            ]);
        
            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return redirect()->back();
            }
        
            $campus = $request->campus;
            $hostelId = $request->hostel_id;
            $typeId = $request->type_id;
            $roomId = $request->room_id;
        
            $roomType = RoomType::find($typeId);
            $amount = $roomType->amount;

            if(!$roomType) {
                alert()->error('Error', 'Selected room type not found.')->persistent('Close');
                return redirect()->back();
            }

            $room = Room::with('allocations', 'type', 'hostel')->find($roomId);
        
            if(!$room) {
                alert()->error('Error', 'Selected room not found.')->persistent('Close');
                return redirect()->back();
            }
        
            $totalBedSpaces = RoomBedSpace::where('room_id', $roomId)->count();
        
            $occupiedSpaces = Allocation::where('room_id', $roomId)
                                         ->whereNull('release_date')
                                         ->count();
        
            if($occupiedSpaces >= $totalBedSpaces) {
                alert()->error('Error', 'No available bed spaces in the selected room.')->persistent('Close');
                return redirect()->back();
            }

            $hostelData = array(
                "room_id" => $roomId,
                "hostel_id" => $hostelId,
                "campus" => $campus,
                "type_id" => $typeId,
            );

            $hostelMeta = json_encode($hostelData);


            $hostelPaymentId = $request->hostel_payment_id;
            if(!empty($hostelPaymentId)){
                $hostelPaymentTx = Transaction::where('student_id', $studentId)->where('id', $hostelPaymentId)->where('status', 1)->first();
                if(!$hostelPaymentTx){
                    alert()->error('Error', 'Accomondation payment not found, contact Bursary')->persistent('Close');
                    return redirect()->back();
                }

                $hostelPaymentAmount = $hostelPaymentTx->amount_payed;
                if($amount != $hostelPaymentAmount){
                    alert()->error('Error', 'Invalid accomondation payment, amount paid dosent match hostel type picked')->persistent('Close');
                    return redirect()->back();
                }

                $hostelPaymentTx->additional_data = $hostelMeta;
                $hostelPaymentTx->save();

                $hostelPaymentTx->refresh();
                $creditStudent = $this->creditAccommodation($hostelPaymentTx);
                if (is_string($creditStudent)) {
                    alert()->error('Oops', $creditStudent)->persistent('Close');
                }

                alert()->success('Room allocated successfully', '')->persistent('Close');
                return redirect()->back();
            }
        
        }

        if(!empty($payment) && strtolower($payment->type) == strtolower(Payment::PAYMENT_TYPE_INTRA_TRANSFER_APPLICATION)) {

            $pendingRequest = ProgrammeChangeRequest::where('student_id', $studentId)
            ->where('status', '!=', 'completed')
            ->first();

            if ($pendingRequest) {
                alert()->error('Oops', 'You already have a pending request.')->persistent('Close');
                return redirect()->back();
            }
        }

        if(!empty($payment) && strtolower($payment->type) == strtolower(Payment::PAYMENT_TYPE_SUMMER_COURSE_REGISTRATION)) {
            $summerCoursesIds = $request->input('failed_selected_courses', []);
            $feePerCourse = $payment->structures->sum('amount');

            $selectedCoursesCount = count($summerCoursesIds);
            $amount = $selectedCoursesCount * $feePerCourse;

             $summerCourses = json_encode($summerCoursesIds);
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

        if(strtolower($paymentGateway) == 'paystack') {
            Log::info("Paystack Amount ****************: ". round($this->getPaystackAmount($amount)));

            if($transaction){
                $transaction->reference = $reference;
                $transaction->save();
            }

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

        if(strtolower($paymentGateway) == 'wallet') {
            Log::info("Wallet Amount ****************: ". round($amount));

            $studentBalance = $student->amount_balance;
            if($studentBalance < ($amount + 20000)){
                $message = 'Insufficient funds, please top up your wallet or use another payment method.';
                alert()->info('Opps!', $message)->persistent('Close');
                return redirect()->back();
            }
            $transactionData = new \stdClass();
            $transactionData->amount = $amount;
            $transactionData->email = $student->email;
            $transactionData->payment_id = $paymentId;
            $transactionData->student_id = $studentId;
            $transactionData->reference = $reference;
            $transactionData->academic_session = $student->academic_session;
            $transactionData->redirect_path = $redirectLocation;
            $transactionData->payment_gateway = $paymentGateway;
            $transactionData->transaction_id = $request->transaction_id;
            $transactionData->plan_id = !empty($bandwidthPlan)?$bandwidthPlan->id:null;
            $transactionData->suspension_id = $suspensionId;
            $transactionData->hostel_meta =!empty($hostelMeta)?$hostelMeta:null;
            $transactionData->additionalData = $additionalData;

            return $this->billStudent($transactionData);
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
                "accountCode" => BankAccount::getBankAccountCode($paymentType),
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
                'description' =>  $transaction->narration,
                'currencyCode' => "NGN",
                'contractCode' => env('MONNIFY_CONTRACT_CODE'),
                'customerEmail' => $student->email,
                'customerName' => $student->applicant->lastname. ' '.$student->applicant->othernames,
                'expiryDate' => $invoiceExpire,
                'paymentMethods' => ["CARD","ACCOUNT_TRANSFER","USSD","PHONE_NUMBER"],
                'redirectUrl'=> env("MONNIFY_REDIRECT_URL"),
                'metaData' => $meta
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
    
    public function transactions(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;

        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $transactions = Transaction::where('student_id', $studentId)->where('payment_id', '!=', 0)->orderBy('status', 'ASC')->get();

        $paymentCheck = $this->checkSchoolFees($student);
    
        return view('student.transactions', [
            'transactions' => $transactions,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'studentPendingTransactions' => $paymentCheck->studentPendingTransactions,
            'schoolPaymentTransaction' => $paymentCheck->schoolPaymentTransaction
        ]);
    }

    public function walletTransactions(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;

        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $transactions = Transaction::where('student_id', $studentId)
        ->where(function($query) {
            $query->where('payment_id', 0)
                ->orWhere('payment_method', 'Wallet');
        })
        ->orderBy('id', 'DESC')
        ->get();


        $paymentCheck = $this->checkSchoolFees($student);


        return view('student.walletTransactions', [
            'transactions' => $transactions,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function mentor(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $mentorId  = $student->mentor_id;
        $paymentCheck = $this->checkSchoolFees($student);

        $mentor = Staff::with('faculty', 'acad_department')->where('id', $mentorId)->first();

        return view('student.mentor', [
            'mentor' => $mentor,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function exits(Request $request){
        $student = Auth::guard('student')->user();
        $levelId = $student->level_id;
        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $paymentCheck = $this->checkSchoolFees($student);

        return view('student.exits', [
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        ]);
    }

    public function getStudent(Request $request) {
        $matricNumber = $request->matric_number;

        $student = Student::with('applicant')->where('matric_number', $matricNumber)
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->first();

        if(!$student){
            return response()->json(['status' => 'record_not_found']);
        }

        return response()->json(['status' => 'record_found', 'student' => $student]);
    }

    public function saveStudentDetails(Request $request){

        $onboarding = FALSE;
        if(empty($request->bandwidth_username)){
            $validator = Validator::make($request->all(), [
                'student_id' => 'required',
                'email' => 'required',
                'lastname' => 'required',
                'othernames' => 'required',
                'password' => 'required',
                'confirm_password' => 'required'
            ]);

            $onboarding = TRUE;
        }else{
            $validator = Validator::make($request->all(), [
                'student_id' => 'required',
                'bandwidth_username' => 'required',
            ]);
        }


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        
        if(!$student = Student::with('applicant')->where('id', $request->student_id)->first()){
            alert()->error('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        if($onboarding && empty(strpos($request->email, env('SCHOOL_DOMAIN')))) {
            alert()->error('Error', 'Invalid student email, your student email must contain @'.env('SCHOOL_DOMAIN'))->persistent('Close');
            return redirect()->back();
        }

        if($onboarding && $student->onboard_status){
            alert()->info('Oops!', 'You have been onboarded successfully, kindly login to your portal with your details')->persistent('Close');
            return redirect()->back();
        }

        $applicantId = $student->user_id;
        if($onboarding && !$applicant = Applicant::find($applicantId)){
            alert()->error('Oops!', 'Student application data mismatch')->persistent('Close');
            return redirect()->back();
        }
        
        if($onboarding && !empty($applicant)){
            $applicant->lastname = $request->lastname;
            $applicant->othernames = $request->othernames;
            $applicant->update();
        }

        if($onboarding){
            if($request->password == $request->confirm_password){
                $student->password = bcrypt($request->password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }

            $student->email = $request->email;
            $student->onboard_status = true;
        }

        if(!empty($request->bandwidth_username)){
            $bandwidth = new Bandwidth();
            $validateUsername = $bandwidth->validateUser($request->bandwidth_username);
            if($validateUsername['status'] != 'success'){
                alert()->error('Oops!', 'Invalid Username, Kindly enter the correct username')->persistent('Close');
                return redirect()->back();
            }
            $student->bandwidth_username = $request->bandwidth_username;
        }

        if($student->update()) {
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

    public function uploadImage (Request $request) {

        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();

        if($request->password == $student->passcode){
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $student->slug)));

            $imageUrl = 'uploads/student/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
            $image = $request->file('image')->move('uploads/student', $imageUrl);
            $student->image = $imageUrl;
        }else{
            alert()->error('Oops', 'Wrong passcode, Try again with the right one')->persistent('Close');
            return redirect()->back();
        }

        if($student->update()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function getPayment(Request $request) {
        $type = $request->type;
        $session = $request->academic_session;
        $programmeId = $request->programme_id;
        $levelId  = $request->level;
        $userType = $request->userType;
        if($userType == 'applicant') {
            $applicant = Applicant::with('programme', 'student')->where('id', $request->student_id)->first();
            $applicantId = $applicant->id;
            if(!empty($applicant->student)){
                $studentId = $applicant->student->id;
            }
        }else{
            $studentId = $request->student_id;
        }

        if(!empty($studentId)){
            $student = Student::find($studentId);
            
            if($type == Payment::PAYMENT_TYPE_OTHER){
                $payment = Payment::with(['structures'])->where([
                    'type' => $type,
                    'academic_session' => $session,
                ])->get();
        
                if(!$payment){
                    return response()->json(['status' =>  'Payment type: '.$type.' doesnt exist']);
                }
            }else{
                $payment = Payment::with(['structures'])->where([
                    'type' => $type,
                    'academic_session' => $session,
                ])->first();
        
                if(!$payment){
                    return response()->json(['status' =>  'Payment type: '.$type.' doesnt exist']);
                }
            }

            return response()->json([
                'status' => 'success',
                'data' => $payment
            ]);
        }

        $payment = Payment::with(['structures'])->where([
            'type' => $type,
            'academic_session' => $session,
        ])->first();

        if(!$payment){
            return response()->json(['status' =>  'Payment type: '.$type.' doesnt exist']);
        }
        
        return response()->json([
            'status' => 'success',
            'data' => $payment
        ]);
    }
    
    public function setMode(Request $request){
        $validator = Validator::make($request->all(), [
            'dashboard_mode' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if($request->has('linkedIn') && !empty($request->linkedIn)) {
            $linkedInUrl = $request->linkedIn;
            if (!$this->isValidLinkedInURL($linkedInUrl)) {
                alert()->error('Oops', 'Invalid LinkedIn URL')->persistent('Close');
                return redirect()->back();
            }
        }

        $student = Auth::guard('student')->user();
        if(!empty($request->dashboard_mode)){
            $student->dashboard_mode = $request->dashboard_mode;
        }
        
        if(!empty($request->linkedIn)){
            $student->linkedIn = $request->linkedIn;
        }

        if($student->update()){
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function exitApplication(Request $request){

        $student = Auth::guard('student')->user();

        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        // $studentRegistrationCount = StudentCourseRegistration::where('student_id', $studentId)
        // ->where('academic_session', $academicSession)
        // ->count();

        // if($studentRegistrationCount < 1){
        //     alert()->error('Oops!', 'You are required to complete your course registration')->persistent('Close');
        //     return redirect()->back();
        // }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'type' => 'required',
            'transport_mode' => 'required',
            'purpose' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }     
        
        $checkPendingExit = StudentExit::where('student_id', $student->id)->where('status', 'Pending')->first();

        if($checkPendingExit){
            alert()->error('Oops!', 'You have a pending exit application, you cant apply for another unless that is attended to, check with student care services')->persistent('Close');
            return redirect()->back();
        }

        $hodId = $student->department->hod_id;

        $newExitApplication = ([
            'student_id' => $student->id,
            'type' => $request->type,
            'transport_mode' => $request->transport_mode,
            'purpose' => $request->purpose,
            'destination' => $request->destination,
            'exit_date' => !empty($request->exit_date) ? $request->exit_date : null,
            'return_date' => !empty($request->return_date) ? $request->return_date : null,
            'hod_id' => $hodId,
        ]);

        if($newExit = StudentExit::create($newExitApplication)){
            $newExitId = $newExit->id;
            $getLatestExit = StudentExit::find($newExitId);

            $pdf = new Pdf();
            $exitApplication = $pdf->generateExitApplication($academicSession, $student->id, $newExitId);
            $getLatestExit->file = $exitApplication;
            $getLatestExit->save();

            alert()->success('Success', 'Application submitted')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function purchaseBandwidth(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;

        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $plans = Plan::all();

        $transactions = Transaction::where('student_id', $studentId)->where('payment_id', '!=', 0)->orderBy('status', 'ASC')->get();

        $paymentCheck = $this->checkSchoolFees($student);
        $bandwidthPayment = Payment::where("type", "Bandwidth Fee")->where("academic_session", $academicSession)->first();
        
        return view('student.purchaseBandwidth', [
            'plans' => $plans,
            'transactions' => $transactions,
            'payment' => $paymentCheck->schoolPayment,
            'bandwidthPayment' => $bandwidthPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function createBandwidthPayment (Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $redirectLocation = 'student/purchaseBandwidth';

        $validator = Validator::make($request->all(), [
            'payment_id' => 'required',
            'plan_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        
        $bandwidthPayment = Payment::find($request->payment_id);
        $bandwidthPlan = Plan::find($request->plan_id);
        $bandwidthPaymentGateway = env("BANDWIDTH_PURCHASE_GATEWAY");

        if($bandwidthPaymentGateway == "UpperLink"){
            $amount = $this->getUpperlinkAmount($bandwidthPlan->amount);
        }elseif($bandwidthPaymentGateway == "Monnify"){
            $amount = $this->getMonnifyAmount($bandwidthPlan->amount);
        }elseif($bandwidthPaymentGateway == "rave"){
            $amount = $this->getMonnifyAmount($bandwidthPlan->amount);
        }else{
            alert()->error('Oops!', 'Invalid Payment Gateway')->persistent('Close');
            return redirect()->back();
        }

        $reference = $this->generateRandomString(25);

        $paymentClass = new Payment();
        $paymentType = $paymentClass->classifyPaymentType($bandwidthPayment->type);

        if(strtolower($bandwidthPaymentGateway) == 'rave') {
            Log::info("Flutterwave Amount ****************: ". round($this->getRaveAmount($amount)));

            $reference = Flutterwave::generateReference();

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
                    "payment_id" => $bandwidthPayment->id,
                    "payment_gateway" => $bandwidthPaymentGateway,
                    "reference" => $reference,
                    "academic_session" => $student->academic_session,
                    "redirect_path" => $redirectLocation,
                    "payment_Type" => $paymentType,
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

        $newBandwidthTx = ([
            'student_id' => $student->id,
            'payment_id' => $request->payment_id,
            'amount_payed' => $amount,
            'reference' => $reference,
            'session' => $academicSession,
            'payment_method' => $bandwidthPaymentGateway,
            'narration' => "Bandwidth Purchase of ".$bandwidthPlan->title,
            'plan_id' => $bandwidthPlan->id,
            'status' => 0,
        ]);

        if($newTx = Transaction::create($newBandwidthTx)){
            if($bandwidthPaymentGateway == "Monnify"){
                $now = Carbon::now();
                $future = $now->addHours(48);
                $invoiceExpire = $future->format('Y-m-d H:i:s');
    
                $monnifyPaymentdata = array(
                    'amount' => ceil($newTx->amount_payed/100),
                    'invoiceReference' => $newTx->reference,
                    'description' =>  $newTx->narration,
                    'currencyCode' => "NGN",
                    'contractCode' => env('MONNIFY_CONTRACT_CODE'),
                    'customerEmail' => $student->email,
                    'customerName' => $student->applicant->lastname. ' '.$student->applicant->othernames,
                    'expiryDate' => $invoiceExpire,
                    'paymentMethods' => ["ACCOUNT_TRANSFER"],
                    "redirectUrl"=> env("MONNIFY_REDIRECT_URL"),
                );
    
                $monnify = new Monnify();
                $createInvoice = $monnify->initiateInvoice($monnifyPaymentdata);
                $checkoutUrl = $createInvoice->responseBody->checkoutUrl;

                $newTx->checkout_url = $checkoutUrl;
                $newTx->save();

                return redirect($checkoutUrl);
            }
            alert()->success('Success', 'Kindly proceed to payment')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }
    

    private function isValidLinkedInURL($url) {
        $pattern = '/^(https:\/\/)?(www\.)?linkedin\.com\/in\/[a-zA-Z0-9_-]+\/?$/';
        
        if (preg_match($pattern, $url)) {
            return true; 
        } else {
            return false;
        }
    }

    public function reffs(Request $request){
        $student = Auth::guard('student')->user();
        $levelId = $student->level_id;

        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;
        $applicationSession = $student->programmeCategory->academicSessionSetting->application_session;

        $referalCode = $student->referral_code;

        $applicants = [];
        if(!empty($referalCode)){
            $applicants = Applicant::with('student')->where('referrer', $referalCode)->where('academic_session', $applicationSession)->get();
        }

        $paymentCheck = $this->checkSchoolFees($student);

        return view('student.reffs', [
            'applicants' => $applicants,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        ]);
    }

    public function applicantWithSession(Request $request){
        $student = Auth::guard('student')->user();
        $levelId = $student->level_id;

        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $applicants = Applicant::with('programme', 'olevels', 'guardian', 'student')->where('academic_session', $request->session)->get();
        
        $paymentCheck = $this->checkSchoolFees($student);

        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        if(empty($student->image)){
            return view('student.updateImage', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        if(empty($student->bandwidth_username)){
            return view('student.bandwidth', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }
        return view('student.reffs', [
            'applicants' => $applicants,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        ]);
    }

    public function student(Request $request, $slug){

        $student = Auth::guard('student')->user();
        $levelId = $student->level_id;
        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $paymentCheck = $this->checkSchoolFees($student);

        $student = Student::with('applicant', 'applicant.utmes', 'programme')->where('slug', $slug)->first();


        return view('student.student', [
            'refStudent' => $student,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        ]);
    }

    public function applicant(Request $request, $slug){

        $student = Auth::guard('student')->user();
        $levelId = $student->level_id;

        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $paymentCheck = $this->checkSchoolFees($student);

        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('slug', $slug)->first();
        
        return view('student.applicant', [
            'applicant' => $applicant,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        ]);
    }

    public function hostelBooking(Request $request) {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;

        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $paymentCheck = $this->checkSchoolFees($student);

        $hostelPayment = Payment::where("type", "Accomondation Fee")->where("academic_session", $academicSession)->first();
        $hostelPaymentTx = Transaction::where('student_id', $studentId)->where('payment_id', $hostelPayment->id)->where('status', 1)->first();

        $allocatedRoom = Allocation::where('student_id', $studentId)->where('academic_session', $academicSession)->first();

        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        // if(empty($student->image)){
        //     return view('student.updateImage', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        // if(empty($student->bandwidth_username)){
        //     return view('student.bandwidth', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }
        
        return view('student.hostelBooking', [
            'allocatedRoom' => $allocatedRoom,
            'hostelPayment' => $hostelPayment,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
            'studentPendingTransactions' => $paymentCheck->studentPendingTransactions,
            'hostelPaymentTx' => $hostelPaymentTx,

        ]);
    }


    public function antiDrugDeclaration(Request $request){
        $student = Auth::guard('student')->user();
        $levelId = $student->level_id;
        $academicSession = $student->programmeCategory->academicSessionSetting->academic_session;

        $paymentCheck = $this->checkSchoolFees($student);

        if(empty($student->image)){
            return view('student.updateImage', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        return view('student.antiDrugDeclaration', [
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }


    

    public function saveAntiDrugDeclaration(Request $request){
        $validator = Validator::make($request->all(), [
            'anti_drug_status' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();
        $studentId = $student->id;

        $pdf = new Pdf();
        $antiDrug = $pdf->generateAntiDrugDeclaration($studentId);

        if(!empty($antiDrug)){
            $student->anti_drug_status = $antiDrug;
            $student->save();

            return redirect(asset($antiDrug));
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

    
    
}
