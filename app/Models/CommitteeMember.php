<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CommitteeMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staff_id',
        'committee_id',
        'status',
    ];

    /**
     * Get the staff that owns the CommitteeMember
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }
}
