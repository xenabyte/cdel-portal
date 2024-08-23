<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LectureAttendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_lecture_id',
        'student_id',
        'status',
    ]; 
    
    /**
     * Get the courseLecture that owns the LectureAttendance
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseLecture()
    {
        return $this->belongsTo(User::class, 'foreign_key', 'other_key');
    }
}
