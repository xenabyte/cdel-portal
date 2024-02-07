<?php

namespace App\Libraries\Pdf;

use PDF as PDFDocument;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\GlobalSetting as Setting;
use App\Models\CourseRegistration;
use App\Models\ResultApprovalStatus;
use App\Models\Payment;
use App\Models\Staff;
use App\Models\StudentCourseRegistration;
use App\Models\Transaction;
use App\Models\StudentExit;

use Carbon\Carbon;
use Log;

Class Pdf {

    public function generateAdmissionLetter($slug){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('programme', 'faculty', 'department', 'applicant')->where('slug', $slug)->first();
        $setting = Setting::first();
        $applicationType = $student->applicant->application_type;

        $acceptancePayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)->where('academic_session', $student->academic_session)->first();
        $type = Payment::PAYMENT_TYPE_SCHOOL;

        if($applicationType != 'UTME' && ($student->level_id == 2)){
            $type = Payment::PAYMENT_TYPE_SCHOOL_DE;
        }

        $schoolPayment = Payment::with('structures')
            ->where('type', $type)
            ->where('programme_id', $student->programme_id)
            ->where('level_id', $student->level_id)
            ->where('academic_session', $student->academic_session)
            ->first();

        if(!$schoolPayment){
            log::error($student->programme->name .' school fee is not available');
            return false;
        }

        $schoolAmount = $schoolPayment->structures->sum('amount');
        $acceptanceAmount = $acceptancePayment->structures->sum('amount');

        $fileDirectory = 'uploads/files/admission/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        } 
        
        $studentData = [
            'levelId' => $student->level_id,
            'created_at' => $student->created_at,
            'jamb_reg_no' => $student->applicant->jamb_reg_no,
            'programme_name' => $student->programme->name,
            'duration' => $student->programme->duration,
            'department_name' => $student->department->name,
            'faculty_name' => $student->faculty->name,
            'student_name' => $student->applicant->lastname .' '. $student->applicant->othernames,
            'academic_session' => $student->academic_session,
            'application_type' => $student->application_type,
            'acceptance_amount' => $acceptanceAmount,
            'school_amount' => $schoolAmount,
            'logo' => asset($setting->logo)
        ];

        $pdf = PDFDocument::loadView('pdf.admissionLetter', $studentData)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateCourseRegistration($studentId, $academicSession, $otherData = null){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $staff = null;
        if(!empty($otherData->staffId)){
            $staff = Staff::find($otherData->staffId);
        }

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' course registration '. $academicSession)));

        $fileDirectory = 'uploads/files/course_registration/'.$slug.'.pdf';
        $courseReg = CourseRegistration::with('course')->where('student_id', $studentId)->where('academic_session', $academicSession)->get();
        
        $studentCourseReg = null;
        if(!empty($otherData->courseRegId)){
            $studentCourseReg = StudentCourseRegistration::find($otherData->courseRegId);
        }

        $staffData = null;
        if(!empty($staff)){
            $staffData = new \stdClass();
            $staffData->staff = $staff;

            if($otherData->type == 'level_adviser'){
                $studentCourseReg->level_adviser_status = true;
                $studentCourseReg->level_adviser_id = $staff->id;
                $studentCourseReg->level_adviser_approved_date = Carbon::now();

                CourseRegistration::where('student_id', $studentId)
                ->where('academic_session', $academicSession)
                ->update(['status' => 'approved']);

                }else{
                $studentCourseReg->hod_status = true;
                $studentCourseReg->hod_id = $staff->id;
                $studentCourseReg->hod_approved_date = Carbon::now();

                CourseRegistration::where('student_id', $studentId)
                ->where('academic_session', $academicSession)
                ->update(['status' => 'approved']);
            }

            $studentCourseReg->save();
            $studentCourseRegNew = StudentCourseRegistration::with('hod', 'levelAdviser')->where('id', $otherData->courseRegId)->first();

            $staffData->studentCourseReg = $studentCourseRegNew;
        }else{
            if(!empty($otherData)){
                CourseRegistration::where('student_id', $studentId)
                ->where('academic_session', $academicSession)
                ->update(['status' => 'approved']);
            }
        }

        $data = ['info'=>$student, 'registeredCourses' => $courseReg, 'staffData' => $staffData];

        $fileDirectory = 'uploads/files/course_registration/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        } 

        $pdf = PDFDocument::loadView('pdf.courseRegistration', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateExamDocket($studentId, $academicSession, $semester){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' examination card '. $semester .' '. $academicSession)));

        $courseRegs = CourseRegistration::with('course')
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('total', null)
            ->where('semester', $semester)
            ->where('status', 'approved')
            ->get();


        $fileDirectory = 'uploads/files/exam_card/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        } 
        $data = ['info'=>$student, 'registeredCourses' => $courseRegs];

        $pdf = PDFDocument::loadView('pdf.examCard', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateExamResult($studentId, $academicSession, $semester, $level){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' examination result '. $semester .' '. $academicSession)));

        $courseRegs = CourseRegistration::with('course')
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('result_approval_id',  ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))
            ->whereHas('course', function ($query) use ($semester) {
                $query->where('semester', $semester);
            })
            ->get();

        
        $student->resultSession = $academicSession;
        $student->resultSemester = $semester;
        $student->resultLevel = $level;

        $fileDirectory = 'uploads/files/result_card/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        } 
        $data = ['info'=>$student, 'registeredCourses' => $courseRegs];

        $pdf = PDFDocument::loadView('pdf.resultCard', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateTransactionInvoice($session, $studentId, $paymentId, $type='all'){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $amountBilled = 0;
        $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;

        if($paymentId > 0){
            $payment = Payment::with('structures')->where('id', $paymentId)->first();
            $amountBilled = $payment->structures->sum('amount');

            $paymentType = $payment->type;
        }

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' transaction invoice '. $session)));

        $transactions = Transaction::where([
            'session' => $session,
            'student_id' => $studentId,
            'payment_id' => $paymentId,
            'status' => 1
        ])->latest()->get();

        if(!$paymentId > 0){
            $amountBilled = $transactions->sum('amount_payed');
        }

        if($type == 'all'){
            $transactions = Transaction::where([
                'session' => $session,
                'student_id' => $studentId,
                'payment_id' => $paymentId,
                'status' => 1
            ])->get();
        }

        $student->session = $session;
        $student->paymentType = $paymentType;
        $student->amountBilled = $amountBilled;

        $fileDirectory = 'uploads/files/invoice/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        } 
        $data = ['info'=>$student, 'transactions' => $transactions];

        $pdf = PDFDocument::loadView('pdf.invoice', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateExitApplication($session, $studentId, $newExitId){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant', 'applicant.guardian', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' Exit Application '. $session .' '. $newExitId)));


        $exitApplication = StudentExit::find($newExitId);

        $fileDirectory = 'uploads/files/exit_applications/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        }   
             
        $data = ['info'=>$student, 'exitApplication' => $exitApplication];

        $pdf = PDFDocument::loadView('pdf.exitApplication', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }
}
