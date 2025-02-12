<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    public function merchants()
    {
        return $this->belongsToMany(Merchant::class, 'merchant_program');
    }

    public function feesCatalogs()
    {
        return $this->belongsToMany(FeesCatelog::class, 'fee_catelog_program');
    }
}
