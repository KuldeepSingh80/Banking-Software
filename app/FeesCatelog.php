<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeesCatelog extends Model
{
    //
    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_id');
    }
}
