<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'department_id',
    ];

    /**
     * Get the staff that owns the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get all of the courseManagement for the CourseManagement
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseManagement()
    {
        return $this->hasMany(CourseManagement::class, 'course_id');
    }

    /**
     * Get all of the CoursePerProgrammePerAcademicSession for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function coursePerProgrammePerAcademicSession()
    {
        return $this->hasMany(CoursePerProgrammePerAcademicSession::class, 'course_id');
    }
}
