<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    const PAYMENT_TYPE_GENERAL_APPLICATION = 'General Application Fee';
    const PAYMENT_TYPE_INTER_TRANSFER_APPLICATION = 'Inter Transfer Application Fee';
    const PAYMENT_TYPE_ACCEPTANCE = 'Acceptance Fee';
    const PAYMENT_TYPE_SCHOOL = 'School Fee';
    const PAYMENT_TYPE_SCHOOL_DE = 'DE School Fee';
    const PAYMENT_TYPE_GENERAL = 'General Fee';
    const PAYMENT_TYPE_OTHER = 'Other Fee';
    const PAYMENT_MODIFY_COURSE_REG = 'Course Reg';
     const PAYMENT_LATE_RESUMPTION = 'Late Resumption Fee';
    const PAYMENT_LATE_COURSE_REG = 'Late Course Reg';
    const PAYMENT_TYPE_WALLET_DEPOSIT = 'Wallet Deposit';
    const PAYMENT_TYPE_BANDWIDTH = 'Bandwidth Fee';
    const PAYMENT_TYPE_ACCOMONDATION = 'Accomondation Fee';
    const PAYMENT_TYPE_INTRA_TRANSFER_APPLICATION = 'Intra Transfer Application Fee';
    const PAYMENT_TYPE_READMISSION_FEE = 'Re-admission Fee';
    const PAYMENT_TYPE_SUMMER_COURSE_REGISTRATION = 'Summer Course Reg';
    // const PAYMENT_TYPE_PROGRAMME_CHANGE = 'Programme Change Fee';


    protected $fillable = [
        'title',
        'description',
        'slug',
        'type',
        'programme_id',
        'level_id',
        'academic_session',
        'programme_category_id',
        'is_charged',
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

    /**
     * Get the level that owns the Payment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(AcademicLevel::class, 'level_id');
    }

    /**
     * Get the programmeCategory that owns the CourseRegistration
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programmeCategory()
    {
        return $this->belongsTo(ProgrammeCategory::class, 'programme_category_id');
    }

    public static function getTotalStructureAmount($id)
    {
        $payment = Payment::find($id);

        if ($payment) {
            $totalAmount = $payment->structures->sum('amount');
            return $totalAmount;
        } 
        return 0;
    }

    public function classifyPaymentType($paymentType) {
        switch ($paymentType) {
            case 'Other Fee':
            case 'Wallet Deposit':
            case 'Late Resumption Fee':
                return 'Other Fee';
            case 'Course Reg':
            case 'Late Course Reg':
            case 'Bandwidth Fee':
                return 'ICT';
            case 'Accomondation Fee':
                return 'Accomondation';
            case 'General Application Fee':
            case 'Inter Transfer Application Fee':
            case 'Intra Transfer Application Fee':
            case 'Acceptance Fee':
            case 'Re-admission Fee':
            case 'Programme Change Fee':
                return 'Other Fee';
            case 'School Fee':
            case 'DE School Fee':
                return 'Tuition Fee';
            default:
            return 'Other Fee';
        }
    }
}
