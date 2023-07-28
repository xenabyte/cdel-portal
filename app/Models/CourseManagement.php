<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'transaction_id',
        'status'
    ];
}
