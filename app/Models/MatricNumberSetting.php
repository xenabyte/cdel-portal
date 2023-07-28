<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatricNumberSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'programme_id',
        'last_number',
    ];
}
