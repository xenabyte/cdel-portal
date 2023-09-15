<?php

namespace App\Http\Controllers\Staff;

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
use App\Models\Faculty;
use App\Models\Department;


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
        $this->middleware(['auth:staff']);
    }

    public function payments(Request $request) {
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $payments = Payment::with(['structures', 'programme'])->where('academic_session', $academicSession)->get();
        $programmes = Programme::get();
        $levels = Level::get();
        $sessions = Session::orderBy('id', 'DESC')->get();

        return view('staff.payments', [
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

        if(strtolower($request->type) == 'school fee' && empty($request->programme_id)){
            alert()->error('Oops!', 'programme is required for school fees')->persistent('Close');
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
        $sessions = Session::orderBy('id', 'DESC')->get();

        return view('staff.payment', [
            'payment' => $payment,
            'programmes' => $programmes,
            'levels' => $levels,
            'sessions' => $sessions
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
        $transactions = Transaction::get();

        return view('staff.transactions', [
            'transactions' => $transactions
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
        return $this->getSingleStudent($studentIdCode, 'staff.chargeStudent');
    }

    public function chargeStudent(Request $request){
        if(!empty($request->student_id)){
            $student = Student::with('programme', 'applicant')->where('id', $request->student_id)->first();
            $studentId = $student->id;
        }

        if(!empty($request->user_id)){
            $applicant = Applicant::with('programme')->where('id', $request->user_id)->first();
            $applicantId = $applicant->id;
        }

        $payment = Payment::find($request->payment_id);
        $session = $request->academic_session;


        if($payment->type == Payment::PAYMENT_TYPE_GENERAl_APPLICATION || $payment->type == Payment::PAYMENT_TYPE_ACCEPTANCE || $payment->type == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
            $validator = Validator::make($request->all(), [
                'amountAcceptance' => 'required',
            ],[
                'amountAcceptance.required' => 'The amount field is required.',
            ]);
        }elseif($payment->type == Payment::PAYMENT_TYPE_SCHOOL){
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

        if(!empty($request->student_id)){
            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return $this->getSingleStudent($student->matric_number, 'staff.chargeStudent');
            }
        }

        if(!empty($request->user_id)){
            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return $this->getSingleApplicant($applicant->application_number, 'staff.chargeStudent');
            }
        }

        if($payment->type == Payment::PAYMENT_TYPE_ACCEPTANCE){
            $amount = $request->amountAcceptance;
        }elseif($payment->type == Payment::PAYMENT_TYPE_GENERAl_APPLICATION){
            $amount = $request->amountAcceptance;
        }elseif($payment->type == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
            $amount = $request->amountAcceptance;
        }elseif($payment->type == Payment::PAYMENT_TYPE_SCHOOL){
            $amount = $request->amountTuition;
        }else{
            $amount = $request->amountGeneral * 100;
        }

        if($payment->type != Payment::PAYMENT_TYPE_GENERAL && !empty($request->student_id)){
            $totalPayment = $payment->structures-sum('amount');
            $paymentTransactions = Transaction::where('student_id', $studentId)
            ->where('payment_id', $payment->id)
            ->where('session', $session)
            ->where('status', 1)
            ->get();

            $totatAmountPaid = $paymentTransactions->sum('amount_payed');
            if(($amount+$totatAmountPaid) > $totalPayment){
                alert()->error('Oops!!!', 'Amount to be paid seems to be an overpayment, student is not charged')->persistent('Close');
                if(!empty($request->student_id)){
                    if($validator->fails()) {
                        alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                        return $this->getSingleStudent($student->matric_number, 'staff.chargeStudent');
                    }
                }
            }
        }

        $reference = $this->generateRandomString(10);
        
        //Create new transaction
        $transaction = Transaction::create([
            'student_id' => $request->student_id,
            'user_id' => $request->user_id,
            'payment_id' => $request->payment_id,
            'amount_payed' => $amount,
            'payment_method' => $request->paymentGateway,
            'reference' => $reference,
            'session' => $session,
            'status' => $request->paymentStatus == 1 ? 1 : null
        ]);

        if(!empty($request->student_id)){
            $student = Student::find($request->student_id);

            if(!empty($student) && $payment->type == Payment::PAYMENT_TYPE_SCHOOL && $request->paymentStatus == 1){
                $this->generateMatricAndEmail($student);
            }
            alert()->success('Good job', 'Student Charged')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'staff.chargeStudent');
        }

        if(!empty($request->user_id)){
            alert()->success('Good Job', 'Applicant Charged')->persistent('Close');
            return $this->getSingleApplicant($applicant->application_number, 'staff.chargeStudent');
        }
    }

    /**
     * Show payment page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function transactionReport()
    {
        $transactions = Transaction::get();
        $faculties = Faculty::get();
        $academicLevels = Level::get();
        $academicSessions = Session::orderBy('id', 'DESC')->get();

        return view('staff.transactionReport', [
            'faculties' => $faculties,
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions
        ]);
    }

    public function generateReport (Request $request){

        $validator = Validator::make($request->all(), [
            'session' => 'required',
            'level_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $facultyId = $request->faculty_id;
        $departmentId = $request->department_id;
        $programmeId = $request->programme_id;
        $academicSession = $request->session;
        $levelId = $request->level_id;
        $paymentType = Payment::PAYMENT_TYPE_SCHOOL;

        $payments = Payment::where('type', $paymentType)->where('level_id', $levelId)->where('academic_session', $academicSession);
        $paymentIds = $payments->pluck('id')->toArray();
        $programmeIds = [];

        $faculty = null;
        $department = null;
        $programme = null;

        $students = Student::with('applicant', 'academicLevel', 'transactions')
        ->whereHas('transactions', function ($query) use ($paymentIds) {
            $query->whereIn('payment_id', $paymentIds);
        })
        ->get();

        if(!empty($facultyId)){
            $faculty = Faculty::find($facultyId);

            $programmeIds = $faculty->departments->flatMap->programmes->pluck('id')->toArray();
            $paymentIds = $payments->whereIn('programme_id', $programmeIds)->pluck('id')->toArray();
            $students = Student::with('applicant', 'academicLevel', 'transactions')
            ->whereHas('transactions', function ($query) use ($paymentIds) {
                $query->whereIn('payment_id', $paymentIds);
            })
            ->get();
        }

        if(!empty($facultyId) && !empty($departmentId)){
            $faculty = Faculty::find($facultyId);
            $department = Department::find($departmentId);

            $programmeIds = $department->programmes->pluck('id')->toArray();
            $paymentIds = $payments->whereIn('programme_id', $programmeIds)->pluck('id')->toArray();
            $students = Student::with('applicant', 'academicLevel', 'transactions')
            ->whereHas('transactions', function ($query) use ($paymentIds) {
                $query->whereIn('payment_id', $paymentIds);
            })
            ->get();

        }

        if(!empty($facultyId) && !empty($departmentId) && !empty($programmeId)){
            $faculty = Faculty::find($facultyId);
            $department = Department::find($departmentId);
            $programme = Programme::find($programmeId);

            $programmeIds = Programme::where('id', $programmeId)->pluck('id')->toArray();
            $paymentIds = $payments->whereIn('programme_id', $programmeIds)->pluck('id')->toArray();
            $students = Student::with('applicant', 'academicLevel', 'transactions')
            ->whereHas('transactions', function ($query) use ($paymentIds) {
                $query->whereIn('payment_id', $paymentIds);
            })
            ->get();
        }
        
        foreach ($students as $student) {
            $student->schoolFeeDetails = $this->checkSchoolFees($student, $academicSession, $levelId);
        }

        $transactions = Transaction::get();
        $faculties = Faculty::get();
        $academicLevels = Level::get();
        $academicSessions = Session::orderBy('id', 'DESC')->get();

        $academicLevel = Level::find($levelId);

        return view('staff.transactionReport', [
            'faculties' => $faculties,
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'students' =>    $students,
            'academicLevel' => $academicLevel,
            'academicSession' => $academicSession,
            'faculty' => $faculty,
            'department' => $department,
            'programme' => $programme
        ]);
    }
}
