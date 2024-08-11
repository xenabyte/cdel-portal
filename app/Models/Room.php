<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Room extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'number',
        'type_id',
        'hostel_id',
    ];

    /**
     * Get the hostel that owns the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class, 'hostel_id');
    }

    /**
     * Get the type that owns the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(RoomType::class, 'type_id');
    }


    /**
     * Get all of the allocations for the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allocations()
    {
        return $this->hasMany(Allocation::class);
    }

    /**
     * Get all of the bedSpaces for the Room
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bedSpaces()
    {
        return $this->hasMany(RoomBedSpace::class, 'room_id');
    }
}
