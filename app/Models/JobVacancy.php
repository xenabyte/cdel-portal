<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JobVacancy extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_JOB = 'Job Vacancy';
    const TYPE_WORKSTUDY = 'Work Study';

    protected $fillable = [
        'title', 
        'description', 
        'requirements', 
        'application_deadline', 
        'type',
        'status',
        'cgpa',
        'slug'
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

}
