<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'election_id',
        'position_id',
        'student_id',
        'manifesto',
        'photo',
    ];

    /**
     * Get the position that this candidate is running for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get the election that this candidate is running for.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    /**
     * Get the student that is running for office.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get all of the votes for the Candidate
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
    
}
