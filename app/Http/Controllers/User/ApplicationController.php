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
use App\Models\Utme;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\BankAccount;
use App\Models\TestApplicant;
use App\Models\ProgrammeCategory;


use App\Mail\ApplicationMail;
use App\Mail\BankDetailsMail;

use App\Libraries\Paygate\Paygate;
use App\Libraries\AdvanceStudy\AdvanceStudy;
use App\Libraries\Monnify\Monnify;



use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

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
        $user = Auth::guard('user')->user();
        $userId = $user->id;
        $applicationSession = $user->programmeCategory->academicSessionSetting->application_session;

        $applicant = Applicant::with('programme', 'olevels', 'guardian')->find($userId);
        $applicantProgrammeCategoryId = $applicant->programme_category_id;

        $programmeCategories = ProgrammeCategory::get();
        $this->programmes = Programme::where('category_id', $applicantProgrammeCategoryId)->get();

        // Payment and Transaction check
        $paymentTypes = [
            Payment::PAYMENT_TYPE_GENERAL_APPLICATION,
            Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION
        ];

        $payments = Payment::with('structures')
            ->where('programme_category_id', $applicantProgrammeCategoryId)
            ->where('academic_session', $applicationSession)
            ->whereIn('type', $paymentTypes)
            ->get()
            ->keyBy('type');

        $transaction = Transaction::where('user_id', $userId)
            ->where('session', $applicationSession)
            ->where('status', 1)
            ->whereIn('payment_id', $payments->pluck('id'))
            ->first();

        if (!$transaction) {
            return view('user.auth.register', [
                'programmes' => $this->programmes,
                'applicant' => $applicant,
                'programmeCategories' => $programmeCategories
            ]);
        }

        // 📊 Calculate application progress based on category
        $percent = $this->calculateApplicationProgress($applicant);

        return view('user.application', [
            'applicant' => $applicant,
            'percent' => $percent,
            'programmes' => $this->programmes,
        ]);
    }

    //applicant
    public function showRegistrationForm(Request $request)
    {
        $advanceStudy = new AdvanceStudy();
        $advanceStudyProgrammes = $advanceStudy->getProgrammes();

        $programmeCategories = ProgrammeCategory::get();


        return view('user.auth.register', [
            'programmes' => $this->programmes,
            'advanceStudyProgrammes' => $advanceStudyProgrammes,
            'programmeCategories'=> $programmeCategories,
        ]);
    }

    public function saveBioData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'dob' => 'required',
            'religion' => 'required',
            'gender' => 'required',
            'marital_status' => 'required',
            'nationality' => 'required',
            'state' => 'required',
            'lga' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('user')->user();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $user->application_number.$user->lastname .' '. $user->othernames)));
        if(!empty($user->slug)){
            $slug = $user->slug;
        }else{
            $user->slug = $slug;
        }
        
        if(!empty($request->dob) && $request->dob != $user->dob){
            $user->dob = $request->dob;
        }

        if(!empty($request->religion) && $request->religion != $user->religion){
            $user->religion = $request->religion;
        }

        if(!empty($request->gender) && $request->gender != $user->gender){
            $user->gender = $request->gender;
        }

        if(!empty($request->marital_status) && $request->marital_status != $user->marital_status){
            $user->marital_status = $request->marital_status;
        }

        if(!empty($request->nationality) && $request->nationality != $user->nationality){
            $user->nationality = $request->nationality;
        }

        if(!empty($request->state) && $request->state != $user->state_of_origin){
            $user->state = $request->state;
        }

        if(!empty($request->address) && $request->address != $user->address){
            $user->address = $request->address;
        }

        if(!empty($request->lga) && $request->lga != $user->lga){
            $user->lga = $request->lga;
        }

        if(!empty($request->image)){
            $imageUrl = 'uploads/applicant/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
            $image = $request->file('image')->move('uploads/applicant', $imageUrl);

            $user->image = $imageUrl;
        }

        session()->put('previous_section', 'bio-data');
        if($user->save()){
            alert()->success('Changes Saved', 'Bio data saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveUtme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jamb_reg_no' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('user')->user();

        if(!empty($request->jamb_reg_no) && $request->jamb_reg_no != $user->jamb_reg_no){
            $user->jamb_reg_no = $request->jamb_reg_no;
        }

        session()->put('previous_section', 'utme');
        if($user->save()){
            alert()->success('Changes Saved', 'Jamb registration number saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveProgramme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('user')->user();

        if(!empty($request->application_type) && $request->application_type != $user->application_type){
            $user->application_type = $request->application_type;
        }

        if(!empty($request->jamb_reg_no) && $request->jamb_reg_no != $user->jamb_reg_no){
            $user->jamb_reg_no = $request->jamb_reg_no;
        }

        if(!empty($request->programme_id) && $request->programme_id != $user->programme_id){
            $user->programme_id = $request->programme_id;
        }

        session()->put('previous_section', 'programme');
        if($user->save()){
            alert()->success('Changes Saved', 'Type saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function guardianBioData(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $user = Auth::guard('user')->user();

        $guardian = Guardian::where('email', $request->email)->first();
        if($guardian && empty($user->guardian_id)){
            $user->guardian_id = $guardian->id;
            $user->save();

            alert()->success('Changes Saved', 'Guardian changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        $guardian = new Guardian;
        if(!empty($request->guardian_id) && !$guardian = Guardian::find($request->guardian_id)){
            alert()->error('Oops', 'Invalid Guardian Information')->persistent('Close');
            return redirect()->back();
        }

        $accessCode = $this->generateAccessCode();
        if(empty($guardian->password)){
            $guardian->password = Hash::make($accessCode);
            $guardian->passcode = $accessCode;
        }

        if(!empty($request->name) &&  $request->name != $guardian->name){
            $guardian->name = ucwords($request->name);
        }

        if(!empty($request->phone_number) &&  $request->phone_number != $guardian->phone_number){
            $guardian->phone_number = $request->phone_number;
        }

        if(!empty($request->email) &&  $request->email != $guardian->email){
            $guardian->email = $request->email;
        }

        if(!empty($request->address) && $request->address != $guardian->address){
            $guardian->address = $request->address;
        }

        session()->put('previous_section', 'guardian');
        if($guardian->save()){
            $gua = Guardian::where('email', $request->email)->first();
            $user->guardian_id = $gua->id;
            $user->save();

            alert()->success('Changes Saved', 'Guardian changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }
  
    public function register(Request $request)
    {
        $userId = $request->user_id;
        $applicationType = !empty($request->applicationTypeDropdown) ? $request->applicationTypeDropdown : $request->input('applicationType');
        $applicantProgrammeCategoryId = $request->programme_category_id;

        $applicantProgrammeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $applicantProgrammeCategoryId)->first();
        $applicationSession = $applicantProgrammeCategory->academicSessionSetting->application_session;

        $programmeCategories = ProgrammeCategory::get();

        $commonConditions = [
            'programme_category_id' => $applicantProgrammeCategoryId,
            'academic_session' => $applicationSession
        ];

        $applicationPayments = Payment::with('structures')
            ->where($commonConditions)
            ->where('type', Payment::PAYMENT_TYPE_GENERAL_APPLICATION)
            ->first();
        
        $interApplicationPayments = Payment::with('structures')
            ->where($commonConditions)
            ->where('type', Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION)
            ->first();

        $payment = $applicationPayments;
        if($applicationType == 'Inter Transfer Application'){
            $payment = $interApplicationPayments;
        }

        $referralCode = $request->referrer;

        $paymentClass = new Payment();
        $paymentType = $paymentClass->classifyPaymentType($payment->type);

        if(!$request->has('user_id')){
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email|max:255|unique:users,email,NULL,id,academic_session,' . $applicationSession,
                'lastname' => 'required',
                'password' => 'required|confirmed',
                'phone_number' => 'required',
                'othernames' => 'required',
                'paymentGateway' => 'required',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'paymentGateway' => 'required',
            ]);
        }

        if(!$request->has('user_id') && $applicant = Applicant::where('email', $request->email)->where('academic_session', $applicationSession)->where('programme_category_id', $applicantProgrammeCategoryId)->first()){
            $transaction = Transaction::where('user_id', $applicant->id)->where('session', $applicationSession)->where('payment_id', $payment->id)->where('status', 1)->first();

            if(!$transaction){
                return view('user.auth.register', [
                    'programmes' => $this->programmes,
                    'applicant' => $applicant,
                    'programmeCategories' => $programmeCategories
                ]);
            }
        }

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $paymentGateway = $request->paymentGateway;
        
        $accessCode = $this->generateAccessCode();
        $amount = $payment->structures->sum('amount');
        $partnerId = null;

        if($request->has('user_id')) {
            //do something
            $applicant = Applicant::where('id', $userId)->first();
            if(!$applicant){
                $message = 'Invalid application';
                alert()->info('Oops!', $message)->persistent('Close');
                return redirect()->back();
            }

            $slug = $applicant->slug;
        }else{
            $partnerId = $this->getPartnerId($referralCode);

            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->lastname .' '. $request->othernames)));
            if($existingApplicant = Applicant::where('slug', $slug)->first()) {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $existingApplicant->lastname .' '. $existingApplicant->othernames.' '. $accessCode)));
            }
        }

        $reference = $this->generateAccessCode();
        if(strtolower($paymentGateway) == 'rave'){
            $reference = Flutterwave::generateReference();
        }

        $metaData = [
            'slug' => $slug,
            'email' => strtolower($request->email),
            'lastname' => ucwords(strtolower($request->lastname)),
            'phone_number' => $request->phone_number,
            'othernames' => ucwords(strtolower($request->othernames)),
            'password' => $request->password,
            'partner_id' => $partnerId,
            'referrer' => $referralCode,
            'application_type' => $applicationType == 'Inter Transfer Application'? $applicationType : null,
            'amount' => $amount,
            'student_id' => null,
            'user_id' => $userId,
            'payment_id' => $payment->id,
            'payment_gateway' => $paymentGateway,
            'reference' => $reference,
            'academic_session' => $applicationSession,
            'redirect_path' => 'applicant/login',
            'payment_Type' => $paymentType,
            'programme_category_id' => $request->programme_category_id,
        ];

        if(strtolower($paymentGateway) == 'paystack') {
            $data = array(
                "amount" => $this->getPaystackAmount($amount),
                "email" => $request->email,
                "currency" => "NGN",
                "metadata" => $metaData,
            );

            return Paystack::getAuthorizationUrl($data)->redirectNow();
        }

        if(strtolower($paymentGateway) == 'rave') {
        
            $data = array(
                "payment_options" => "card,banktransfer",
                "amount" => round($this->getRaveAmount($amount)),
                "tx_ref" => $reference,
                "redirect_url" => env("FLW_REDIRECT_URL"),
                "email" => $request->email,
                "currency" => "NGN",
                "customer" => [
                    "email" => $request->email,
                    "phone_number" => $request->phone_number,
                    "name" => $request->lastname.' '.$request->othernames,
                ],
                "meta" => $metaData,
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

        if(strtolower($paymentGateway) == 'banktransfer'){
            
            $userData = new \stdClass();
            $userData->lastname = $applicant->lastname;
            $userData->othernames = $applicant->othernames;
            $userData->application_id = $applicant->application_number;
            $userData->amount = $this->getPaystackAmount($amount);
            
            //create email to sennd bank details
            if(env('SEND_MAIL')){
                Mail::to($request->email)->send(new BankDetailsMail($userData));
            }
            $message = 'Kindly proceed to your email to complete application';
            alert()->info('Nice Work!', $message)->persistent('Close');
            return redirect()->back();
        }

        if(strtolower($paymentGateway) == 'upperlink') {
            Log::info("Upperlink Amount ****************: ". round($this->getUpperlinkAmount($amount)));

            $testApplicantId = TestApplicant::create([
                'slug' => $slug,
                'email' => strtolower($request->email),
                'lastname' => ucwords(strtolower($request->lastname)),
                'phone_number' => $request->phone_number,
                'othernames' => ucwords(strtolower($request->othernames)),
                'passcode' => $request->password,
                'partner_id' => $partnerId,
                'referrer' => $referralCode,
                'application_type' => $applicationType == 'Inter Transfer Application'? $applicationType : null,
                'academic_session' => $applicationSession,
                'reference' => $reference,
                'programme_category_id' => $request->programme_category_id,
                ])->id;

            $metaData = [
                'test_applicant_id' => $testApplicantId,
                'user_id' => $userId,
                'payment_id' => $payment->id,
                'reference' => $reference,
                "payment_gateway" => $paymentGateway,
                'academic_session' => $applicationSession,
                'redirect_path' => 'applicant/login',
                'payment_Type' => $paymentType,
            ];

            $data = array(
                "amount" => round($this->getUpperlinkAmount($amount)/100),
                "phone" => $request->phone_number,
                "city" => "Lagos",
                "address" => env('SCHOOL_NAME'),
                "email" => $request->email,
                "payGateRef" => $reference,
                "merchantId" => env('UPPERLINK_REF'),
                "countryCode" =>  "NG",
                "currency" => "NGN",
                "logoUrl" => env('SCHOOL_LOGO'),
                "firstName" => ucwords(strtolower($request->othernames)),
                "lastName" => ucwords(strtolower($request->lastname)),
                "redirectUrl" => env("UPPERLINK_REDIRECT_URL"),
                "accountCode" => BankAccount::getBankAccountCode($paymentType)->upperlinkAccountCode,
                "meta" => json_encode($metaData),
            );

            $paygate = new Paygate();
            $paymentData = $paygate->initializeTransaction($data);
            if($paymentData['code'] != "200"){
                Log::info($paymentData);
                $message = 'Payment Gateway not available, try again.';

                alert()->error('Payment Error', 'Unable to initiate upperlink payment')->persistent('Close');
                return redirect()->back();
            }

            $transaction = Transaction::create([
                'payment_id' =>  $payment->id,
                'amount_payed' => $amount,
                'payment_method' => $paymentGateway,
                'reference' => $reference,
                'session' => $applicationSession,
            ]);

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
                'invoiceReference' => $reference,
                'description' =>  $payment->type,
                'currencyCode' => "NGN",
                'contractCode' => env('MONNIFY_CONTRACT_CODE'),
                'customerEmail' =>strtolower($request->email),
                'customerName' => ucwords(strtolower($request->lastname)). ' '.ucwords(strtolower($request->othernames)),
                'expiryDate' => $invoiceExpire,
                'paymentMethods' => ["CARD","ACCOUNT_TRANSFER","USSD","PHONE_NUMBER"],
                'redirectUrl'=> env("MONNIFY_REDIRECT_URL"),
                'metaData' => $metaData,
                'incomeSplitConfig' => [
                    [
                        'subAccountCode' => BankAccount::getBankAccountCode($paymentType)->monnifyAccountCode,
                        'feePercentage' => 100,
                        'splitPercentage' => 100,
                        'feeBearer' => true,
                    ]
                ]
            );

            // dd($monnifyPaymentdata);

            $monnify = new Monnify();
            $createInvoice = $monnify->initiateInvoice($monnifyPaymentdata);

            if (!$createInvoice->requestSuccessful) {
                Log::error('Monnify Invoice Creation Failed', [
                    'response' => $createInvoice,
                    'data_sent' => $monnifyPaymentdata
                ]);

                alert()->error('Payment Error', $createInvoice->responseMessage . ' Unable to initiate Monnify payment')->persistent('Close');
                return redirect()->back();
            }

            $checkoutUrl = $createInvoice->responseBody->checkoutUrl ?? null;
            $paymentGatewayRef = $createInvoice->responseBody->transactionReference ?? null;

            if (!$checkoutUrl || !$paymentGatewayRef) {
                alert()->error('Payment Error', 'Missing checkout details from Monnify')->persistent('Close');
                return redirect()->back();
            }

            $transaction = Transaction::create([
                'payment_id' =>  $payment->id,
                'amount_payed' => $amount,
                'payment_method' => $paymentGateway,
                'reference' => $reference,
                'session' => $applicationSession,
                'payment_gateway_ref' => $paymentGatewayRef
            ]);


            return redirect($checkoutUrl);
        }
    }

    public function programmeById($id) {

        $programme = Programme::where('id', $id)->first();
        return $programme;
    }

    public function facultyById($id) {

        $faculty = Faculty::where('id', $id)->first();
        return $faculty;
    }

    public function departmentById($id) {

        $department = Department::where('id', $id)->first();
        return $department;
    }
    
    public function uploadUtme(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $validator = Validator::make($request->all(), [
            'utme' => 'required',
        ]);
        
        if(!empty($request->utme)){
            if(!empty($user->utme)){
                unlink($user->utme);
            }

            $slug = $user->slug;
            $imageUrl = 'uploads/utme/'.$slug.'.'.$request->file('utme')->getClientOriginalExtension();
            $image = $request->file('utme')->move('uploads/utme', $imageUrl);

            $user->utme = $imageUrl;
        } 

        if($user->save()){
            alert()->success('Good Job', 'UTME Result Uploaded')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

    public function deleteFile(Request $request){
        $user = Auth::guard('user')->user();
        
        $validator = Validator::make($request->all(), [
            'file_type' => 'required',
        ]);

        if($request->file_type =='utme'){
            if(!empty($user->utme)){
                unlink($user->utme);
            }

            $user->utme = null;
        }
        
        if($request->file_type =='de'){
            if(!empty($user->de_result)){
                unlink($user->de_result);
            }

            $user->de_result = null;
        }

        if($request->file_type =='olevel_1'){
            if(!empty($user->olevel_1)){
                unlink($user->olevel_1);
            }

            $user->olevel_1 = null;
        }

        if($request->file_type =='olevel_2'){
            if(!empty($user->olevel_2)){
                unlink($user->olevel_2);
            }

            $user->olevel_2 = null;
        }

        if($user->save()){
            alert()->success('Good Job', 'File Deleted')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveDe(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $validator = Validator::make($request->all(), [
            'de_result' => 'required',
            'de_school_attended' => 'required',
        ]);
        
        if(!empty($request->de_result)){
            if(!empty($user->de_result)){
                unlink($user->de_result);
            }

            $slug = $user->slug;
            $imageUrl = 'uploads/de/'.$slug.'.'.$request->file('de_result')->getClientOriginalExtension();
            $image = $request->file('de_result')->move('uploads/de', $imageUrl);

            $user->de_result = $imageUrl;
        } 

        if(!empty($request->de_school_attended) && $request->de_school_attended != $user->de_school_attended){
            $user->de_school_attended = $request->de_school_attended;
        }

        session()->put('previous_section', 'de');
        if($user->save()){
            alert()->success('Good Job', 'DE Result Uploaded')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

    public function nokBioData(Request $request){

        $user = Auth::guard('user')->user();
        
        $nok = NextOfKin::where('email', $request->email)->first();
        if($nok && empty($user->next_of_kin_id)){
            $user->next_of_kin_id = $nok->id;
            $user->save();

            alert()->success('Changes Saved', 'Next of kin changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        $nextOfKin = new NextOfKin;
        if(!empty($request->nextOfKin_id) && !$nextOfKin = NextOfKin::find($request->nextOfKin_id)){
            alert()->error('Oops', 'Invalid Next of Kin Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->name) &&  $request->name != $nextOfKin->name){
            $nextOfKin->name = ucwords($request->name);
        }

        if(!empty($request->relationship) &&  $request->relationship != $nextOfKin->relationship){
            $nextOfKin->relationship = $request->relationship;
        }

        if(!empty($request->phone_number) &&  $request->phone_number != $nextOfKin->phone_number){
            $nextOfKin->phone_number = $request->phone_number;
        }

        if(!empty($request->email) &&  $request->email != $nextOfKin->email){
            $nextOfKin->email = $request->email;
        }

        if(!empty($request->address) && $request->address != $nextOfKin->address){
            $nextOfKin->address = $request->address;
        }

        session()->put('previous_section', 'nok');
        if($nextOfKin->save()){
            $nok = NextOfKin::where('email', $request->email)->first();
            $user->next_of_kin_id = $nok->id;
            $user->save();
            
            alert()->success('Changes Saved', 'Next of Kin changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveSitting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sitting_no' => 'required',
            'schools_attended' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('user')->user();

        if(!empty($request->sitting_no) && $request->sitting_no != $user->sitting_no){
            $user->sitting_no = $request->sitting_no;
        }

        if(!empty($request->schools_attended) && $request->schools_attended != $user->schools_attended){
            $user->schools_attended = $request->schools_attended;
        }

        session()->put('previous_section', 'olevel');
        if($user->save()){
            alert()->success('Changes Saved', 'Number of sittings saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function addOlevel(Request $request)
    {
        $userId = Auth::guard('user')->user()->id;
        $sittingNo = Auth::guard('user')->user()->sitting_no;
        $subjects = $request->input('subjects');

        foreach ($subjects as $subject) {
            if (!empty($subject['subject'])) {
                $validator = Validator::make($subject, [
                    'subject' => 'nullable|string|max:255',
                    'grade' => 'required|string',
                    'year' => 'required|integer|min:1998|max:2099',
                    'reg_no' => 'required|string|max:255',
                ]);
        
                if ($validator->fails()) {
                    alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                    return redirect()->back();
                }
        
                if (Olevel::where('user_id', $userId)->count() > 8) {
                    alert()->error('oops', 'Nine subjects already added, you can\'t add more')->persistent('Close');
                    return redirect()->back();
                }
        
                // Check registration numbers
                $regNos = Olevel::where('user_id', $userId)->pluck('reg_no')->toArray();
                $regNos[] = $subject['reg_no']; // Corrected this line
        
                $uniqueRegNos = array_unique($regNos);
                $uniqueRegNosCount = count($uniqueRegNos);
        
                if ($uniqueRegNosCount > $sittingNo) {
                    alert()->error('oops', 'You specified ' . $sittingNo . ' sittings but we are getting more than specified')->persistent('Close');
                    return redirect()->back();
                }
        
                // Check years
                $years = Olevel::where('user_id', $userId)->pluck('year')->toArray();
                $years[] = $subject['year']; // Corrected this line
        
                $uniqueYears = array_unique($years);
                $uniqueYearsCount = count($uniqueYears);
        
                if ($uniqueYearsCount > $sittingNo) {
                    alert()->error('oops', 'You specified ' . $sittingNo . ' sittings but we are getting more than specified')->persistent('Close');
                    return redirect()->back();
                }
        
                if (Olevel::where('user_id', $userId)->where('subject', $subject['subject'])->count() > 0) { 
                    continue;
                }
        
                Olevel::create([
                    'subject' => $subject['subject'],
                    'grade' => $subject['grade'],
                    'year' => $subject['year'],
                    'reg_no' => $subject['reg_no'],
                    'user_id' => $userId,
                ]);
            }
        }
        
        session()->put('previous_section', 'olevel');
        alert()->success('Good Job!', 'Subjects and grades added successfully')->persistent('Close');
        return redirect()->back();
    }

    public function updateOlevel(Request $request){
        $validator = Validator::make($request->all(), [
            'olevel_id' => 'required|min:1',
        ]);

        if($validator->fails()) {

            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$olevel = Olevel::find($request->olevel_id)){
            alert()->error('Oops', 'Invalid OLevel Information')->persistent('Close');
            return redirect()->back();
        }

        $olevel->year = $request->year;
        $olevel->subject = $request->subject;
        $olevel->reg_no = $request->reg_no;
        $olevel->grade = $request->grade;

        session()->put('previous_section', 'olevel');
        if($olevel->save()){
            alert()->success('Record Updated', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    /**
     * Delete Olevel
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deleteOlevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'olevel_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }


        if(!$olevel = Olevel::find($request->olevel_id)){
            alert()->error('Oops', 'Invalid OLevel Information')->persistent('Close');
            return redirect()->back();
        }

        session()->put('previous_section', 'olevel');
        if($olevel->delete()){
            alert()->success('Record Deleted', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function addUtme(Request $request)
    {
        $userId = Auth::guard('user')->user()->id;        
        $subjects = $request->input('subjects');

        foreach ($subjects as $subject) {
            if (!empty($subject['subject'])) {
                $validator = Validator::make($subject, [
                    'subject' => 'required|string|max:255',
                    'score' => 'required|integer|min:0|max:100',
                ]);
        
                if($validator->fails()) {
                    alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                    return redirect()->back();
                }

                Utme::create([
                    'subject' => $subject['subject'],
                    'score' => $subject['score'],
                    'user_id' => $userId,
                ]);
            }
        }

        session()->put('previous_section', 'utme');

        alert()->success('Changes Saved', 'Subject saved successfully')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateUtme(Request $request){
        $validator = Validator::make($request->all(), [
            'utme_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }


        if(!$utme = Utme::find($request->utme_id)){
            alert()->error('Oops', 'Invalid UTME Information')->persistent('Close');
            return redirect()->back();
        }

        $utme->subject = $request->subject;
        $utme->score = $request->score;

        session()->put('previous_section', 'utme');
        if($utme->save()){
            alert()->success('Record updated', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    /**
     * Delete UTME
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deleteUtme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'utme_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }


        if(!$utme = Utme::find($request->utme_id)){
            alert()->error('Oops', 'Invalid UTME Information')->persistent('Close');
            return redirect()->back();
        }

        if($utme->delete()){
            alert()->success('Record Deleted', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function submitApplication(Request $request)
    {
        $user = Auth::guard('user')->user();

        $user->status = APPLICANT::SUBMITTED;

        if($user->save()){
            alert()->success('Good Job', 'Application Submitted')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function uploadOlevel(Request $request)
    {
        $user = Auth::guard('user')->user();
        
        $validator = Validator::make($request->all(), [
            'olevel_1' => 'required',
        ]);

        if($user->sitting_no > 1){
            $validator = Validator::make($request->all(), [
                'olevel_1' => 'required',
                'olevel_2' => 'required',
            ]);
        }

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        
        if(!empty($request->olevel_1)){
            if(!empty($user->olevel_1)){
                unlink($user->olevel_1);
            }

            $slug = $user->slug .'-1';
            $imageUrl = 'uploads/olevel/'.$slug.'.'.$request->file('olevel_1')->getClientOriginalExtension();
            $image = $request->file('olevel_1')->move('uploads/olevel', $imageUrl);

            $user->olevel_1 = $imageUrl;
        } 

        if(!empty($request->olevel_2)){
            if(!empty($user->olevel_2)){
                unlink($user->olevel_2);
            }

            $slug = $user->slug .'-2';
            $imageUrl2 = 'uploads/olevel/'.$slug.'.'.$request->file('olevel_2')->getClientOriginalExtension();
            $image = $request->file('olevel_2')->move('uploads/olevel', $imageUrl2);

            $user->olevel_2 = $imageUrl2;
        } 

        session()->put('previous_section', 'olevel');
        if($user->save()){
            alert()->success('Good Job', 'Olevel Result Uploaded')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

    public function uploadSpgsDocuments(Request $request)
    {
        $user = Auth::guard('user')->user();

        // Base validation rules
        $rules = [
            'olevel_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'degree_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png',
            'academic_transcript' => 'nullable|file|mimes:pdf,jpg,jpeg,png',
            'nysc_certificate'   => 'required|file|mimes:pdf,jpg,jpeg,png',
        ];

        // Doctorate-specific fields
        if ($user->programme_category_id == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::DOCTORATE)) {
            $rules['masters_certificate'] = 'required|file|mimes:pdf,jpg,jpeg,png';
            $rules['research_proposal']  = 'required|file|mimes:pdf,doc,docx';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        // Helper function for upload
        $uploadFile = function($fieldName, $slugSuffix) use ($request, $user) {
            if ($request->hasFile($fieldName)) {
                // Delete old file if exists
                if (!empty($user->{$fieldName}) && file_exists(public_path($user->{$fieldName}))) {
                    unlink(public_path($user->{$fieldName}));
                }

                $slug = $user->slug . '-' . $slugSuffix;
                $extension = $request->file($fieldName)->getClientOriginalExtension();
                $filePath = 'uploads/spgs/' . $slug . '.' . $extension;

                $request->file($fieldName)->move(public_path('uploads/spgs'), $slug . '.' . $extension);
                $user->{$fieldName} = $filePath;
            }
        };

        // Perform uploads
        $uploadFile('olevel_certificate', 'olevel_certificate');
        $uploadFile('degree_certificate', 'degree_certificate');
        $uploadFile('academic_transcript', 'academic_transcript');
        $uploadFile('nysc_certificate', 'nysc_certificate');

        if ($user->programme_category_id == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::DOCTORATE)) {
            $uploadFile('masters_certificate', 'masters_certificate');
            $uploadFile('research_proposal', 'research_proposal');
        }

        session()->put('previous_section', 'spgsDocs');

        if ($user->save()) {
            alert()->success('Good Job', 'Documents uploaded successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveSpgsExtraDetails(Request $request)
    {
        $user = Auth::guard('user')->user();

        $validator = Validator::make($request->all(), [
            'field_of_interest' => 'nullable|string|max:255',
            'previous_institutions' => 'nullable|string',
            'work_experience' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        $user->field_of_interest = $request->input('field_of_interest');
        $user->previous_institutions = $request->input('previous_institutions');
        $user->work_experience = $request->input('work_experience');

        if ($user->save()) {
            alert()->success('Success', 'Details saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    private function calculateApplicationProgress($applicant)
    {
        $programmeCategoryId = $applicant->programme_category_id;
        $spgsCategories = [
            ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::PGD),
            ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::MASTER),
            ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::DOCTORATE),
        ];

        $programmeCategoryType = in_array($programmeCategoryId, $spgsCategories) ? 'spgs' : 'undergraduate';

        if ($programmeCategoryType === 'spgs') {
            return $this->calculateSpgsProgress($applicant);
        }

        return $this->calculateUndergraduateProgress($applicant);
    }

    private function calculateSpgsProgress($applicant)
    {
        // Required form fields
        $formFields = ['lastname', 'programme', 'field_of_interest', 'previous_institutions', 'work_experience'];

        // Check if next of kin is required and add it conditionally
        if (filled($applicant->nok)) {
            $formFields[] = 'nok'; // We'll count it only if it exists
        }

        // Document requirements by programme category
        $docRequirements = [
            ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::PGD) => [
                'olevel_certificate', 'degree_certificate', 'nysc_certificate'
            ],

             ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::MASTER) => [
                'olevel_certificate', 'degree_certificate', 'nysc_certificate'
            ],

            ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::DOCTORATE) => [
                'olevel_certificate', 'degree_certificate', 'nysc_certificate', 'masters_certificate', 'research_proposal'
            ]
        ];

        $docs = $docRequirements[$applicant->programme_category_id] ?? [];

        // Combine form fields and required documents
        $allFields = array_merge($formFields, $docs);
        $totalFields = count($allFields);

        // Count how many of them are filled
        $filledCount = collect($allFields)->filter(fn($field) => filled($applicant->$field))->count();

        // Return percentage progress
        return $totalFields > 0 ? round(($filledCount / $totalFields) * 100) : 0;
    }

    private function calculateUndergraduateProgress($applicant)
    {
        $score = 0;
        $total = 4;

        if (filled($applicant->lastname)) $score++;
        if (filled($applicant->programme)) $score++;
        if (filled($applicant->guardian)) $score++;
        if (count($applicant->olevels) > 4 && $applicant->sitting_no != 0) $score++;
        // if (filled($applicant->olevel_1)) $score++;

        if ($applicant->application_type === 'UTME') {
            if ($applicant->utmes->count() > 3) $score++;
            if (filled($applicant->utme)) $score++;
            $total += 2;
        } elseif (!empty($applicant->application_type)) {
            if (filled($applicant->de_result)) $score++;
            $total += 1;
        }

        if (filled($applicant->nok)) {
            $score++;
            $total += 1;
        }

        return round(($score / $total) * 100);
    }

}
