<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgrammeCreditLoad extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'programme_id',
        'max_credit',
        'min_credit',
    ];
}
