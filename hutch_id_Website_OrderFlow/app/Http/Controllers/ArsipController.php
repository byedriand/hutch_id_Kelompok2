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

        $pesanan = $query->latest()->paginate(15);

        return view('arsip.index', compact('pesanan'));
    }
}