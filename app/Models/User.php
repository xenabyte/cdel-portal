<?php

namespace App\Models;

use App\Notifications\UserResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug', 
        'application_number', 
        'passcode',
        'lastname',
        'othernames',
        'email',
        'phone_number',
        'image',
        'programme_id',
        'dob',
        'nationality',
        'religion',
        'marital_status',
        'state',
        'lga',
        'gender',
        'address',
        'sitting_no',
        'lga',
        'olevel_1',
        'olevel_2',
        'schools_attended',
        'status',
        'academic_session',
        'guardian_id',
        'next_of_kin_id',
        'partner_id'
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
        $this->notify(new UserResetPassword($token));
    }
}
