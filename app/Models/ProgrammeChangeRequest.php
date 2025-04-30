<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgrammeChangeRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug',
        'student_id',
        'old_programme_id',
        'new_programme_id',
        'reason',
        'status',
        'current_stage',
        'transaction_id',
        'academic_session',
    
        'old_programme_hod_id',
        'old_programme_dean_id',
        'new_programme_hod_id',
        'new_programme_dean_id',
        'dap_id',
        'registrar_id',
    
        'hod_old_approved_at',
        'dean_old_approved_at',
        'hod_new_approved_at',
        'dean_new_approved_at',
        'dap_approved_at',
        'registrar_approved_at',
    
        'rejection_reason',
    ];

    protected $casts = [
        'hod_old_approved_at' => 'datetime',
        'dean_old_approved_at' => 'datetime',
        'hod_new_approved_at' => 'datetime',
        'dean_new_approved_at' => 'datetime',
        'dap_approved_at' => 'datetime',
        'registrar_approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function newProgramme()
    {
        return $this->belongsTo(Programme::class, 'new_programme_id');
    }

    public function oldProgramme()
    {
        return $this->belongsTo(Programme::class, 'old_programme_id');
    }

    public function oldHod()
    {
        return $this->belongsTo(Staff::class, 'old_programme_hod_id');
    }

    public function newHod()
    {
        return $this->belongsTo(Staff::class, 'new_programme_hod_id');
    }

    public function oldDean()
    {
        return $this->belongsTo(Staff::class, 'old_programme_dean_id');
    }

    public function newDean()
    {
        return $this->belongsTo(Staff::class, 'new_programme_dean_id');
    }

    public function dap()
    {
        return $this->belongsTo(Staff::class, 'dap_id');
    }

    public function registrar()
    {
        return $this->belongsTo(Staff::class, 'registrar_id');
    }

}
