<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseManagement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'transaction_id',
        'status'
    ];
}
