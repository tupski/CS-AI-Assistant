<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Route Autentikasi
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'tampilkanLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'prosesLogin'])->name('login.proses');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/generate', [DashboardController::class, 'generateJawaban'])->name('dashboard.generate');
    Route::get('/dashboard/riwayat', [DashboardController::class, 'riwayat'])->name('dashboard.riwayat');
});
