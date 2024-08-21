<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class FinalClearance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'experience',
        'hod_id',
        'hod_status',
        'hod_comment',
        'hod_approval_date',
        'dean_id',
        'dean_status',
        'dean_comment',
        'dean_approval_date',
        'student_care_dean_id',
        'student_care_dean_status',
        'student_care_dean_comment',
        'student_care_dean_approval_date',
        'registrar_id',
        'registrar_status',
        'registrar_comment',
        'registrar_approval_date',
        'bursary_id',
        'bursary_status',
        'bursary_comment',
        'bursary_approval_date',
        'library_id',
        'library_status',
        'library_comment',
        'library_approval_date',
        'file',
        'status'
    ];

    /**
     * Get the staff that owns the Clearance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the Librarian that owns the Clearance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function librarian()
    {
        return $this->belongsTo(Staff::class, 'library_id');
    }

    /**
     * Get the hod that owns the Clearance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hod()
    {
        return $this->belongsTo(Staff::class, 'hod_id');
    }

    /**
     * Get the dean that owns the Clearance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dean()
    {
        return $this->belongsTo(Staff::class, 'dean_id');
    }

    /**
     * Get the bursary that owns the Clearance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bursary()
    {
        return $this->belongsTo(Staff::class, 'bursary_id');
    }

    /**
     * Get the registrar that owns the Clearance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function registrar()
    {
        return $this->belongsTo(Staff::class, 'registrar_id');
    }

    /**
     * Get the viceChancellor that owns the Clearance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student_care_dean()
    {
        return $this->belongsTo(Staff::class, 'student_care_dean_id');
    }


}
