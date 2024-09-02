<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
		'last_name', 
        'company_name',
		'email', 
		'mobile', 
		'address_one', 
		'address_two', 
        'city', 
        'state', 
        'zip_code'
    ];
}
