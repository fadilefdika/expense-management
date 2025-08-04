<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LedgerAccount extends Model
{
    protected $table = 'em_ledger_account';
    protected $fillable = ['ledger_account', 'desc_coa'];

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'em_vendor_ledger_accounts', 'ledger_account_id', 'vendor_id');
    }

}
