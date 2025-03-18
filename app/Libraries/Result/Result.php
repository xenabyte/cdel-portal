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
use App\Models\ProgrammeCategory;


use Log;


class Result
{
    public static function processResult(UploadedFile $file, $courseId, $type, $programmeCategoryId, $academicSession)
    {
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();
        $type = strtolower($type);

        foreach ($records as $row) {
            $matricNumber = trim($row['Matric Number']);
            $courseCodeMain = trim($row['Course Code']);

            // Extract Scores
            $examScore = isset($row['Exam Score']) ? self::formatScore($row['Exam Score']) : 0;
            $testScore = isset($row['Test Score']) ? self::formatScore($row['Test Score']) : 0;

            
            $student = Student::with('applicant')->where('matric_number', $matricNumber)->first();
            if (!$student) {
                Log::info("Student {$matricNumber} not registered for this course.");
                continue;
            }

            $studentId = $student->id;
            $course = Course::find($courseId);
            if (!$course || $course->code !== $courseCodeMain) {
                Log::info("Uploaded result does not match course: {$courseCodeMain}");
                continue;
            }

            $studentRegistration = CourseRegistration::where([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'result_approval_id' => null,
                'academic_session' => $academicSession,
                'programme_category_id' => $programmeCategoryId
            ])->first();

            if (!$studentRegistration) {
                Log::info("{$student->applicant->lastname} {$student->applicant->othernames} not registered for {$course->code} @ {$academicSession}");
                continue;
            }

            if ($type == 'test') {
                $examScore = $studentRegistration->exam_score;
                $studentRegistration->ca_score = $testScore;
            }

            if($type == 'exam') {
                $testScore = $studentRegistration->ca_score;
                $studentRegistration->exam_score = $examScore;
            }

            if ($type == 'both') {
                $studentRegistration->ca_score = $testScore;
                $studentRegistration->exam_score = $examScore;
            }

            $totalScore = $testScore + $examScore;
            if ($totalScore > 100) {
                Log::warning("Total score for {$matricNumber} exceeds 100: {$totalScore}");
                continue;
            }

            // Compute Grade
            if ($totalScore > 0) {
                $grading = GradeScale::computeGrade($totalScore);
                $grade = $grading->grade;
                $points = $grading->point;

                $studentFaculty = Faculty::find($student->faculty_id);
                if (in_array($studentFaculty->id, [3, 7]) && $student->department_id == $course->department_id && $totalScore < 50) {
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

    /**
     * Helper function to format scores.
     */
    private static function formatScore($score)
    {
        return number_format(floatval($score), 2);
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

        $student = Student::find($studentId);
        $student->cgpa = $CGPA;
        $student->degree_class = $class;
        $student->save();


        // $standing = $classGrade->id > 4 ? 'Not in Good Standing(NGS)' : 'Good Standing(GS)'; 
        // $student->standing = $standing;


        return $CGPA;
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



    public static function getPreviousSemester($academicSession, $semester, $totalSemesters = 2)
    {

        [$startYear, $endYear] = explode('/', $academicSession);
        

        $startYear = (int) $startYear;
        $endYear = (int) $endYear;

        if ($semester > 1) {

            $previousSemester = $semester - 1;
            $previousAcademicSession = $academicSession;
        } else {

            $previousSemester = $totalSemesters;
            $previousAcademicSession = ($startYear - 1) . '/' . ($endYear - 1);
        }

        return [
            'academicSession' => $previousAcademicSession,
            'semester' => $previousSemester,
        ];
    }


    public static function getPreviousGPA($student, $academicSession, $semester)
    {

        $totalSemesters = 2;
        $programmeCategoryId = $student->programme_category_id;

        if($programmeCategoryId != ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::UNDERGRADUATE)){
            $totalSemesters = 3;
        }

        $studentId = $student->id;
        // Determine the previous academic session and semester
        $previousSemesterData = self::getPreviousSemester($academicSession, $semester, $totalSemesters);
        $previousAcademicSession = $previousSemesterData['academicSession'];
        $previousSemester = $previousSemesterData['semester'];
    
        // Fetch all course registrations for the previous session and semester
        $registrations = CourseRegistration::where('student_id', $studentId)
            ->where('academic_session', $previousAcademicSession)
            ->where('semester', $previousSemester)
            ->get();
    
        // If no records found, return null
        if ($registrations->isEmpty()) {
            return null;
        }
    
        // Compute total points and total credit units
        $totalPoints = $registrations->sum('point');
        $totalCreditUnits = $registrations->sum('course_credit_unit');
    
        // Avoid division by zero
        if ($totalCreditUnits == 0) {
            return 0;
        }
    
        // Calculate CGPA
        $gpa = $totalPoints / $totalCreditUnits;
    
        return round($gpa, 2); // Round to 2 decimal places
    }

    public static function getPresentGPA($student, $academicSession, $semester)
    {
        $studentId = $student->id;
        
        // Fetch all course registrations for the previous session and semester
        $registrations = CourseRegistration::where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('semester', $semester)
            ->get();
    
        // If no records found, return null
        if ($registrations->isEmpty()) {
            return 0;
        }
    
        // Compute total points and total credit units
        $totalPoints = $registrations->sum('point');
        $totalCreditUnits = $registrations->sum('course_credit_unit');
    
        // Avoid division by zero
        if ($totalCreditUnits == 0) {
            return 0;
        }
    
        // Calculate CGPA
        $gpa = $totalPoints / $totalCreditUnits;
    
        return round($gpa, 2); // Round to 2 decimal places
    }

    public static function checkProbation($student, $cgpa, $currentGPA, $previousGPA)
    {
        $status = "Good Stading";

        if ($cgpa < 1.50) {
            $status = 'Probation';
        }

        if ($student->level >= 200 && $cgpa < 1.50) {
            if ($currentGPA < 1.5 && $previousGPA < 1.5) {
                $status = 'Withdrawn';
            }
        }

        $student->academic_status = $status;
        $student->save();
        
        return $student->academic_status;
    }
    
}