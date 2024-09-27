<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeInfo extends Model
{
    protected $table = 'fee_informations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'top_up_amount',
        'charges_type',
        'levels',
        'partners',
        'minimum',
        'maximum',
        'fixed_fee',
        'percentage_fee',
        'total_fee',
        'transaction_category',
        'payer',
        'sender_pay',
        'receiver_pay'
    ];

    const SPLIT = 'split';
    const DEPOSIT = 'deposit';

    public function feeSharing()
    {
        return $this->hasMany(FeeSharing::class, 'fee_id');
    }

    public function getChargeSign()
    {
        $chargeType = $this->charges_type;

        if($chargeType == 'percentage') {
            return '%';
        }

        return '$';
    }
}
