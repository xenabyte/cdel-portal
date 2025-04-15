<?php

namespace App\Models;

use App\Notifications\StudentResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\StudentExpulsion;
use App\Models\StudentSuspension;
use App\Models\ResultApprovalStatus;
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

   
    /**
     * Get all of the studentSuspensions for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suspensions()
    {
        return $this->hasMany(StudentSuspension::class, 'student_id', 'id');
    }



    /**
     * Get all of the studentExpulsions for the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
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

    /**
     * Get the student's semester GPA records.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function semesterWithGPA(){
        return $this->hasMany(StudentSemesterGPA::class, 'student_id');
    }


    /**
     * Determine if a student can be promoted to the next level.
     *
     * This method checks if the student has met the promotion requirements for their current level.
     * The requirements are as follows:
     * 1. The student's CGPA must be at or above the minimum required CGPA.
     * 2. The student must not have any carry overs if the programme requires no carry overs.
     *
     * @return array An array containing a boolean 'status' key and an array 'reasons' key.
     *               The 'status' key is true if the student can be promoted and false otherwise.
     *               The 'reasons' key contains an array of strings explaining why the student cannot be promoted.
     */
    public function canPromote(){
        $requirement = ProgrammeRequirement::where('programme_id', $this->programme_id)
            ->where('programme_category_id', $this->programme_category_id)
            ->where('level_id', $this->level_id)
            ->first();

        if (!$requirement) {
            return ['status' => true];
        }

        $reasons = [];

        // 1️⃣ Check CGPA
        if ($this->cgpa < $requirement->min_cgpa) {
            $reasons[] = "Your CGPA ({$this->cgpa}) is below the minimum required ({$requirement->min_cgpa}).";
        }

        // 2️⃣ Check for carry over if required
        $criteria = $requirement->additional_criteria;

        if (
            isset($criteria['no_carry_over']['enabled']) &&
            $criteria['no_carry_over']['enabled'] === true &&
            isset($criteria['no_carry_over']['applicable_from_level_id']) &&
            $this->level_id >= $criteria['no_carry_over']['applicable_from_level_id']
        ) {
            $failedCourses = $this->registeredCourses()
                ->where('grade', 'F')
                ->whereNull('re_reg')
                ->where('result_approval_id', ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))
                ->get();

            if ($failedCourses->isNotEmpty()) {
                $courseCodes = $failedCourses->pluck('course_code')->implode(', ');
                $reasons[] = "You have carry over(s) in the following courses: {$courseCodes}.";
            }
        }

        return [
            'status' => count($reasons) === 0,
            'reasons' => $reasons
        ];
    }


    /**
     * Get a list of programmes that the student is qualified to transfer into.
     *
     * This method evaluates the student's CGPA against the minimum CGPA required
     * for intra-transfer as specified in the programme requirements. It returns
     * a collection of programmes where the student meets or exceeds the minimum
     * CGPA for transfer eligibility.
     *
     * @return \Illuminate\Database\Eloquent\Collection  A collection of qualified transfer programmes.
     */

    public function getQualifiedTransferProgrammes(){
        
        $studentCgpa = $this->cgpa;
        $programmeCategoryId = $this->programme_category_id;

        // Get all programme requirements under the same programme category and level
        $requirements = ProgrammeRequirement::where('programme_category_id', $programmeCategoryId)
            ->where('level_id', $this->level_id)
            ->get();

        $qualifiedProgrammeIds = [];

        foreach ($requirements as $requirement) {
            $criteria = json_decode($requirement->additional_criteria, true);

            // Skip if intra_transfer not enabled
            if (!isset($criteria['intra_transfer']['enabled']) || !$criteria['intra_transfer']['enabled']) {
                continue;
            }

            $minCgpa = $criteria['intra_transfer']['min_cgpa'] ?? 0;

            if ($studentCgpa >= $minCgpa) {
                $qualifiedProgrammeIds[] = $requirement->programme_id;
            }
        }

        // Ensure no duplicates
        $uniqueProgrammeIds = array_unique($qualifiedProgrammeIds);

        return Programme::whereIn('id', $uniqueProgrammeIds)->get();
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
