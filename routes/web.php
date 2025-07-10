<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('pages.dashboard');
    })->name('admin.dashboard'); // <- .name() diletakkan setelah Route::get()    
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');
