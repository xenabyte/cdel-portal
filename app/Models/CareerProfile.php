<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class CareerProfile extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'career_id', 
        'biodata',
        'education_history', 
        'professional_information', 
        'publications', 
        'cv_path',
    ];

    public function career()
    {
        return $this->belongsTo(Career::class);
    }
}
