<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'image',
    ];
    
    /**
     * Get all of the board_messages for the Board
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function board_messages()
    {
        return $this->hasMany(BoardMessage::class, 'board_id');
    }
}
