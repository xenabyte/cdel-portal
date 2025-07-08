<?php

namespace App\Libraries\Pdf;

use App\Http\Controllers\Controller as BaseController;
use App\Http\Controllers\Staff\StaffController;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\GlobalSetting as Setting;
use App\Models\Payment;
use App\Models\ProgrammeCategory;
use App\Models\ResultApprovalStatus;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentCourseRegistration;
use App\Models\StudentExit;
use App\Models\SummerCourseRegistration;
use App\Models\Transaction;
use App\Models\User as Applicant;
use Carbon\Carbon;
use Log;
use PDF as PDFDocument;

class Pdf
{
    public function generateAdmissionLetter($slug)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('programme', 'faculty', 'department', 'applicant')->where('slug', $slug)->first();
        $setting = Setting::first();
        $applicationType = $student->applicant->application_type;
        $programmeCategoryId = $student->programme_category_id;
        $programmeCategory = $student->programmeCategory;
        $academicSession = $student->academic_session;

        $acceptancePayment = Payment::where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)
        ->where('academic_session', $academicSession)
        ->where('programme_id', $student->programme_id)
        ->where('programme_category_id', $student->programme_category_id)
            ->first();

        if (!$acceptancePayment) {
            $acceptancePayment = Payment::where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)
            ->where('academic_session', $academicSession)
            ->where('programme_category_id', $student->programme_category_id)
            ->first();
        }


        $type = Payment::PAYMENT_TYPE_SCHOOL;
        if ($applicationType != 'UTME' && ($student->level_id == 2) && ($student->programmeCategoryId == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::UNDERGRADUATE))) {
            $type = Payment::PAYMENT_TYPE_SCHOOL_DE;
        }

        $schoolPayment = Payment::with('structures')
            ->where('type', $type)
            ->where('programme_id', $student->programme_id)
            ->where('programme_category_id', $programmeCategoryId)
            ->where('level_id', $student->level_id)
            ->where('academic_session', $student->academic_session)
            ->first();

        if (!$schoolPayment) {
            log::error($student->programme->name .' school fee is not available');
            return false;
        }

        $schoolAmount = $schoolPayment->structures->sum('amount');
        $acceptanceAmount = $acceptancePayment->structures->sum('amount');

        $dir = public_path('uploads/files/admission');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/files/admission/'.$slug.time().'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        }

        $studentData = [
            'applicant_number' => $student->applicant->application_number,
            'programmeCategory' => $programmeCategory,
            'levelId' => $student->level_id,
            'created_at' => $student->created_at,
            'jamb_reg_no' => $student->applicant->jamb_reg_no,
            'programme_name' => $student->programme->name,
            'duration' => $student->programme->duration,
            'department_name' => $student->department->name,
            'faculty_name' => $student->faculty->name,
            'student_name' => $student->applicant->lastname .' '. $student->applicant->othernames,
            'academic_session' => $student->academic_session,
            'application_type' => $student->applicant->application_type,
            'acceptance_amount' => $acceptanceAmount,
            'school_amount' => $schoolAmount,
            'logo' => asset($setting->logo)
        ];

        if ($programmeCategoryId == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::UNDERGRADUATE)) {
            $pdf = PDFDocument::loadView('pdf.admission_letters.admissionLetter', $studentData)
            ->setOptions($options)
            ->save($fileDirectory);
        }

        if ($programmeCategoryId == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::TOPUP)) {
            $pdf = PDFDocument::loadView('pdf.admission_letters.topAdmissionLetter', $studentData)
            ->setOptions($options)
            ->save($fileDirectory);
        }

        $isPGD = $student->programme_category_id == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::PGD);
        $isMaster = $student->programme_category_id == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::MASTER);
        $isDoctorate = $student->programme_category_id == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::DOCTORATE);

        $isSPGS = $isPGD || $isMaster || $isDoctorate;

        if ($isSPGS) {
            $pdf = PDFDocument::loadView('pdf.admission_letters.spgsAdmissionLetter', $studentData)
            ->setOptions($options)
            ->save($fileDirectory);
        }

        return $fileDirectory;
    }

    public function generateCourseRegistration($studentId, $academicSession, $otherData = null)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $staff = null;
        if (!empty($otherData->staffId)) {
            $staff = Staff::find($otherData->staffId);
        }

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' course registration '. $academicSession)));

        $dir = public_path('uploads/files/course_registration');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/files/course_registration/'.$slug.time().'.pdf';
        $courseReg = CourseRegistration::with('course')->where('student_id', $studentId)->where('academic_session', $academicSession)->get();

        $studentCourseReg = null;
        if (!empty($otherData->courseRegId)) {
            $studentCourseReg = StudentCourseRegistration::find($otherData->courseRegId);
        }

        $staffData = null;
        if (!empty($staff)) {
            $staffData = new \stdClass();
            $staffData->staff = $staff;

            if ($otherData->type == 'Level Adviser') {
                $studentCourseReg->level_adviser_status = true;
                $studentCourseReg->level_adviser_id = $staff->id;
                $studentCourseReg->level_adviser_approved_date = Carbon::now();

                CourseRegistration::where('student_id', $studentId)
                ->where('academic_session', $academicSession)
                ->update(['status' => 'approved']);

            } else {
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
        } else {
            if (!empty($otherData)) {
                CourseRegistration::where('student_id', $studentId)
                ->where('academic_session', $academicSession)
                ->update(['status' => 'approved']);
            }
        }

        $data = ['info' => $student, 'registeredCourses' => $courseReg, 'studentCourseReg' => $studentCourseReg, 'staffData' => $staffData];

        $pdf = PDFDocument::loadView('pdf.courseRegistration', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateSummerCourseRegistration($studentId, $academicSession)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' course registration '. $academicSession)));

        $dir = public_path('uploads/files/summer/course_registration');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/files/summer/course_registration/'.$slug.time().'.pdf';
        $summerCourseReg = SummerCourseRegistration::with('course_registration')->where('student_id', $studentId)->where('academic_session', $academicSession)->get();

        $data = ['info' => $student, 'registeredCourses' => $summerCourseReg];

        $pdf = PDFDocument::loadView('pdf.summerCourseRegistration', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateExamDocket($studentId, $academicSession, $semester)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames.' '.time();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' examination card '. $semester .' '. $academicSession.' '.time())));

        $courseRegs = CourseRegistration::with('course')
        ->where('student_id', $studentId)
        ->where('academic_session', $academicSession)
        ->whereNull('total')
        ->where('semester', $semester)
        ->where('status', 'approved')
        ->get();
        // ->reject(fn($courseReg) => !is_null($student->cgpa) && round($courseReg->attendancePercentage()) <= 75);

        $dir = public_path('uploads/files/exam_card');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/files/exam_card/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        }
        $data = ['info' => $student, 'registeredCourses' => $courseRegs];

        $pdf = PDFDocument::loadView('pdf.examCard', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateExamResult($studentId, $academicSession, $semester, $level)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme', 'registeredCourses')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' examination result '. $semester .' '. $academicSession)));

        $courseRegs = CourseRegistration::with('course')
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('result_approval_id', ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))
            ->whereHas('course', function ($query) use ($semester) {
                $query->where('semester', $semester);
            })
            ->get();


        $allRegisteredCourses = $student->registeredCourses->filter(function ($course) use ($level, $semester) {
            if ($course->level_id < $level) {
                return true;
            }

            if ($course->level_id == $level) {
                return ($semester == 2) || ($course->semester == 1);
            }

            return false;
        })->where('grade', '!=', null);

        $allRegisteredCreditUnits =  $allRegisteredCourses->sum('course_credit_unit');
        $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
        $levelCGPA = $allRegisteredGradePoints > 0 ? number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2) : 0;

        $cgpaData = new \stdClass();
        $cgpaData->levelCGPA = $levelCGPA;
        $cgpaData->levelTotalUnit = $allRegisteredCreditUnits;
        $cgpaData->levelTotalPoint = $allRegisteredGradePoints;


        $student->resultSession = $academicSession;
        $student->resultSemester = $semester;
        $student->resultLevel = $level;

        $dir = public_path('uploads/files/result_card');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/files/result_card/'.$slug.time().'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        }
        $data = ['info' => $student, 'registeredCourses' => $courseRegs, 'cgpaData' => $cgpaData];

        $pdf = PDFDocument::loadView('pdf.resultCard', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateTransactionInvoice($session, $studentId, $paymentId, $type = 'all')
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $amountBilled = 0;
        $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;

        if ($paymentId > 0) {
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

        if (!$paymentId > 0) {
            $amountBilled = $transactions->sum('amount_payed');
        }

        if ($type == 'all') {
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

        $dir = public_path('uploads/files/invoice');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/files/invoice/'.$slug.time().'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        }
        $data = ['info' => $student, 'transactions' => $transactions];

        $pdf = PDFDocument::loadView('pdf.invoice', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateExitApplication($session, $studentId, $newExitId)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant', 'applicant.guardian', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' Exit Application '. $session .' '. $newExitId)));


        $exitApplication = StudentExit::find($newExitId);

        $dir = public_path('uploads/files/exit_applications');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/files/exit_applications/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        }

        $data = ['info' => $student, 'exitApplication' => $exitApplication];

        $pdf = PDFDocument::loadView('pdf.exitApplication', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateDownloadClearance($studentId)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('finalClearance', 'applicant')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' final clearance')));


        $clearance = $student->finalClearance;

        $backupDir = public_path('uploads/files/final_clearance');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $fileDirectory = 'uploads/files/final_clearance/'.$slug.'.pdf';
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        }

        $data = ['info' => $student, 'finalClearance' => $clearance];

        $pdf = PDFDocument::loadView('pdf.finalClearance', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateAntiDrugDeclaration($studentId)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' anti-drug declaration')));


        $dir = public_path('uploads/student/anti_drug_declarations');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/student/anti_drug_declarations/'.$slug.'.pdf';

        $data = ['info' => $student];

        $pdf = PDFDocument::loadView('pdf.antiDrugDeclaration', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }


    public function generateTranscript($studentId, $otherData = null)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $staff = null;
        if (!empty($otherData->staffId)) {
            $staff = Staff::find($otherData->staffId);
        }

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' transcripts')));

        $dir = public_path('uploads/files/transcript');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileDirectory = 'uploads/files/transcript/'.$slug.time().'.pdf';
        $courseReg = CourseRegistration::with('course')->where('student_id', $studentId)->get();


        $staffData = null;


        $data = ['info' => $student, 'registeredCourses' => $courseReg, 'staffData' => $staffData];

        $pdf = PDFDocument::loadView('pdf.transcript', $data)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    /**
     * Generates and downloads a PDF result broadsheet for students.
     *
     * @param Collection $students The collection of students to include in the broadsheet.
     * @param string $semester The current semester for which the broadsheet is being generated.
     * @param AcademicLevel $academicLevel The academic level of the students.
     * @param string $academicSession The academic session for which the broadsheet is being generated.
     * @param array $classifiedCourses The classified courses for the students.
     * @param Programme $programme The programme to which the students belong.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse The response to initiate the PDF download.
     */

    public function studentResultBroadSheet($students, $semester, $academicLevel, $academicSession, $classifiedCourses, $programme, $faculty, $department)
    {
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'pdf' => true,
        ];

        $dir = public_path('uploads/files/result_broad_sheets');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $semesterName = match((int)$semester) {
            1 => 'Harmattan',
            2 => 'Rain',
            3 => 'Summer',
            default => 'Unknown'
        };

        $fileName = $programme->name . ' ' . $academicLevel->level . ' ' . $semesterName . ' ' . $academicSession . ' resultBroadSheet.pdf';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $fileName))) . '.pdf';

        $pdf = PDFDocument::loadView('pdf.resultBroadSheet', [
            'students' => $students,
            'semester' => $semester,
            'semesterName' => $semesterName,
            'academicLevel' => $academicLevel,
            'academicSession' => $academicSession,
            'classifiedCourses' => $classifiedCourses,
            'programme' => $programme,
            'faculty' => $faculty,
            'department' => $department,
            'pdf' => true
        ]);

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOptions($options);

        return $pdf->download($slug);
    }

    public static function getexportAuthorizedStudent($courseId, $programmeCategory, $academicSession, $type)
    {
        $authorizedData = (new BaseController())->getAuthorizedStudents($courseId, $programmeCategory, $academicSession);
        $students = $authorizedData['students'];
        $course = Course::find($courseId);

        // CSV or Excel (unchanged)
        if ($type === 'csv' || $type === 'excel') {
            $headers = [
                'S/No',
                'Course Code',
                'Full Name',
                'Matric Number',
                'Sex',
                'Level',
                'Faculty',
                'Department',
                'Programme',
                'Attendance (%)'
            ];

            $exportArray = [];
            $exportArray[] = $headers;
            $counter = 1;
            foreach ($students as $entry) {
                $exportArray[] = [
                    'S/No' => $counter,
                    'Course Code' => $course->code,
                    'Full Name' => $entry['student']->applicant->lastname . ' ' . $entry['student']->applicant->othernames,
                    'Matric Number' => $entry['student']->matric_number ?? 'N/A',
                    'Sex' => $entry['student']->applicant->gender,
                    'Level' => $entry['student']->academicLevel->level,
                    'Faculty' => $entry['student']->faculty->name,
                    'Department' => $entry['student']->department->name,
                    'Programme' => $entry['student']->programme->award,
                    'Attendance (%)' => $entry['attendancePercentage'],
                ];
                $counter++;
            }

            $filename = $course->code.'_eligible_students_' . now()->format('dmY_His') . '.' . ($type === 'csv' ? 'csv' : 'xlsx');
            return Excel::download(new \App\Exports\AuthorizedStudentsExport($exportArray), $filename);
        }

        // PDF Format (updated)
        if ($type === 'pdf') {
            $options = [
                'isRemoteEnabled' => true,
                'encryption' => '128',
                'no_modify' => true,
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'pdf' => true,
            ];

            $dir = public_path('uploads/files/authorized_students');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $fileName = $course->name . ' ' . $programmeCategory . ' ' . $academicSession . ' eligible_students.pdf';
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $fileName))) . '.pdf';

            if (!$course) {
                abort(404, 'Course not found');
            }

            if (empty($students)) {
                abort(400, 'No students found to export');
            }

            if (!$academicSession) {
                abort(404, 'Academic Session not found');
            }
            if (!$programmeCategory) {
                abort(404, 'Programme Category not found');
            }
            // dd($students, $course, $academicSession, $programmeCategory);

            $pdf = PDFDocument::loadView('pdf.authorized-students', [
                'students' => $students,
                'course' => $course,
                'academicSession' => $academicSession,
                'programmeCategory' => $programmeCategory,
                'pdf' => true
            ]);

            $pdf->setPaper('a4', 'landscape');
            $pdf->setOptions($options);

            return $pdf->download($slug);
        }

        abort(404);
    }


}
