<?php

namespace App\Models;

use App\Notifications\StudentResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\StudentExpulsion;
use App\Models\StudentSuspension;
use Carbon\Carbon;

class Student extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'matric_number', 
        'email', 
        'password',
        'passcode',
        'programme_id',
        'faculty_id',
        'department_id',
        'is_active',
        'academic_session',
        'image',
        'level_id',
        'credit_load',
        'is_passed_out',
        'is_rusticated',
        'amount_balance',
        'entry_year',
        'max_graduating_level',
        'user_id',
        'partner_id',
        'admission_letter',
        'slug',
        'mentor_id',
        'degree_class',
        'standing',
        'cgpa',
        'onboard_status',
        'linkedIn',
        'dashboard_mode',
        'bandwidth_username',
        'onesignal_id ',
        'batch',
        'graduation_date',
        'graduation_session',
        'clearance_status',
        'one_signal_token',
        'programme_category_id',
        'signature',
        'anti_drug_status',
        'matriculation_status',
        'transcript',
        'academic_status',
        'center_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new StudentResetPassword($token));
    }

    /**
     * Get the programme that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }

    /**
     * Get the faculty that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Get the department that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get the level that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class, 'level_id');
    }

    /**
     * Get the applicant that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function applicant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the partner that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    /**
     * Get all of the registeredCourses for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function registeredCourses()
    {
        return $this->hasMany(CourseRegistration::class, 'student_id');
    }

    /**
     * Get all of the courseRegistrationDocument for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseRegistrationDocument()
    {
        return $this->hasMany(StudentCourseRegistration::class, 'student_id');
    }

    /**
     * Get all of the transactions for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'student_id');
    }

    /**
     * Get all of the notifications for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'student_id');
    }

    /**
     * Get the mentor that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mentor()
    {
        return $this->belongsTo(Staff::class, 'mentor_id');
    }

    /**
     * Get all of the exitApplications for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exitApplications()
    {
        return $this->hasMany(StudentExit::class, 'student_id');
    }

    /**
     * Get the finalClearance associated with the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function finalClearance()
    {
        return $this->hasOne(FinalClearance::class, 'student_id', 'id');
    }

    /**
     * Get all of the applications for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'student_id', 'id');
    }

    /**
     * Get all of the hostelAllocations for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hostelAllocations()
    {
        return $this->hasMany(Allocations::class, 'student_id', 'id');
    }

    /**
     * Get the currentHostelAllocation associated with the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentHostelAllocation()
    {
        return $this->hasOne(Allocation::class, 'bed_id', 'id')
                    ->whereNull('release_date');
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

   
    public function suspensions()
    {
        return $this->hasMany(StudentSuspension::class, 'student_id', 'id');
    }


    public function expulsions()
    {
        return $this->hasMany(StudentExpulsion::class, 'student_id', 'id');
    }

    /**
     * Get the studyCenter that owns the student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function studyCenter()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }


    public function canPromote() {
        $requirement = ProgrammeRequirement::where('programme_id', $this->programme_id)
            ->where('programme_category_id', $this->programme_category_id)
            ->where('level_id', $this->level_id)
            ->first();

        if (!$requirement) {
            return true; 
        }

        // Check if CGPA meets the minimum requirement
        if ($this->cgpa < $requirement->min_cgpa) {
            return false;
        }

        return true;
    }

    /**
     * Check if a student is expelled.
     *
     * @param int $studentId
     * @return bool
     */
    public function isExpelled()
    {
        return StudentExpulsion::where('student_id', $this->id)->exists();
    }

    /**
     * Check if a student is currently suspended.
     *
     * @param int $studentId
     * @return bool
     */
    public function isSuspended()
    {
        return StudentSuspension::where('student_id', $this->id)
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>=', Carbon::now());
            })
            ->exists();
    }
}
