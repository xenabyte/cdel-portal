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
        'level_id',
        'employment_letter',
        'slug'
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get the jobLevel that owns the JobVacancy
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function jobLevel()
    {
        return $this->belongsTo(JobLevel::class, 'level_id', 'id');
    }

}
