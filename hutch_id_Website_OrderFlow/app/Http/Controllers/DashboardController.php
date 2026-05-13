<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;

class DashboardController extends Controller
{
    public function index()
    {
        $userRole = auth()->user()->role;
        
        // Base query with role-based filtering
        $baseQuery = Pesanan::query();
        if ($userRole === 'staf_penjualan') {
            // Staf Penjualan hanya lihat PO mereka sendiri
            $baseQuery->where('created_by', auth()->id());
        } elseif ($userRole === 'operator_gudang') {
            // Operator Gudang hanya lihat PO yang sudah dikonfirmasi
            $baseQuery->whereIn('status', ['dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai']);
        }
        // Pemilik UMKM dan Administrator dapat lihat semua

        $totalAktif = (clone $baseQuery)->whereNotIn('status', ['selesai', 'dibatalkan'])->count();
        $jumlahMenunggu = (clone $baseQuery)->where('status', 'menunggu_konfirmasi')->count();
        $siapKirim = (clone $baseQuery)->where('status', 'siap_kirim')->count();
        
        $selesaiBulanIni = (clone $baseQuery)->where('status', 'selesai')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        
        $nilaiSelesai = (clone $baseQuery)->where('status', 'selesai')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_nilai');

        $pesananMenunggu = (clone $baseQuery)->with('pelanggan', 'detailPesanan.produk')
            ->where('status', 'menunggu_konfirmasi')
            ->latest()
            ->take(10)
            ->get();

        $pesananProduksi = (clone $baseQuery)->with('pelanggan', 'detailPesanan.produk')
            ->where('status', 'dalam_produksi')
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard', compact(
            'totalAktif', 'jumlahMenunggu', 'siapKirim', 'selesaiBulanIni', 'nilaiSelesai',
            'pesananMenunggu', 'pesananProduksi'
        ));
    }
}
