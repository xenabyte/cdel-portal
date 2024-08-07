<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'academic_session',
        'admission_session',
        'application_session',
        'resumption_date',
        'registrar_signature',
        'registrar_name',
        'school_fee_status',
        'accomondation_booking_status',
        'campus_wide_message',
    ];
}
