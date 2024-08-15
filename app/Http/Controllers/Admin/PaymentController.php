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
use League\Csv\Reader;

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
use App\Models\AcademicLevel;
use App\Models\Notification;
use App\Models\BankAccount;

use App\Libraries\Pdf\Pdf;
use App\Mail\TransactionMail;


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

    public function billsForSessions(Request $request) {
        $globalData = $request->input('global_data');
        $academicSession = $request->academic_session;


        $payments = Payment::with(['structures', 'programme'])->where('academic_session', $academicSession)->get();
        $programmes = Programme::get();
        $levels = Level::get();
        $sessions = Session::orderBy('id', 'DESC')->get();

        return view('admin.otherSessionPayments', [
            'payments' => $payments,
            'programmes' => $programmes,
            'levels' => $levels,
            'sessions' => $sessions,
            'academicSession' => $academicSession
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

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->title.'-'.($request->level_id*100).'Level-'.$request->academic_session)));

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
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->title.'-'.($payment->level_id*100).'Level-'.$payment->academic_session)));
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
        $students = null;
        if(!empty($payment->level_id)){
            $students = Student::with('applicant')->where('level_id', $payment->level_id)->where('academic_session', $payment->academic_session)->where('programme_id', $payment->programme_id)->get();

            foreach($students as $student){
                $transaction = Transaction::where('student_id', $student->id)->where('payment_id', $payment->id)->where('session', $payment->academic_session)->where('status', 1)->get();
                if($transaction){
                    $student->paymentTransaction = $transaction;
                }
            }
        }

        return view('admin.payment', [
            'payment' => $payment,
            'programmes' => $programmes,
            'levels' => $levels,
            'sessions' => $sessions,
            'students' => $students
        ]);
    }

    public function chargeStudents(Request $request){
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $payment = Payment::with(['structures'])->where('id', $request->payment_id)->first();
        if(!$payment){
            alert()->error('Oops', 'Invalid Payment')->persistent('Close');
            return redirect()->back();
        }

        $totalPayment = $payment->structures->sum('amount');

        $students = Student::where('level_id', $payment->level_id)->where('academic_session', $payment->academic_session)->where('programme_id', $payment->programme_id)->get();

        foreach($students as $student){
            //Create new transaction
            $reference = $this->generateRandomString(10);
            $transaction = Transaction::create([
                'student_id' => $student->id,
                'payment_id' => $payment->id,
                'amount_payed' => $totalPayment,
                'session' => $payment->academic_session,
                'reference' => $reference,
            ]);

            $message = 'Dear '.$student->applicant->lastname.' '.$student->applicant->othername.', you have been charged ₦'.number_format($totalPayment/100, 2).' for '.$payment->title .', kindly proceed to make payment';

            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'status' => 0
            ]);
        }

        $payment->is_charged = 1;
        $payment->save();

        alert()->success('Good job!!', 'Student charged successfully')->persistent('Close');
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
            
            if ($type == Payment::PAYMENT_TYPE_SCHOOL || $type == Payment::PAYMENT_TYPE_SCHOOL_DE) {
                $paymentCheck = $this->checkSchoolFees($student, $session, $levelId);
                if($paymentCheck->status != 'success'){
                    return response()->json(['status' => 'School fee not setup for the section and programme']);
                }

                $payment = Payment::with(['structures'])->where([
                    'type' => $type,
                    'programme_id' => $programmeId,
                    'level_id' => $levelId,
                    'academic_session' => $session,
                ])->first();

                if(!$payment){
                    return response()->json(['status' =>  'Payment type: '.$type.' doesnt exist']);
                }

                $payment->passTuition = $paymentCheck->passTuitionPayment;
                $payment->fullTuitionPayment = $paymentCheck->fullTuitionPayment;
                $payment->passEightyTuition = $paymentCheck->passEightyTuition;

            }elseif($type == Payment::PAYMENT_TYPE_OTHER){
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

    public function uploadBulkPayment(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                'file' => 'required',
                'academic_session' => 'required',
            ]);
    
            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return redirect()->back();
            }
    
            if ($request->hasFile('file')) {
                $file = $request->file('file');
    
                // Create a CSV reader instance
                $csv = Reader::createFromPath($file->getPathname());
    
                // Set the header offset (skip the first row)
                $csv->setHeaderOffset(0);
    
                // Get all records from the CSV file
                $records = $csv->getRecords();
    
                foreach ($records as $row) {
                    $level = !empty($row['level'])?$row['level']:null;
                    $academicSession = $request->academic_session;
                    $title = $row['payment_name'];
                    $amount = !empty($row['payment_amount']) ? $row['payment_amount'] :null;
                    $type = !empty($row['payment_type'])?$row['payment_type']: null;
                    $programmeId = !empty($row['programme'])?$row['programme']: null;

                    if(!empty($title) && !empty($type)){
                        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title.'-'.($level*100).'Level-'.$academicSession)));
                        $paymentData = ([
                            'title' => $title,
                            'description' => $title,
                            'programme_id' => $programmeId,
                            'level_id' => $level,
                            'type' => $type,
                            'academic_session' => $academicSession,
                            'slug' => $slug
                        ]);

                        //check existing
                        $existingPayment = Payment::where([
                            'programme_id' => $programmeId,
                            'level_id' => $level,
                            'type' => $type,
                            'academic_session' => $academicSession,
                        ])->first();

                        if(empty($existingPayment)){
                            $newPayment = Payment::create($paymentData);

                            if(!empty($amount) && !empty($newPayment)){
                                $addStructure = ([            
                                    'payment_id' => $newPayment->id,
                                    'title' => $title,
                                    'amount' => $amount * 100
                                ]);
                        
                                Structure::create($addStructure); 
                            }
                        }
                    }
                }
    
                alert()->success('Changes Saved', 'Payments uploaded successfully')->persistent('Close');
                return redirect()->back();
            }
        } catch (QueryException $e) {
            $errorMessage = 'Something went wrong';
            alert()->error('Oops!', $errorMessage)->persistent('Close');
            return redirect()->back();
        }
    }

    /**
     * Show payment page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function transactions()
    {
        $transactions = Transaction::get();

        return view('admin.transactions', [
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
        return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
    }

    public function chargeStudent(Request $request){
        $studentId = null;
        $applicantId = null;
        if(!empty($request->student_id)){
            $student = Student::with('programme', 'applicant')->where('id', $request->student_id)->first();
            $studentId = $student->id;
            $applicantId = $student->applicant->id;
        }

        if(!empty($request->user_id)){
            $applicant = Applicant::with('programme', 'student')->where('id', $request->user_id)->first();
            $applicantId = $applicant->id;
            if(!empty($applicant->student)){
                $studentId = $applicant->student->id;
                $student = Student::with('programme', 'applicant')->where('id', $studentId)->first();
            }
        }

        $levelId = $request->level;
        $session = $request->academic_session;
        $programmeId = $request->programme_id;
        $type = $request->type;
        $paymentId = $request->payment_id;

        $payment = Payment::with(['structures'])->find($paymentId);

        if(!$payment){
            alert()->error('Oops', 'Bill not found')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
        }


        if(($type == Payment::PAYMENT_TYPE_SCHOOL || $type == Payment::PAYMENT_TYPE_SCHOOL_DE) && empty($studentId)){
            alert()->error('Oops', 'The applicant does not have admission yet.')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
        }


        if($type == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $type == Payment::PAYMENT_TYPE_ACCEPTANCE || $type == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
            $validator = Validator::make($request->all(), [
                'amountAcceptance' => 'required',
            ],[
                'amountAcceptance.required' => 'The amount field is required.',
            ]);
        }elseif($type == Payment::PAYMENT_TYPE_SCHOOL || $type == Payment::PAYMENT_TYPE_SCHOOL_DE || $type == Payment::PAYMENT_TYPE_ACCOMONDATION){
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
                return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
            }
        }

        if(!empty($request->user_id)){
            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return $this->getSingleApplicant($applicant->application_number, 'admin.chargeStudent');
            }
        }

        if($payment->type == Payment::PAYMENT_TYPE_ACCEPTANCE){
            $amount = $request->amountAcceptance;
        }elseif($payment->type == Payment::PAYMENT_TYPE_GENERAL_APPLICATION){
            $amount = $request->amountAcceptance;
        }elseif($payment->type == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
            $amount = $request->amountAcceptance;
        }elseif($payment->type == Payment::PAYMENT_TYPE_SCHOOL || $payment->type == Payment::PAYMENT_TYPE_SCHOOL_DE){
            $amount = env('PAYMENT_TYPE')=='Percentage'?$request->amountTuition:$request->amountTuition*100;
        }elseif($payment->type == Payment::PAYMENT_TYPE_ACCOMONDATION) {
            $amount = $request->amountTuition*100;
        }else{
            $amount = $request->amountGeneral * 100;
        }

        if($payment->type != Payment::PAYMENT_TYPE_OTHER && !empty($request->student_id)){
            $totalPayment = $payment->structures->sum('amount');
            $paymentTransactions = Transaction::where('student_id', $studentId)
            ->where('payment_id', $payment->id)
            ->where('session', $session)
            ->where('status', 1)
            ->get();

            $totatAmountPaid = $paymentTransactions->sum('amount_payed');
            if($totalPayment> 0 && ($amount+$totatAmountPaid) > $totalPayment){
                alert()->error('Oops!!!', 'Amount to be paid seems to be an overpayment, student is not charged')->persistent('Close');
                if(!empty($request->student_id)){
                    if($validator->fails()) {
                        alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                        return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
                    }
                }
                return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
            }
        }

        $reference = $this->generateRandomString(10);

        //check existing transaction
        $existingTx = Transaction::where([
            'student_id' => $studentId,
            'user_id' => $applicantId,
            'payment_id' => $request->payment_id,
            'amount_payed' => $amount,
            'payment_method' => $request->paymentGateway,
            'session' => $session,
            'narration' => $request->narration,
            'status' => $request->paymentStatus == 1 ? 1 : null
        ])->first();

        if(!empty($request->student_id)){
            if($existingTx){
                alert()->info('Good Job!!!', 'Payment already processed.')->persistent('Close');
                return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
            }
        }

        if(!empty($request->user_id)){
            if($existingTx){
                alert()->info('Good Job!!!', 'Payment already processed.')->persistent('Close');
                return $this->getSingleApplicant($applicant->application_number, 'admin.chargeStudent');
            }
        }

        if($existingTx){
            alert()->info('Good Job!!!', 'Payment already processed.')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
        }
        
        //Create new transaction
        $transaction = Transaction::create([
            'student_id' => $studentId,
            'user_id' => $applicantId,
            'payment_id' => $request->payment_id,
            'amount_payed' => $amount,
            'payment_method' => $request->paymentGateway,
            'reference' => $reference,
            'session' => $session,
            'narration' => $request->narration,
            'status' => $request->paymentStatus == 1 ? 1 : null
        ]);

        if(!empty($studentId)){
            $student = Student::with('applicant')->where('id', $studentId)->first();

            if(!empty($student)){
                $pdf = new Pdf();
                $invoice = $pdf->generateTransactionInvoice($session, $studentId, $payment->id, 'single');
                            
                $data = new \stdClass();
                $data->lastname = $student->applicant->lastname;
                $data->othernames = $student->applicant->othernames;
                $data->amount = $amount;
                $data->invoice = $invoice;
                
                Mail::to($student->email)->send(new TransactionMail($data));
            }

            if(!empty($student) && ($payment->type == Payment::PAYMENT_TYPE_SCHOOL || $payment->type == Payment::PAYMENT_TYPE_SCHOOL_DE) && $request->paymentStatus == 1){
                $this->generateMatricAndEmail($student);
            }

            if(!empty($request->student_id)){
                alert()->info('Good Job', 'Student Charged.')->persistent('Close');
                return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
            }

            if(!empty($request->user_id)){
                alert()->success('Good Job', 'Applicant Charged')->persistent('Close');
                return $this->getSingleApplicant($applicant->application_number, 'admin.chargeStudent');
            }
        }

        if(!empty($request->student_id)){
            alert()->info('Good Job', 'Student Charged.')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'admin.chargeStudent');
        }

        if(!empty($request->user_id)){
            alert()->success('Good Job', 'Applicant Charged')->persistent('Close');
            return $this->getSingleApplicant($applicant->application_number, 'admin.chargeStudent');
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

        return view('admin.transactionReport', [
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
        ->where('is_active', true)
        ->where('is_passed_out', false)
            ->where('is_rusticated', false)
        ->whereHas('transactions', function ($query) use ($paymentIds) {
            $query->whereIn('payment_id', $paymentIds);
        })
        ->get();

        if(!empty($facultyId)){
            $faculty = Faculty::find($facultyId);

            $programmeIds = $faculty->departments->flatMap->programmes->pluck('id')->toArray();
            $paymentIds = $payments->whereIn('programme_id', $programmeIds)->pluck('id')->toArray();
            $students = Student::with('applicant', 'academicLevel', 'transactions')
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
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
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
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
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
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

        return view('admin.transactionReport', [
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
        $type = $request->has('type') ? $request->type : null;

        $pdf = new Pdf();
        $invoice = $pdf->generateTransactionInvoice($session, $studentId, $paymentId, $type);

        return redirect(asset($invoice));

    }

    public function getStudentPayment (Request $request){
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

        $amountBilled = 0;
        $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;

        $levels = AcademicLevel::orderBy('id', 'DESC')->get();
        $sessions = Session::orderBy('id', 'DESC')->get();

        $transactionsQuery = Transaction::where([
            'session' => $session,
            'student_id' => $studentId,
        ]);
        
        if ($paymentId > 0) {
            $payment = Payment::with('structures')->where('id', $paymentId)->first();
            $amountBilled = $payment->structures->sum('amount');

            if ($payment) {
                $paymentType = $payment->type;
        
                $transactionsQuery->whereHas('paymentType', function ($query) use ($paymentType) {
                    $query->where('type', $paymentType);
                });
            }
        } else {
            $amountBilled = $transactionsQuery->sum('amount_payed');
        }
        
        $transactions = $transactionsQuery->orderBy('id', 'DESC')->get();

        if ($amountBilled == 0) {
            $amountBilled = $transactions->sum('amount_payed');
        }
        

        $student = Student::with('applicant')->where('id', $studentId)->first();


        $student->session = $session;
        $student->paymentType = $paymentType;
        $student->amountBilled = $amountBilled;
        $student->paymentId = $paymentId;

        
        return view('admin.getStudentPayment', [
            'student' => $student,
            'transactions' => $transactions,
            'levels' => $levels,
            'sessions' => $sessions
        ]);

    }

    public function editTransaction(Request $request){
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'academic_session' => 'required',
            'level' => 'required',
            'amount' => 'required',
            'paymentStatus' => 'required'
        ]);

        $student = Student::with('applicant')->where('id', $request->student_id)->first();
        $studentIdCode = $student->matric_number;

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        if(!$transaction = Transaction::find($request->transaction_id)){
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        if(!empty($request->academic_session) && $transaction->session != $request->academic_session){
            $transaction->session = $request->academic_session;
        }

        if(!empty($request->amount) && $transaction->amount_payed != $request->amount*100){
            $transaction->amount_payed = $request->amount*100;
        }

        if(!empty($request->narration) && $transaction->narration != $request->narration){
            $transaction->narration = $request->narration;
        }

        $transaction->status = $request->paymentStatus;

        if($transaction->update()){
            alert()->success('Good job!!', 'Transaction changes saved successfully')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        alert()->info('Oops!', 'Something went wrong')->persistent('Close');
        return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
    } 

    public function deleteTransaction(Request $request){
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
        ]);

        $student = Student::with('applicant')->where('id', $request->student_id)->first();
        $studentIdCode = $student->matric_number;

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        if(!$transaction = Transaction::find($request->transaction_id)){
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        if($transaction->delete()){
            alert()->success('Good job!!', 'Transaction deleted successfully')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        alert()->info('Oops!', 'Something went wrong')->persistent('Close');
        return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
    } 

    public function walletTopUp (Request $request){
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required',
            'amount' => 'required',
        ]);

        $globalData = $request->input('global_data');
        $session = $globalData->sessionSetting['academic_session'];

        $student = Student::with('applicant')->where('id', $request->student_id)->first();
        $studentIdCode = $student->matric_number;
        $studentId = $student->id;
        $paymentId = $request->payment_id;
        

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        $amount = $request->amount * 100;
        $reference = $this->generateAccessCode();

        $transaction = Transaction::create([
            'user_id' => null,
            'student_id' => $student->id,
            'payment_id' => $paymentId,
            'amount_payed' => $amount,
            'payment_method' => 'Bursary',
            'reference' => $reference,
            'session' => $session,
            'status' => 1
        ]);

        if($this->creditStudentWallet($studentId, $amount)){
            $pdf = new Pdf();
            $invoice = $pdf->generateTransactionInvoice($session, $studentId, $paymentId, 'single');
                    
            $data = new \stdClass();
            $data->lastname = $student->applicant->lastname;
            $data->othernames = $student->applicant->othernames;
            $data->amount = $amount;
            $data->invoice = $invoice;
            
            Mail::to($student->email)->send(new TransactionMail($data));

            alert()->success('Good job!!', 'Wallet credited successfully')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        alert()->info('Oops!', 'Something went wrong')->persistent('Close');
        return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
    }

    public function bankAccounts(){
        $bankAccounts = BankAccount::all();

        return view('admin.bankAccounts', [
            'bankAccounts' => $bankAccounts
        ]);
    }

    public function addBankAccount(Request $request){

        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string',
            'account_name' => 'required|string',
            'account_purpose' => 'required|string',
            'account_code' => 'required|string',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $bankAccount = ([
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'account_purpose' => $request->account_purpose,
            'account_code' => $request->account_code
        ]);

        if(BankAccount::create($bankAccount)){
            alert()->success('Room Type added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateBankAccount(Request $request){
        $validator = Validator::make($request->all(), [
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $bankAccount = BankAccount::findOrFail($request->bank_account_id);

        $bankAccountData = array_filter([
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'account_purpose' => $request->account_purpose,
            'account_code' => $request->account_code
        ]);

        if ($bankAccount->update($bankAccountData)) {
            alert()->success('Changes saved successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function deleteBankAccount(Request $request){

        $validatedData = $request->validate([
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        $bankAccount = BankAccount::findOrFail($request->bank_account_id);

        if($bankAccount->delete()){
            alert()->success('Record deleted successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

}
