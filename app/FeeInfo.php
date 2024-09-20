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
        'levels',
        'partners',
        'minimum',
        'maximum',
        'fixed_fee',
        'percentage_fee',
        'total_fee'
    ];

    public function feeSharing()
    {
        return $this->hasMany(FeeSharing::class, 'fee_id');
    }
}
