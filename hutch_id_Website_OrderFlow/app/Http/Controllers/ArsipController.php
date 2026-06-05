<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $query = Pesanan::with('pelanggan', 'detailPesanan.produk')
            ->whereIn('status', ['selesai', 'dibatalkan']);

        if ($request->cari) {
            $query->whereHas('pelanggan', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->dari) {
            $query->whereDate('tanggal_pengiriman', '>=', $request->dari);
        }

        if ($request->sampai) {
            $query->whereDate('tanggal_pengiriman', '<=', $request->sampai);
        }

        // If API request, return JSON list
        if ($request->expectsJson() || $request->is('api/*')) {
            $pesanan = $query->latest()->get();
            
            return response()->json($pesanan->map(function ($po) {
                return [
                    'id' => $po->id,
                    'no' => $po->nomor_po,
                    'pelanggan' => $po->pelanggan->nama ?? 'Umum',
                    'pelanggan_id' => $po->pelanggan_id,
                    'tanggal' => $po->tanggal_pesanan->format('d M Y'),
                    'status' => $po->status,
                    'total_nilai' => (int) $po->total_nilai,
                    'total_item' => $po->detailPesanan->count(),
                    'created_at' => $po->created_at,
                    'updated_at' => $po->updated_at,
                ];
            }));
        }

        $pesanan = $query->latest()->paginate(15);

        return view('arsip.index', compact('pesanan'));
    }

    /**
     * Show a specific archived pesanan
     */
    public function show($id)
    {
        $pesanan = Pesanan::with('pelanggan', 'detailPesanan.produk')
            ->whereIn('status', ['selesai', 'dibatalkan'])
            ->find($id);

        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json($pesanan);
    }

    /**
     * Delete an archived pesanan
     */
    public function destroy($id)
    {
        $pesanan = Pesanan::whereIn('status', ['selesai', 'dibatalkan'])->find($id);

        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $pesanan->delete();

        return response()->json(['message' => 'Pesanan berhasil dihapus'], 200);
    }
}