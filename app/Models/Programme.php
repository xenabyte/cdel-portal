<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'award',
        'duration',
        'max_duration',
        'department_id',
    ];
}
