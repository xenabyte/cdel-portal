<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Committee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'duties',
        'chairman_id',
        'secretary_id',
        'status',
    ];

    /**
     * Get the chairman that owns the Committee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chairman()
    {
        return $this->belongsTo(Staff::class, 'chairman_id', 'id');
    }

    /**
     * Get the secretary that owns the Committee
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function secretary()
    {
        return $this->belongsTo(Staff::class, 'secretary_id', 'id');
    }

    /**
     * Get all of the members for the Committee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members()
    {
        return $this->hasMany(CommitteeMember::class);
    }

    /**
     * Get all of the meetings for the Committee
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }
}
