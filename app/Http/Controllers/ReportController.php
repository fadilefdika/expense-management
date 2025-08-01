<?php

namespace App\Http\Controllers;

use App\Models\Advance;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{


    public function expenseTypeReport()
    {
        $year = now()->year;

        $categories = DB::table('em_advances')
            ->select(
                'expense_type',
                'expense_category',
                DB::raw('MONTH(date_settlement) as month'),
                DB::raw('SUM(nominal_settlement) as total')
            )
            ->whereYear('date_settlement', $year)
            ->groupBy('expense_type', 'expense_category', DB::raw('MONTH(date_settlement)'))
            ->get();

        $categoryList = \App\Models\ExpenseCategory::with('expenseType')->get();

        $report = [];
        foreach ($categoryList as $category) {
            $row = [
                'expense_type' => $category->expenseType->name ?? '-',
                'category' => $category->name,
                'monthly' => array_fill(1, 12, 0),
                'total' => 0
            ];

            foreach ($categories as $c) {
                if ($c->expense_category == $category->id) {
                    $row['monthly'][$c->month] = (float) $c->total;
                    $row['total'] += (float) $c->total;
                }
            }

            $report[] = $row;
        }

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

        return view('pages.report.expense-type.index', compact(
            'report',
            'monthlyTotals',
            'headers',
            'expenseTypes'
        ));
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
