<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'board_id',
        'board_user_id',
        'message',
    ];

    /**
     * Get the user that owns the BoardMessage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function board_user()
    {
        return $this->belongsTo(BoardUser::class, 'board_user_id');
    }

    /**
     * Get the board that owns the BoardMessage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id');
    }
}
