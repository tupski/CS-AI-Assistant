<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PeraturanController;
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

    // Route FAQ (admin & supervisor)
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::resource('faq', FaqController::class)->except(['show', 'create', 'edit']);
    });

    // Route Kategori (hanya admin)
    Route::middleware('role:admin')->group(function () {
        Route::resource('kategori', KategoriController::class)->except(['show', 'create', 'edit']);
    });

    // Route Peraturan (semua bisa lihat, admin bisa edit)
    Route::get('/peraturan', [PeraturanController::class, 'index'])->name('peraturan.index');
    Route::middleware('role:admin')->group(function () {
        Route::post('/peraturan', [PeraturanController::class, 'store'])->name('peraturan.store');
        Route::put('/peraturan/{peraturan}', [PeraturanController::class, 'update'])->name('peraturan.update');
        Route::delete('/peraturan/{peraturan}', [PeraturanController::class, 'destroy'])->name('peraturan.destroy');
    });

    // Route Pengaturan (hanya admin)
    Route::middleware('role:admin')->prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('/', [PengaturanController::class, 'index'])->name('index');
        Route::post('/api', [PengaturanController::class, 'updateApi'])->name('update-api');
        Route::post('/guidelines', [PengaturanController::class, 'updateGuidelines'])->name('update-guidelines');
        Route::post('/user', [PengaturanController::class, 'tambahUser'])->name('tambah-user');
        Route::put('/user/{user}', [PengaturanController::class, 'updateUser'])->name('update-user');
        Route::delete('/user/{user}', [PengaturanController::class, 'hapusUser'])->name('hapus-user');
    });
});
