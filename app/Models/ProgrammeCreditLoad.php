<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgrammeCreditLoad extends Model
{
    use HasFactory;

    protected $fillable = [
        'programme_id',
        'max_credit',
        'min_credit',
    ];
}
