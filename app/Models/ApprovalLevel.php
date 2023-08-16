<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApprovalLevel extends Model
{
    use HasFactory, SoftDeletes;

    const SENATE_APPROVED = 'Senate';

    protected $fillable = [
        'level',
    ];
}
