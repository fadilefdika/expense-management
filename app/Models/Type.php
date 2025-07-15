<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use SoftDeletes;

    protected $table = 'em_types';

    protected $fillable = [ 'name'];

    protected $dates = ['deleted_at'];
    
    public function vendors()
    {
        return $this->hasMany(Vendor::class, 'em_type_id');
    }
}
