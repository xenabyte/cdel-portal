<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'faculty_id',
        'hod_id',
        'web_id',
        'slug'
    ];

     /**
     * Get all of the programmes for the Department
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function programmes()
    {
        return $this->hasMany(Programme::class, 'department_id');
    }

    /**
     * Get the facl that owns the Department
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function faculty()
    {
        return $this->belongsTo(Faculty::class, 'faculty_id');
    }

    /**
     * Get all of the students for the Department
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'department_id');
    }
}
