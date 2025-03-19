<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentSuspension extends Model
{
    use HasFactory, SoftDeletes;

    use HasFactory;

    protected $fillable = [
        'slug',
        'student_id',
        'reason', 
        'start_date', 
        'end_date', 
        'file',
        'status',
        'academic_session',
        'transaction_id',
        'court_affidavit',
        'undertaking_letter',
        'traditional_ruler_reference',
        'ps_reference',
        'admin_comment',
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
