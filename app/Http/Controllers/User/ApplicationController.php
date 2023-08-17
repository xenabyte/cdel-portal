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


use App\Mail\ApplicationMail;
use App\Mail\BankDetailsMail;

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

        $applicationPayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_APPLICATION)->first();
        $paymentId = $applicationPayment->id;
        $transaction = Transaction::where('user_id', $applicant->id)->where('session', $applicationSession)->where('payment_id', $paymentId)->where('status', 1)->first();

        if(!$transaction){
            return view('user.auth.register', [
                'programmes' => $this->programmes,
                'applicant' => $applicant,
                'payment' => $applicationPayment
            ]);
        }

        // if(strtolower($applicant->status) == 'admitted'){
        //     alert()->success('Congratulation', 'You have been admitted, proceed to student portal, check our mail for more information')->persistent('Close');
        //     return view('student.auth.login');
        // }

        $percent = 1;
        $total = 6;
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
        if(!empty($applicant->application_type) && $applicant->application_type == 'UTME'){
            if(count($applicant->utmes) > 3){
                $percent = $percent + 1;
            }
            if(!empty($applicant->utme)){
                $percent = $percent + 1;
            }
            $total = $total + 2;
        }elseif(!empty($applicant->application_type) && $applicant->application_type == 'DE'){
            if(!empty($applicant->de_result)){
                $percent = $percent + 1;
            }
            $total = $total + 1;
        }

        if(!empty($applicant->nok)){
            $percent = $percent + 1;
            $total = $total + 1;
        }

        $percent = round(($percent/$total)*100);

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

    public function saveBioData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dob' => 'required',
            'religion' => 'required',
            'gender' => 'required',
            'marital_status' => 'required',
            'nationality' => 'required',
            'state' => 'required',
            'lga' => 'required',
            'image' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('user')->user();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $user->lastname .' '. $user->othernames)));
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
            'application_type' => 'required',
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
            $guardian->name = $request->name;
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

        if($gua = $guardian->save()){
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
                'referrer' => $request->referrer,
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
                    'academic_session' => $applicationSession
                ),
            );

            return Paystack::getAuthorizationUrl($data)->redirectNow();
        }

        if(strtolower($paymentGateway) == 'banktransfer'){
            
            $userData = new \stdClass();
            $userData->lastname = $applicant->lastname;
            $userData->othernames = $applicant->othernames;
            $userData->application_id = $applicant->application_number;
            $userData->amount = $this->getPaystackAmount($amount);
            
            //create email to sennd bank details
            Mail::to($request->email)->send(new BankDetailsMail($userData));

            $message = 'Kindly proceed to your email to complete application';
            alert()->info('Nice Work!', $message)->persistent('Close');
            return redirect()->back();
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
            $nextOfKin->name = $request->name;
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

        if($nok = $nextOfKin->save()){
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
        
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'year' => 'required',
            'reg_no' => 'required',
            'grade' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(Olevel::where('user_id', $userId)->count() > 8){
            alert()->error('oops', 'Nine subject already added, you cant add more')->persistent('Close');
            return redirect()->back();
        }

        
        $sittingNo = Auth::guard('user')->user()->sitting_no;
        // checks
        $regNos = Olevel::where('user_id', $userId)->pluck('reg_no')->toArray();
        $regNos[] = $request->reg_no;

        $uniqueRegNos = array_unique($regNos);  
        $uniqueRegNosCount = count($uniqueRegNos);

        if($uniqueRegNosCount > $sittingNo){
            alert()->error('oops', 'You specified '.$sittingNo.' sittings but we are getting more than specified')->persistent('Close');
            return redirect()->back();
        }

        $years = Olevel::where('user_id', $userId)->pluck('year')->toArray();
        $years[] = $request->year;

        $uniqueYears= array_unique($years);  
        $uniqueYearsCount = count($uniqueYears);

        if($uniqueYearsCount > $sittingNo){
            alert()->error('oops', 'You specified '.$sittingNo.' sittings but we are getting more than specified')->persistent('Close');
            return redirect()->back();
        }

        if(Olevel::where('user_id', $userId)->where('subject', $request->subject)->count() > 0){
            alert()->error('oops', 'Subject already added')->persistent('Close');
            return redirect()->back();
        }


        $newOlevel = ([
            'subject' => $request->subject,
            'year' => $request->year,
            'user_id' => $userId,
            'grade' => $request->grade,
            'reg_no' => $request->reg_no,
        ]);

        if(Olevel::create($newOlevel)){
            alert()->success('Changes Saved', 'Subject saved successfully')->persistent('Close');
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

        if($olevel->delete()){
            alert()->success('Record Deleted', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function addUtme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'score' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $userId = Auth::guard('user')->user()->id;        

        if(Utme::where('user_id', $userId)->count() > 3){
            alert()->error('oops', 'Four subject already added, you cant add more')->persistent('Close');
            return redirect()->back();
        }

        if(Utme::where('user_id', $userId)->where('subject', $request->subject)->count() > 0){
            alert()->error('oops', 'Subject already added')->persistent('Close');
            return redirect()->back();
        }

        $newUtme = ([
            'subject' => $request->subject,
            'user_id' => $userId,
            'score' => $request->score,
        ]);

        if(Utme::create($newUtme)){
            alert()->success('Changes Saved', 'Subject saved successfully')->persistent('Close');
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

        if($user->save()){
            alert()->success('Good Job', 'Olevel Result Uploaded')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

}
