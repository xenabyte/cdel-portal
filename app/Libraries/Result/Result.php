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


use Log;


class Result
{
    public static function processResult(UploadedFile $file, $courseId, $type, $academicSession)
    {
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $row) {
            $matricNumber = $row['Matric No'];
            $courseCodeMain = $row['Course Code'];
            $testScore = $row['Test Score'];
            $examScore = $row['Exam Score'];
            $testScore = floatval($testScore);
            $examScore = floatval($examScore);
            $testScore =  number_format($testScore, 2);
            $examScore =  number_format($examScore, 2);

            $student = Student::with('applicant')->where('matric_number', $matricNumber)->first();
            if(!$student){
                Log::info("Student with ". $matricNumber ." didnt register for this course.");
                continue;
            }

            $studentId = $student->id;

            $course = Course::find($courseId);
            $courseCode = $course->code;

            if($courseCode != $courseCodeMain){
                Log::info("Result Uploaded is not for course: " . $courseCode);
                continue;
            }

            $studentRegistration = CourseRegistration::where([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'result_approval_id' => null,
                'academic_session' => $academicSession
            ])->first();

            if(!$studentRegistration){
                Log::info($student->applicant->lastname."  ".$student->applicant->othernames." course registration not found for ".$courseCode." @ ".$academicSession);
                continue;
            }

            if(strtolower($type) == 'both'){
                $studentRegistration->ca_score = $testScore;
                $studentRegistration->exam_score = $examScore;
            }elseif(strtolower($type) == 'exam'){
                $testScore = $studentRegistration->ca_score;
                $studentRegistration->ca_score = $testScore;
                $studentRegistration->exam_score = $examScore;
            }else{
                $examScore = $studentRegistration->exam_score;
                $studentRegistration->test_score = $testScore;
            }


            $totalScore = 0;
            if($examScore > 0 && strtolower($type) != 'test'){
                $totalScore = $testScore + $examScore;
            }

            if(strtolower($type) == 'both'){
                $totalScore = $testScore + $examScore;
            }
            

            if($totalScore > 0 && $totalScore > 100){
                alert()->success('Oops', 'Total score is greater than 100.')->persistent('Close');
                return redirect()->back();
            }
    
            if($totalScore > 0){
                $grading = GradeScale::computeGrade($totalScore);
                $grade = $grading->grade;
                $points = $grading->point;
        
                $courseCode = $studentRegistration->course_code;
        
                if (strpos($courseCode, 'NSC') !== false && $student->programme_id == 15) {
                    if($totalScore < 50){
                        $grade = 'F';
                        $points = 0;
                    }
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

    public static function processVocationResult(UploadedFile $file,  $globalSettings){
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $academicSession = $globalSettings->sessionSetting['academic_session'];
        $academicSession = '2023/2024';

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
                'result_approval_id' => null,
                'academic_session' => $academicSession
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