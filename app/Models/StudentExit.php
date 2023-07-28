<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentExit extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exit_date',
        'return_date',
        'purpose',
        'exited_at',
        'return_at',
        'status',
        'is_dap_approved',
        'is_dap_approved_date',
        'is_registrar_approved',
        'is_registrar_approved_date',
        'is_guardian_approved',
        'is_guardian_approved_date',
    ];
}
