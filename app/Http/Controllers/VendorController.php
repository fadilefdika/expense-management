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
                $vendors = Vendor::with('type', 'ledgerAccounts')->get();

                // Flatten menjadi satu baris per ledger account
                $data = collect();

                foreach ($vendors as $vendor) {
                    if ($vendor->ledgerAccounts->isEmpty()) {
                        // Jika tidak ada ledger account, masukkan tetap satu baris
                        $data->push([
                            'id' => $vendor->id,
                            'name' => $vendor->name,
                            'type_name' => $vendor->type->name ?? '-',
                            'cost_center' => $vendor->cost_center ?? '-',
                            'vendor_number' => $vendor->vendor_number ?? '-',
                            'ledger_account' => '-', // Tidak ada
                            'desc_coa' => '-',
                        ]);
                    } else {
                        foreach ($vendor->ledgerAccounts as $ledger) {
                            $data->push([
                                'id' => $vendor->id,
                                'name' => $vendor->name,
                                'type_name' => $vendor->type->name ?? '-',
                                'cost_center' => $vendor->cost_center ?? '-',
                                'vendor_number' => $vendor->vendor_number ?? '-',
                                'ledger_account' => $ledger->ledger_account,
                                'desc_coa' => $ledger->desc_coa,
                            ]);
                        }
                    }
                }

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('ledger_accounts', function ($row) {
                        return $row['ledger_account'] . ' - ' . $row['desc_coa'];
                    })
                    ->addColumn('action', function ($row) {
                        return '
                            <button class="btn btn-sm btn-warning btn-edit" data-id="' . $row['id'] . '">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="' . $row['id'] . '">
                                <i class="bi bi-trash"></i>
                            </button>
                        ';
                    })
                    ->rawColumns(['ledger_accounts', 'action'])
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
