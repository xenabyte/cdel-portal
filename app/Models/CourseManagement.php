<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseManagement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'staff_id',
        'academic_session',
        'status',
        'passcode',
        'programme_category_id'
    ];


    /**
     * Get the course that owns the CourseManagement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the staff that owns the CourseManagement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

}
