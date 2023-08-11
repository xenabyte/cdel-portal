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
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use App\Models\Course;
use App\Models\CourseRegistrationSetting;
use App\Models\CourseRegistration;
use App\Models\StudentCourseRegistration;
use App\Models\Payment;
use App\Models\Transaction;


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
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $courses = Course::where('programme_id', $student->programme_id)->where('level_id', $student->level_id)->get();
        $existingRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession
        ])->get();

        //carryover courses
        $failedCourses = CourseRegistration::with('course')->where('student_id', $studentId)->where('grade', 'F')->get();
        $failedCourseIds = $failedCourses->pluck('course.id')->toArray();
        $carryOverCourses = Course::where('programme_id', $student->programme_id)->whereIn('id', $failedCourseIds)->get();

        $addOrRemoveTxPay = Payment::with('structures')->where('type', Payment::PAYMENT_MODIFY_COURSE_REG)->first();
        $addOrRemoveTxId = $addOrRemoveTxPay->id;
        $addOrRemoveTxs = Transaction::where([
            'student_id' =>  $studentId,
            'payment_id' => $addOrRemoveTxId,
            'is_used' => null,
            'status' => 1
        ])->orderBy('id', 'DESC')->get();

        $courseRegMgt = CourseRegistrationSetting::first();

        return view('student.courseRegistration', [
            'courseRegMgt' => $courseRegMgt,
            'courses' => $courses,
            'existingRegistration' => $existingRegistration,
            'carryOverCourses' => $carryOverCourses,
            'addOrRemoveTxs' => $addOrRemoveTxs
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
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

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
                'file' => $courseReg
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
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

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
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $studentRegistrations = StudentCourseRegistration::where([
            'student_id' => $studentId,
        ])->orderBy('id', 'DESC')->get();

        return view('student.allCourseRegs', [
            'studentRegistrations' => $studentRegistrations,
        ]);
    }

    public function editCourseReg(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $addOrRemoveTxPay = Payment::with('structures')->where('type', Payment::PAYMENT_MODIFY_COURSE_REG)->first();
        $addOrRemoveTxId = $addOrRemoveTxPay->id;
        $addOrRemoveTxs = Transaction::where('student_id', $studentId)->where('payment_id', $addOrRemoveTxId)->where('status', 1)->orderBy('id', 'DESC')->get();

        return view('student.editCourseReg', [
            'addOrRemoveTxs' => $addOrRemoveTxs,
            'payment' => $addOrRemoveTxPay
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

        $schoolPayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_SCHOOL)->where('programme_id', $student->programme_id)->where('level_id', $levelId)->first();
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

        $courseRegs = CourseRegistration::with('course')
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->whereHas('course', function ($query) use ($semester) {
                $query->where('semester', $semester);
            })
            ->get();


        return view('student.examDocket', [
            'courseRegs' => $courseRegs,
            'payment' => $schoolPaymentTransaction,
            'passTuition' => $passTuitionPayment,
            'fullTuitionPayment' => $fullTuitionPayment,
            'passEightyTuition' => $passEightyTuition
        ]);
    }
    
}
