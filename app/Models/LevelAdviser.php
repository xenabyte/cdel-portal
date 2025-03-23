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
        'programme_category_id',
        'level_id',
        'staff_id',
        'academic_session',
        'course_approval_status',
        'comment',
        'course_registration'
    ];

    /**
     * Get the staff that owns the LevelAdviser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Get the level that owns the LevelAdviser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(AcademicLevel::class, 'level_id');
    }

    /**
     * Get the programme that owns the LevelAdviser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }

    /**
     * Get the programme_category that owns the LevelAdviser
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programmeCategory()
    {
        return $this->belongsTo(ProgrammeCategory::class, 'programme_category_id');
    }

    public function studentCount()
    {
        return Student::where('programme_category_id', $this->programme_category_id)
            ->where('level_id', $this->level_id)
            ->where('programme_id', $this->programme_id)
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', 'false')
            ->count();
    }
}