<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LedgerAccount;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class LedgerAccountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = LedgerAccount::select(['id', 'ledger_account', 'desc_coa', 'created_at','tax_percent']);
            Log::debug('Data ledger account:', $data->get()->toArray());

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tax_percent', fn($row) => $row->tax_percent !== null ? ($row->tax_percent * 100) . '%' : '-')
                ->addColumn('action', function ($row) {
                    $editUrl = route('admin.ledger-account.edit', $row->id);
                    $deleteUrl = route('admin.ledger-account.destroy', $row->id);
                
                    return '
                        <button class="btn btn-sm btn-warning btn-edit" data-url="' . $editUrl . '" data-id="' . $row->id . '">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-delete" data-url="' . $deleteUrl . '" data-id="' . $row->id . '">
                            <i class="bi bi-trash"></i>
                        </button>
                    ';
                })                
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.master-data.ledger-account.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ledger_account' => 'required|string|max:255',
            'desc_coa' => 'required|string|max:255',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            LedgerAccount::create([
                'ledger_account' => $request->ledger_account,
                'desc_coa' => $request->desc_coa,
                'tax_percent' => $request->tax_percent !== null
                    ? $request->tax_percent / 100
                    : null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Ledger account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create ledger account: ' . $e->getMessage());
        }
    }


    public function edit($id)
    {
        $ledger = LedgerAccount::findOrFail($id);
        return response()->json($ledger);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ledger_account' => 'required|string|max:255',
            'desc_coa' => 'required|string|max:255',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::beginTransaction();

        try {
            $ledger = LedgerAccount::findOrFail($id);

            $ledger->update([
                'ledger_account' => $request->ledger_account,
                'desc_coa' => $request->desc_coa,
                'tax_percent' => $request->tax_percent !== null
                    ? $request->tax_percent / 100
                    : null,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Ledger account updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update ledger account: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        $ledger = LedgerAccount::findOrFail($id);
        $ledger->delete();

        return response()->json(['message' => 'Ledger account deleted successfully.']);
    }



}
