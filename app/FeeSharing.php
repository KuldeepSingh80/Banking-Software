<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeSharing extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fee_id',
        'sharing_level',
        'base_cost_partner_id',
        'fixed_base_cost',
        'percentage_base_cost',
        'fixed_markup',
        'percentage_markup',
        'fixed_markup_base_cost',
        'percentage_markup_base_cost',
    ];

    public function partnerFeeSharing()
    {
        return $this->hasMany(PartnerFeeSharing::class, 'sharing_level_id');
    }
}
