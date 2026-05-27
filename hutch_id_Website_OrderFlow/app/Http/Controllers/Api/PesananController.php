<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class PesananController extends Controller
{
    public function index()
    {
        return response()->json(Pesanan::orderBy('id', 'desc')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'pelanggan' => 'required|string',
            'deskripsi' => 'required|string',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
            'status' => 'required|string',
        ], [
            'pelanggan.required' => 'Nama pelanggan wajib dipilih.',
            'deskripsi.required' => 'Deskripsi pesanan wajib diisi.',
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.integer' => 'Jumlah barang harus berupa angka.',
            'jumlah.min' => 'Jumlah barang minimal 1.',
            'harga.required' => 'Harga wajib diisi.',
            'harga.integer' => 'Harga harus berupa angka.',
            'harga.min' => 'Harga tidak boleh negatif.',
        ]);

        // Generate PO number auto-incremented
        $lastPesanan = Pesanan::orderBy('id', 'desc')->first();
        $nextNumber = 1;
        if ($lastPesanan && preg_match('/PO-(\d+)/', $lastPesanan->no, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }
        $no = 'PO-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $pesanan = Pesanan::create([
            'no' => $no,
            'pelanggan' => $request->pelanggan,
            'deskripsi' => $request->deskripsi,
            'jumlah' => $request->jumlah,
            'harga' => $request->harga,
            'status' => $request->status,
        ]);

        // Increment customer's jumlah_po if customer exists
        $pelanggan = Pelanggan::where('nama', $request->pelanggan)->first();
        if ($pelanggan) {
            $pelanggan->increment('jumlah_po');
        }

        return response()->json($pesanan, 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $pesanan = Pesanan::findOrFail($id);

        $request->validate([
            'status' => 'required|string|in:Draft,Pending,Proses,Selesai',
        ]);

        $pesanan->status = $request->status;
        $pesanan->save();

        return response()->json($pesanan);
    }

    public function destroy($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        
        // Decrement customer's jumlah_po if customer exists
        $pelanggan = Pelanggan::where('nama', $pesanan->pelanggan)->first();
        if ($pelanggan && $pelanggan->jumlah_po > 0) {
            $pelanggan->decrement('jumlah_po');
        }

        $pesanan->delete();

        return response()->json(['message' => 'Pesanan berhasil dihapus']);
    }
}
