<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    /**
     * Clear cached dashboard data so changes to pesanan are reflected immediately.
     *
     * Because dashboard data is cached per-user (5 minutes), any create/update/
     * delete on Pesanan must invalidate the cache for:
     *  - the staff member who owns/created the pesanan (their dashboard is
     *    filtered by created_by), and
     *  - every administrator, pemilik_umkm, and operator_gudang user, since
     *    their dashboards aggregate data across all/most pesanan.
     */
    public static function clearCacheFor(?int $createdBy = null): void
    {
        $userIds = collect();

        if ($createdBy) {
            $userIds->push($createdBy);
        }

        $userIds = $userIds->merge(
            User::whereIn('role', ['administrator', 'pemilik_umkm', 'operator_gudang'])->pluck('id')
        )->unique();

        foreach ($userIds as $userId) {
            Cache::forget('dashboard_' . $userId);
            Cache::forget('dashboard_api_' . $userId);
        }
    }

    public function index()
    {
        $cacheKey = 'dashboard_' . auth()->id();
        
        // Try to get from cache first (5 minutes)
        $dashboardData = Cache::remember($cacheKey, 300, function () {
            return $this->getDashboardData();
        });

        return view('dashboard', $dashboardData);
    }

    private function getDashboardData()
    {
        $userRole = auth()->user()->role;
        
        // Base query with role-based filtering
        $baseQuery = Pesanan::query();
        if ($userRole === 'staf_penjualan') {
            $baseQuery->where('created_by', auth()->id());
        } elseif ($userRole === 'operator_gudang') {
            $baseQuery->whereIn('status', ['dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai']);
        }

        // Fetch all data needed in one optimized query
        $allPesanan = $baseQuery->with('pelanggan', 'detailPesanan.produk')->get();
        
        // Process data in memory (faster than multiple database queries)
        $totalAktif = $allPesanan->whereNotIn('status', ['selesai', 'dibatalkan'])->count();
        $jumlahMenunggu = $allPesanan->where('status', 'menunggu_konfirmasi')->count();
        $siapKirim = $allPesanan->where('status', 'siap_kirim')->count();
        
        $selesaiBulanIni = $allPesanan->where('status', 'selesai')
            ->filter(fn($p) => $p->created_at->year === now()->year && $p->created_at->month === now()->month)
            ->count();
        
        $nilaiSelesai = $allPesanan->where('status', 'selesai')
            ->filter(fn($p) => $p->created_at->year === now()->year && $p->created_at->month === now()->month)
            ->sum('total_nilai');

        // Get latest orders with pagination in-memory
        $pesananMenunggu = $allPesanan->where('status', 'menunggu_konfirmasi')
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        $pesananProduksi = $allPesanan->where('status', 'dalam_produksi')
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        // Compute stock availability flag for dashboard items
        $processCollection = function ($collection) {
            return $collection->map(function ($po) {
                $stokCukup = true;
                $shortageTotal = 0;

                foreach ($po->detailPesanan as $detail) {
                    $produk = $detail->produk;
                    if (!$produk) continue;
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

        return compact(
            'totalAktif', 'jumlahMenunggu', 'siapKirim', 'selesaiBulanIni', 'nilaiSelesai',
            'pesananMenunggu', 'pesananProduksi'
        );
    }

    /**
     * Get dashboard data for API
     */
    public function apiIndex()
    {
        $cacheKey = 'dashboard_api_' . auth()->id();
        
        // Try to get from cache first (5 minutes)
        return response()->json(
            Cache::remember($cacheKey, 300, function () {
                $userRole = auth()->user()->role;
                
                // Base query with role-based filtering
                $baseQuery = Pesanan::query();
                if ($userRole === 'staf_penjualan') {
                    $baseQuery->where('created_by', auth()->id());
                } elseif ($userRole === 'operator_gudang') {
                    $baseQuery->whereIn('status', ['dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai']);
                }

                $allPesanan = $baseQuery->get();

                $totalAktif = $allPesanan->whereNotIn('status', ['selesai', 'dibatalkan'])->count();
                $totalMenunggu = $allPesanan->where('status', 'menunggu_konfirmasi')->count();
                $totalSiapKirim = $allPesanan->where('status', 'siap_kirim')->count();
                
                $totalSelesaiBulanIni = $allPesanan->where('status', 'selesai')
                    ->filter(fn($p) => $p->created_at->year === now()->year && $p->created_at->month === now()->month)
                    ->count();
                
                $nilaiSelesaiBulanIni = $allPesanan->where('status', 'selesai')
                    ->filter(fn($p) => $p->created_at->year === now()->year && $p->created_at->month === now()->month)
                    ->sum('total_nilai');

                return [
                    'total_aktif' => $totalAktif,
                    'total_menunggu' => $totalMenunggu,
                    'total_siap_kirim' => $totalSiapKirim,
                    'total_selesai_bulan_ini' => $totalSelesaiBulanIni,
                    'nilai_selesai_bulan_ini' => intval($nilaiSelesaiBulanIni ?? 0),
                ];
            })
        );
    }
}
