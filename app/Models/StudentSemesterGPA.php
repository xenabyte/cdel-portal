<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StudentSemesterGPA extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id', 
        'level_id',
        'session', 
        'semester', 
        'gpa'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
