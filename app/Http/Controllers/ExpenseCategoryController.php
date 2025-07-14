<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\ExpenseType;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ExpenseCategory::with('type'); // relasi: type()

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('expense_type', fn($row) => $row->type->name ?? '-') // pakai relasi 'type'
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.expense-category.update', $row->id);
                    $deleteUrl = route('admin.expense-category.destroy', $row->id);

                    return view('components.table-actions', compact('editUrl', 'deleteUrl'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $expenseTypes = ExpenseType::all();
        return view('pages.master-data.expense-category.index', compact('expenseTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'name' => 'required|string|max:100',
        ]);

        ExpenseCategory::create([
            'expense_type_id' => $request->expense_type_id,
            'name' => $request->name,
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil ditambahkan.']);
    }

    public function edit($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'name' => 'required|string|max:100',
        ]);

        $category = ExpenseCategory::findOrFail($id);
        $category->update([
            'expense_type_id' => $request->expense_type_id,
            'name' => $request->name,
        ]);

        return response()->json(['success' => true, 'message' => 'Kategori berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        ExpenseCategory::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Kategori berhasil dihapus.']);
    }
}
