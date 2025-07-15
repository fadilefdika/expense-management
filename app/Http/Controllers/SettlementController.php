<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $codeSettlement = $this->generateAdvanceCode($advance->sub_type_advance);

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

        return view('pages.settlement.index', [
            'advance' => $advance,
            'expenseTypes' => $expenseTypes,
            'expenseCategories' => $expenseCategories,
            'readonly' => true // <-- kondisi diatur di sini
        ]);
    }

    public function edit($id)
    {
        $advance = Advance::with(['settlementItems'])->findOrFail($id);
        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();

        return view('pages.settlement.index', [
            'advance' => $advance,
            'expenseTypes' => $expenseTypes,
            'expenseCategories' => $expenseCategories,
            'readonly' => false // default-nya bisa juga tidak dikirim
        ]);
    }


    public function update(Request $request, $id)
    {
        Log::info('Masuk ke fungsi update', ['id' => $id]);

        // Helper untuk parsing angka format rupiah
        $toInt = fn($str) => (int) str_replace('.', '', $str);

        // Logging request awal
        Log::debug('Data request awal', $request->all());

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
            'items.*.nominal'     => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Cek apakah settlement ditemukan
            $settlement = Advance::findOrFail($id);
            $date = Carbon::parse($request->date_advance)
                    ->timezone('Asia/Jakarta') // Konversi ke WIB
                    ->format('Y-m-d H:i:s');    // Format ke SQL datetime

            // Update data utama
            $settlement->update([
                'code_settlement'     => $request->code_settlement,
                'vendor_name'         => $request->vendor_name,
                'expense_type'          => $request->expense_type,
                'date_settlement'     => $date,
                'expense_category'  => $request->expense_category,
                'nominal_advance'     => $toInt($request->nominal_advance),
                'nominal_settlement'  => $toInt($request->nominal_settlement),
                'difference'          => $toInt($request->difference),
                'description'         => $request->description,
            ]);

            Log::info('Advance berhasil diupdate');

            // Hapus item sebelumnya
            $settlement->settlementItems()->delete();
            Log::info('Item sebelumnya dihapus');

            // Tambahkan item baru
            foreach ($request->items as $index => $item) {
                $settlement->settlementItems()->create([
                    'description' => $item['description'],
                    'qty'         => $item['qty'],
                    'nominal'     => $item['nominal'],
                    'total'       => $item['qty'] * $item['nominal'],
                ]);
                Log::debug("Item ke-$index ditambahkan", $item);
            }

            DB::commit();
            Log::info('Settlement berhasil disimpan dan transaksi di-commit');

            return redirect()->route('admin.all-report')->with('success', 'Settlement berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal update settlement: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
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
