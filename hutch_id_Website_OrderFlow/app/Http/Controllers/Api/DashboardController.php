<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPesanan = Pesanan::count();
        $totalPelanggan = Pelanggan::count();
        $poPending = Pesanan::where('status', 'Pending')->count();
        $poSelesai = Pesanan::where('status', 'Selesai')->count();

        return response()->json([
            'totalPesanan' => $totalPesanan,
            'totalPelanggan' => $totalPelanggan,
            'poPending' => $poPending,
            'poSelesai' => $poSelesai,
        ]);
    }
}
