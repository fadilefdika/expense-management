<?php

use App\Http\Controllers\AdvanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettlementController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    
        // Dashboard
        Route::get('/all-report', [DashboardController::class, 'index'])->name('all-report');

        // Advance
        Route::get('/advance', [AdvanceController::class, 'index'])->name('advance.index');
        Route::post('/advance', [AdvanceController::class, 'store'])->name('advance.store');

        // Settlement
        Route::get('/settlement/{id}', [SettlementController::class, 'create'])->name('settlement.create');
        Route::post('/settlement', [SettlementController::class, 'store'])->name('settlement.store');
  
    });

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
