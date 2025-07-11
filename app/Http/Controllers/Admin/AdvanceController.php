<?php

namespace App\Http\Controllers\Admin;

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
                ->editColumn('date', function ($row) {
                return Carbon::parse($row->date)->format('d-m-Y h:i A'); // contoh: 09:07 AM
                })
                ->editColumn('nominal', function ($row) {
                    return number_format($row->nominal, 0, ',', '.');
                })
                ->make(true);
        }

        return view('pages.advance.index');
    }

    public function store(Request $request)
    {

        $request->validate([
            'type' => 'required|string|in:GAA,HRA',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'nominal' => 'required|numeric|min:0',
        ]);
        try {
            $date = Carbon::parse($request->date)->format('Y-m-d H:i:s');

            Advance::create([
                'type' => $request->type,
                'date' => $date,
                'code' => $this->generateAdvanceCode($request->type),
                'description' => $request->description,
                'nominal' => $request->nominal,
            ]);
            return redirect()->route('admin.advance.index')->with('success', 'Advance berhasil ditambahkan.');
        } catch (\Exception $e) {
            // ✅ Log detail error untuk developer
            Log::error('Gagal menyimpan advance: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);
    
            // ✅ Pesan user-friendly untuk pengguna
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi admin.');
        }
    }

    protected function generateAdvanceCode($type)
    {
        $month = now()->format('m');
        $year = now()->format('y');

        // Hitung total advance secara global untuk bulan dan tahun yang sama
        $count = Advance::whereMonth('date', now()->month)
                        ->whereYear('date', now()->year)
                        ->count() + 1;

        return sprintf('%s-%04d-%s-%s', $type, $count, $month, $year);
    }


}
