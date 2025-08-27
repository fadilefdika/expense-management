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
        // Ambil data advance beserta relasi settlementItems dan type
        $advance = Advance::with(['settlementItems.ledgerAccount', 'type', 'costCenterItems'])->findOrFail($id);

        // Ambil data tambahan
        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();

        // Buat kode settlement baru berdasarkan sub_type_advance
        $codeSettlement = $this->generateSettlementCode($advance->sub_type_advance);

        // Ambil vendor berdasarkan em_type_id yang sama dengan advance.type.id
        $vendors = collect(); // Default collection kosong
        if ($advance->type) {
            $vendors = Vendor::where('em_type_id', $advance->type->id)->get();
        }

        $ledgerAccounts = $advance->settlementItems
        ->pluck('ledgerAccount')
        ->filter()
        ->unique('id')
        ->values();

        // dd($advance->settlementItems);
        return view('pages.settlement.index', [
            'advance' => $advance,
            'expenseTypes' => $expenseTypes,
            'expenseCategories' => $expenseCategories,
            'codeSettlement' => $codeSettlement,
            'vendors' => $vendors,
            'selectedVendor' => $advance->vendor_name, // Sesuaikan jika kolomnya vendor_id
            'readonly' => false,
            'ledgerAccounts' => $ledgerAccounts
        ]);
    }



    public function update(Request $request, $id)
    {
        $toInt = fn($str) => (int) str_replace('.', '', $str);

        $request->validate([
            'code_advance'        => 'required|string|max:50',
            'code_settlement'     => 'required|string|max:50',
            'vendor_id'         => 'required|exists:em_vendors,id',
            'expense_type'        => 'required|exists:em_expense_type,id',
            'expense_category'    => 'required|exists:em_expense_category,id',
            'nominal_advance'     => 'required|string',
            'nominal_settlement'  => 'required|string',
            'difference'          => 'required|string',
            'description'         => 'required|string',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.nominal'     => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $settlement = Advance::findOrFail($id);

            $sub_type_settlement = match($settlement->sub_type_advance) {
                'GAO' => 'GAO',
                'HRO' => 'HRO',
                'GAA' => 'GAS',
                'HRA' => 'HRS',
                default => null,
            };

            $date = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

            $settlement->update([
                'code_settlement'     => $request->code_settlement,
                'vendor_name'         => $request->vendor_id,
                'sub_type_settlement' => $sub_type_settlement,
                'expense_type'        => $request->expense_type,
                'expense_category'    => $request->expense_category,
                'date_settlement'     => $date,
                'nominal_advance'     => $toInt($request->nominal_advance),
                'nominal_settlement'  => $toInt($request->nominal_settlement),
                'difference'          => $toInt($request->difference),
                'description'         => $request->description,
            ]);

            $settlement->settlementItems()->delete();

            foreach ($request->items as $item) {
                $qty = (int) $item['qty'];
                $nominal = $toInt($item['nominal']);

                $settlement->settlementItems()->create([
                    'description' => $item['description'],
                    'qty'         => $qty,
                    'nominal'     => $nominal,
                    'total'       => $qty * $nominal,
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
