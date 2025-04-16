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
    const POSTGRADUATE = 'Postgraduate';
    const TOPUP = 'Topup';
    const MASTER = 'Masters';
    const DOCTORATE = 'Doctorate';

    protected $fillable = [
        'category',
    ];

    public  static function getProgrammeCategory($programmeCategory){
        if($category = self::where('category', $programmeCategory)->first()) {
            return $category->id;
        }
        return null;
    }
    
    
}
