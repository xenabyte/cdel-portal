<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class RoomBedSpace extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_id',
        'space',
    ];

    /**
     * Get the room that owns the RoomBedSpace
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

     /**
     * Get the current allocation for this bed space
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currentAllocation()
    {
        return $this->hasOne(Allocation::class, 'bed_id', 'id')
                    ->whereNull('release_date');
    }
}
