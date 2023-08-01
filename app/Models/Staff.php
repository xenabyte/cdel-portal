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
        'tau_staff_id', 
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

    
}
