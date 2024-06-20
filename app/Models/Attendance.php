<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'leave_id',
        'staff_id',
        'month',
        'year',
        'date',
        'clock_in',
        'clock_out',
        'status'
    ];

    /**
     * Get the staff that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
    
    /**
     * Get the leave that owns the Attendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leave()
    {
        return $this->belongsTo(Leave::class, 'leave_id');
    }

}
