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
                ->addColumn('expense_type', fn($row) => $row->type->name ?? '-') 
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

        $expenseTypes = ExpenseType::all();
        return view('pages.master-data.expense-category.index', compact('expenseTypes'));
    }

    public function show($id)
    {
        $category = ExpenseCategory::findOrFail($id);
        return response()->json($category);
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:em_expense_type,id',
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
            'expense_type_id' => 'required|exists:em_expense_type,id',
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
