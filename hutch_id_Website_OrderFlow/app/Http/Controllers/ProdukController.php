<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    /**
     * Display a listing of products (for stock management).
     */
    public function index()
    {
        // Only Operator Gudang can access stock management
        if (auth()->user()->role !== 'operator_gudang') {
            abort(403, 'Anda tidak memiliki akses ke manajemen stok.');
        }

        $produk = Produk::orderBy('nama')->paginate(15);
        $totalStok = Produk::sum('stok');
        $jumlahProduk = Produk::count();

        return view('produk.index', compact('produk', 'totalStok', 'jumlahProduk'));
    }

    /**
     * Show the form for editing product stock.
     */
    public function edit(Produk $produk)
    {
        // Only Operator Gudang can access stock management
        if (auth()->user()->role !== 'operator_gudang') {
            abort(403, 'Anda tidak memiliki akses ke manajemen stok.');
        }

        return view('produk.edit', compact('produk'));
    }

    /**
     * Update the product stock.
     */
    public function update(Request $request, Produk $produk)
    {
        // Only Operator Gudang can update stock
        if (auth()->user()->role !== 'operator_gudang') {
            abort(403, 'Anda tidak memiliki akses ke manajemen stok.');
        }

        $validated = $request->validate([
            'stok' => 'required|integer|min:0|max:999999',
            'tipe_perubahan' => 'required|in:set,tambah,kurangi',
            'jumlah_perubahan' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string|max:500',
        ], [
            'stok.required' => 'Jumlah stok harus diisi.',
            'stok.integer' => 'Jumlah stok harus berupa angka.',
            'stok.min' => 'Jumlah stok tidak boleh negatif.',
            'tipe_perubahan.required' => 'Tipe perubahan harus dipilih.',
            'tipe_perubahan.in' => 'Tipe perubahan tidak valid.',
        ]);

        $stokLama = $produk->stok;

        // Process stock change
        if ($validated['tipe_perubahan'] === 'set') {
            $produk->stok = $validated['stok'];
        } elseif ($validated['tipe_perubahan'] === 'tambah') {
            $jumlah = $validated['jumlah_perubahan'] ?? 0;
            $produk->stok = $stokLama + $jumlah;
        } elseif ($validated['tipe_perubahan'] === 'kurangi') {
            $jumlah = $validated['jumlah_perubahan'] ?? 0;
            if ($stokLama - $jumlah < 0) {
                return back()->withErrors(['jumlah_perubahan' => 'Stok tidak cukup untuk dikurangi sebanyak itu.'])->withInput();
            }
            $produk->stok = $stokLama - $jumlah;
        }

        $produk->save();

        // Create notification
        $perubahan = $produk->stok - $stokLama;
        $tipePerubahan = $perubahan > 0 ? 'Penambahan' : ($perubahan < 0 ? 'Pengurangan' : 'Penyesuaian');
        
        $judul = "Stok Produk '{$produk->nama}' {$tipePerubahan}";
        $pesan = "Stok {$produk->nama} diubah dari {$stokLama} menjadi {$produk->stok} oleh " . auth()->user()->name . ".";
        
        if ($validated['keterangan']) {
            $pesan .= " Catatan: {$validated['keterangan']}";
        }

        Notifikasi::create([
            'pesanan_id' => null, // Stock updates are not tied to a specific order
            'tipe' => 'stok_ditambah',
            'judul' => $judul,
            'pesan' => $pesan,
            'data' => [
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama,
                'stok_lama' => $stokLama,
                'stok_baru' => $produk->stok,
                'perubahan' => $perubahan,
                'tipe_perubahan' => $validated['tipe_perubahan'],
                'keterangan' => $validated['keterangan'] ?? null,
            ],
            'untuk_roles' => ['administrator', 'pemilik_umkm', 'staf_penjualan', 'operator_gudang'],
            'created_by' => auth()->id(),
        ]);

        // Log the action
        \Log::info("Stok Produk '{$produk->nama}' diubah dari {$stokLama} menjadi {$produk->stok} oleh " . auth()->user()->name);

        return redirect()->route('produk.index')
            ->with('success', "Stok produk '{$produk->nama}' berhasil diperbarui dari {$stokLama} menjadi {$produk->stok}");
    }

    /**
     * Quick update stock via AJAX.
     */
    public function quickUpdate(Request $request, Produk $produk)
    {
        // Only Operator Gudang can update stock
        if (auth()->user()->role !== 'operator_gudang') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'stok' => 'required|integer|min:0|max:999999',
        ]);

        $stokLama = $produk->stok;
        $produk->stok = $validated['stok'];
        $produk->save();

        // Create notification for quick update
        $perubahan = $produk->stok - $stokLama;
        $tipePerubahan = $perubahan > 0 ? 'Penambahan' : ($perubahan < 0 ? 'Pengurangan' : 'Penyesuaian');
        
        Notifikasi::create([
            'pesanan_id' => null,
            'tipe' => 'stok_ditambah',
            'judul' => "Stok Produk '{$produk->nama}' {$tipePerubahan}",
            'pesan' => "Stok {$produk->nama} diperbarui dari {$stokLama} menjadi {$produk->stok} oleh " . auth()->user()->name . " (Update Cepat).",
            'data' => [
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama,
                'stok_lama' => $stokLama,
                'stok_baru' => $produk->stok,
                'perubahan' => $perubahan,
                'tipe_perubahan' => 'set',
            ],
            'untuk_roles' => ['administrator', 'pemilik_umkm', 'staf_penjualan', 'operator_gudang'],
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Stok '{$produk->nama}' diperbarui dari {$stokLama} menjadi {$produk->stok}",
            'stok' => $produk->stok,
        ]);
    }

    /**
     * Quick update stock by product name (fallback for notifications without product id).
     */
    public function quickUpdateByName(Request $request)
    {
        if (auth()->user()->role !== 'operator_gudang') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string',
            'stok' => 'required|integer|min:0|max:999999',
        ]);

        $nama = $validated['nama'];

        // Try exact match first
        $produk = Produk::where('nama', $nama)->first();

        // Fallback to LIKE (case-insensitive depending on collation)
        if (! $produk) {
            $candidates = Produk::where('nama', 'like', '%' . $nama . '%')->get();
            if ($candidates->count() === 0) {
                return response()->json(['error' => 'Produk tidak ditemukan'], 404);
            }
            if ($candidates->count() === 1) {
                $produk = $candidates->first();
            } else {
                // Return candidate list to frontend for user selection
                return response()->json([
                    'success' => false,
                    'candidates' => $candidates->map(function($p){
                        return ['id' => $p->id, 'nama' => $p->nama, 'stok' => $p->stok];
                    })->values(),
                ], 200);
            }
        }

        $stokLama = $produk->stok;
        $produk->stok = $validated['stok'];
        $produk->save();

        $perubahan = $produk->stok - $stokLama;

        Notifikasi::create([
            'pesanan_id' => null,
            'tipe' => 'stok_ditambah',
            'judul' => "Stok Produk '{$produk->nama}' " . ($perubahan > 0 ? 'Penambahan' : ($perubahan < 0 ? 'Pengurangan' : 'Penyesuaian')),
            'pesan' => "Stok {$produk->nama} diperbarui dari {$stokLama} menjadi {$produk->stok} oleh " . auth()->user()->name . " (Update Cepat).",
            'data' => [
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama,
                'stok_lama' => $stokLama,
                'stok_baru' => $produk->stok,
                'perubahan' => $perubahan,
                'tipe_perubahan' => 'set',
            ],
            'untuk_roles' => ['administrator', 'pemilik_umkm', 'staf_penjualan', 'operator_gudang'],
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Stok '{$produk->nama}' diperbarui dari {$stokLama} menjadi {$produk->stok}",
            'stok' => $produk->stok,
        ]);
    }
}
