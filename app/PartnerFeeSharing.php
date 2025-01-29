<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerFeeSharing extends Model
{
    protected $fillable = [
        'sharing_level_id',
        'sharing',
        'partner_id',
        'fixed_cost',
        'percentage_cost'
    ];

    public function partners()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
