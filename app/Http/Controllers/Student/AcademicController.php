<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Course;
use App\Models\CourseRegistrationSetting;
use App\Models\CourseRegistration;
use App\Models\StudentCourseRegistration;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\StudentExamCard;
use App\Models\Session;
use App\Models\AcademicLevel;
use App\Models\ResultApprovalStatus;

use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;


class AcademicController extends Controller
{
    //

    public function courseRegistration(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        $courses = Course::where('programme_id', $student->programme_id)->where('level_id', $student->level_id)->get();
        $existingRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession
        ])->get();

        //carryover courses
        $failedCourses = CourseRegistration::with('course')->where('student_id', $studentId)->where('grade', 'F')->get();
        $failedCourseIds = $failedCourses->pluck('course.id')->toArray();
        $carryOverCourses = Course::where('programme_id', $student->programme_id)->whereIn('id', $failedCourseIds)->get();

        $addOrRemoveTxPay = Payment::with('structures')->where('type', Payment::PAYMENT_MODIFY_COURSE_REG)->where('academic_session', $academicSession)->first();
        $addOrRemoveTxId = $addOrRemoveTxPay->id;
        $addOrRemoveTxs = Transaction::where([
            'student_id' =>  $studentId,
            'payment_id' => $addOrRemoveTxId,
            'is_used' => null,
            'status' => 1
        ])->orderBy('id', 'DESC')->get();

        $courseRegMgt = CourseRegistrationSetting::first();

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);

        return view('student.courseRegistration', [
            'courseRegMgt' => $courseRegMgt,
            'courses' => $courses,
            'existingRegistration' => $existingRegistration,
            'carryOverCourses' => $carryOverCourses,
            'addOrRemoveTxs' => $addOrRemoveTxs,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function registerCourses(Request $request)
    {
        $selectedCourses = $request->input('selected_courses', []);
        $txId = $request->input('tx_id');

        if(empty($selectedCourses)){
            alert()->info('Kindly select your courses', '')->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        if(!empty($txId)) {
            //delete existing registrattion
            CourseRegistration::where([
                'student_id' => $studentId,
                'academic_session' => $academicSession
            ])->forceDelete();

            StudentCourseRegistration::where([
                'student_id' => $studentId,
                'academic_session' => $academicSession,
            ])->forceDelete();

            Transaction::where([
                'student_id' =>  $studentId,
                'id' => $txId,
                'is_used' => null,
                'status' => 1
            ])->update(['is_used' => 1]);
        }
        
        try {
            foreach ($selectedCourses as $courseId) {
                $course = Course::findOrFail($courseId);

                // Check if the student is already registered for this course
                $existingRegistration = CourseRegistration::where([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'academic_session' => $academicSession,
                    'level_id' => $student->level_id
                ])->first();

                if (!$existingRegistration) {
                    $courseReg = CourseRegistration::create([
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'course_credit_unit' => $course->credit_unit,
                        'course_code' => $course->code,
                        'semester' => $course->semester,
                        'academic_session' => $academicSession,
                        'level_id' => $student->level_id,
                    ]);
                }
            }
    
            $pdf = new Pdf();
            $courseReg = $pdf->generateCourseRegistration($studentId, $academicSession);

            $studentRegistration = StudentCourseRegistration::create([
                'student_id' => $studentId,
                'academic_session' => $academicSession,
                'file' => $courseReg,
                'level_id' => $student->level_id
            ]);

        
            alert()->success('Changes Saved', 'Course registration saved successfully')->persistent('Close');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::info($e);
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return redirect()->back();
        }
    }

    public function printCourseReg(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        //create record for file
        $studentRegistration = StudentCourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession,
        ])->first();

        return redirect(asset($studentRegistration->file));
    }

    public function allCourseRegs(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $studentRegistrations = StudentCourseRegistration::where([
            'student_id' => $studentId,
        ])->orderBy('id', 'DESC')->get();

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        return view('student.allCourseRegs', [
            'studentRegistrations' => $studentRegistrations,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function editCourseReg(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $addOrRemoveTxPay = Payment::with('structures')->where('type', Payment::PAYMENT_MODIFY_COURSE_REG)->first();
        $addOrRemoveTxId = $addOrRemoveTxPay->id;
        $addOrRemoveTxs = Transaction::where('student_id', $studentId)->where('payment_id', $addOrRemoveTxId)->where('status', 1)->orderBy('id', 'DESC')->get();

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        return view('student.editCourseReg', [
            'addOrRemoveTxs' => $addOrRemoveTxs,
            'payment' => $addOrRemoveTxPay,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function examDocket(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $semester  = $globalData->examSetting['semester'];
        $levelId = $student->level_id;

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }


        $schoolPayment = Payment::with('structures')
            ->where('type', Payment::PAYMENT_TYPE_SCHOOL)
            ->where('programme_id', $student->programme_id)
            ->where('level_id', $levelId)
            ->where('academic_session', $student->academic_session)
            ->first();

        if(!$schoolPayment){
            alert()->info('Programme info missing, contact administrator', '')->persistent('Close');
            return redirect()->back();
        }

        $schoolPaymentId = $schoolPayment->id;
        $schoolAmount = $schoolPayment->structures->sum('amount');
        $schoolPaymentTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $schoolPaymentId)->where('session', $student->academic_session)->where('status', 1)->get();

        $passTuitionPayment = false;
        $fullTuitionPayment = false;
        $passEightyTuition = false;
        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.4){
            $passTuitionPayment = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.7){
            $passEightyTuition = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') >= $schoolAmount){
            $passEightyTuition = true;
            $fullTuitionPayment = true;
        }

        $studentExamCards = StudentExamCard::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession,
            'semester' => $semester
        ])->get();

        $courseRegs = CourseRegistration::with('course')
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('total', null)
            ->whereHas('course', function ($query) use ($semester) {
                $query->where('semester', $semester);
            })
            ->get();


        return view('student.examDocket', [
            'courseRegs' => $courseRegs,
            'payment' => $schoolPaymentTransaction,
            'passTuition' => $passTuitionPayment,
            'fullTuitionPayment' => $fullTuitionPayment,
            'passEightyTuition' => $passEightyTuition,
            'studentExamCards' => $studentExamCards
        ]);
    }

    public function genExamDocket(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $semester  = $globalData->examSetting['semester'];

        try {

            $pdf = new Pdf();
            $examDocket = $pdf->generateExamDocket($studentId, $academicSession, $semester);

            $studentExamCard = StudentExamCard::create([
                'student_id' => $studentId,
                'academic_session' => $academicSession,
                'semester' => $semester,
                'file' => $examDocket,
                'level_id' => $student->level_id
            ]);

            alert()->success('Good Job', 'Examination card generate successfully')->persistent('Close');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::info($e);
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return redirect()->back();
        }

    }

    public function printExamCard(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $semester  = $globalData->examSetting['semester'];

        //create record for file
        $studentExamCard = StudentExamCard::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession,
            'semester' => $semester
        ])->first();

        return redirect(asset($studentExamCard->file));
    }

    public function allExamDockets(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $studentExamCards = StudentExamCard::where([
            'student_id' => $studentId
        ])->get();

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }
        
        return view('student.examDockets', [
            'studentExamCards' => $studentExamCards,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function examResult(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $sessions = Session::orderBy('id', 'DESC')->get();
        $academicLevels = AcademicLevel::get();

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        if(!$paymentCheck->passTuitionPayment){
            return view('student.schoolFee', [
                'payment' => $paymentCheck->schoolPayment,
                'passTuition' => $paymentCheck->passTuitionPayment,
                'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
                'passEightyTuition' => $paymentCheck->passEightyTuition,
                'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
            ]);
        }

        return view('student.examResult', [
            'sessions' => $sessions,
            'academicLevels' => $academicLevels,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function generateResult(Request $request){
        $validator = Validator::make($request->all(), [
            'semester' => 'required',
            'session' => 'required',
            'level_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');

        $semester = $request->semester;
        $academicSession = $request->session;
        $levelId = $request->level_id;
        $academicLevel = AcademicLevel::find($levelId);
        $level = $academicLevel->level;

        $courseRegs = CourseRegistration::with('course')
        ->where('student_id', $studentId)
        ->where('academic_session', $academicSession)
        ->where('result_approval_id',  ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))
        ->whereHas('course', function ($query) use ($semester) {
            $query->where('semester', $semester);
        })
        ->get();

        if(!$courseRegs->count() > 0) {
            alert()->info('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        $schoolPayment = Payment::with('structures')
            ->where('type', Payment::PAYMENT_TYPE_SCHOOL)
            ->where('programme_id', $student->programme_id)
            ->where('level_id', $levelId)
            ->where('academic_session', $academicSession)
            ->first();

        if(!$schoolPayment){
            alert()->info('Programme info missing, contact administrator', '')->persistent('Close');
            return redirect()->back();
        }

        $schoolPaymentId = $schoolPayment->id;
        $schoolAmount = $schoolPayment->structures->sum('amount');
        $schoolPaymentTransaction = Transaction::where('student_id', $studentId)
            ->where('payment_id', $schoolPaymentId)
            ->where('session', $academicSession)
            ->where('status', 1)
            ->get();

        $passTuitionPayment = false;
        $fullTuitionPayment = false;
        $passEightyTuition = false;
        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.4){
            $passTuitionPayment = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.7){
            $passEightyTuition = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') >= $schoolAmount){
            $passEightyTuition = true;
            $fullTuitionPayment = true;
        }

        if($semester == 1 && !$passTuitionPayment){
            alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 100% of your school fees.')->persistent('Close');
            return redirect()->back();
        }

        if($semester == 2 && !$fullTuitionPayment){
            alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 100% of your school fees.')->persistent('Close');
            return redirect()->back();
        }

        $pdf = new Pdf();
        $examResult = $pdf->generateExamResult($studentId, $academicSession, $semester, $level);

        return redirect(asset($examResult));
    }
}
