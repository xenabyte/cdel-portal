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
use App\Models\ProgrammeRequirement;


use Log;


class Result
{
    public static function processResult(UploadedFile $file, $courseId, $type, $programmeCategoryId, $academicSession, $isSummer = false){
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);
        $records = $csv->getRecords();
        $type = strtolower($type);

        foreach ($records as $row) {
            $matricNumber = trim($row['Matric Number']);
            $courseCodeMain = trim($row['Course Code']);

            $testScore = null;
            $examScore = null;

            if ($type == 'test' && isset($row['Test Score'])) {
                $testScore = self::formatScore($row['Test Score']);
            }

            if ($type == 'exam' && isset($row['Exam Score'])) {
                $examScore = self::formatScore($row['Exam Score']);
            }

            if ($type == 'both') {
                $testScore = isset($row['Test Score']) ? self::formatScore($row['Test Score']) : null;
                $examScore = isset($row['Exam Score']) ? self::formatScore($row['Exam Score']) : null;
            }

            $student = Student::with('applicant')->where('matric_number', $matricNumber)->first();
            if (!$student) {
                Log::info("Student {$matricNumber} not found.");
                continue;
            }

            $course = Course::find($courseId);
            if (!$course || $course->code !== $courseCodeMain) {
                Log::info("Course code mismatch for {$matricNumber}: expected {$course->code}, got {$courseCodeMain}");
                continue;
            }

            $query = CourseRegistration::where([
                'student_id' => $student->id,
                'course_id' => $courseId,
                'academic_session' => $academicSession,
                'programme_category_id' => $programmeCategoryId
            ]);

            if (!$isSummer) {
                $query->whereNull('result_approval_id');
            }

            $studentRegistration = $query->first();
            if (!$studentRegistration) {
                Log::info("{$student->applicant->lastname} {$student->applicant->othernames} not registered for {$course->code} @ {$academicSession}");
                continue;
            }

            // Format existing DB scores
            $existingTestScore = self::formatScore($studentRegistration->ca_score);
            $existingExamScore = self::formatScore($studentRegistration->exam_score);

            // Assign new scores based on upload type
            if ($type == 'test') {
                $studentRegistration->ca_score = $testScore;
                $examScore = $existingExamScore;
            } elseif ($type == 'exam') {
                $studentRegistration->exam_score = $examScore;
                $testScore = $existingTestScore;
            } elseif ($type == 'both') {
                $studentRegistration->ca_score = $testScore;
                $studentRegistration->exam_score = $examScore;
            }

            // Compute total score with ABS handling
            $totalScore = null;

            if (is_numeric($testScore) && is_numeric($examScore)) {
                $totalScore = floatval($testScore) + floatval($examScore);
            } elseif (!is_numeric($testScore) && is_numeric($examScore)) {
                $totalScore = floatval($examScore);
            } elseif (is_numeric($testScore) && !is_numeric($examScore)) {
                $totalScore = floatval($testScore);
            } else {
                $totalScore = 'ABS';
            }

            if ($totalScore !== 'ABS') {
                if ($totalScore > 100) {
                    Log::warning("Total score for {$matricNumber} exceeds 100: {$totalScore}");
                    continue;
                }

                $grading = GradeScale::computeGrade($totalScore);
                $grade = $grading->grade;
                $points = $grading->point;

                $requiredPassMark = self::getRequiredPassMark($student, $course->code);
                if ($totalScore < $requiredPassMark) {
                    $grade = 'F';
                    $points = 0;
                }

                $studentRegistration->total = $totalScore;
                $studentRegistration->grade = $grade;
                $studentRegistration->points = $studentRegistration->course_credit_unit * $points;
            } else {
                $studentRegistration->total = 'ABS';
                $grading = GradeScale::computeGrade('ABS');
                $studentRegistration->grade = $grading->grade;
                $studentRegistration->points = 0;
            }

            $studentRegistration->save();
        }

        return 'success';
    }

    /**
     * Helper function to format scores.
     */
    private static function formatScore($score){
        $score = trim(strtoupper((string)$score));

        if (in_array($score, ['ABS', '-', 'N/A', 'NA', 'NULL', ''])) {
            return 'ABS';
        }

        return is_numeric($score) ? number_format(floatval($score), 2, '.', '') : 'ABS';
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

    public static function processVocationResult(UploadedFile $file, $programmeCategoryId){
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting')->where('id', $programmeCategoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session;
        // $academicSession = "2023/2024";

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
        if ($totalCreditUnits < 1) {
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
            return null;
        }

        // Compute total points and total credit units
        $totalPoints = (int) $registrations->sum('points');
        $totalCreditUnits = (int) $registrations->sum('course_credit_unit');
    
        // Avoid division by zero
        if ($totalCreditUnits < 1) {
            return 0;
        }

        // Calculate CGPA
        $gpa = $totalPoints / $totalCreditUnits;
    
        return round($gpa, 2); // Round to 2 decimal places
    }

    public static function checkProbation($student, $semester, $cgpa, $currentGPA, $previousGPA)
    {
        $status = "Good Standing"; // Default status

        // Fetch student's programme
        $programme = Programme::find($student->programme_id);

        // Use 1.50 if programme->minimum_cgpa is null
        $probationCGPA = $programme ? (float) ($programme->minimum_cgpa ?? 1.50) : 1.50;

        // Probation check
        if ($cgpa < $probationCGPA) {
            $status = 'Probation';
        }

        // Withdrawal check for 200 level and above
        if ($semester == 2 && $student->level >= 200 && $cgpa < $probationCGPA) {
            $status = 'Withdrawn';
        }

        // Save and return academic status
        $student->academic_status = $status;
        $student->save();

        return $status;
    }


    public static function getRequiredPassMark($student, $courseCode){

        $requirement = ProgrammeRequirement::where('programme_id', $student->programme_id)->where('level_id', $student->level_id)->first();
        $requiredPassMark = 40;

        if ($requirement && $requirement->additional_criteria) {
            $additional = json_decode($requirement->additional_criteria, true);

            if (
                isset($additional['course_code_50_pass']['enabled']) &&
                $additional['course_code_50_pass']['enabled'] &&
                isset($additional['course_code_50_pass']['prefixes'])
            ) {
                foreach ($additional['course_code_50_pass']['prefixes'] as $prefix) {
                    if (stripos($courseCode, $prefix) === 0) {
                        return 50;
                    }
                }
            }
        }

        return $requiredPassMark;
    }
    
}