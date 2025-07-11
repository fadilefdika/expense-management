<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advance extends Model
{
    protected $table = 'em_advances';

    protected $fillable = [
        'type',
        'code',
        'date',
        'description',
        'nominal',
    ];

    // Jika kamu ingin mengatur waktu otomatis untuk `created_at`
    public $timestamps = false;

    // Optional: Jika kamu ingin mengakses tanggal pembuatan
    protected $casts = [
        'created_at' => 'datetime',
    ];
}
