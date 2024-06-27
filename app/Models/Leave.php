<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Leave extends Model
{
    use Notifiable, SoftDeletes;

    protected $fillable = [
        'staff_id',
        'slug',
        'purpose',
        'start_date',
        'end_date',
        'days',
        'status',
        'destination_address',
        'assisting_staff_id',
        'assisting_staff_status',
        'hod_id',
        'hod_status',
        'hod_comment',
        'dean_id',
        'dean_status',
        'dean_comment',
        'hr_id',
        'hr_status',
        'hr_comment',
        'registrar_id',
        'registrar_status',
        'registrar_comment',
        'registrar_approval_date',
        'vc_id',
        'vc_status',
        'vc_comment',
        'vc_approval_date',
        'status'
    ];

    /**
     * Get the staff that owns the Leave
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Get all of the attendance for the Leave
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendance()
    {
        return $this->hasMany(Attendance::class, 'leave_id');
    }
}
