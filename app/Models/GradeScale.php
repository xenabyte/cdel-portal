<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    use HasFactory;

    protected $table = 'gradings';

    protected $fillable = [
        'from_score',
        'to_score',
        'grade',
        'point'
    ];

    /**
     * Compute the grade based on a given score.
     *
     * @param  float  $score
     * @return string|null
     */
    public static function computeGrade($score)
    {
        $gradeScale = self::where('from_score', '<=', $score)
                          ->where('to_score', '>=', $score)
                          ->first();

        if ($gradeScale) {
            return $gradeScale;
        }

        return null;
    }
}
