<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursePerProgrammePerAcademicSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'programme_id',
        'course_id',
        'course_code',
        'semester',
        'credit_unit',
        'level_id',
        'status',
        'academic_session',
        'programme_category_id',
    ];

    /**
     * Get the course that owns the CoursePerProgrammePerAcademicSession
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the programme that owns the CoursePerProgrammePerAcademicSession
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }

    /**
     * Get all of the students for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function students()
    {
        return $this->hasManyThrough(Student::class, CourseRegistration::class);
    }

    /**
     * Get the level that owns the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(AcademicLevel::class, 'level_id');
    }

    /**
     * Get all of the registrations for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function registrations()
    {
        return $this->hasMany(CourseRegistration::class, 'programme_course_id', 'id');
    }

    /**
     * Get the programmeCategory that owns the CourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programmeCategory()
    {
        return $this->belongsTo(ProgrammeCategory::class, 'programme_category_id');
    }
}
