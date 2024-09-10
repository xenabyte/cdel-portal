<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_vacancy_id', 
        'career_id', 
        'student_id',
        'status',
        'appointment_letter'
    ];

    public function vacancy()
    {
        return $this->belongsTo(JobVacancy::class, 'job_vacancy_id');
    }

    public function jobApplicant()
    {
        return $this->belongsTo(Career::class, 'career_id');
    }

    public function workStudyApplicant()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    
}
