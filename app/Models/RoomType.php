<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class RoomType extends Model
{
    const ROOM_GOLD = 'Gold';
    const ROOM_SILVER = 'Silver';

    const EAST_CAMPUS = 'East';
    const WEST_CAMPUS = 'West';

    const GENDER_MALE = 'Male';
    const GENDER_FEMALE = 'Female';

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'capacity',
        'amount',
        'campus',
        'gender',
    ];

    public static function getTypePerCampus ($name, $campus) {
        if($roomType = self::where('name', $name)->where('campus', $campus)->first()) {
            return $roomType->id;
        }
        return null;
    }
}
