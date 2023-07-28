<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelAdviser extends Model
{
    use HasFactory;

    protected $fillable = [
        'programme_id',
        'level_id',
        'staff_id'
    ];
}
