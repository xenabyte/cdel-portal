<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class StudentCourseRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'level_id',
        'academic_session',
        'file',
        'level_adviser_status',
        'hod_status',
        'level_adviser_id',
        'hod_id',
        'level_adviser_approved_date',
        'hod_approved_date',
        'programme_category_id',
    ];

    /**
     * Get the student that owns the StudentCourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the LevelAdviser that owns the StudentCourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function levelAdviser()
    {
        return $this->belongsTo(Staff::class, 'level_adviser_id', 'id');
    }

    /**
     * Get the hod that owns the StudentCourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hod()
    {
        return $this->belongsTo(Staff::class, 'hod_id', 'id');
    }

    /**
     * Get the programmeCategory that owns the StudentCourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programmeCategory()
    {
        return $this->belongsTo(ProgrammeCategory::class, 'programme_category_id');
    }
}
