<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Advance;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AdvanceController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Advance::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date_advance', function ($row) {
                return Carbon::parse($row->date_advance)->format('d-m-Y h:i A'); // contoh: 09:07 AM
                })
                ->editColumn('nominal_advance', function ($row) {
                    return number_format($row->nominal_advance, 0, ',', '.');
                })
                ->make(true);
        }

        return view('pages.advance.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'main_type' => 'required|string|in:Advance,PR-Online',
            'sub_type_advance' => 'required|string|in:GAA,HRA,GAO,HRO',
            'date_advance' => 'required|date',
            'description' => 'required|string|max:255',
            'nominal_advance' => 'required|numeric|min:0',
        ]);

        try {
            $date = Carbon::parse($request->date_advance)
                    ->timezone('Asia/Jakarta') // Konversi ke WIB
                    ->format('Y-m-d H:i:s');    // Format ke SQL datetime


            Advance::create([
                'main_type' => $request->main_type,
                'sub_type_advance' => $request->sub_type_advance,
                'date_advance' => $date,
                'code_advance' => $this->generateAdvanceCode($request->sub_type_advance),
                'description' => $request->description,
                'nominal_advance' => $request->nominal_advance,
            ]);

            return redirect()->route('admin.advance.index')->with('success', 'Advance berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan advance: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi admin.');
        }
    }

    protected function generateAdvanceCode($type)
    {
        $month = now()->format('m');
        $year = now()->format('y');

        // Hitung total advance secara global untuk bulan dan tahun yang sama
        $count = Advance::whereMonth('date_advance', now()->month)
                        ->whereYear('date_advance', now()->year)
                        ->count() + 1;

        return sprintf('%s-%04d-%s-%s', $type, $count, $month, $year);
    }


}
