<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                // Include relasi ke em_type agar bisa tampilkan nama tipe
                $query = Vendor::with('type', 'ledgerAccounts');

                return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('type_name', function ($row) {
                    return $row->type->name ?? '-';
                })
                ->addColumn('cost_center', function ($row) {
                    return $row->cost_center ?? '-';
                })
                ->addColumn('vendor_number', function ($row) {
                    return $row->vendor_number ?? '-';
                })
                ->addColumn('ledger_accounts', function ($row) {
                    if ($row->ledgerAccounts->isEmpty()) {
                        return '-';
                    }
            
                    return $row->ledgerAccounts->map(function ($ledger) {
                        return $ledger->ledger_account . ' - ' . $ledger->desc_coa;
                    })->implode('<br>');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-sm btn-warning btn-edit" data-id="' . $row->id . '">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row->id . '">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['ledger_accounts', 'action']) // digabung di sini
                ->make(true);
            
            } catch (\Exception $e) {
                Log::error('Error fetching vendor data: ' . $e->getMessage());
                return response()->json(['error' => 'Data fetch failed'], 500);
            }
        }
        $types = Type::all();

        return view('pages.master-data.vendor.index', compact('types'));
    }

    public function create()
    {
        return view('admin.types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'vendor_number' => 'required|integer',
            'type_id' => 'required|exists:em_types,id',
        ]);

        try {
            // Cek apakah sudah ada vendor dengan kombinasi name dan em_type_id, termasuk yang soft-deleted
            $existing = Vendor::withTrashed()
                ->where('name', $request->name)
                ->where('vendor_number', $request->vendor_number)
                ->where('em_type_id', $request->type_id)
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    // Jika ditemukan dan soft-deleted, lakukan restore
                    $existing->restore();
                    $existing->updated_at = now();
                    $existing->save();

                    return response()->json([
                        'message' => 'Vendor berhasil direstore.',
                        'status' => 'success',
                    ]);
                }

                // Jika ditemukan dan belum dihapus, tolak karena duplikat
                return response()->json([
                    'message' => 'Vendor dengan nama dan tipe yang sama sudah ada.',
                    'status' => 'error',
                ], 422);
            }

            // Jika belum ada, buat baru
            Vendor::create([
                'name' => $request->name,
                'vendor_number' => $request->vendor_number,
                'em_type_id' => $request->type_id,
            ]);

            return response()->json([
                'message' => 'Vendor berhasil ditambahkan.',
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating vendor: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan vendor: ' . $e->getMessage());
        }
    }


    public function show($id)
    {
        $type = Vendor::findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'vendor_number' => 'required|integer',
            'type_id' => 'required|exists:em_types,id',
        ]);
    
        try {
            $vendor = Vendor::findOrFail($id);
            $vendor->update([
                'name' => $request->name,
                'vendor_number' => $request->vendor_number,
                'type_id' => $request->type_id,
            ]);
    
            return response()->json(['success' => true, 'message' => 'Vendor berhasil diperbarui']);
        } catch (\Exception $e) {
            Log::error('Error updating vendor: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui vendor.'], 500);
        }
    }
    

    public function destroy($id)
    {
        try {
            $type = Vendor::findOrFail($id);
            $type->delete();

            
            return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
        } catch (\Exception $e) {
            Log::error('Error deleting type: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
