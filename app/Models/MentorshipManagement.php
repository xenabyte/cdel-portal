<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorshipManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staff_id',
        'student_id'
    ];
}
