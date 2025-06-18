<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'election_id',
        'student_id',
        'candidate_id',
        'position_id',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
