<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'description',
        'start_time',
        'end_time',
        'eligible_group',
        'show_result',
        'image',
        'slug',
    ];


    /**
     * The positions that belong to the Election
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function positions()
    {
        return $this->hasMany(Position::class);
    }


    /**
     * Get all of the poll options for the Election
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pollOptions()
    {
        return $this->hasMany(PollOption::class);
    }


    /**
     * Get all of the votes for the Election
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get all of the candidates for the Election
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'election_id', 'id');
    }
}
