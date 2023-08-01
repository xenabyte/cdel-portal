<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'dean_id',
        'sub_dean_id',
        'web_id',
        'slug'
    ];

     /**
     * Get all of the departments for the Faculty
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function departments()
    {
        return $this->hasMany(Department::class, 'faculty_id');
    }

    /**
     * Get all of the staffs for the Faculty
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function staffs()
    {
        return $this->hasMany(Staff::class, 'faculty_id');
    }

    /**
     * Get all of the students for the Faculty
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function students()
    {
        return $this->hasMany(Student::class, 'faculty_id');
    }

    /**
     * Get the dean that owns the Faculty
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dean()
    {
        return $this->belongsTo(Staff::class, 'dean_id', 'id');
    }
}
