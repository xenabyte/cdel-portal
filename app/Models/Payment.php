<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'type',
        'programme_id',
    ];

    /**
     * Get all of the structures for the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function structures()
    {
        return $this->hasMany(PaymentStructure::class, 'payment_id', 'id');
    }

    /**
     * Get the programme that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programme()
    {
        return $this->belongsTo(Programme::class, 'programme_id');
    }
}
