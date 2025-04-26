<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CourseLecture extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_session',
        'course_id',
        'topic',
        'date',
        'duration',
        'notes',
        'video_link',
        'slug',
        'programme_category_id',
        'code'
    ];

    /**
     * Get all of the lectureAttendance for the CourseLecture
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lectureAttendance()
    {
        return $this->hasMany(LectureAttendance::class, 'course_lecture_id', 'id')->where('status', 1);
    }

    /**
     * Get the course that owns the CourseLecture
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
