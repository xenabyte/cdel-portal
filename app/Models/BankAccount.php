<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_purpose',
        'account_code',
        'bank_name',
        'account_number',
        'account_name',
        'status',
    ];


    public static function getBankAccountCode ($accountPurpose) {
        if($account = self::where('account_purpose', $accountPurpose)->first()) {
            return $account->account_code;
        }
        return null;
    }
}
