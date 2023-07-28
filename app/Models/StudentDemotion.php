<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentDemotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'old_level_id',
        'new_level_id',
        'old_programme_id',
        'new_programme_id',
        'reason',
        'academic_session',
        'is_approved',
    ];
}
