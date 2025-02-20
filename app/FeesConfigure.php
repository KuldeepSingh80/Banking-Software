<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeesConfigure extends Model
{
    public function feeInfo()
    {
        return $this->hasMany(FeeInfo::class, 'fees_config_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
