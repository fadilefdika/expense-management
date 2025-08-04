<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\ExpenseType;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{


    public function expenseTypeReport()
    {
        $year = now()->year;

        // Ambil total nominal settlement per type & category per bulan
        $advances = DB::table('em_advances')
            ->select(
                'expense_type',
                'expense_category',
                DB::raw('MONTH(date_settlement) as month'),
                DB::raw('SUM(nominal_settlement) as total')
            )
            ->whereYear('date_settlement', $year)
            ->groupBy('expense_type', 'expense_category', DB::raw('MONTH(date_settlement)'))
            ->get();

        // Ambil hanya kategori yang dipakai
        $usedCategoryIds = $advances->pluck('expense_category')->unique();
        $categoryList = \App\Models\ExpenseCategory::with('expenseType')->get();


        $report = [];
        foreach ($categoryList as $category) {
            $row = [
                'expense_type' => $category->expenseType->name ?? '-',
                'category'     => $category->name,
                'monthly'      => array_fill(1, 12, 0),
                'total'        => 0
            ];

            foreach ($advances as $entry) {
                if ((int) $entry->expense_category === $category->id) {
                    $row['monthly'][$entry->month] = (float) $entry->total;
                    $row['total'] += (float) $entry->total;
                }
            }

            $report[] = $row;
        }

        // Group by Expense Type untuk keperluan Drilldown
        $highchartsSeries = [];
        $highchartsDrill  = [];

        $groupedByType = collect($report)->groupBy('expense_type');

        foreach ($groupedByType as $type => $items) {
            $total = $items->sum('total');
            $typeId = 'type-' . crc32($type);

            $highchartsSeries[] = [
                'name' => $type,
                'y'    => $total,
                'drilldown' => $typeId
            ];

            $drillData = [];
            foreach ($items as $item) {
                $drillData[] = [
                    'name' => $item['category'],
                    'y'    => $item['total']
                ];
            }

            $highchartsDrill[] = [
                'id'   => $typeId,
                'data' => $drillData
            ];
        }

        // Hitung total semua bulan
        $monthlyTotals = array_fill(0, 12, 0);
        foreach ($report as $row) {
            foreach ($row['monthly'] as $month => $value) {
                $index = (int) $month - 1;
                if ($index >= 0 && $index <= 11) {
                    $monthlyTotals[$index] += $value;
                }
            }
        }

        ksort($monthlyTotals);

        $headers = array_values([
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
            4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
            10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ]);

        $expenseTypes = \App\Models\ExpenseType::pluck('name')->toArray();


        return view('pages.report.expense-type.index', [
            'report' => $report,
            'monthlyTotals' => $monthlyTotals,
            'headers' => $headers,
            'highchartsSeries' => $highchartsSeries,
            'expenseTypes' => $expenseTypes,
            'highchartsDrill' => $highchartsDrill
        ]);
        
    }


    public function vendorReport()
    {
        $year = now()->year;

        $vendors = DB::table('em_advances')
            ->select(
                'vendor_name',
                DB::raw('MONTH(date_settlement) as month'),
                DB::raw('SUM(nominal_settlement) as total')
            )
            ->whereYear('date_settlement', $year)
            ->groupBy('vendor_name', DB::raw('MONTH(date_settlement)'))
            ->get();

        $vendorList = \App\Models\Vendor::all();

        $vendorTotals = array_fill(0, 12, 0);
        $vendorReport = [];

        foreach ($vendorList as $v) {
            $row = [
                'vendor' => $v->name,
                'monthly' => array_fill(0, 12, 0),
                'total' => 0,
            ];

            foreach ($vendors as $data) {
                if ($data->vendor_name == $v->id) {
                    $month = (int) $data->month;
                    if ($month >= 1 && $month <= 12) {
                        $row['monthly'][$month] = (float) $data->total;
                        $row['total'] += (float) $data->total;
                        $vendorTotals[$month] += (float) $data->total;
                    }
                }
            }

            $vendorReport[] = $row;
        }

        ksort($vendorTotals);

        $headers = array_values([
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar',
            4 => 'Apr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
            10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ]);

        return view('pages.report.vendor.index', compact(
            'vendorReport',
            'vendorTotals',
            'headers'
        ));
    }




}
