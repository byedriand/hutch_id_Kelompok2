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

        // Compute stock availability flag for dashboard items
        $processCollection = function ($collection) {
            return $collection->map(function ($po) {
                $stokCukup = true;
                $shortageTotal = 0;

                foreach ($po->detailPesanan as $detail) {
                    $produk = $detail->produk;
                    if (! $produk) continue;
                    $requested = intval($detail->jumlah);
                    $available = intval($produk->stok ?? 0);
                    if ($requested > $available) {
                        $stokCukup = false;
                        $shortageTotal += ($requested - $available);
                    }
                }

                $po->stok_cukup = $stokCukup;
                $po->shortage_total = $shortageTotal;
                return $po;
            });
        };

        $pesananMenunggu = $processCollection($pesananMenunggu);
        $pesananProduksi = $processCollection($pesananProduksi);

        return view('dashboard', compact(
            'totalAktif', 'jumlahMenunggu', 'siapKirim', 'selesaiBulanIni', 'nilaiSelesai',
            'pesananMenunggu', 'pesananProduksi'
        ));
    }

    /**
     * Get dashboard data for API
     */
    public function apiIndex()
    {
        $userRole = auth()->user()->role;
        
        // Base query with role-based filtering
        $baseQuery = Pesanan::query();
        if ($userRole === 'staf_penjualan') {
            $baseQuery->where('created_by', auth()->id());
        } elseif ($userRole === 'operator_gudang') {
            $baseQuery->whereIn('status', ['dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai']);
        }

        $totalAktif = (clone $baseQuery)->whereNotIn('status', ['selesai', 'dibatalkan'])->count();
        $totalMenunggu = (clone $baseQuery)->where('status', 'menunggu_konfirmasi')->count();
        $totalSiapKirim = (clone $baseQuery)->where('status', 'siap_kirim')->count();
        
        $totalSelesaiBulanIni = (clone $baseQuery)->where('status', 'selesai')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->count();
        
        $nilaiSelesaiBulanIni = (clone $baseQuery)->where('status', 'selesai')
            ->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('total_nilai');

        return response()->json([
            'total_aktif' => $totalAktif,
            'total_menunggu' => $totalMenunggu,
            'total_siap_kirim' => $totalSiapKirim,
            'total_selesai_bulan_ini' => $totalSelesaiBulanIni,
            'nilai_selesai_bulan_ini' => intval($nilaiSelesaiBulanIni ?? 0),
        ]);
    }
}
