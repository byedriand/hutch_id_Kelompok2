<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\ArsipController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Authentication Routes (no Sanctum required)
Route::post('/login', [AuthController::class, 'login']);

// Public Routes (no authentication required)
// Products are public data for ordering
Route::get('/produk', [ProdukController::class, 'apiIndex']);
Route::get('/produk/{id}', [ProdukController::class, 'apiShow']);
Route::get('/pelanggan', [PelangganController::class, 'index']);

// Protected Routes - Require Sanctum Authentication
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Pesanan (Orders) API
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::post('/pesanan', [PesananController::class, 'store']);
    Route::get('/pesanan/{pesanan}', [PesananController::class, 'show']);
    Route::put('/pesanan/{pesanan}', [PesananController::class, 'update']);
    Route::delete('/pesanan/{pesanan}', [PesananController::class, 'destroy']);
    Route::patch('/pesanan/{pesanan}/status', [PesananController::class, 'updateStatus']);

    // Pelanggan (Customers) API (authenticated versions with write access)
    Route::post('/pelanggan', [PelangganController::class, 'store']);
    Route::get('/pelanggan/{pelanggan}', [PelangganController::class, 'show']);
    Route::put('/pelanggan/{pelanggan}', [PelangganController::class, 'update']);
    Route::delete('/pelanggan/{pelanggan}', [PelangganController::class, 'destroy']);
    Route::get('/pelanggan/search', [PelangganController::class, 'search']);

    // Arsip PDF API
    Route::get('/arsip-pdf', [ArsipController::class, 'index']);
    Route::get('/arsip-pdf/{id}', [ArsipController::class, 'show']);
    Route::delete('/arsip-pdf/{id}', [ArsipController::class, 'destroy']);

    // Dashboard API
    Route::get('/dashboard', [DashboardController::class, 'apiIndex']);

    // Notifikasi API
    Route::get('/notifikasi', [NotifikasiController::class, 'apiIndex']);
    Route::patch('/notifikasi/{id}/baca', [NotifikasiController::class, 'markAsRead']);
    Route::patch('/notifikasi/tandai-semua-dibaca', [NotifikasiController::class, 'markAllAsRead']);

    // Produk API - Staff/Admin Functions
    Route::post('/produk', [ProdukController::class, 'apiStore']);
    Route::put('/produk/{produk}', [ProdukController::class, 'apiUpdate']);
    Route::post('/produk/{produk}/stok', [ProdukController::class, 'apiUpdateStok']);
    Route::patch('/produk/{produk}/stok-quick', [ProdukController::class, 'apiQuickUpdateStok']);

    // Pesanan PDF API
    Route::get('/pesanan/{pesanan}/pdf', [PesananController::class, 'apiDownloadPdf']);
    Route::get('/pesanan/{pesanan}/pdf-file', [PesananController::class, 'apiPdfFile']);

    // Arsip PDF Download
    Route::get('/arsip-pdf/{id}/pdf', [ArsipController::class, 'apiDownloadPdf']);

    // Chatbot API
    Route::post('/chatbot/message', [\App\Http\Controllers\Api\ChatbotController::class, 'sendMessage']);
});

