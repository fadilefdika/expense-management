<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Type;
use App\Models\Vendor;
use App\Models\Advance;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use App\Models\LedgerAccount;
use App\Models\ExpenseCategory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettlementController extends Controller
{
    public function index($id)
    {
        // opsional: ambil data advance berdasarkan ID
        $advance = Advance::findOrFail($id);
        
        $noAdvance = null;
        if($advance->main_type == 'PR-Online'){
            $noAdvance = $advance->main_type;
        }else{
            $noAdvance = $advance->code_advance;
        }
        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();

        
        return view('pages.settlement.index', compact('advance','expenseTypes','expenseCategories','codeSettlement','noAdvance'));
    }
    
    public function show($id)
    {
        $advance = Advance::with(['settlementItems.ledgerAccount','vendor', 'costCenterItems.ledgerAccount'])->findOrFail($id);
        // dd($advance->costCenterItems);
        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();

        return view('pages.settlement.index', [
            'advance' => $advance,
            'expenseTypes' => $expenseTypes,
            'expenseCategories' => $expenseCategories,
            'readonly' => true // <-- kondisi diatur di sini
        ]);
    }

    public function edit($id)
    {
        
        $advance = Advance::with(['settlementItems.ledgerAccount', 'type', 'costCenterItems.ledgerAccount'])->findOrFail($id);

        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();

        $codeSettlement = $this->generateSettlementCode($advance->sub_type_advance);

        // Semua vendor GA (GAO/GAS/GAA) punya em_type_id=1, HR (HRO/HRS/HRA) punya em_type_id=2
        $vendors = collect();
        if ($advance->type) {
            $typeName = $advance->type->name; // e.g. GAA, HRO, GAS, etc.
            $vendorTypeId = str_starts_with($typeName, 'GA') ? 1 : (str_starts_with($typeName, 'HR') ? 2 : null);
            if ($vendorTypeId) {
                $vendors = Vendor::where('em_type_id', $vendorTypeId)->get();
            }
        }

        // Semua ledger account (untuk dropdown row baru)
        $allLedgerAccounts = LedgerAccount::all();

        $vendorLedgers = collect();
        if ($advance->vendor_name) {
            $vendorModel = Vendor::with('ledgerAccounts')->find($advance->vendor_name);
            if ($vendorModel) {
                $vendorLedgers = $vendorModel->ledgerAccounts;
            }
        }

        $ledgerAccountsSettlement = $vendorLedgers->filter(fn($l) => is_null($l->tax_percent))->values();
        $allVendorLedgers = $vendorLedgers->values();

        $costCenters = Vendor::whereNotNull('cost_center')->where('cost_center', '!=', '')->distinct()->pluck('cost_center');

        if ($advance->main_type === 'Advance' && !$advance->code_settlement) {
            $advance->nominal_settlement = 0;
            $advance->difference = $advance->nominal_advance;
        }

        return view('pages.settlement.index', [
            'advance' => $advance,
            'expenseTypes' => $expenseTypes,
            'expenseCategories' => $expenseCategories,
            'codeSettlement' => $codeSettlement,
            'vendors' => $vendors,
            'costCenters' => $costCenters,
            'selectedVendor' => $advance->vendor_name,
            'readonly' => false,
            'ledgerAccountsSettlement' => $ledgerAccountsSettlement,
            'allVendorLedgers' => $allVendorLedgers,
            'allLedgerAccounts' => $allLedgerAccounts
        ]);
    }




    public function update(Request $request, $id)
    {
        $toInt = fn($str) => (int) str_replace('.', '', $str);

        $request->validate([
            'code_advance_edit'        => 'required|string|max:50',
            'code_settlement_edit'     => 'required|string|max:50',
            'vendor_id_edit'         => 'required|exists:em_vendors,id',
            'expense_type_edit'        => 'required|exists:em_expense_type,id',
            'expense_category_edit'    => 'required|exists:em_expense_category,id',
            'nominal_advance_edit'     => 'required|string',
            'nominal_settlement_edit'  => 'required|string',
            'difference_edit'          => 'required|string',
            'description_edit'         => 'required|string',
            
            'usage_items'               => 'required|array|min:1',
            'usage_items.*.ledger_account_id' => 'required|integer',
            'usage_items.*.description' => 'required|string',
            'usage_items.*.qty'         => 'required|integer|min:1',
            'usage_items.*.nominal'     => 'required|string',

            'items_costcenter' => 'required|array|min:1',
            'items_costcenter.*.cost_center' => 'required|string',
            'items_costcenter.*.ledger_account_id' => 'required|integer',
            'items_costcenter.*.description' => 'required|string',
            'items_costcenter.*.amount' => 'required|string',

            'usd_settlement' => 'nullable|numeric',
            'yen_settlement' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            $settlement = Advance::findOrFail($id);

            $typeName = $settlement->type ? $settlement->type->name : null;
            $sub_type_settlement = match($typeName) {
                'GAO' => 1,
                'HRO' => 2,
                'GAA' => 3, // GAS
                'HRA' => 4, // HRS
                default => $settlement->sub_type_advance,
            };

            $date = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
            
            $usd = $request->usd_settlement ?? 0;
            $yen = $request->yen_settlement ?? 0;

            $settlement->update([
                'code_settlement'     => $request->code_settlement_edit,
                'vendor_name'         => $request->vendor_id_edit,
                'sub_type_settlement' => $sub_type_settlement,
                'expense_type'        => $request->expense_type_edit,
                'expense_category'    => $request->expense_category_edit,
                'date_settlement'     => $date,
                'nominal_advance'     => $toInt($request->nominal_advance_edit),
                'nominal_settlement'  => $toInt($request->nominal_settlement_edit),
                'difference'          => $toInt($request->difference_edit),
                'description'         => $request->description_edit,
                'usd_settlement'      => $usd,
                'yen_settlement'      => $yen,
            ]);

            $settlement->settlementItems()->delete();

            foreach ($request->usage_items as $item) {
                $qty = (int) $item['qty'];
                $nominal = $toInt($item['nominal']);

                $settlement->settlementItems()->create([
                    'ledger_account' => $item['ledger_account_id'],
                    'description' => $item['description'],
                    'qty'         => $qty,
                    'nominal'     => $nominal,
                    'total'       => $qty * $nominal,
                ]);
            }

            $settlement->costCenterItems()->delete();

            foreach ($request->items_costcenter as $cc) {
                $settlement->costCenterItems()->create([
                    'cost_center' => $cc['cost_center'],
                    'ledger_account_id' => $cc['ledger_account_id'],
                    'description' => $cc['description'],
                    'amount' => $toInt($cc['amount']),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.settlement.show', $id)->with('success', 'Settlement berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating settlement', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }


    protected function generateSettlementCode($type)
    {
        $type = Type::where('id', $type)->first()->name;
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
