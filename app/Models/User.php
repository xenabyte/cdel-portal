<?php

namespace App\Models;

use App\Notifications\UserResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;

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
        'partner_id',
        'password',
        'jamb_reg_no'
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
     * Custom method to authenticate a user based on email, password, and academic session.
     *
     * @param string $email
     * @param string $password
     * @param string $academicSession
     * @return \App\Models\User|null
     */
    public static function authenticateUser($email, $password, $academicSession)
    {
        // Retrieve the user based on the given email and academic session
        $user = self::where('email', $email)
        ->where('academic_session', $academicSession)
        ->first();

        // Check if the user exists and the password is correct
        if ($user && Hash::check($password, $user->password)) {
            return $user;
        }

        return null;
    }

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

    /**
     * Get the programme that owns the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id', 'id');
    }


    /**
     * Get all of the olevels for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function olevels()
    {
        return $this->hasMany(Olevel::class);
    }

    /**
     * Get the guardian associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    /**
     * Get the nok associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function nok()
    {
        return $this->belongsTo(NextOfKin::class, 'next_of_kin_id');
    }

    /**
     * Get the student associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Get all of the utmes for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function utmes()
    {
        return $this->hasMany(Utme::class);
    }
}
