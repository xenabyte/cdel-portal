<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Programme extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'award',
        'duration',
        'max_duration',
        'department_id',
        'web_id',
        'slug',
        'code',
        'code_number',
        'matric_last_number'
    ];

    /**
     * Get all of the courses for the Programme
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'programme_id');
    }

    public function firstSemesterCourses(){
        return $this->hasMany(Course::class, 'programme_id')->where('course_semester', 1);
    }

    public function secondSemesterCourses(){
        return $this->hasMany(Course::class, 'programme_id')->where('course_semester', 2);
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get all of the payments for the Programme
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function programmePayments()
    {
        return $this->hasMany(Payment::class, 'programme_id', 'id')->where('type', '!=', 'General Fee');
    }

    /**
     * Get all of the students for the Programme
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'programme_id');
    }

    public function programmeCategory(){
        return $this->belongsTo(ProgrammeCategory::class, 'category_id');
    }
}
