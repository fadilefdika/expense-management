<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $table = 'em_exchange_rates';

    protected $fillable = [
        'date', 'usd', 'jpy',
    ];

    public $timestamps = true;
}
