<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PartnerFeeSharing extends Model
{
    protected $fillable = [
        'sharing_level_id',
        'sharing',
        'partner',
        'fixed_cost',
        'percentage_cost'
    ];
}
