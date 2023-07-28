<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'programme_id',
        'code',
        'name',
        'semester',
        'credit_unit',
        'level_id',
        'staff_id',
        'status'
    ];
}
