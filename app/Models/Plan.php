<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use HasFactory, SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'bandwidth',
        'bonus',
        'amount',
    ];

    public static function formatBytes($bytes, $precision = 2) {
        if($bytes < 1){
            return "0 MB";
        }
        $base = log($bytes, 1024);
        $suffixes = array('', 'KB', 'MB', 'GB', 'TB');   
        
        return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
    }


}
