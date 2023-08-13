<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Payment;
use App\Models\PaymentStructure as Structure;
use App\Models\Programme;
use App\Models\Transaction;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\AcademicLevel as Level;
use App\Models\Session;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware(['auth:admin']);
    }

    public function payments(Request $request) {
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $payments = Payment::with(['structures', 'programme'])->where('academic_session', $academicSession)->get();
        $programmes = Programme::get();
        $levels = Level::get();
        $sessions = Session::orderBy('id', 'DESC')->get();

        return view('admin.payments', [
            'payments' => $payments,
            'programmes' => $programmes,
            'levels' => $levels,
            'sessions' => $sessions
        ]);
    }

    public function addPayment(Request $request){
        $validator = Validator::make($request->all(), [
            'description' => 'required',
            'title' => 'required',
            'type' => 'required',
            'academic_session' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->title)));

        $addPayment = ([            
            'description' => $request->description,
            'title' => $request->title,
            'programme_id' => $request->programme_id,
            'level_id' => $request->level_id,
            'type' => $request->type,
            'slug' => $slug,
            'academic_session' => $request->academic_session
        ]);

        if(Payment::create($addPayment)){
            alert()->success('Payment added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updatePayment(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$payment = Payment::find($request->payment_id)){
            alert()->error('Oops', 'Invalid Payment')->persistent('Close');
            return redirect()->back();
        }

        
        if(!empty($request->title) &&  $request->title != $payment->title){
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->title)));
            $payment->slug = $slug;
            $payment->title = $request->title;
        }

        if(!empty($request->description) &&  $request->description != $payment->description){
            $payment->description = $request->description;
        }

        if(!empty($request->type) &&  $request->type != $payment->type){
            $payment->type = $request->type;
        }

        if(!empty($request->programme_id) &&  $request->programme_id != $payment->programme_id){
            $payment->programme_id = $request->programme_id;
        }

        if(!empty($request->level_id) &&  $request->level_id != $payment->level_id){
            $payment->level_id = $request->level_id;
        }

        if(!empty($request->academic_session) &&  $request->academic_session != $payment->academic_session){
            $payment->academic_session = $request->academic_session;
        }

        if($payment->save()){
            alert()->success('Changes Saved', 'Payment changes saved successfully')->persistent('Close');
            return redirect()->back();
        }
    }

    public function deletePayment(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|min:1',
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$payment = Payment::find($request->payment_id)){
            alert()->error('Oops', 'Invalid Payment Requirement')->persistent('Close');
            return redirect()->back();
        }

        if($payment->delete()){ 
            alert()->success('Record Deleted', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function payment($slug) {

        $payment = Payment::with(['structures'])->where('slug', $slug)->first();
        $programmes = Programme::get();
        $levels = Level::get();

        return view('admin.payment', [
            'payment' => $payment,
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function getPayment(Request $request) {
        $type = $request->type;
        $session = $request->academic_session;
        $programmeId = $request->programme_id;
        $studentId = $request->student_id;
        $levelId  = $request->level;
        

        $student = Student::find($studentId);
        $paymentCheck = $this->checkSchoolFees($student, $session, $levelId);

        $payment = Payment::with(['structures'])->where([
            'type' => $type,
            'academic_session' => $session,
        ])->first();

        if ($type == Payment::PAYMENT_TYPE_SCHOOL) {
            $payment = Payment::with(['structures'])->where([
                'type' => $type,
                'academic_session' => $session,
                'programme_id' => $programmeId
            ])->first();

            $payment->passTuition = $paymentCheck->passTuitionPayment;
            $payment->fullTuitionPayment = $paymentCheck->fullTuitionPayment;
            $payment->passEightyTuition = $paymentCheck->passEightyTuition;
        }

        return $payment;
    }

    
    public function addStructure(Request $request){
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'title' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $addStructure = ([            
            'payment_id' => $request->payment_id,
            'title' => $request->title,
            'amount' => $request->amount * 100
        ]);

        if(Structure::create($addStructure)){
            alert()->success('Structure added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateStructure(Request $request){
        $validator = Validator::make($request->all(), [
            'structure_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$structure = Structure::find($request->structure_id)){
            alert()->error('Oops', 'Invalid Structure')->persistent('Close');
            return redirect()->back();
        }

        
        if(!empty($request->title) &&  $request->title != $structure->title){
            $structure->title = $request->title;
        }

        if(!empty($request->payment_id) &&  $request->payment_id != $structure->payment_id){
            $structure->payment_id = $request->payment_id;
        }

        if(!empty($request->amount) &&  $request->amount != $structure->amount){
            $structure->amount = $request->amount * 100;
        }

        if($structure->save()){
            alert()->success('Changes Saved', 'Structure changes saved successfully')->persistent('Close');
            return redirect()->back();
        }
    }

    public function deleteStructure(Request $request){
        $validator = Validator::make($request->all(), [
            'structure_id' => 'required|min:1',
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$structure = Structure::find($request->structure_id)){
            alert()->error('Oops', 'Invalid Structure Requirement')->persistent('Close');
            return redirect()->back();
        }

        if($structure->delete()){ 
            alert()->success('Record Deleted', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    /**
     * Show payment page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function transactions()
    {
        $payments = Transaction::get();

        return view('admin.transactions', [
            'payments' => $payments
        ]);
    }

    public function getStudent(Request $request){
        $validator = Validator::make($request->all(), [
            'reg_number' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentIdCode = $request->reg_number;
        return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
    }

    public function chargeStudent(Request $request){
        $student = Student::with('programme', 'applicant')->where('id', $request->student_id)->first();
        $payment = Payment::find($request->payment_id);
        $session = $request->academic_session;

        if($payment->type == 'Application Fee'){
            $validator = Validator::make($request->all(), [
                'amountApplication' => 'required',
            ],[
                'amountApplication.required' => 'The amount field is required.',
            ]);
        }elseif($payment->type == 'School Fee'){
            $validator = Validator::make($request->all(), [
                'amountTuition' => 'required',
            ],[
                'amountTuition.required' => 'The amount field is required.',
            ]);
        }else{
            $validator = Validator::make($request->all(), [
                'amountGeneral' => 'required',
            ],[
                'amountGeneral.required' => 'The amount field is required.',
            ]);
        }

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
        }

        if($payment->type == 'Acceptance Fee'){
            $amount = $request->amountAcceptance;
        }elseif($payment->type == 'School Fee'){
            $amount = $request->amountTuition;
        }else{
            $amount = $request->amountGeneral * 100;
        }


        $transactions = Transaction::where('student_id', $student->id)->get();
        $programme = $student->programme;

        $reference = $this->generateRandomString(10);
        
        //Create new transaction
        $transaction = Transaction::create([
            'student_id' => $request->student_id,
            'payment_id' => $request->payment_id,
            'amount_payed' => $amount,
            'payment_method' => $request->paymentGateway,
            'reference' => $reference,
            'session' => $session,
            'status' => $request->paymentStatus == 1 ? 1 : null
        ]);

        return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
    }
}
