<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EmVendorLedgerAccount extends Pivot
{
    // Nama tabel sesuai di SQL Server 
    protected $table = 'em_vendor_ledger_accounts';

    // Jika tidak ada kolom created_at & updated_at
    public $timestamps = false;

    // Kolom yang boleh diisi
    protected $fillable = [
        'vendor_id',
        'ledger_account_id',
        'desc_override'
    ];

    // Relasi ke Vendor
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    // Relasi ke LedgerAccount (COA)
    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }

}