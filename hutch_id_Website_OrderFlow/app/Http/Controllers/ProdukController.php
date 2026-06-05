<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProdukController extends Controller
{
    /**
     * Helper function to store product photo directly to /public/images/
     */
    private function storeProductPhoto($file)
    {
        if (!$file) {
            return null;
        }
        
        // Generate unique filename with original extension
        $extension = $file->getClientOriginalExtension();
        $filename = \Str::random(32) . '.' . $extension;
        
        $file->move(public_path('images'), $filename);
        return 'images/' . $filename;
    }
    
    /**
     * Helper function to delete product photo from /public/images/
     */
    private function deleteProductPhoto($path)
    {
        if (!$path) {
            return;
        }
        
        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    /**
     * Display a listing of products (for stock management).
     */
    public function index()
    {
        // Only Operator Gudang and Administrator can access stock management
        if (!in_array(auth()->user()->role, ['operator_gudang', 'administrator'])) {
            abort(403, 'Anda tidak memiliki akses ke manajemen stok.');
        }

        $produk = Produk::orderBy('nama')->paginate(15);
        $totalStok = Produk::sum('stok');
        $jumlahProduk = Produk::count();

        return view('produk.index', compact('produk', 'totalStok', 'jumlahProduk'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        // Only Operator Gudang and Administrator can create products
        if (!in_array(auth()->user()->role, ['operator_gudang', 'administrator'])) {
            abort(403, 'Anda tidak memiliki akses ke manajemen stok.');
        }

        return view('produk.create');
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        // Only Operator Gudang and Administrator can create products
        if (!in_array(auth()->user()->role, ['operator_gudang', 'administrator'])) {
            abort(403, 'Anda tidak memiliki akses ke manajemen stok.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:produk,nama',
            'harga_jual' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0|max:999999',
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'nama.required' => 'Nama produk harus diisi.',
            'nama.unique' => 'Nama produk sudah terdaftar dalam sistem.',
            'harga_jual.required' => 'Harga jual harus diisi.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'stok.required' => 'Stok awal harus diisi.',
            'stok.integer' => 'Stok awal harus berupa angka bulat.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $this->storeProductPhoto($request->file('foto'));
        }

        $produk = Produk::create([
            'nama' => $validated['nama'],
            'harga_jual' => $validated['harga_jual'],
            'stok' => $validated['stok'],
            'keterangan' => $validated['keterangan'] ?? null,
            'foto' => $fotoPath,
        ]);

        // Create notification for new product
        Notifikasi::create([
            'pesanan_id' => null,
            'tipe' => 'produk_baru',
            'judul' => "Produk Baru '{$produk->nama}' Ditambahkan",
            'pesan' => "Produk baru '{$produk->nama}' telah ditambahkan dengan stok awal {$produk->stok} unit oleh " . auth()->user()->name . ".",
            'data' => [
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama,
                'harga_jual' => $produk->harga_jual,
                'stok' => $produk->stok,
            ],
            'untuk_roles' => ['administrator', 'pemilik_umkm', 'staf_penjualan', 'operator_gudang'],
            'created_by' => auth()->id(),
        ]);

        // Log the action
        \Log::info("Produk baru '{$produk->nama}' ditambahkan oleh " . auth()->user()->name);

        return redirect()->route('produk.index')
            ->with('success', "Produk '{$produk->nama}' berhasil ditambahkan dengan stok awal {$produk->stok} unit.");
    }

    /**
     * Show the form for editing product stock.
     */
    public function edit(Produk $produk)
    {
        // Only Operator Gudang and Administrator can access stock management
        if (!in_array(auth()->user()->role, ['operator_gudang', 'administrator'])) {
            abort(403, 'Anda tidak memiliki akses ke manajemen stok.');
        }

        return view('produk.edit', compact('produk'));
    }

    /**
     * Update the product stock.
     */
    public function update(Request $request, Produk $produk)
    {
        // Only Operator Gudang and Administrator can update stock
        if (!in_array(auth()->user()->role, ['operator_gudang', 'administrator'])) {
            abort(403, 'Anda tidak memiliki akses ke manajemen stok.');
        }

        $validated = $request->validate([
            'tipe_perubahan' => ['required', Rule::in(['set','tambah','kurangi'])],
            'stok' => [
                Rule::requiredIf($request->input('tipe_perubahan') === 'set'),
                'nullable', 'integer', 'min:0', 'max:999999'
            ],
            'jumlah_perubahan' => [
                Rule::requiredIf(in_array($request->input('tipe_perubahan'), ['tambah','kurangi'])),
                'nullable', 'integer', 'min:0'
            ],
            'keterangan' => 'nullable|string|max:500',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'stok.required' => 'Jumlah stok harus diisi ketika memilih "Set Ke Nilai Baru".',
            'stok.integer' => 'Jumlah stok harus berupa angka.',
            'stok.min' => 'Jumlah stok tidak boleh negatif.',
            'tipe_perubahan.required' => 'Tipe perubahan harus dipilih.',
            'tipe_perubahan.in' => 'Tipe perubahan tidak valid.',
            'jumlah_perubahan.required' => 'Jumlah perubahan harus diisi untuk tipe perubahan ini.',
            'jumlah_perubahan.integer' => 'Jumlah perubahan harus berupa angka.',
            'jumlah_perubahan.min' => 'Jumlah perubahan tidak boleh negatif.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 5MB.',
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

        // Handle photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            $this->deleteProductPhoto($produk->foto);
            $produk->foto = $this->storeProductPhoto($request->file('foto'));
        }

        $produk->save();

        // Resolve or delete related 'stok_kurang' notifications when this product's shortage is addressed
        $this->resolveStokKurangNotificationsForProduct($produk);

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
        // Only Operator Gudang and Administrator can update stock
        if (!in_array(auth()->user()->role, ['operator_gudang', 'administrator'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'stok' => 'required|integer|min:0|max:999999',
        ]);

        $stokLama = $produk->stok;
        $produk->stok = $validated['stok'];
        $produk->save();

        // Resolve or delete related 'stok_kurang' notifications when this product's shortage is addressed
        $this->resolveStokKurangNotificationsForProduct($produk);

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

    private function resolveStokKurangNotificationsForProduct(Produk $produk)
    {
        try {
            // Get ALL stok_kurang notifications (both read and unread) to resolve them
            $notifs = Notifikasi::where('tipe', 'stok_kurang')->get();

            foreach ($notifs as $notif) {
                $data = $notif->data ?? [];
                $detailKurang = $data['detail_kurang'] ?? [];
                $updatedDetails = [];
                $changed = false;

                foreach ($detailKurang as $detail) {
                    $matchesProduct = false;

                    if (isset($detail['produk_id']) && $detail['produk_id'] == $produk->id) {
                        $matchesProduct = true;
                    } elseif (isset($detail['nama_produk']) && str_contains(strtolower($detail['nama_produk']), strtolower($produk->nama))) {
                        $matchesProduct = true;
                    }

                    if (! $matchesProduct) {
                        $updatedDetails[] = $detail;
                        continue;
                    }

                    $needed = $detail['jumlah_dipesan'] ?? $detail['kebutuhan'] ?? null;
                    if ($needed === null) {
                        $needed = ($detail['stok_tersedia'] ?? 0) + ($detail['kurang'] ?? 0);
                    }

                    if ($produk->stok >= $needed) {
                        $changed = true;
                        continue;
                    }

                    // Keep the detail but adjust the remaining shortage.
                    $detail['stok_tersedia'] = $produk->stok;
                    $detail['kurang'] = max(0, $needed - $produk->stok);
                    $updatedDetails[] = $detail;
                    $changed = true;
                }

                if (! $changed) {
                    continue;
                }

                if (empty($updatedDetails)) {
                    // Delete notification if all shortages are resolved
                    $notif->delete();
                    continue;
                }

                $data['detail_kurang'] = $updatedDetails;
                $pesanDetail = collect($updatedDetails)->map(function ($item) {
                    return $item['nama_produk'] . ': ' . $item['kurang'] . ' unit (Stok: ' . ($item['stok_tersedia'] ?? 0) . ')';
                })->implode(', ');

                $notif->update([
                    'data' => $data,
                    'pesan' => 'Pesanan membutuhkan produksi tambahan: ' . $pesanDetail . '. Mohon operator gudang menambah stok yang kurang.',
                ]);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to resolve related stok_kurang notifications: ' . $e->getMessage());
        }
    }

    /**
     * Quick update stock by product name (fallback for notifications without product id).
     */
    public function quickUpdateByName(Request $request)
    {
        if (!in_array(auth()->user()->role, ['operator_gudang', 'administrator'])) {
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

        $this->resolveStokKurangNotificationsForProduct($produk);
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

    /**
     * Display products management page for staff.
     */
    public function staffView()
    {
        // Only Staf Penjualan can access this
        if (auth()->user()->role !== 'staf_penjualan') {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        $produk = Produk::orderBy('nama')->get();

        return view('produk.staff-tambah', compact('produk'));
    }

    /**
     * Store a newly created product by staff.
     */
    public function staffStore(Request $request)
    {
        // Only Staf Penjualan can create products through this method
        if (auth()->user()->role !== 'staf_penjualan') {
            abort(403, 'Anda tidak memiliki akses untuk menambah produk.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:produk,nama',
            'harga_jual' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'nama.required' => 'Nama produk harus diisi.',
            'nama.unique' => 'Nama produk sudah terdaftar dalam sistem.',
            'harga_jual.required' => 'Harga jual harus diisi.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $this->storeProductPhoto($request->file('foto'));
        }

        $produk = Produk::create([
            'nama' => $validated['nama'],
            'harga_jual' => $validated['harga_jual'],
            'stok' => 0,  // Default stok value, staff cannot set initial stock
            'keterangan' => $validated['keterangan'] ?? null,
            'foto' => $fotoPath,
        ]);

        // Create notification for new product
        Notifikasi::create([
            'pesanan_id' => null,
            'tipe' => 'produk_baru',
            'judul' => "Produk Baru '{$produk->nama}' Ditambahkan",
            'pesan' => "Produk baru '{$produk->nama}' telah ditambahkan oleh " . auth()->user()->name . " (Staf Penjualan).",
            'data' => [
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama,
                'harga_jual' => $produk->harga_jual,
                'ditambah_oleh' => auth()->user()->name,
                'role' => 'staf_penjualan',
            ],
            'untuk_roles' => ['administrator', 'pemilik_umkm', 'staf_penjualan', 'operator_gudang'],
            'created_by' => auth()->id(),
        ]);

        // Log the action
        \Log::info("Produk baru '{$produk->nama}' ditambahkan oleh Staf Penjualan " . auth()->user()->name);

        return redirect()->route('produk.staff')
            ->with('success', "Produk '{$produk->nama}' berhasil ditambahkan.");
    }

    /**
     * Show edit form for staff.
     */
    public function staffEdit(Produk $produk)
    {
        // Only Staf Penjualan can edit products through this method
        if (auth()->user()->role !== 'staf_penjualan') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit produk.');
        }

        $allProduk = Produk::orderBy('nama')->get();

        return view('produk.staff-edit', compact('produk', 'allProduk'));
    }

    /**
     * Update product by staff (name, price, description, foto only - NOT STOCK).
     */
    public function staffUpdate(Request $request, Produk $produk)
    {
        // Only Staf Penjualan can update products through this method
        if (auth()->user()->role !== 'staf_penjualan') {
            abort(403, 'Anda tidak memiliki akses untuk mengedit produk.');
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:produk,nama,' . $produk->id,
            'harga_jual' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string|max:1000',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ], [
            'nama.required' => 'Nama produk harus diisi.',
            'nama.unique' => 'Nama produk sudah terdaftar dalam sistem.',
            'harga_jual.required' => 'Harga jual harus diisi.',
            'harga_jual.numeric' => 'Harga jual harus berupa angka.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Format gambar harus jpeg, png, jpg, atau gif.',
            'foto.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $produk->nama = $validated['nama'];
        $produk->harga_jual = $validated['harga_jual'];
        $produk->keterangan = $validated['keterangan'] ?? null;

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            $this->deleteProductPhoto($produk->foto);
            $produk->foto = $this->storeProductPhoto($request->file('foto'));
        }

        $produk->save();

        // Create notification
        Notifikasi::create([
            'pesanan_id' => null,
            'tipe' => 'produk_baru',
            'judul' => "Produk '{$produk->nama}' Diperbarui",
            'pesan' => "Produk '{$produk->nama}' telah diperbarui oleh " . auth()->user()->name . " (Staf Penjualan).",
            'data' => [
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama,
                'harga_jual' => $produk->harga_jual,
                'action' => 'edit',
                'updated_by' => auth()->user()->name,
            ],
            'untuk_roles' => ['administrator', 'pemilik_umkm', 'operator_gudang'],
            'created_by' => auth()->id(),
        ]);

        // Log the action
        \Log::info("Produk '{$produk->nama}' diperbarui oleh Staf Penjualan " . auth()->user()->name);

        return redirect()->route('produk.staff')
            ->with('success', "Produk '{$produk->nama}' berhasil diperbarui.");
    }
}

