<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostCenterItem extends Model
{
    protected $table = 'em_costcenter_items';
    protected $fillable = ['advance_id', 'cost_center', 'ledger_account_id', 'description'];

}
