<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class WorkStudy extends Model
{
    use HasFactory, SoftDeletes;

    // Define the table associated with the model
    protected $table = 'work_studies';

    // Define the fillable properties for mass assignment
    protected $fillable = [
        'student_id', 
        'job_title', 
        'job_requirements',
        'job_description',
        'job_level_id', 
        'appointment_letter',
        'supervisor_name', 
        'status'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
