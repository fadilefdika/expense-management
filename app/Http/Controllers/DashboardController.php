<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Advance;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('em_advances as a')
                ->leftJoin('em_expense_type as et', 'a.expense_type', '=', 'et.id')
                ->leftJoin('em_expense_category as ec', 'a.expense_category', '=', 'ec.id')
                ->select([
                    'a.id',
                    'a.date_advance',
                    'a.date_settlement',
                    'a.code_advance',
                    'a.code_settlement',
                    'a.description',
                    'a.nominal_advance',
                    'et.name as expense_type',
                    'ec.name as expense_category',
                    'a.vendor_name',
                ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date_advance', function ($row) {
                    return $row->date_advance
                        ? \Carbon\Carbon::parse($row->date_advance)->format('j F Y, h:i A')
                        : '-';
                })
                ->editColumn('date_settlement', function ($row) {
                    return $row->date_settlement
                        ? \Carbon\Carbon::parse($row->date_settlement)->format('j F Y, h:i A')
                        : '-';
                })
                
                ->editColumn('nominal_advance', fn($row) => number_format($row->nominal_advance, 0, ',', '.'))
                ->make(true);
        }

        return view('pages.dashboard');
    }

}


