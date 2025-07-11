<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advance extends Model
{
    protected $table = 'em_advances';

    protected $fillable = [
        'main_type',
        'sub_type',
        'code_advance',
        'code_settlement',
        'date_advance',
        'date_settlement',
        'description',
        'nominal',
        'expense_type',
        'expense_category',
        'vendor_name',
        // tambahkan semua field yang ingin digunakan
    ];

    // Nonaktifkan timestamps default (created_at, updated_at)
    public $timestamps = false;

    // Format cast untuk field yang perlu jadi Carbon instance
    protected $casts = [
        'created_at' => 'datetime',
        'date_advance' => 'datetime',
        'date_settlement' => 'datetime',
    ];
}
