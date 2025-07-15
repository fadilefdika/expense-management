<?php

namespace App\Models;

use App\Models\Type;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use SoftDeletes;

    protected $table = 'em_vendors';

    protected $fillable = [
        'name',
        'em_type_id',
    ];

    protected $dates = ['deleted_at'];

    public function type()
    {
        return $this->belongsTo(Type::class, 'em_type_id');
    }
}
