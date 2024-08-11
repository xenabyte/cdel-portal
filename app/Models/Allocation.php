<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Allocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'room_id',
        'bed_id',
        'academic_session',
        'allocation_date',
        'release_date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * Get the bedSpace that owns the Allocation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bedSpace()
    {
        return $this->belongsTo(RoomBedSpace::class, 'bed_id', 'id');
    }
}
