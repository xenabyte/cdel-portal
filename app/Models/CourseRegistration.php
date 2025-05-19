<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Log;
class CourseRegistration extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'course_id',
        'course_credit_unit',
        'course_status',
        'course_code',
        'semester',
        'ca_score',
        'exam_score',
        'total',
        'grade',
        'points',
        'academic_session',
        'level_id',
        'result_approval_id',
        'programme_course_id',
        're_reg',
        'status',
        'programme_category_id',
    ];

    /**
     * Get the student that owns the CourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the course that owns the CourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the academicLevel that owns the CourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class, 'level_id');
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

    /**
     * Get all of the SummerRegistration for the CourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function SummerRegistration()
    {
        return $this->hasMany(SummerCourseRegistration::class, 'course_registration_id', 'id');
    }


    /**
     * Calculate the attendance percentage for this registration.
     *
     * @return float
     */
    public function attendancePercentage()
    {
        // Get all lectures for the course
        $totalLectures = CourseLecture::where('course_id', $this->course_id)
                                      ->where('academic_session', $this->academic_session)
                                      ->count();

        // Get the number of lectures the student attended
        $attendedLectures = LectureAttendance::whereIn('course_lecture_id', function ($query) {
            $query->select('id')
                  ->from('course_lectures')
                  ->where('course_id', $this->course_id)
                  ->where('academic_session', $this->academic_session);
        })
        ->where('student_id', $this->student_id)
        ->where('status', 1) // Assuming 1 means present
        ->count();        

        // Calculate the attendance percentage
        if ($totalLectures > 0) {
            return ($attendedLectures / $totalLectures) * 100;
        } else {
            return 0;
        }
    }
}
