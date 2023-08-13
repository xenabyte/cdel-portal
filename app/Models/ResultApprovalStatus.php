<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResultApprovalStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'status',
    ];

    public static function getApprovalStatusId ( $status ) {
        $status = self::where('status', $status)->first();
        return $status['id'];
    }
}
