<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementItem extends Model
{
    protected $table = 'em_settlement_items'; // ← WAJIB kalau tabel bukan default
    protected $fillable = [
        'settlement_code',
        'description',
        'qty',
        'nominal',
        'total',
    ];

    public $timestamps = true;
}
