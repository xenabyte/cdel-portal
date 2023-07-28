<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExaminationSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_docket_status',
        'academic_session',
        'result_processing_status',
    ];
}
