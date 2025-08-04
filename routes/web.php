<?php

use App\Http\Controllers\AdvanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LedgerAccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\VendorController;

Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    
        // Dashboard
        Route::get('/all-report', [DashboardController::class, 'index'])->name('all-report');
        Route::get('/all-report/settlement/{id}', [SettlementController::class, 'show'])->name('settlement.show');
        Route::get('/all-report/settlement/{id}/edit', [SettlementController::class, 'edit'])->name('settlement.edit');
        Route::post('/all-report/settlement/{id}', [SettlementController::class, 'update'])->name('settlement.update');
        Route::get('/all-report/create', [AdvanceController::class, 'create'])->name('all-report.create');
        Route::get('/api/exchange-rates', [AdvanceController::class, 'getRates'])->name('exchange.rates');


        // Advance
        Route::get('/advance', [AdvanceController::class, 'index'])->name('advance.index');
        Route::post('/advance', [AdvanceController::class, 'store'])->name('advance.store');
        Route::get('/advance/export', [AdvanceController::class, 'export'])->name('advance.export-excel');

        //Reports 
        Route::get('/report/expense-type', [ReportController::class, 'expenseTypeReport'])->name('report.expense-type.index');
        Route::get('/report/vendor', [ReportController::class, 'vendorReport'])->name('report.vendor.index');
  
        Route::get('/master-data/expense-type', [ExpenseTypeController::class, 'index'])->name('expense-type.index');
        Route::get('/master-data/expense-type/{id}', [ExpenseTypeController::class, 'show'])->name('expense-type.show');
        Route::post('/master-data/expense-type', [ExpenseTypeController::class, 'store'])->name('expense-type.store');
        Route::put('/master-data/expense-type/{id}', [ExpenseTypeController::class, 'update'])->name('expense-type.update');
        Route::delete('/master-data/expense-type/{id}', [ExpenseTypeController::class, 'destroy'])->name('expense-type.destroy');

        Route::get('/master-data/expense-category', [ExpenseCategoryController::class, 'index'])->name('expense-category.index');
        Route::get('/master-data/expense-category/{id}', [ExpenseCategoryController::class, 'show'])->name('expense-category.show');
        Route::post('/master-data/expense-category', [ExpenseCategoryController::class, 'store'])->name('expense-category.store');
        Route::put('/master-data/expense-category/{id}', [ExpenseCategoryController::class, 'update'])->name('expense-category.update');
        Route::delete('/master-data/expense-category/{id}', [ExpenseCategoryController::class, 'destroy'])->name('expense-category.destroy');
        
        Route::get('/master-data/type', [TypeController::class, 'index'])->name('type.index');
        Route::get('/master-data/type/{id}', [TypeController::class, 'show'])->name('type.show');
        Route::post('/master-data/type', [TypeController::class, 'store'])->name('type.store');
        Route::put('/master-data/type/{id}', [TypeController::class, 'update'])->name('type.update');
        Route::delete('/master-data/type/{id}', [TypeController::class, 'destroy'])->name('type.destroy');
        
        Route::get('/master-data/vendor', [VendorController::class, 'index'])->name('vendor.index');
        Route::get('/master-data/vendor/{id}', [VendorController::class, 'show'])->name('vendor.show');
        Route::post('/master-data/vendor', [VendorController::class, 'store'])->name('vendor.store');
        Route::put('/master-data/vendor/{id}', [VendorController::class, 'update'])->name('vendor.update');
        Route::delete('/master-data/vendor/{id}', [VendorController::class, 'destroy'])->name('vendor.destroy');

        Route::get('/master-data/ledger-account', [LedgerAccountController::class, 'index'])->name('ledger-account.index');
        Route::get('/master-data/ledger-account/{id}', [LedgerAccountController::class, 'show'])->name('ledger-account.show');
        Route::post('/master-data/ledger-account', [LedgerAccountController::class, 'store'])->name('ledger-account.store');
        Route::put('/master-data/ledger-account/{id}', [LedgerAccountController::class, 'update'])->name('ledger-account.update');
        Route::delete('/master-data/ledger-account/{id}', [LedgerAccountController::class, 'destroy'])->name('ledger-account.destroy');
    });

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
