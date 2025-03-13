<?php

namespace App\Libraries\Result;

use Illuminate\Http\UploadedFile;
use League\Csv\Reader;


use App\Models\Student;
use App\Models\Programme;
use App\Models\AcademicLevel;
use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Notification;
use App\Models\GradeScale;
use App\Models\ResultApprovalStatus;
use App\Models\DegreeClass;
use App\Models\Faculty;


use Log;


class Result
{
    public static function processResult(UploadedFile $file, $courseId, $type, $programmeCategoryId, $academicSession){
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();

        $type = strtolower($type); // Normalize type

        foreach ($records as $row) {
            if (!isset($row['Matric Number'], $row['Course Code'])) {
                Log::warning("Skipping row due to missing columns", ['row' => $row]);
                continue;
            }

            $matricNumber = trim($row['Matric Number']);
            $courseCodeMain = trim($row['Course Code']);

            $examScore = $testScore = 0;

            if ($type == 'exam' || $type == 'both') {
                $examScore = isset($row['Exam Score']) ? round(floatval($row['Exam Score']), 2) : 0;
            }
            if ($type == 'test' || $type == 'both') {
                $testScore = isset($row['Test Score']) ? round(floatval($row['Test Score']), 2) : 0;
            }

            $student = Student::with('applicant')->where('matric_number', $matricNumber)->first();
            if (!$student) {
                Log::info("Student not found", ['matricNumber' => $matricNumber]);
                continue;
            }

            $course = Course::find($courseId);
            if (!$course || $course->code !== $courseCodeMain) {
                Log::info("Course mismatch", ['expected' => $course->code ?? 'N/A', 'uploaded' => $courseCodeMain]);
                continue;
            }

            $studentRegistration = CourseRegistration::where([
                'student_id' => $student->id,
                'course_id' => $courseId,
                'result_approval_id' => null,
                'academic_session' => $academicSession,
                'programme_category_id' => $programmeCategoryId
            ])->first();

            if (!$studentRegistration) {
                Log::info("Registration not found", ['student' => $student->applicant->lastname . " " . $student->applicant->othernames, 'course' => $courseCode]);
                continue;
            }

            if ($type == 'both') {
                $studentRegistration->ca_score = $testScore;
                $studentRegistration->exam_score = $examScore;
            } elseif ($type == 'exam') {
                $studentRegistration->exam_score = $examScore;
            } else {
                $studentRegistration->ca_score = $testScore;
            }

            $totalScore = ($type == 'test') ? $testScore : ($testScore + $examScore);

            if ($totalScore > 100) {
                alert()->success('Oops', 'Total score is greater than 100.')->persistent('Close');
                return redirect()->back();
            }

            if ($totalScore > 0) {
                $grading = GradeScale::computeGrade($totalScore);
                $grade = $grading->grade;
                $points = $grading->point;

                $facultyId = $student->faculty_id;
                $isMedicineOrNursing = ($facultyId == 3 || $facultyId == 7);
                if ($isMedicineOrNursing && $student->department_id == $course->department_id && $totalScore < 50) {
                    $grade = 'F';
                    $points = 0;
                }

                $studentRegistration->total = $totalScore;
                $studentRegistration->grade = $grade;
                $studentRegistration->points = $studentRegistration->course_credit_unit * $points;
            }

            $studentRegistration->save();
        }

        return 'success';
    }

    public static function calculateCGPA($studentId){

        $allRegisteredCourses = CourseRegistration::where([
            'student_id' => $studentId,
            'result_approval_id' => ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED)
        ])->where('grade', '!=', null)->get();

        $allRegisteredCreditUnits =  $allRegisteredCourses->sum('course_credit_unit');
        $allRegisteredGradePoints = $allRegisteredCourses->sum('points');
        if(!$allRegisteredCreditUnits > 0) {
            return false;
        }
        $CGPA = floor($allRegisteredGradePoints / $allRegisteredCreditUnits * 100) / 100;
        $classGrade = DegreeClass::computeClass($CGPA);
        $class = $classGrade->degree_class;
        $standing = $classGrade->id > 4 ? 'Not in Good Standing(NGS)' : 'Good Standing(GS)'; 

        $student = Student::find($studentId);
        $student->cgpa = $CGPA;
        $student->degree_class = $class;
        $student->standing = $standing;
        $student->save();

        return true;
    }

    public static function processVocationResult(UploadedFile $file, $programmeCategoryId, $globalSettings){
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        // $academicSession = $globalSettings->sessionSetting['academic_session'];
        $academicSession = "2023/2024";

        foreach ($records as $row) {
            $email = $row['email'];
            $courseCode = $row['Course Code'];
            $examScore = $row['Exam Score'];
            $examScore = floatval($examScore);
            $examScore =  number_format($examScore, 2);

            $student = Student::with('applicant')->where('email', $email)->first();
            if(!$student){
                Log::info("Student with ". $email ." not found");
                continue;
            }

            if( $examScore < 1){
                continue;
            }
            
            $totalScore = round($examScore);

            if($totalScore > 100){
                Log::info($student->applicant->lastname.' '.$student->applicant->othernames ." total score is greater than 100.");
                continue;
            }

            $grading = GradeScale::computeGrade($totalScore);
            $grade = $grading->grade;
            $points = $grading->point;

            $course = Course::where('code', $courseCode)->first();

            if(!$course){
                Log::info("course not found: " . $courseCode);
                continue;
            }

            $studentId = $student->id;

            $studentRegistration = CourseRegistration::where([
                'student_id' => $studentId,
                'course_id' => $course->id,
                // 'academic_session' => $academicSession,
                // 'programme_category_id' => $programmeCategoryId,
            ])->first();

            if(!$studentRegistration){
                Log::info($student->applicant->lastname."  ".$student->applicant->othernames."Course registration not found for ".$courseCode." @ ".$academicSession);
                continue;
            }

            if ($studentRegistration) {
                $studentRegistration->exam_score = $examScore;
                $studentRegistration->total = $totalScore;
                $studentRegistration->grade = $grade;
                $studentRegistration->points = $points * $studentRegistration->course_credit_unit;
                $studentRegistration->save();
            }
        }

        return 'success';

    }
    
}