<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'movement_type',  // entry or exit
        'movement_time',
        'reason',
        'approved_by',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}

