<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    const UNIT_REGISTRY = 'Registry';
    const UNIT_BURSARY = 'Bursary';
    const UNIT_STUDENT_CARE = 'Student Care Services';
    const UNIT_LIBRARY = 'Library';
    const UNIT_WORK_STUDY = 'Work Study';
    const UNIT_PPD = 'PPD Services';

    protected $fillable = [
        'name',
        'unit_head_id',
        'slug',
    ];

    /**
     * Get the unit_head that owns the Unit
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unit_head()
    {
        return $this->belongsTo(Staff::class, 'unit_head_id');
    }
}
