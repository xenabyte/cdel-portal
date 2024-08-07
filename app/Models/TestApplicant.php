<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestApplicant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'slug', 
        'passcode',
        'lastname',
        'othernames',
        'email',
        'phone_number',
        'partner_id',
        'application_type',
        'referrer',
        'academic_session',
        'reference'
    ];


}
