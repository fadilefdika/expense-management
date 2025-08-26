<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementItem extends Model
{
    protected $table = 'em_settlement_items'; // ← WAJIB kalau tabel bukan default
    protected $fillable = [
        'settlement_code',
        'ledger_account',
        'description',
        'qty',
        'nominal',
        'total',
    ];

    public $timestamps = true;
    
    public function advance()
    {
        return $this->belongsTo(Advance::class, 'settlement_id');
    }

    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account','id');
    }
}
