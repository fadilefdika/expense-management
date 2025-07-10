<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; // tetap pakai 'users' sesuai tabel

    protected $primaryKey = 'npk'; // opsional jika pakai sebagai primary

    protected $fillable = [
        'npk',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public $timestamps = false;
}

