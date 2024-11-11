<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardUser extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name', 
        'email', 
        'password'
    ];

    /**
     * Get all of the messages for the BoardUser
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
        return $this->hasMany(BoardMessage::class, 'board_user_id', 'id');
    }
}
