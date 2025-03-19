<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentExpulsion extends Model
{
    use HasFactory, SoftDeletes;

    use HasFactory;

    protected $fillable = [
        'student_id', 
        'reason', 
        'start_date',
        'academic_session'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Check if suspension is active
    public function isActive()
    {
        return is_null($this->end_date) || now()->lt($this->end_date);
    }
}
