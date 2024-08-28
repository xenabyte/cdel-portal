<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_vacancy_id', 
        'career_id', 
        'status',
    ];

    public function vacancy()
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function applicant()
    {
        return $this->belongsTo(Career::class);
    }
    
}
