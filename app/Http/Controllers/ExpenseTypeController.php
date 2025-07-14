<?php

namespace App\Http\Controllers;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
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
                    $editUrl = route('admin.expense-type.update', $row->id);
                    $deleteUrl = route('admin.expense-type.destroy', $row->id);
                    return view('components.table-actions', compact('editUrl', 'deleteUrl'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.master-data.expense-type.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:expense_types,name',
        ]);

        ExpenseType::create([
            'name' => $request->name,
        ]);

        return response()->json(['success' => true, 'message' => 'Expense Type berhasil ditambahkan']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:expense_types,name,' . $id,
        ]);

        $type = ExpenseType::findOrFail($id);
        $type->update(['name' => $request->name]);

        return response()->json(['success' => true, 'message' => 'Expense Type berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $type = ExpenseType::findOrFail($id);
        $type->delete();

        return response()->json(['success' => true, 'message' => 'Expense Type berhasil dihapus']);
    }
}

