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
        'upperlink_account_code',
        'monnify_account_code',
        'bank_name',
        'account_number',
        'account_name',
        'status',
    ];

    public static function getBankAccountCode($accountPurpose)
    {
        if ($account = self::where('account_purpose', $accountPurpose)->first()) {
            return new BankAccountCodeDTO(
                $account->upperlink_account_code,
                $account->monnify_account_code
            );
        }

        return null;
    }
}

// ✅ Class declared outside of BankAccount class, but in the same file
class BankAccountCodeDTO
{
    public $upperlinkAccountCode;
    public $monnifyAccountCode;

    public function __construct($upperlinkAccountCode, $monnifyAccountCode)
    {
        $this->upperlinkAccountCode = $upperlinkAccountCode;
        $this->monnifyAccountCode = $monnifyAccountCode;
    }
}