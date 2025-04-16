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
    public function semestersWithGPA(){
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
            return [
                'promotion' => ['status' => true],
                'professional_exam' => ['status' => true]
            ];
        }

        $criteria = $requirement->additional_criteria;
        $reasons = [];
        $examRejection = null;

        // 1️⃣ Check CGPA for promotion
        if ($this->cgpa < $requirement->min_cgpa) {
            $reasons[] = "Your CGPA ({$this->cgpa}) is below the minimum required ({$requirement->min_cgpa}).";
        }

        // 2️⃣ Check for carry-overs
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
                $type = $criteria['no_carry_over']['type'] ?? null;

                if ($type === 'promotion') {
                    $reasons[] = "You have carry over(s) in the following courses: {$courseCodes}. You are not eligible for promotion.";
                } elseif ($type === 'professional_exam') {
                    $examRejection = "You are not eligible to write the professional exam due to carry-overs in: {$courseCodes}.";
                }
            }
        }

        return [
            'promotion' => [
                'status' => count($reasons) === 0,
                'reasons' => $reasons
            ],
            'professional_exam' => [
                'status' => $examRejection === null,
                'message' => $examRejection
            ]
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


    public function getAcademicAdvisory(){
        $advisory = [
            'promotion_eligible' => true,
            'promotion_message' => '',
            'professional_exam_eligible' => true,
            'professional_exam_message' => '',
            'transfer_options' => [],
            'failed_courses' => [],
            'advisory_notes' => [],
            'graduation_ready' => false,
            'graduation_message' => '',
            'trajectory_analysis' => [
                'cgpa_trend' => 'unknown',
                'academic_risk' => null,
                'strengths' => [],
                'weaknesses' => [],
                'tips' => [],
            ]
        ];

        // === Promotion check
        $promotionCheck = $this->canPromote(); // assumed to return array with both promotion and professional_exam

        // Handle promotion
        if (!$promotionCheck['promotion']['status']) {
            $advisory['promotion_eligible'] = false;
            $advisory['promotion_message'] = implode("; ", $promotionCheck['promotion']['reasons']);
            $advisory['advisory_notes'][] = $advisory['promotion_message'];
        } else {
            $advisory['promotion_message'] = "You are eligible for promotion.";
        }

        // Handle professional exam eligibility
        $advisory['professional_exam_eligible'] = $promotionCheck['professional_exam']['status'];
        $advisory['professional_exam_message'] = $promotionCheck['professional_exam']['status']
            ? "You are eligible to take the professional exam."
            : implode("; ", $promotionCheck['professional_exam']['reasons']);

        if (!$promotionCheck['professional_exam']['status']) {
            $advisory['advisory_notes'][] = $advisory['professional_exam_message'];
        }

        // === Failed Courses
        $failedCourses = $this->registeredCourses()
            ->where('grade', 'F')
            ->whereNull('re_reg')
            ->where('result_approval_id', ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))
            ->with('course')
            ->get();

        if ($failedCourses->count() > 0) {
            $advisory['failed_courses'] = $failedCourses->pluck('course.code')->toArray();
            $advisory['advisory_notes'][] = "You have failed courses: " . implode(', ', $advisory['failed_courses']);
        }

        // === Transfer options
        $transferProgrammes = $this->getQualifiedTransferProgrammes();
        if ($transferProgrammes->count() > 0) {
            $advisory['transfer_options'] = $transferProgrammes->pluck('name')->toArray();
            $advisory['advisory_notes'][] = "You're eligible to transfer to: " . implode(', ', $advisory['transfer_options']);
        }

        // === Graduation readiness
        if ($this->level_id >= $this->programme->duration) {
            $advisory['graduation_ready'] = false;
            $advisory['graduation_message'] = "Graduation readiness check not yet implemented.";
        }

        // === CGPA Trend Analysis
        $gpas = $this->semestersWithGPA()->orderBy('level')->pluck('gpa')->toArray();

        if (count($gpas) >= 2) {
            $first = $gpas[0];
            $last = end($gpas);
            if ($last > $first) $trend = 'Upward';
            elseif ($last < $first) $trend = 'Downward';
            else $trend = 'Flat';
            $advisory['trajectory_analysis']['cgpa_trend'] = $trend;
        }

        // === Academic Risk Level
        $requirement = ProgrammeRequirement::where('programme_id', $this->programme_id)
        ->where('programme_category_id', $this->programme_category_id)
        ->where('level_id', $this->level_id)
        ->first();

        if ($requirement) {
            $minCgpa = $requirement->min_cgpa;

            if ($this->cgpa < 1.5) {
                $advisory['trajectory_analysis']['academic_risk'] = 'High risk of withdrawal';
            } elseif ($this->cgpa < $minCgpa) {
                $advisory['trajectory_analysis']['academic_risk'] = 'At risk of not meeting promotion criteria';
            } elseif ($this->cgpa < 2.5) {
                $advisory['trajectory_analysis']['academic_risk'] = 'Needs improvement';
            }
        } else {
            // fallback if no requirement data found
            if ($this->cgpa < 1.5) {
                $advisory['trajectory_analysis']['academic_risk'] = 'High risk of withdrawal';
            } elseif ($this->cgpa < 2.0) {
                $advisory['trajectory_analysis']['academic_risk'] = 'At risk of probation';
            } elseif ($this->cgpa < 2.5) {
                $advisory['trajectory_analysis']['academic_risk'] = 'Needs improvement';
            }
        }

        // === Strengths / Weaknesses
        $courseResults = $this->registeredCourses()
            ->whereNotNull('grade')
            ->with('course')
            ->get()
            ->groupBy(function ($reg) {
                return explode(' ', $reg->course->code)[0]; // e.g., "CSC" from "CSC 201"
            });

        foreach ($courseResults as $prefix => $regs) {
            $average = $regs->avg('total');
            if ($average >= 60) {
                $advisory['trajectory_analysis']['strengths'][] = $prefix;
            } elseif ($average < 45) {
                $advisory['trajectory_analysis']['weaknesses'][] = $prefix;
            }
        }

        // === Tips
        if ($advisory['trajectory_analysis']['cgpa_trend'] === 'downward') {
            $advisory['trajectory_analysis']['tips'][] = "Your CGPA is declining. Consider seeking tutoring.";
        }

        if (count($advisory['trajectory_analysis']['strengths']) > 0) {
            $advisory['trajectory_analysis']['tips'][] = "You perform well in: " . implode(', ', $advisory['trajectory_analysis']['strengths']) . ". Focus on excelling there.";
        }

        if (count($advisory['trajectory_analysis']['weaknesses']) > 0) {
            $advisory['trajectory_analysis']['tips'][] = "Consider revisiting your understanding in: " . implode(', ', $advisory['trajectory_analysis']['weaknesses']) . ".";
        }

        return $advisory;
    }
}
