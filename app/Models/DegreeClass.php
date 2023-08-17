<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class DegreeClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'point_to',
        'point_from',
        'degree_class',
        'code',
    ];


    /**
     * Compute the grade based on a given score.
     *
     * @param  float  $score
     * @return string|null
     */
    public static function computeClass($point)
    {
        $classScale = self::where('point_from', '<=', $point)
                          ->where('point_to', '>=', $point)
                          ->first();

        if ($classScale) {
            return $classScale;
        }

        return null;
    }
}
