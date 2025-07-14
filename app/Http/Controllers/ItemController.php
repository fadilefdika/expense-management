<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SettlementItem;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $items = SettlementItem::with('advance')
                ->select([
                    'id',
                    'settlement_id',
                    'description',
                    'qty',
                    'nominal',
                    'total',
                    'created_at',
                ]);

            return DataTables::of($items)
                ->addIndexColumn()
                ->addColumn('code_settlement', function ($row) {
                    return $row->advance?->code_settlement ?? '-';
                })
                ->filterColumn('code_settlement', function ($query, $keyword) {
                    $query->whereHas('advance', function ($q) use ($keyword) {
                        $q->where('code_settlement', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('total', fn($row) => number_format($row->total, 0, ',', '.'))
                ->editColumn('created_at', fn($row) => $row->created_at->format('d-m-Y H:i'))
                ->make(true);
            
        }

        return view('pages.item.index');
    }

}
