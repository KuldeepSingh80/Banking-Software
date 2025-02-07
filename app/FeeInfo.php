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
        'fees_id',
        'fees_catalog_id',
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
        'transaction_category_id',
        'payer',
        'sender_pay',
        'receiver_pay',
        'merchant_id'
    ];

    const SPLIT = 'split';
    const DEPOSIT = 'deposit';

    public function feeSharing()
    {
        return $this->hasMany(FeeSharing::class, 'fee_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function feesCatalog()
    {
        return $this->belongsTo(FeesCatelog::class, 'fees_catalog_id');
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_id');
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
