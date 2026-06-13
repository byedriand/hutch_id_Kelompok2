<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Http\Request;

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

// Mobile Sync Route - Accept token from mobile and sync session
Route::get('/auth/mobile-sync', function (Request $request) {
    $token = $request->query('token');
    
    if (!$token) {
        return redirect()->route('login')->with('error', 'Token tidak ditemukan');
    }

    try {
        // Find user by Sanctum token
        $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
        
        if (!$personalAccessToken) {
            return redirect()->route('login')->with('error', 'Token tidak valid');
        }

        $user = $personalAccessToken->tokenable;
        
        // Log the user in using web session
        Auth::login($user, remember: true);
        
        return redirect()->route('dashboard')->with('success', 'Sinkronisasi berhasil! Anda sudah login');
    } catch (\Exception $e) {
        \Log::error('Mobile sync error: ' . $e->getMessage());
        return redirect()->route('login')->with('error', 'Terjadi kesalahan saat sinkronisasi');
    }
});

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

    // Notifications - All authenticated users
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/api/notifikasi/count-unread', [NotifikasiController::class, 'countUnread'])->name('api.notifikasi.countUnread');
    Route::get('/api/notifikasi/recent', [NotifikasiController::class, 'recent'])->name('api.notifikasi.recent');
    Route::post('/notifikasi/{notifikasi}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.markAsRead');
    Route::post('/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.markAllAsRead');
    // Notify operator gudang for stock shortage from draft (no PO saved)
    Route::post('/notifikasi/stok-kurang-draft', [NotifikasiController::class, 'storeStokKurangDraft'])->name('notifikasi.stokKurangDraft');
    Route::delete('/notifikasi/{notifikasi}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');

    // Stock Management - Operator Gudang & Administrator
    Route::middleware(['role:operator_gudang,administrator'])->group(function () {
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/create', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
        Route::post('/produk/{produk}/quick-update', [ProdukController::class, 'quickUpdate'])->name('produk.quickUpdate');
        // Quick update by product name (fallback for notifications without produk_id)
        Route::post('/produk/quick-update-by-name', [ProdukController::class, 'quickUpdateByName'])->name('produk.quickUpdateByName');
    });

    // Product Management for Staff - Staf Penjualan
    Route::middleware(['role:staf_penjualan'])->group(function () {
        Route::get('/produk/staf/tambah', [ProdukController::class, 'staffView'])->name('produk.staff');
        Route::post('/produk/staf/tambah', [ProdukController::class, 'staffStore'])->name('produk.staff.store');
        Route::get('/produk/staf/{produk}/edit', [ProdukController::class, 'staffEdit'])->name('produk.staff.edit');
        Route::put('/produk/staf/{produk}', [ProdukController::class, 'staffUpdate'])->name('produk.staff.update');
    });
});

// Admin Dashboard - Administrator Only
Route::middleware(['auth', 'role:administrator'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

// Public Share - No auth required
Route::get('/po/share/{token}', [PesananController::class, 'publicShare'])->name('pesanan.publicShare');
