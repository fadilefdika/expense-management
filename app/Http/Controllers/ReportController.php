<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $year = now()->year;

        // Ambil semua kategori beserta tipe dan pengeluaran per bulan
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

        // Ambil master data
        $categoryList = \App\Models\ExpenseCategory::with('expenseType')->get();

        // Buat struktur data laporan
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

        // Hitung grand total per bulan
        $monthlyTotals = array_fill(1, 12, 0);
        foreach ($report as $row) {
            foreach ($row['monthly'] as $month => $value) {
                $monthlyTotals[$month] += $value;
            }
        }

        return view('pages.report.index', compact('report', 'monthlyTotals'));
    }
}
