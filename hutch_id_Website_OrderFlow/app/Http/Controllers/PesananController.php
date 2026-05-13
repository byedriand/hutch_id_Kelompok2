<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\DetailPesanan;
use App\Models\HistoriStatus;

class PesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pesanan::with('pelanggan', 'detailPesanan.produk');

        // Role-based filtering
        $userRole = auth()->user()->role;
        if ($userRole === 'staf_penjualan') {
            // Staf Penjualan hanya lihat PO yang mereka buat
            $query->where('created_by', auth()->id());
        } elseif ($userRole === 'operator_gudang') {
            // Operator Gudang hanya lihat PO yang sudah dikonfirmasi
            $query->whereIn('status', ['dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai']);
        }
        // Pemilik UMKM dan Administrator dapat lihat semua PO

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

        return view('pesanan.index', compact('pesanan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nomorPo = 'PO-' . now()->format('Ymmd') . '-001';
        $produk = Produk::all();

        return view('pesanan.create', compact('nomorPo', 'produk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_po' => 'required|string|max:255',
            'tanggal_pesanan' => 'required|date',
            'tanggal_pengiriman' => 'required|date|after_or_equal:tanggal_pesanan',
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'total_nilai' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produk,id',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.spesifikasi' => 'nullable|string|max:500',
        ]);

        $items = collect($request->input('items', []))->filter(function ($item) {
            return isset($item['produk_id']) && isset($item['jumlah']) && intval($item['jumlah']) > 0;
        });

        if ($items->isEmpty()) {
            return back()->withInput()->withErrors(['items' => 'Tambahkan minimal satu item pesanan.']);
        }

        $pelanggan = Pelanggan::find($validated['pelanggan_id']);
        if (! $pelanggan) {
            return back()->withInput()->withErrors(['pelanggan_id' => 'Pelanggan tidak ditemukan.']);
        }

        $pesanan = null;
        DB::transaction(function () use ($validated, $items, $pelanggan, &$pesanan, $request) {
            $pesanan = Pesanan::create([
                'nomor_po' => $validated['nomor_po'],
                'tanggal_pesanan' => $validated['tanggal_pesanan'],
                'tanggal_pengiriman' => $validated['tanggal_pengiriman'],
                'pelanggan_id' => $pelanggan->id,
                'total_nilai' => $validated['total_nilai'],
                'status' => 'menunggu_konfirmasi',
                'catatan' => $request->input('catatan'),
                'created_by' => auth()->id(),
            ]);

            $totalCalculated = 0;
            foreach ($items as $item) {
                $produk = Produk::find($item['produk_id']);
                if (! $produk) {
                    throw ValidationException::withMessages(['items' => 'Produk tidak ditemukan.']);
                }

                $jumlah = intval($item['jumlah']);
                $hargaSatuan = $produk->harga_jual;

                $pesanan->detailPesanan()->create([
                    'produk_id' => $produk->id,
                    'jumlah' => $jumlah,
                    'spesifikasi' => $item['spesifikasi'] ?? null,
                    'harga_satuan' => $hargaSatuan,
                ]);

                $totalCalculated += $jumlah * $hargaSatuan;
            }

            if (round($totalCalculated, 2) !== round($validated['total_nilai'], 2)) {
                throw ValidationException::withMessages(['total_nilai' => 'Total nilai tidak sesuai dengan jumlah item yang dipilih.']);
            }

            $pesanan->historiStatus()->create([
                'user_id' => auth()->id(),
                'status' => 'menunggu_konfirmasi',
                'keterangan' => 'Pesanan dibuat dan menunggu persetujuan.',
            ]);
        });

        return redirect()->route('pesanan.index')->with('success', 'PO berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pesanan $pesanan)
    {
        // Use policy authorization
        $this->authorize('view', $pesanan);

        $pesanan->load('pelanggan', 'detailPesanan.produk', 'historiStatus.user', 'creator');

        return view('pesanan.show', compact('pesanan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pesanan $pesanan)
    {
        // Use policy authorization
        $this->authorize('update', $pesanan);

        $produk = Produk::all();
        $pelanggan = Pelanggan::all();

        return view('pesanan.edit', compact('pesanan', 'produk', 'pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pesanan $pesanan)
    {
        // Use policy authorization
        $this->authorize('update', $pesanan);

        $validated = $request->validate([
            'tanggal_pengiriman' => 'required|date|after_or_equal:' . $pesanan->tanggal_pesanan->format('Y-m-d'),
            'catatan' => 'nullable|string|max:1000',
        ]);

        $pesanan->update([
            'tanggal_pengiriman' => $validated['tanggal_pengiriman'],
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('pesanan.show', $pesanan)->with('success', 'Data pesanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pesanan $pesanan)
    {
        $this->authorize('delete', $pesanan);

        $pesanan->delete();

        return redirect()->route('pesanan.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    /**
     * Confirm PO - Change status from menunggu_konfirmasi to dikonfirmasi
     */
    public function confirm(Request $request, Pesanan $pesanan)
    {
        // Use policy authorization
        $this->authorize('confirm', $pesanan);

        // Validate that PO is in "menunggu_konfirmasi" status
        if ($pesanan->status !== 'menunggu_konfirmasi') {
            return back()->with('error', 'Hanya pesanan dengan status "Menunggu Konfirmasi" yang dapat dikonfirmasi.');
        }

        $pesanan->update(['status' => 'dikonfirmasi']);
        $pesanan->historiStatus()->create([
            'user_id' => auth()->id(),
            'status' => 'dikonfirmasi',
            'keterangan' => 'Pesanan dikonfirmasi oleh ' . auth()->user()->name,
        ]);

        return redirect()->route('pesanan.show', $pesanan)->with('success', 'PO berhasil dikonfirmasi.');
    }

    /**
     * Update PO status with role-based restrictions
     */
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        // Use policy authorization
        $this->authorize('changeStatus', $pesanan);

        // Validate status
        $request->validate([
            'status' => 'required|in:dalam_produksi,siap_kirim,selesai,dibatalkan',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $newStatus = $request->status;

        // Check if PO can be changed to this status
        if (!$this->canChangeStatusTo(auth()->user()->role, $newStatus, $pesanan)) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah status ke ' . $newStatus . '.');
        }

        // Prevent status change if already finished or cancelled
        if (in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return back()->with('error', 'Status pesanan tidak dapat diubah setelah selesai atau dibatalkan.');
        }

        // Ensure proper transition (can only mark siap_kirim after dalam_produksi)
        if ($newStatus === 'siap_kirim' && !in_array($pesanan->status, ['dalam_produksi', 'siap_kirim'])) {
            return back()->with('error', 'Pesanan harus dalam status "Dalam Produksi" sebelum dapat ditandai "Siap Kirim".');
        }

        $pesanan->update(['status' => $newStatus]);
        $pesanan->historiStatus()->create([
            'user_id' => auth()->id(),
            'status' => $newStatus,
            'keterangan' => $request->keterangan ?? 'Perubahan status oleh ' . auth()->user()->name,
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui.');
    }

    /**
     * Cancel the specified order by marking it dibatalkan
     */
    public function batalkan(Request $request, Pesanan $pesanan)
    {
        $this->authorize('changeStatus', $pesanan);

        if (in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return back()->with('error', 'Pesanan tidak dapat dibatalkan karena sudah selesai atau dibatalkan.');
        }

        $pesanan->update(['status' => 'dibatalkan']);
        $pesanan->historiStatus()->create([
            'user_id' => auth()->id(),
            'status' => 'dibatalkan',
            'keterangan' => 'Pesanan dibatalkan oleh ' . auth()->user()->name,
        ]);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Helper method to check if user can change status to specific status
     */
    private function canChangeStatusTo(string $userRole, string $newStatus, Pesanan $pesanan): bool
    {
        if ($userRole === 'administrator') {
            return true;
        }

        if ($userRole === 'pemilik_umkm') {
            // Can change to: dalam_produksi, siap_kirim, selesai, dibatalkan
            return in_array($newStatus, ['dalam_produksi', 'siap_kirim', 'selesai', 'dibatalkan']);
        }

        if ($userRole === 'operator_gudang') {
            // Can only change to: dalam_produksi
            return $newStatus === 'dalam_produksi';
        }

        return false;
    }

    public function downloadPdf(Pesanan $pesanan)
    {
        $pesanan->load('pelanggan', 'detailPesanan.produk', 'creator');

        $pdf = Pdf::loadView('pesanan.pdf', compact('pesanan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream($pesanan->nomor_po . '.pdf');
    }

    public function generateShareLink(Request $request, Pesanan $pesanan)
    {
        $token = Crypt::encryptString($pesanan->id . '|' . now()->timestamp);
        $shareLink = route('pesanan.publicShare', ['token' => $token]);

        return back()->with('success', 'Link berbagi telah dibuat.')->with('share_link', $shareLink);
    }

    public function publicShare($token)
    {
        try {
            [$id] = explode('|', Crypt::decryptString($token));
        } catch (\Throwable $e) {
            abort(404);
        }

        $pesanan = Pesanan::with('pelanggan', 'detailPesanan.produk')->findOrFail($id);

        return view('pesanan.public', compact('pesanan'));
    }
}
