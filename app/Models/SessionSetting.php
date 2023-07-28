<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SessionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_session',
        'admission_session',
    ];
}
