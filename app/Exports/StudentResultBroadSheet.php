<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class StudentResultBroadSheet implements FromView
{
    public $students;
    public $semester;
    public $academicLevel;
    public $academicSession;
    public $classifiedCourses;
    public $programme;

    public function __construct($students, $semester, $academicLevel, $academicSession, $classifiedCourses, $programme)
    {
        $this->students = $students;
        $this->semester = $semester;
        $this->academicLevel = $academicLevel;
        $this->academicSession = $academicSession;
        $this->classifiedCourses = $classifiedCourses;
        $this->programme = $programme;
    }

    public function view(): View
    {
        return view('exports.resultBroadSheet', [
            'students' => $this->students,
            'semester' => $this->semester,
            'academicLevel' => $this->academicLevel,
            'academicSession' => $this->academicSession,
            'classifiedCourses' => $this->classifiedCourses,
            'programme' => $this->programme
        ]);
    }
}
