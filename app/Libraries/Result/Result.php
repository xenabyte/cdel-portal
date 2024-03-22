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



class Result
{
    public static function processResult(UploadedFile $file, $courseId, $globalSettings)
    {
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $row) {
            $matricNumber = $row['Matric No'];
            $testScore = $row['Test Score'];
            $examScore = $row['Exam Score'];
            $testScore = (int)$testScore;
            $examScore = (int)$examScore;
            
            $totalScore = round($testScore + $examScore);
            $grading = GradeScale::computeGrade($totalScore);
            $grade = $grading->grade;
            $points = $grading->point;

            $course = Course::find($courseId);
            $courseCode = $course->code;

            if (strpos($courseCode, 'NSC') !== false) {
                if($totalScore < 50){
                    $grade = 'F';
                    $points = 0;
                }
            }

            $student = Student::with('applicant')->where('matric_number', $matricNumber)->first();
            if(!$student){
                return "Student with ". $matricNumber ." did register for this course.";
            }

            if($testScore > 30){
                return $student->applicant->lastname.' '.$student->applicant->othernames ." tests score is greater than 30.";
            }

            if($examScore > 70){
                return $student->applicant->lastname.' '.$student->applicant->othernames ." examination score is greater than 70.";
            }

            $studentId = $student->id;

            $studentRegistration = CourseRegistration::where([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'result_approval_id' => null,
            ])->first();


            if ($studentRegistration) {
                $studentRegistration->ca_score = $testScore;
                $studentRegistration->exam_score = $examScore;
                $studentRegistration->total = $totalScore;
                $studentRegistration->grade = $grade;
                $studentRegistration->points = $points * $studentRegistration->course_credit_unit;
                $studentRegistration->save();
            }
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
        $CGPA = number_format($allRegisteredGradePoints / $allRegisteredCreditUnits, 2);
        $classGrade = DegreeClass::computeClass($CGPA);
        $class = $classGrade->degree_class;
        $standing = $classGrade->id > 3? 'Not in Good Standing(NGS)' : 'Good Standing(GS)'; 

        $student = Student::find($studentId);
        $student->cgpa = $CGPA;
        $student->degree_class = $class;
        $student->standing = $standing;
        $student->save();

        return true;
    }
}