<?php

namespace App\Models;

use App\Notifications\StaffResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lastname',
        'othernames',
        'staffId', 
        'email', 
        'password',
        'phone_number',
        'image',
        'faculty_id',
        'department_id',
        'dob',
        'nationality',
        'religion',
        'marital_status',
        'state',
        'lga',
        'gender',
        'address',
        'qualification',
        'department',
        'current_position',
        'description',
        'slug',
        'url',
        'title',
        'category',
        'referral_code'
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
        $this->notify(new StaffResetPassword($token));
    }

    /**
     * Get the faculty that owns the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Get the department that owns the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function acad_department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Get all of the courses for the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'staff_id');
    }

    /**
     * Get all of the notifications for the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'staff_id');
    }

    /**
     * Get all of the roles for the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffRoles()
    {
        return $this->hasMany(StaffRole::class, 'staff_id');
    }

    /**
     * Get all of the mentee for the Staff
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mentees()
    {
        return $this->hasMany(Student::class, 'mentor_id');
    }
    
    
}
