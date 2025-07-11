<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdvanceController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    
        // Dashboard
        Route::get('/dashboard', function () {
            return view('pages.dashboard');
        })->name('dashboard');

        // Advance
        Route::get('/advance', [AdvanceController::class, 'index'])->name('advance.index');
        Route::post('/advance', [AdvanceController::class, 'store'])->name('advance.store');
        // Tambahkan route lainnya di sini
    });

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
