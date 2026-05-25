<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Display all notifications
     */
    public function index(Request $request)
    {
        $query = Notifikasi::with('pesanan', 'creator')
            ->whereJsonContains('untuk_roles', auth()->user()->role);

        if ($request->filter === 'unread') {
            $query->whereNull('dibaca_at');
        }

        $notifikasis = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('notifikasi.index', compact('notifikasis'));
    }

    /**
     * Get unread notifications count
     */
    public function countUnread()
    {
        $count = Notifikasi::whereJsonContains('untuk_roles', auth()->user()->role)
            ->whereNull('dibaca_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent unread notifications for dropdown
     */
    public function recent()
    {
        $notifikasis = Notifikasi::with('pesanan', 'creator')
            ->whereJsonContains('untuk_roles', auth()->user()->role)
            ->whereNull('dibaca_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($notifikasis);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Notifikasi $notifikasi)
    {
        $notifikasi->update(['dibaca_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        Notifikasi::whereJsonContains('untuk_roles', auth()->user()->role)
            ->whereNull('dibaca_at')
            ->update(['dibaca_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Semua notifikasi berhasil ditandai sudah dibaca.');
    }

    /**
     * Delete notification
     */
    public function destroy(Notifikasi $notifikasi)
    {
        $notifikasi->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }

    /**
     * Create stock shortage notification
     */
    public static function createStokKurangNotifikasi(Pesanan $pesanan, array $detail_kurang)
    {
        $pesan_detail = collect($detail_kurang)->map(function ($item) {
            return $item['nama_produk'] . ': ' . $item['kurang'] . ' unit (Stok: ' . $item['stok_tersedia'] . ')';
        })->implode(', ');

        Notifikasi::create([
            'pesanan_id' => $pesanan->id,
            'tipe' => 'stok_kurang',
            'judul' => 'Stok Tidak Cukup - ' . $pesanan->nomor_po,
            'pesan' => 'Pesanan membutuhkan produksi tambahan: ' . $pesan_detail . '. Mohon operator gudang menambah stok yang kurang.',
            'data' => [
                'nomor_po' => $pesanan->nomor_po,
                'detail_kurang' => $detail_kurang,
            ],
            'untuk_roles' => ['operator_gudang', 'pemilik_umkm', 'administrator'],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Store stock shortage notification coming from a draft PO (without saving Pesanan).
     */
    public function storeStokKurangDraft(Request $request)
    {
        $data = $request->validate([
            'nomor_po' => 'nullable|string',
            'detail_kurang' => 'required|array|min:1',
            'detail_kurang.*.nama_produk' => 'required|string',
            'detail_kurang.*.stok_tersedia' => 'required|integer',
            'detail_kurang.*.kebutuhan' => 'required|integer',
            'detail_kurang.*.kurang' => 'required|integer',
        ]);

        $pesan_detail = collect($data['detail_kurang'])->map(function ($item) {
            return $item['nama_produk'] . ': ' . $item['kurang'] . ' unit (Stok: ' . $item['stok_tersedia'] . ')';
        })->implode(', ');

        $notifikasi = Notifikasi::create([
            'pesanan_id' => null,
            'tipe' => 'stok_kurang',
            'judul' => 'Stok Tidak Cukup' . ($data['nomor_po'] ? ' - ' . $data['nomor_po'] : ''),
            'pesan' => 'Pesanan (draft) membutuhkan produksi tambahan: ' . $pesan_detail . '. Mohon operator gudang menambah stok yang kurang.',
            'data' => [
                'nomor_po' => $data['nomor_po'] ?? null,
                'detail_kurang' => $data['detail_kurang'],
            ],
            'untuk_roles' => ['operator_gudang', 'pemilik_umkm', 'administrator'],
            'created_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'notifikasi_id' => $notifikasi->id]);
    }

    /**
     * Create order created notification
     */
    public static function createPesananDibuatNotifikasi(Pesanan $pesanan)
    {
        Notifikasi::create([
            'pesanan_id' => $pesanan->id,
            'tipe' => 'pesanan_dibuat',
            'judul' => 'Pesanan Baru Dibuat',
            'pesan' => 'Pesanan ' . $pesanan->nomor_po . ' telah dibuat oleh ' . auth()->user()->name,
            'data' => [
                'nomor_po' => $pesanan->nomor_po,
                'pelanggan' => $pesanan->pelanggan->nama ?? 'Unknown',
            ],
            'untuk_roles' => ['pemilik_umkm', 'administrator'],
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Create stock added notification
     */
    public static function createStokDitambahNotifikasi(Pesanan $pesanan, $jumlah_tambah, $detail = [])
    {
        Notifikasi::create([
            'pesanan_id' => $pesanan->id,
            'tipe' => 'stok_ditambah',
            'judul' => 'Stok Ditambahkan - ' . $pesanan->nomor_po,
            'pesan' => 'Operator gudang telah menambahkan stok untuk pesanan ini. Jumlah: ' . $jumlah_tambah . ' unit',
            'data' => [
                'nomor_po' => $pesanan->nomor_po,
                'jumlah_tambah' => $jumlah_tambah,
                'detail' => $detail,
            ],
            'untuk_roles' => ['pemilik_umkm', 'administrator', 'staf_penjualan'],
            'created_by' => auth()->id(),
        ]);
    }
}
