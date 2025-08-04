<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\LedgerAccount;

class LedgerAccountController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = LedgerAccount::select(['id', 'ledger_account', 'desc_coa', 'created_at']);

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

        return view('pages.master-data.ledger-account.index');
    }

    public function store(Request $request)
    {
        return response()->json(['message' => 'Ledger account created successfully']);
    }
}
