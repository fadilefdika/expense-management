<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseType extends Model
{
    use SoftDeletes;

    protected $table = 'em_expense_type';

    protected $fillable = [
        'name',
    ];

    protected $dates = ['deleted_at']; // opsional tapi baik ditambahkan

    public function categories()
    {
        return $this->hasMany(ExpenseCategory::class, 'expense_type_id');
    }
}