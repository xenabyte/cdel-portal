<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    const PAYMENT_TYPE_APPLICATION = 'Application Fee';
    const PAYMENT_TYPE_ACCEPTANCE = 'Acceptance Fee';
    const PAYMENT_TYPE_SCHOOL = 'School Fee';
    const PAYMENT_TYPE_GENERAL = 'General Fee';
    const PAYMENT_MODIFY_COURSE_REG = 'Course Reg';

    protected $fillable = [
        'title',
        'description',
        'slug',
        'type',
        'programme_id',
        'level_id'
    ];

    /**
     * Get all of the structures for the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function structures()
    {
        return $this->hasMany(PaymentStructure::class, 'payment_id', 'id');
    }

    /**
     * Get the programme that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }

    /**
     * Get the level that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(AcademicLevel::class, 'level_id');
    }
}
