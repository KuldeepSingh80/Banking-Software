<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    //
    
    protected $fillable = [
        'name',
		'key'
    ];

    public function programs()
    {
        return $this->belongsToMany(Program::class, 'merchant_program');
    }
}
