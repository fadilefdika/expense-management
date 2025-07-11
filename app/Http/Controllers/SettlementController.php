<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use App\Models\ExpenseCategory;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class SettlementController extends Controller
{
    public function create($id)
    {
        // opsional: ambil data advance berdasarkan ID
        $advance = Advance::findOrFail($id);
        $codeSettlement = $this->generateAdvanceCode($advance->sub_type_advance);

        $noAdvance = null;
        if($advance->main_type == 'PR-Online'){
            $noAdvance = $advance->main_type;
        }else{
            $noAdvance = $advance->code_advance;
        }
        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();

        return view('pages.settlement.create', compact('advance','expenseTypes','expenseCategories','codeSettlement','noAdvance'));
    }


    public function store()
    {
        return redirect()->route('admin.settlement.index');
    }

    protected function generateAdvanceCode($type)
    {
        if($type == 'GAO'){
            $type = 'GAO';
        }elseif($type == 'HRO'){
            $type = 'HRO';
        }elseif($type == 'GAA'){
            $type = 'GAS';
        }elseif($type == 'HRA'){
            $type = 'HRS';
        }

        
        $month = now()->format('m');
        $year = now()->format('y');

        // Hitung total advance secara global untuk bulan dan tahun yang sama
        $count = Advance::whereMonth('date_settlement', now()->month)
                        ->whereYear('date_settlement', now()->year)
                        ->count() + 1;

        return sprintf('%s-%04d-%s-%s', $type, $count, $month, $year);
    }
}
