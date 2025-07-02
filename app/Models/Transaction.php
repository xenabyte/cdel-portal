<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    const APPLICATION = 'Application Fee';

    protected $fillable = [
        'user_id',
        'student_id',
        'payment_id',
        'amount_payed',
        'reference',
        'payment_method',
        'status',
        'session',
        'narration',
        'checkout_url',
        'redirect_url',
        'plan_id',
        'is_used',
        'additional_data',
        'cron_status',
    ];

    /**
     * Get the owner that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function applicant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    /**
     * Get the owner that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }


    /**
     * Get the paymentType that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function paymentType()
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}
