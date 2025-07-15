<?php
namespace App\Http\Controllers;
use App\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TypeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = Type::query(); // pakai ->latest() agar langsung terurut

                return DataTables::of($query)
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
            } catch (\Exception $e) {
                Log::error('Error fetching type data: ' . $e->getMessage());
                return response()->json(['error' => 'Data fetch failed'], 500);
            }
        }

        return view('pages.master-data.type.index');
    }

    public function create()
    {
        return view('admin.types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        try {
            Type::create($request->only('name'));
            return redirect()->route('admin.types.index')->with('success', 'Type created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating type: ' . $e->getMessage());
            return back()->with('error', 'Failed to create type.')->withInput();
        }
    }

    public function show($id)
    {
        $type = Type::findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $type = Type::findOrFail($id);
        $type->update($request->only('name'));

        return response()->json(['success' => true, 'message' => 'Expense Type berhasil diperbarui']);
       
    }

    public function destroy($id)
    {
        try {
            $type = Type::findOrFail($id);
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

