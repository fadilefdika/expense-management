<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Advance;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Advance::select([
                'id',
                'date_advance',
                'date_settlement',
                'code_advance',
                'code_settlement',
                'description',
                'nominal',
                'expense_type',
                'expense_category',
                'vendor_name',
            ]);

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date_advance', function ($row) {
                    return $row->date_advance 
                        ? Carbon::parse($row->date_advance)->format('d-m-Y h:i A') 
                        : '-';
                })
                ->editColumn('date_settlement', function ($row) {
                    return $row->date_settlement 
                        ? Carbon::parse($row->date_settlement)->format('d-m-Y h:i A') 
                        : '-';
                })
                ->editColumn('nominal', fn($row) => number_format($row->nominal, 0, ',', '.'))
                ->make(true);
        }

        return view('pages.dashboard');
    }

}


