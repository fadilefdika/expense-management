<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ExpenseTypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExpenseType::select(['id', 'name', 'created_at']);

            return DataTables::of($data)
                ->addIndexColumn()
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
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.master-data.expense-type.index');
    }

    public function show($id)
    {
        $type = ExpenseType::findOrFail($id);
        return response()->json($type);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100',
            ]);

            // Cari yang soft deleted
            $existing = ExpenseType::withTrashed()
                ->where('name', $request->name)
                ->first();

            if ($existing && $existing->trashed()) {
                // Restore record yang sudah di-soft delete
                $existing->restore();

                // Update timestamp jika perlu
                $existing->updated_at = now();
                $existing->save();

                return redirect()->back()->with('success', 'Expense Type berhasil ditambahkan');
            }

            if ($existing) {
                // Jika sudah ada dan belum dihapus, kembalikan error
                return response()->json([
                    'success' => false,
                    'message' => 'Nama Expense Type sudah digunakan.'
                ], 422);
            }

            // Jika tidak ada sama sekali, buat baru
            ExpenseType::create([
                'name' => $request->name,
            ]);

            return redirect()->back()->with('success', 'Expense Type berhasil ditambahkan');

        } catch (\Exception $e) {
            Log::error('Gagal menambahkan Expense Type', ['error' => $e->getMessage()]);

            return redirect()->back()->with('error', 'Terjadi kesalahan saat menambahkan expense type: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:100|unique:em_expense_type,name,' . $id,
            ]);

            $type = ExpenseType::findOrFail($id);
            $type->update(['name' => $request->name]);

            return response()->json(['success' => true, 'message' => 'Expense Type berhasil diperbarui']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal mengupdate Expense Type', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $type = ExpenseType::findOrFail($id);
            $type->delete();

            return response()->json(['success' => true, 'message' => 'Expense Type berhasil dihapus']);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan.',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Gagal menghapus Expense Type', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

