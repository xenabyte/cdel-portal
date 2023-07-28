<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelAdviser extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'programme_id',
        'level_id',
        'staff_id'
    ];
}
