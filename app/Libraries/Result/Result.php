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
            $totalScore = $testScore + $examScore;
            $grading = GradeScale::computeGrade($totalScore);
            $grade = $grading->grade;
            $points = $grading->point;

            $student = Student::where('matric_number', $matricNumber)->first();
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
                $studentRegistration->points = $points;
                $studentRegistration->save();
            }
        }

        return true;
    }
}