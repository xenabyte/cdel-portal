<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffRole extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_id',
        'staff_id',
    ];
}
