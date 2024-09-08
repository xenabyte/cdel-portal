<?php

namespace App\Models;

use App\Notifications\CareerResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;


class Career extends Authenticatable
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
        'email', 
        'password',
        'phone_number',
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
        $this->notify(new CareerResetPassword($token));
    }

    /**
     * Get the profile associated with the Career
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function profile()
    {
        return $this->hasOne(CareerProfile::class, 'career_id', 'id');
    }

    /**
     * Get all of the notifications for the Career
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'career_id', 'id');
    }

    public function calculateProfileCompletion()
    {
        $percent = 1;
        $total = 8;

        if (!empty($this->lastname)) {
            $percent++;
        }
        if (!empty($this->profile)) {
            $percent++;
        }
        if (!empty($this->profile->biodata)) {
            $percent++;
        }
        if (!empty($this->profile->education_history)) {
            $percent++;
        }
        if (!empty($this->profile->professional_information)) {
            $percent++;
        }
        if (!empty($this->profile->publications)) {
            $percent++;
        }
        if (!empty($this->profile->cv_path)) {
            $percent++;
        }

        return round(($percent / $total) * 100);
    }

    /**
     * Get all of the applications for the Career
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'career_id', 'id');
    }
}
