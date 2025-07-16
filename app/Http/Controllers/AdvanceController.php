<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Type;
use App\Models\Advance;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Vendor;

class AdvanceController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = Advance::where('main_type', 'Advance')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('date_advance', function ($row) {
                return Carbon::parse($row->date_advance)->format('d-m-Y h:i A'); // contoh: 09:07 AM
                })
                ->editColumn('nominal_advance', function ($row) {
                    return number_format($row->nominal_advance, 0, ',', '.');
                })
                ->make(true);
        }

        return view('pages.advance.index');
    }

    public function create(){
        $expenseTypes = ExpenseType::all();
        $expenseCategories = ExpenseCategory::all();
        $typeAdvance = Type::whereIn('name', ['GAA', 'HRA'])->get();
        $typePRO = Type::whereIn('name', ['GAO', 'HRO'])->get();
        $vendor = Vendor::select('id', 'name', 'em_type_id')->get();

        return view('pages.advance.create', compact('expenseTypes', 'expenseCategories','typeAdvance','typePRO', 'vendor'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'main_type' => 'required|string|in:advance,pr_online',
        ]);

        if ($request->main_type === 'advance') {
            $request->validate([
                'type_advance' => 'required|integer',
                'submitted_date_advance' => 'required|date',
                'description' => 'required|string|max:255',
                'nominal_advance' => 'required|string',
            ]);

            $nominal = str_replace('.', '', $request->nominal_advance);

            $typeAdvance = Type::find($request->type_advance);
            $typeName = $typeAdvance->name; 

            $submittedDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->submitted_date_advance, 'Asia/Jakarta')
            ->setTimezone('Asia/Jakarta') // jangan timezone() karena ini akan mengkonversi
            ->format('Y-m-d H:i:s');

            Advance::create([
                'main_type' => 'Advance',
                'sub_type_advance' => $request->type_advance,
                'date_advance' => $submittedDate,
                'code_advance' => $this->generateAdvanceCode($typeName),
                'description' => $request->description,
                'nominal_advance' => $nominal,
            ]);

        } elseif ($request->main_type === 'pr_online') {
            $request->validate([
                'type_settlement' => 'required|integer',
                'submitted_date_settlement' => 'required|date',
                'vendor_name' => 'required|integer',
                'expense_type' => 'required|integer',
                'expense_category' => 'required|integer',
                'nominal_settlement' => 'required|string',
                'description' => 'required|string|max:255',
                'items' => 'required|array|min:1',
                'items.*.description' => 'required|string',
                'items.*.qty' => 'required|integer|min:1',
                'items.*.nominal' => 'required|string',
            ]);
            
            $nominal = str_replace('.', '', $request->nominal_settlement);
            
            // Gunakan format sesuai input (tanpa detik)
            $submittedDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->submitted_date_settlement, 'Asia/Jakarta')
                ->setTimezone('Asia/Jakarta')
                ->format('Y-m-d H:i:s');
            
            $settlement = Advance::create([
                'main_type' => 'PR-Online',
                'sub_type_advance' => $request->type_settlement,
                'sub_type_settlement' => $request->type_settlement,
                'date_advance' => $submittedDate,
                'date_settlement' => $submittedDate,
                'code_advance' => 'PR-Online',
                'code_settlement' => $this->generateSettlementCode($request->type_settlement),
                'vendor_name' => $request->vendor_name,
                'expense_type' => $request->expense_type,
                'expense_category' => $request->expense_category,
                'description' => $request->description,
                'nominal_advance' => $nominal,
                'nominal_settlement' => $nominal,
            ]);
            
            foreach ($request->items as $item) {
                $qty = (int) $item['qty'];
                $itemNominal = (int) str_replace('.', '', $item['nominal']);
            
                $settlement->settlementItems()->create([
                    'description' => $item['description'],
                    'qty' => $qty,
                    'nominal' => $itemNominal,
                    'total' => $qty * $itemNominal,
                ]);
            }
        }

        return redirect()->route('admin.all-report')->with('success', 'Expense berhasil ditambahkan.');
    }


    protected function generateAdvanceCode($type)
    {
        $month = now()->format('m');
        $year = now()->format('y');

        // Hitung total advance secara global untuk bulan dan tahun yang sama
        $count = Advance::whereMonth('date_advance', now()->month)
                        ->whereYear('date_advance', now()->year)
                        ->count() + 1;

        return sprintf('%s-%04d-%s-%s', $type, $count, $month, $year);
    }

    protected function generateSettlementCode($type)
    {
        if($type == 'GAO'){
            $type = 'GAO';
        }elseif($type == 'HRO'){
            $type = 'HRO';
        }elseif($type == 'GAA'){
            $type = 'GAS';
        }elseif($type == 'HRA'){
            $type = 'HRS';
        }

        
        $month = now()->format('m');
        $year = now()->format('y');

        // Hitung total advance secara global untuk bulan dan tahun yang sama
        $count = Advance::whereMonth('date_settlement', now()->month)
                        ->whereYear('date_settlement', now()->year)
                        ->count() + 1;

        return sprintf('%s-%04d-%s-%s', $type, $count, $month, $year);
    }

}
