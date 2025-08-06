<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advance extends Model
{
    protected $table = 'em_advances';

    protected $primaryKey = 'id';

    protected $fillable = [
        'main_type',
        'sub_type_advance',
        'sub_type_settlement',
        'code_advance',
        'code_settlement',
        'date_advance',
        'date_settlement',
        'description',
        'nominal_advance',
        'nominal_settlement',
        'difference',
        'expense_type',
        'expense_category',
        'vendor_name',
        'invoice_number',
        'usd_settlement',
        'yen_settlement',
        // tambahkan semua field yang ingin digunakan
    ];

    // Nonaktifkan timestamps default (created_at, updated_at)
    public $timestamps = true;

    // Format cast untuk field yang perlu jadi Carbon instance
    protected $casts = [
        'created_at' => 'datetime',
        'date_advance' => 'datetime',
        'date_settlement' => 'datetime',
    ];

    public function settlementItems()
    {
        return $this->hasMany(SettlementItem::class, 'settlement_id', 'id'); 
    }

    public function costCenterItems()
    {
        return $this->hasMany(CostCenterItem::class, 'advance_id', 'id');
    }

    public function type()
    {
        return $this->belongsTo(Type::class, 'sub_type_advance', 'id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_name', 'id');
    }
}
