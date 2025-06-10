<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgrammeCategory extends Model
{
    use HasFactory, SoftDeletes;

    const DIPLOMA = 'Diploma';
    const UNDERGRADUATE = 'Undergraduate';
    const PGD = 'Postgraduate-Diploma';
    const TOPUP = 'Topup';
    const MASTER = 'Masters';
    const DOCTORATE = 'Doctorate';

    protected $fillable = [
        'category',
        'code'
    ];

    /**
     * Get all academic session settings for this programme category.
     */
    public function academicSessionSetting()
    {
        return $this->hasOne(AcademicSessionSetting::class, 'programme_category_id', 'id');
    }

    /**
     * Get the examSetting associated with the ProgrammeCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function examSetting()
    {
        return $this->hasOne(ExaminationSetting::class, 'programme_category_id', 'id');
    }

    public static function getProgrammeCategory($programmeCategory)
    {
        if ($category = self::where('category', $programmeCategory)->first()) {
            return $category->id;
        }
        return null;
    }

}
