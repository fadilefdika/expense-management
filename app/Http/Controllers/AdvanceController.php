<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Type;
use App\Models\Vendor;
use App\Models\Advance;
use App\Models\ExpenseType;
use Illuminate\Http\Request;
use App\Exports\AdvanceExport;
use App\Models\ExpenseCategory;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;

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
        $vendors = Vendor::select('id', 'name', 'em_type_id')->get();
        

        return view('pages.advance.create', compact('expenseTypes', 'expenseCategories','typeAdvance','typePRO', 'vendors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'main_type' => 'required|string|in:advance,pr_online',
        ]);
    
        DB::beginTransaction();
        try {
            if ($request->main_type === 'advance') {
                $request->validate([
                    'type_advance' => 'required|integer',
                    'submitted_date_advance' => 'required|date',
                    'description' => 'required|string|max:255',
                    'nominal_advance' => 'required|string',
                ]);
    
                $nominal = (int) str_replace('.', '', $request->nominal_advance);
    
                $typeAdvance = Type::findOrFail($request->type_advance);
                $typeName = $typeAdvance->name;
    
                $submittedDate = Carbon::createFromFormat('Y-m-d\TH:i', $request->submitted_date_advance, 'Asia/Jakarta')
                    ->setTimezone('Asia/Jakarta')
                    ->format('Y-m-d H:i:s');
    
                Advance::create([
                    'main_type' => 'Advance',
                    'sub_type_advance' => $request->type_advance,
                    'date_advance' => $submittedDate,
                    'code_advance' => $this->generateAdvanceCode($typeName),
                    'description' => $request->description,
                    'nominal_advance' => $nominal,
                ]);
            }
    
            if ($request->main_type === 'pr_online') {
                $request->validate([
                    'type_settlement' => 'required|integer',
                    'submitted_date_settlement' => 'required|date',
                    'vendor_id' => 'required|integer',
                    'expense_type' => 'required|integer',
                    'expense_category' => 'required|integer',
                    'nominal_settlement' => 'required|string',
                    'description' => 'required|string|max:255',
                    'items' => 'required|array|min:1',
                    'items.*.description' => 'required|string',
                    'items.*.qty' => 'required|integer|min:1',
                    'items.*.nominal' => 'required|string',
                ]);
    
                $nominal = (int) str_replace('.', '', $request->nominal_settlement);
    
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
                    'vendor_name' => $request->vendor_id, // FIXED
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
    
            DB::commit();
            return redirect()->route('admin.all-report')->with('success', 'Expense berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menyimpan data Advance/PR-Online: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
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
        $type = Type::where('id', $type)->first()->name;
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

    public function export()
    {
        return Excel::download(new AdvanceExport, 'advance-data.xlsx');
    }

    public function getRates()
    {
        $today = now()->toDateString();
        Log::info('Memulai pengecekan kurs untuk tanggal: ' . $today);

        $existing = ExchangeRate::where('date', $today)->first();

        if ($existing) {
            Log::info('Data kurs sudah ada di DB:', [
                'USD' => $existing->usd,
                'JPY' => $existing->jpy
            ]);

            return response()->json([
                'base_currency' => 'IDR',
                'data' => [
                    'USD' => $existing->usd,
                    'JPY' => $existing->jpy,
                ]
            ]);
        }

        $apiKey = config('services.freecurrencyapi.key');
        $baseUrl = config('services.freecurrencyapi.url');
        $url = $baseUrl . '/latest';

        Log::info('Mengambil kurs dari API', [
            'url' => $url,
            'apikey' => $apiKey,
        ]);

        try {
            $response = Http::get($url, [
                'apikey' => $apiKey,
                'base_currency' => 'IDR',
                'currencies' => 'USD,JPY',
            ]);

            Log::info('Response mentah dari API:', $response->json());

            if ($response->successful()) {
                $data = [
                    'base_currency' => 'IDR',
                    'data' => [
                        'USD' => number_format((float) $response['data']['USD'], 10, '.', ''),
                        'JPY' => number_format((float) $response['data']['JPY'], 10, '.', ''),
                    ]
                ];

                ExchangeRate::create([
                    'date' => $today,
                    'usd' => $data['data']['USD'],
                    'jpy' => $data['data']['JPY'],
                ]);

                Log::info('Response JSON dikirim ke FE:', $data);

                return response()->json($data);
            }

            Log::warning('API response tidak successful', ['status' => $response->status()]);
            return response()->json(['error' => 'Gagal mengambil kurs dari API.'], 500);

        } catch (\Exception $e) {
            Log::error('Exception saat mengambil kurs', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Terjadi kesalahan saat mengambil data.'], 500);
        }
    }





}
