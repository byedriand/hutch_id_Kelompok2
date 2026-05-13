<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ArsipController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // PO Creation - Staf Penjualan, Pemilik UMKM, Administrator
    Route::middleware(['role:staf_penjualan,pemilik_umkm,administrator'])->group(function () {
        Route::get('/pesanan/create', [PesananController::class, 'create'])->name('pesanan.create');
        Route::post('/pesanan', [PesananController::class, 'store'])->name('pesanan.store');
    });

    // PO Confirmation - Pemilik UMKM, Administrator
    Route::middleware(['role:pemilik_umkm,administrator'])->group(function () {
        Route::post('/pesanan/{pesanan}/confirm', [PesananController::class, 'confirm'])->name('pesanan.confirm');
    });

    // PO Status Update - Pemilik UMKM, Operator Gudang, Administrator
    Route::middleware(['role:pemilik_umkm,operator_gudang,administrator'])->group(function () {
        Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus'])->name('pesanan.updateStatus');
    });

    // PO Cancel - Pemilik UMKM, Administrator
    Route::middleware(['role:pemilik_umkm,administrator'])->group(function () {
        Route::delete('/pesanan/{pesanan}', [PesananController::class, 'batalkan'])->name('pesanan.batalkan');
    });

    // PO View & Download - Staf Penjualan, Pemilik UMKM, Operator Gudang, Administrator
    Route::middleware(['role:staf_penjualan,pemilik_umkm,operator_gudang,administrator'])->group(function () {
        Route::get('/pesanan', [PesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');
        Route::get('/pesanan/{pesanan}/pdf', [PesananController::class, 'downloadPdf'])->name('pesanan.pdf');
    });

    // PO Edit & Share - Pemilik UMKM, Administrator
    Route::middleware(['role:pemilik_umkm,administrator'])->group(function () {
        Route::get('/pesanan/{pesanan}/edit', [PesananController::class, 'edit'])->name('pesanan.edit');
        Route::put('/pesanan/{pesanan}', [PesananController::class, 'update'])->name('pesanan.update');
        Route::post('/pesanan/{pesanan}/share', [PesananController::class, 'generateShareLink'])->name('pesanan.shareLink');
    });

    // Customer Management - Staf Penjualan, Pemilik UMKM, Administrator
    Route::middleware(['role:staf_penjualan,pemilik_umkm,administrator'])->group(function () {
        Route::resource('pelanggan', PelangganController::class);
    });

    // Archive - Administrator, Pemilik UMKM
    Route::middleware(['role:administrator,pemilik_umkm'])->group(function () {
        Route::get('/arsip', [ArsipController::class, 'index'])->name('arsip.index');
    });

    // API - Staf Penjualan, Pemilik UMKM, Administrator
    Route::middleware(['role:staf_penjualan,pemilik_umkm,administrator'])->group(function () {
        Route::get('/api/pelanggan/search', [PelangganController::class, 'search'])->name('api.pelanggan.search');
        Route::get('/api/pelanggan/{pelanggan}', [PelangganController::class, 'show'])->name('api.pelanggan.show');
    });
});

// Admin Dashboard - Administrator Only
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Public Share - No auth required
Route::get('/po/share/{token}', [PesananController::class, 'publicShare'])->name('pesanan.publicShare');
