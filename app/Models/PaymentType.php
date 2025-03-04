<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PaymentType extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'type',
    ];

    public  static function getPaymentTypeId($paymentType){
        if($type = self::where('type', $paymentType)->first()) {
            return $type->id;
        }
        return null;
    }
}
