<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JobVacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 
        'description', 
        'requirements', 
        'application_deadline', 
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

}
