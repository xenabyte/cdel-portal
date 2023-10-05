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

use App\Models\AcademicLevel;
use App\Models\ApprovalLevel;
use App\Models\ResultApprovalStatus;
use App\Models\Session;
use App\Models\SessionSetting;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\CourseRegistrationSetting;
use App\Models\ExaminationSetting;
use App\Models\Programme;
use App\Models\Student;
use App\Models\StudentDemotion;
use App\Models\StudentCourseRegistration;
use App\Models\CourseRegistration;
use App\Models\Payment;
use App\Models\Transaction;

use App\Libraries\Result\Result;
use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class ResultController extends Controller
{
    //
    public function getStudentResults(){
        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();

        return view('staff.getStudentResults',[
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'faculties' => $faculties
        ]);
    }

    public function generateStudentResults(Request $request){
        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();

        $programme = Programme::find($request->programme_id);

        $academicLevel = AcademicLevel::find($request->level_id);

        $students = Student::
        with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'registeredCourses.course', 'partner', 'academicLevel', 'department', 'faculty'])
        ->where([
            'is_active' => true,
            'is_passed_out' => false,
            'is_rusticated' => false,
            'programme_id' => $request->programme_id,
            'department_id' => $request->department_id,
            'faculty_id' => $request->faculty_id,
        ])
        ->whereHas('registeredCourses', function ($query) use ($request) {
            $query->where('level_id', $request->level_id)
                  ->where('academic_session', $request->session);
        })
        ->get();

        return view('staff.getStudentResults',[
            'students' => $students,
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'faculties' => $faculties,
            'semester' => $request->semester,
            'academicSession' => $request->session,
            'academiclevel' => $academicLevel,
            'programme' => $programme,
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
        ]);
    }

    public function approveResult(Request $request){

        $studentIds = $request->input('student_ids', []);
        $students = Student::whereIn('id', $studentIds)->get();

        foreach ($students as $student) {
            Log::info("message:". $student->Id);
            
            $studentRegistration = CourseRegistration::where([
                'student_id' => $student->id,
                'level_id' => $request->level_id,
                'academic_session' => $request->session,
                'semester' => $request->semester,
            ])->where('grade', '!=', null)->where('result_approval_id', '!=', ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))->update(['result_approval_id' => ResultApprovalStatus::getApprovalStatusId($request->type)]);
        }


        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();

        alert()->success('Result Approved', '')->persistent('Close');
        return view('staff.getStudentResults',[
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'faculties' => $faculties
        ]);
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
        $student = Student::find($studentId);
        $globalData = $request->input('global_data');

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
            alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 100% of school fees.')->persistent('Close');
            return redirect()->back();
        }

        if($semester == 2 && !$fullTuitionPayment){
            alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 100% of school fees.')->persistent('Close');
            return redirect()->back();
        }

        $pdf = new Pdf();
        $examResult = $pdf->generateExamResult($studentId, $academicSession, $semester, $level);

        return redirect(asset($examResult));
    }
}
