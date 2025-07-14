<?php

use App\Http\Controllers\AdvanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseTypeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettlementController;

Route::get('/', function () {
    return redirect('/login');
});
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    
        // Dashboard
        Route::get('/all-report', [DashboardController::class, 'index'])->name('all-report');
        Route::get('/all-report/settlement/{id}', [SettlementController::class, 'create'])->name('settlement.create');
        Route::post('/all-report/settlement/{id}', [SettlementController::class, 'update'])->name('settlement.update');

        // Advance
        Route::get('/advance', [AdvanceController::class, 'index'])->name('advance.index');
        Route::post('/advance', [AdvanceController::class, 'store'])->name('advance.store');

        //Reports 
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');

        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
  
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
    });

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
