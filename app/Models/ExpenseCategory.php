<?php

namespace App\Models;

use App\Models\ExpenseType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes;

    protected $table = 'em_expense_category';

    protected $fillable = [
        'expense_type_id',
        'name',
    ];

    protected $dates = ['deleted_at'];

    public function type()
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id');
    }
}
