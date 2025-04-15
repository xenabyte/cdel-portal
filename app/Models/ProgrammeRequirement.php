<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgrammeRequirement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'programme_id',
        'programme_category_id',
        'level_id', 
        'min_cgpa', 
        'additional_criteria'
    ];

    protected $casts = [
        'additional_criteria' => 'array',
    ];  

    /**
     * Get the programme that owns the ProgrammeRequirement
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id', 'id');
    }
}
