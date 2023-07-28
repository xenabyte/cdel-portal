<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'course_id',
        'ca_score',
        'exam_score',
        'total',
        'grade',
        'points',
        'academic_session',
        'level',
        'result_approval_id',
        'status'
    ];
}
