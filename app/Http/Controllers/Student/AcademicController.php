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
use App\Models\CoursePerProgrammePerAcademicSession;
use App\Models\ProgrammeChangeRequest;
use App\Models\Staff;
use App\Models\Notification;
use App\Models\Programme; 

use App\Libraries\Pdf\Pdf;
use App\Mail\NotificationMail;


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;


class AcademicController extends Controller
{
    //
    public function registeredCourses(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        $courseRegs = CourseRegistration::where('student_id', $studentId)->where('academic_session', $academicSession)->get();


        return view('student.registeredCourses', [
            'courseRegs' => $courseRegs,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
        ]);
    }

    public function courseRegistration(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $isUTME = $student->applicant->application_type == 'UTME'? true :false;
        $minLevel = $isUTME ? 1 : 2;

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        $checkNewStudentStatus = $this->checkNewStudentStatus($student);

        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        $coursePerProgrammePerAcademicSession = CoursePerProgrammePerAcademicSession::where('programme_id', $student->programme_id)
            ->where('level_id', $student->level_id)
            ->where('programme_category_id', $student->programme_category_id)
            ->where('academic_session', $academicSession)
            ->where('dap_approval_status', 'approved')
            ->get();

        $existingRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession
        ])->get();

        $unregisteredRequiredCoursesIds = [];

        $prevAcademicSession = $academicSession;

        for ($level = $levelId - 1; $level >= $minLevel; $level--) {
            $prevAcademicSession = $this->getPreviousAcademicSession($prevAcademicSession);

            $allRequiredCourses = CoursePerProgrammePerAcademicSession::where('programme_id', $student->programme_id)
                ->where('status', '!=', 'Elective')
                ->where('level_id', $level)
                ->where('academic_session', $prevAcademicSession)
                ->get();

            $allRequiredCoursesIds = $allRequiredCourses->pluck('course_id')->toArray();

            $registeredCourseIds = CourseRegistration::where('student_id', $student->id)
                ->whereIn('course_id', $allRequiredCoursesIds)
                ->pluck('course_id')
                ->toArray();

            $missingRequiredCourses = array_diff($allRequiredCoursesIds, $registeredCourseIds);

            $unregisteredRequiredCoursesIds = array_merge($unregisteredRequiredCoursesIds, $missingRequiredCourses);
        }

        $unregisteredRequiredCoursesIds = array_unique($unregisteredRequiredCoursesIds);

        
        $checkLateReg = $this->checkLateRegistration();        
        $lateRegTxPay = Payment::with('structures')->where('type', Payment::PAYMENT_LATE_COURSE_REG)->where('academic_session', $academicSession)->first();
        $lateRegTxPayId = $lateRegTxPay->id;
        
        $lateRegTx = Transaction::where([
            'student_id' =>  $studentId,
            'payment_id' => $lateRegTxPayId,
            'status' => 1
        ])->first();

        $addOrRemoveTxPay = Payment::with('structures')->where('type', Payment::PAYMENT_MODIFY_COURSE_REG)->where('academic_session', $academicSession)->first();
        $addOrRemoveTxId = $addOrRemoveTxPay->id;
        $addOrRemoveTxs = Transaction::where([
            'student_id' =>  $studentId,
            'payment_id' => $addOrRemoveTxId,
            'is_used' => null,
            'status' => 1
        ])->orderBy('id', 'DESC')->get();

        if($addOrRemoveTxs->count() > 0){
            $registeredCourses =  CourseRegistration::where([
                'student_id' => $studentId,
                'academic_session' => $academicSession
            ])->get();

            foreach ($registeredCourses as $registeredCourse) { 
                $courseId = $registeredCourse->course_id;
    
                $checkCarryOver = CourseRegistration::where([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'grade' => 'F',
                ])->first();
    
                if(!empty($checkCarryOver)){
                    $checkCarryOver->re_reg = null;
                    $checkCarryOver->save();
                }
            }

        }

        $failedCourses = CourseRegistration::with('course')->where('student_id', $studentId)->where('grade', 'F')->where('re_reg', null)->get();
        $failedCourseIds = $failedCourses->pluck('programme_course_id')->toArray();
        $carryOverCourses = CoursePerProgrammePerAcademicSession::whereIn('id', $failedCourseIds)->get(); //where('programme_id', $student->programme_id)->

        $unregisteredRequiredCourses = CoursePerProgrammePerAcademicSession::where('programme_id', $student->programme_id)
         ->whereIn('course_id', $unregisteredRequiredCoursesIds)
         ->whereIn('level_id', range($minLevel, $levelId - 1))
         ->get()
         ->unique('course_id');

        $courseRegMgt = CourseRegistrationSetting::first();

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);

        return view('student.courseRegistration', [
            'courseRegMgt' => $courseRegMgt,
            'courses' => $coursePerProgrammePerAcademicSession,
            'existingRegistration' => $existingRegistration,
            'carryOverCourses' => $carryOverCourses,
            'unregisteredRequiredCourses' => $unregisteredRequiredCourses,
            'addOrRemoveTxs' => $addOrRemoveTxs,
            'checkLateReg' => $checkLateReg,
            'lateRegTx' => $lateRegTx,
            'lateRegTxPay' => $lateRegTxPay,
            'checkNewStudentStatus' => $checkNewStudentStatus,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function registerCourses(Request $request){
        $selectedCourses = $request->input('selected_courses', []);
        $failedSelectedCourses = $request->input('failed_selected_courses', []);
        
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

        $checkCourseRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession
        ])->get();

        if($checkCourseRegistration->count() > 0 && empty($txId)){
            alert()->info('You have already registered for courses', '')->persistent('Close');
            return redirect()->back();
        }

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        // Collect semester information from selected courses
        $semesters = [
            'semester_1' => [],
            'semester_2' => [],
            'dsa_courses' => 0,
        ];

        foreach ($selectedCourses as $courseId) {
            $coursePerProgrammeAndLevel = CoursePerProgrammePerAcademicSession::with('course')->findOrFail($courseId);
            $course = $coursePerProgrammeAndLevel->course;
            
            if ($coursePerProgrammeAndLevel->semester == 1) {
                $semesters['semester_1'][] = $courseId;
            } elseif ($coursePerProgrammeAndLevel->semester == 2) {
                $semesters['semester_2'][] = $courseId;
            }
            
            // Check if the course code starts with "DSA"
            if (preg_match('/^DSA\s?\d{3}$/', $course->code)) {
                $semesters['dsa_courses']++;
            }
        }

        // Check if courses are selected for both semesters
        if (empty($semesters['semester_1']) || empty($semesters['semester_2'])) {
            alert()->info('Please select courses for both Semester 1 and Semester 2', '')->persistent('Close');
            return redirect()->back();
        }

        // Check if more than one DSA course is selected
        if ($semesters['dsa_courses'] > 1) {
            alert()->error('You can only select 1 DSA course in a session', '')->persistent('Close');
            return redirect()->back();
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
            foreach ($failedSelectedCourses as $failedCourseId) { 
                $coursePerProgrammeAndLevel = CoursePerProgrammePerAcademicSession::with('course')->findOrFail($failedCourseId);
                $course = $coursePerProgrammeAndLevel->course;

                // Check if the student is already registered for this course
                $existingRegistration = CourseRegistration::where([
                    'student_id' => $studentId,
                    'course_id' => $failedCourseId,
                    'academic_session' => $academicSession,
                    'level_id' => $student->level_id
                ])->first();

                if (!$existingRegistration) {
                    $checkCarryOver = CourseRegistration::where([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'grade' => 'F',
                    ])->first();

                    if(!empty($checkCarryOver)){
                        $checkCarryOver->re_reg = true;
                        $checkCarryOver->save();
                    }
                    
                    $courseReg = CourseRegistration::create([
                        'student_id' => $studentId,
                        'course_id' => $course->id,
                        'programme_category_id' => $student->programme_category_id,
                        'course_credit_unit' => $coursePerProgrammeAndLevel->credit_unit,
                        'course_code' => $course->code,
                        'semester' => $coursePerProgrammeAndLevel->semester,
                        'academic_session' => $academicSession,
                        'level_id' => $student->level_id,
                        'programme_course_id' => $failedCourseId,
                        'course_status' => $coursePerProgrammeAndLevel->status
                    ]);
                }
            }

            foreach ($selectedCourses as $courseId) {
                $coursePerProgrammeAndLevel = CoursePerProgrammePerAcademicSession::with('course')->findOrFail($courseId);
                $course = $coursePerProgrammeAndLevel->course;

                // Check if the student is already registered for this course
                $existingRegistration = CourseRegistration::where([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'academic_session' => $academicSession,
                    'level_id' => $student->level_id
                ])->first();

                if (!$existingRegistration) {
                    $checkCarryOver = CourseRegistration::where([
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'grade' => 'F',
                    ])->first();

                    if(!empty($checkCarryOver)){
                        $checkCarryOver->re_reg = true;
                        $checkCarryOver->save();
                    }
                    
                    $courseReg = CourseRegistration::create([
                        'student_id' => $studentId,
                        'programme_category_id' => $student->programme_category_id,
                        'course_id' => $course->id,
                        'course_credit_unit' => $coursePerProgrammeAndLevel->credit_unit,
                        'course_code' => $course->code,
                        'semester' => $coursePerProgrammeAndLevel->semester,
                        'academic_session' => $academicSession,
                        'level_id' => $student->level_id,
                        'programme_course_id' => $courseId,
                        'course_status' => $coursePerProgrammeAndLevel->status
                    ]);
                }
            }
    
            $pdf = new Pdf();
            $courseReg = $pdf->generateCourseRegistration($studentId, $academicSession);

            $studentRegistration = StudentCourseRegistration::create([
                'student_id' => $studentId,
                'academic_session' => $academicSession,
                'file' => $courseReg,
                'level_id' => $student->level_id,
                'programme_category_id' => $student->programme_category_id
            ]);

        
            alert()->success('Changes Saved', 'Course registration saved successfully')->persistent('Close');
            return redirect()->back();

        } catch (\Exception $e) {
            Log::info($e);
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return redirect()->back();
        }
    }

    public function printCourseReg(Request $request){

        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        //create record for file
        $studentRegistration = StudentCourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession,
            'programme_category_id' => $student->programme_category_id
        ])->first();

        $pdf = new Pdf();

        if(!empty($studentRegistration)){

            // $studentRegistration = StudentCourseRegistration::create([
            //     'student_id' => $studentId,
            //     'academic_session' => $academicSession,
            //     'file' => $courseReg,
            //     'level_id' => $student->level_id,
            //     'programme_category_id' => $student->programme_category_id
            // ]);

            $staffIds = [];
            if (!empty($studentRegistration->level_adviser_id)) {
                $staffIds[] = $studentRegistration->level_adviser_id;
            }
            if (!empty($studentRegistration->hod_id)) {
                $staffIds[] = $studentRegistration->hod_id;
            }

            if (!empty($staffIds)) {
                $otherData = new \stdClass();
                $otherData->staffId = implode(',', $staffIds); // Join multiple IDs if both exist
                $otherData->courseRegId = $studentRegistration->id;

                if (!empty($studentRegistration->level_adviser_id)) {
                    $otherData->type = 'Level Adviser';
                } elseif (!empty($studentRegistration->hod_id)) {
                    $otherData->type = 'Hod';
                }

                $courseReg = $pdf->generateCourseRegistration($studentId, $academicSession, $otherData);

                $studentRegistration->file = $courseReg;
                $studentRegistration->save();

                $senderName = env('SCHOOL_NAME');
                $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
                $message = 'Your course registration has been regenerated';
        
                $mail = new NotificationMail($senderName, $message, $receiverName, $courseReg);
                if(env('SEND_MAIL')){
                    Mail::to($student->email)->send($mail);
                }

                return redirect(asset($courseReg));
            }

        }

        alert()->error('Oops!', 'Course registration is yet to be approved')->persistent('Close');
        return redirect()->back();
    }

    public function allCourseRegs(Request $request){

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
        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        return view('student.allCourseRegs', [
            'studentRegistrations' => $studentRegistrations,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function editCourseReg(Request $request){

        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $addOrRemoveTxPay = Payment::with('structures')->where('type', Payment::PAYMENT_MODIFY_COURSE_REG)->where('academic_session', $academicSession)->first();
        $addOrRemoveTxId = $addOrRemoveTxPay->id;
        $addOrRemoveTxs = Transaction::where('student_id', $studentId)->where('payment_id', $addOrRemoveTxId)->where('status', 1)->orderBy('id', 'DESC')->get();

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

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

        $checkStudentPayment = $this->checkSchoolFees($student, $academicSession, $levelId);
        if($checkStudentPayment->status != 'success'){
            alert()->error('Oops!', 'Something went wrong with School fees')->persistent('Close');
            return redirect()->back();
        }

        $passTuitionPayment = $checkStudentPayment->passTuitionPayment;
        $fullTuitionPayment = $checkStudentPayment->fullTuitionPayment;
        $passEightyTuition = $checkStudentPayment->passEightyTuition;
        $schoolPaymentTransaction = $checkStudentPayment->schoolPaymentTransaction;

        // if(!$passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $checkStudentPayment->schoolPayment,
        //         'passTuition' => $passTuitionPayment,
        //         'fullTuitionPayment' => $fullTuitionPayment,
        //         'passEightyTuition' => $passEightyTuition,
        //         'studentPendingTransactions' => $checkStudentPayment->studentPendingTransactions
        //     ]);
        // }


        $studentExamCards = StudentExamCard::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession,
            'semester' => $semester
        ])->get();

        $courseRegs = CourseRegistration::with('course')
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('total', null)
            ->where('semester', $semester)
            ->where('status', 'approved')
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
        $academicSession = $globalData->sessionSetting['academic_session'];
        $semester  = $globalData->examSetting['semester'];

        if(!empty($request->exam_card_id)){
            $examCard = StudentExamCard::find($request->exam_card_id);

            $academicSession = $examCard->academic_session;
            $semester  = $examCard->semester;
        }

        $courseRegs = CourseRegistration::with('course')
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('total', null)
            ->where('semester', $semester)
            ->where('status', 'approved')
            ->get();

        if(empty($courseRegs)){
            alert()->error('Oops!', 'No approved course registration for this semester and session.')->persistent('Close');
            return redirect()->back();
        }

        try {

            $pdf = new Pdf();
            $examDocket = $pdf->generateExamDocket($studentId, $academicSession, $semester);

             //create record for file
            $studentExamCard = StudentExamCard::where([
                'student_id' => $studentId,
                'academic_session' => $academicSession,
                'semester' => $semester
            ])->first();


            if(empty($studentExamCard)){
                $studentExamCard = StudentExamCard::create([
                    'student_id' => $studentId,
                    'academic_session' => $academicSession,
                    'semester' => $semester,
                    'file' => $examDocket,
                    'level_id' => $student->level_id
                ]);
            }else{
                $fileDirectory = $studentExamCard->file;
                if (file_exists($fileDirectory)) {
                    unlink($fileDirectory);
                } 
                $studentExamCard->file = $examDocket;
                $studentExamCard->save();
            }

            return redirect(asset($examDocket));

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

    public function allExamDockets(Request $request){
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
        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }
        
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
        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

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

        $sessionId = Session::getSessionId($academicSession);

        if($sessionId > 3) {
            $checkStudentPayment = $this->checkSchoolFees($student, $academicSession, $levelId);
            if($checkStudentPayment->status != 'success'){
                alert()->error('Oops!', 'Something went wrong with School fees')->persistent('Close');
                return redirect()->back();
            }

            $passTuition = $checkStudentPayment->passTuitionPayment;
            $fullTuitionPayment = $checkStudentPayment->fullTuitionPayment;

            if($semester == 1 && !$passTuition){
                alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 40% of school fees for '.$academicSession.' acaddemic session')->persistent('Close');
                return redirect()->back();
            }

            if($semester == 2 && !$fullTuitionPayment){
                alert()->info('Oops!', 'Please be informed that in order to generate your examination results, it is necessary to clear 100% of school fees for '.$academicSession.' acaddemic session')->persistent('Close');
                return redirect()->back();
            }
        }

        $pdf = new Pdf();
        $examResult = $pdf->generateExamResult($studentId, $academicSession, $semester, $levelId);

        return redirect(asset($examResult));
    }

    public function transcript(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);


        return view('student.transcripts', [
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function programmeChangeRequests(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);
        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        $programmeChangePayment = Payment::where("type", Payment::PAYMENT_TYPE_INTRA_TRANSFER_APPLICATION)->where("academic_session", $academicSession)->first();
        $programmeChangeRequests = ProgrammeChangeRequest::where('student_id', $studentId)->get();

        return view('student.programmeChangeRequests', [
            'programmeChangePayment' => $programmeChangePayment,
            'programmeChangeRequests' => $programmeChangeRequests,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition
        ]);
    }

    public function viewProgrammeChangeRequest(Request $request, $slug){
        $student = Auth::guard('student')->user();

        $programmeChangeRequest = ProgrammeChangeRequest::where('slug', $slug)->first();

        return view('student.viewProgrammeChangeRequest', [
            'programmeChangeRequest' => $programmeChangeRequest
        ]);
    }

    public function programmeChange(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'programme_change_request_id' => 'required',
            'new_programme_id' => 'required',
            'reason' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $programmeChangeRequestId = $request->programme_change_request_id;

        $programmeChangeRequest = ProgrammeChangeRequest::find($programmeChangeRequestId);
        if (!$programmeChangeRequest) {
            alert()->error('Error', 'Programme change request not found')->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();
        $globalData = $request->input('global_data');

        $academicSession = $globalData->sessionSetting['academic_session'];

        $studentProgrammeId = $student->programme_id;
        $studentHODId = $student->department->hod_id;
        $studentDeanId = $student->faculty->dean_id;

        $studentNewProgrammeId = $request->new_programme_id;

        $studentNewProgramme = Programme::find($studentNewProgrammeId);
        if (!$studentNewProgramme) {
            alert()->error('Error', 'New programme not found')->persistent('Close');
            return redirect()->back();
        }

        $studentNewDepartment = $studentNewProgramme->department;
        $studentNewFaculty = $studentNewDepartment->faculty;

        $studentNewHODId = $studentNewDepartment->hod_id;
        $studentNewDeanId = $studentNewFaculty->dean_id;

        // Update the programme change request
        $programmeChangeRequest->update([
            'student_id' => $student->id,
            'old_programme_id' => $studentProgrammeId,
            'new_programme_id' => $studentNewProgrammeId,
            'reason' => $request->input('reason', null),
            'status' => 'pending',
            'current_stage' => 'Pending HOD Approval',
            'academic_session' => $academicSession,

            'old_programme_hod_id' => $studentHODId,
            'old_programme_dean_id' => $studentDeanId,
            'new_programme_hod_id' => $studentNewHODId,
            'new_programme_dean_id' => $studentNewDeanId,
        ]);

        // Notify student's current HOD
        $message = "Student {$student->full_name} has submitted a programme change request and requires your approval.";
        Notification::create([
            'staff_id' => $studentHODId,
            'description' => $message,
            'status' => 0
        ]);

        if(env('SEND_MAIL')){
            $studentHOD = Staff::find($studentHODId);
            $senderName = env('SCHOOL_NAME');
            $receiverName = $studentHOD->lastname.' '. $studentHOD->othernames;

            $mail = new NotificationMail($senderName, $message, $receiverName);
            Mail::to($student->email)->send($mail);
        }

        alert()->success('Success', 'Programme change request submitted successfully')->persistent('Close');
        return redirect()->back();
    }


    public function summerCourseReg(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $paymentCheck = $this->checkSchoolFees($student, $academicSession, $levelId);

        // if(!$paymentCheck->passTuitionPayment){
        //     return view('student.schoolFee', [
        //         'payment' => $paymentCheck->schoolPayment,
        //         'passTuition' => $paymentCheck->passTuitionPayment,
        //         'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
        //         'passEightyTuition' => $paymentCheck->passEightyTuition,
        //         'studentPendingTransactions' => $paymentCheck->studentPendingTransactions
        //     ]);
        // }

        $failedCourseRegs = CourseRegistration::where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('grade', 'F')
            ->where('level_id', $levelId)
            ->get();


        return view('student.summerCourseReg', [
            'failedCourseRegs' => $failedCourseRegs,
            'payment' => $paymentCheck->schoolPayment,
            'passTuition' => $paymentCheck->passTuitionPayment,
            'fullTuitionPayment' => $paymentCheck->fullTuitionPayment,
            'passEightyTuition' => $paymentCheck->passEightyTuition,
        ]);
    }
}
