<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Type;
use App\Models\Vendor;
use App\Models\Advance;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
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
        $advance = Advance::with(['settlementItems'])->findOrFail($id);
        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();
        $codeSettlement = $this->generateSettlementCode($advance->sub_type_advance);

        return view('pages.settlement.index', [
            'advance' => $advance,
            'expenseTypes' => $expenseTypes,
            'expenseCategories' => $expenseCategories,
            'codeSettlement' => $codeSettlement,
            'readonly' => true // <-- kondisi diatur di sini
        ]);
    }

    public function edit($id)
    {
        $advance = Advance::with(['settlementItems', 'type'])->findOrFail($id);
        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();
        $codeSettlement = $this->generateSettlementCode($advance->sub_type_advance);
        // dd($advance->sub_type_advance, $codeSettlement);

        $typeName = null;
        if($advance->type->name == "HRA") {
            $typeName = "HRS";
        } elseif($advance->type->name == "GAA") {
            $typeName = "GAS";
        }

        // Ambil semua vendor berdasarkan type yang sesuai
        $vendors = collect(); // Default empty collection
        
        if ($typeName) {
            $type = Type::where('name', $typeName)->first();
            if ($type) {
                $vendors = Vendor::where('em_type_id', $type->id)->get();
            }
        }

        return view('pages.settlement.index', [
            'advance' => $advance,
            'expenseTypes' => $expenseTypes,
            'expenseCategories' => $expenseCategories,
            'codeSettlement' => $codeSettlement,
            'vendors' => $vendors, // Mengirim koleksi vendors, bukan single vendor
            'selectedVendor' => $advance->vendor_name, // Asumsi ada kolom vendor_id di advance
            'readonly' => false
        ]);
    }


    public function update(Request $request, $id)
    {
        // Hilangkan titik dari string-format currency
        $toInt = fn($str) => (int) str_replace('.', '', $str);

        $request->validate([
            'code_advance'        => 'required|string|max:50',
            'code_settlement'     => 'required|string|max:50',
            'vendor_name'         => 'required|string|max:100',
            'expense_type'        => 'required|exists:em_expense_type,id',
            'expense_category'    => 'required|exists:em_expense_category,id',
            'nominal_advance'     => 'required|string',
            'nominal_settlement'  => 'required|string',
            'difference'          => 'required|string',
            'description'         => 'required|string',
            'items'               => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.qty'         => 'required|integer|min:1',
            'items.*.nominal'     => 'required|string', // string karena masih format Rp
        ]);

        try {
            DB::beginTransaction();

            $settlement = Advance::findOrFail($id);

            // Generate sub_type_settlement
            $sub_type_settlement = match($settlement->sub_type_advance) {
                'GAO' => 'GAO',
                'HRO' => 'HRO',
                'GAA' => 'GAS',
                'HRA' => 'HRS',
                default => null,
            };

            // Simpan tanggal
            $date = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

            // Update data utama
            $settlement->update([
                'code_settlement'     => $request->code_settlement,
                'vendor_name'         => $request->vendor_name,
                'sub_type_settlement' => $sub_type_settlement,
                'expense_type'        => $request->expense_type,
                'expense_category'    => $request->expense_category,
                'date_settlement'     => $date,
                'nominal_advance'     => $toInt($request->nominal_advance),
                'nominal_settlement'  => $toInt($request->nominal_settlement),
                'difference'          => $toInt($request->difference),
                'description'         => $request->description,
            ]);

            // Hapus item sebelumnya
            $settlement->settlementItems()->delete();

            // Simpan item baru
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

            return redirect()->to("/admin/all-report/settlement/$id")->with('success', 'Settlement berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
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
