<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PollOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'election_id',
        'option_text',
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}
