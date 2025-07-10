<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'em_admin';

    protected $fillable = ['username', 'password'];

    public $timestamps = true;

    protected $hidden = ['password'];
}
