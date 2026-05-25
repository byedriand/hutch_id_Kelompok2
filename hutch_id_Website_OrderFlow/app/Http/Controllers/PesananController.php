<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Pesanan;
use App\Models\Produk;
use App\Models\Pelanggan;
use App\Models\DetailPesanan;
use App\Models\HistoriStatus;
use App\Models\Notifikasi;

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
            $query->where(function ($q) use ($request) {
                $q->where('nomor_po', 'like', '%' . $request->cari . '%')
                    ->orWhereHas('pelanggan', function ($q) use ($request) {
                        $q->where('nama', 'like', '%' . $request->cari . '%');
                    })
                    ->orWhereHas('detailPesanan.produk', function ($q) use ($request) {
                        $q->where('nama', 'like', '%' . $request->cari . '%');
                    });
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

        // Advanced filters
        if ($request->min_total) {
            $query->where('total_nilai', '>=', $request->min_total);
        }

        if ($request->max_total) {
            $query->where('total_nilai', '<=', $request->max_total);
        }

        if ($request->produk) {
            $query->whereHas('detailPesanan.produk', function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->produk . '%');
            });
        }

        if ($request->multi_item) {
            $query->whereHas('detailPesanan', function ($q) {
                $q->selectRaw('pesanan_id, count(*) as item_count')
                  ->groupBy('pesanan_id')
                  ->havingRaw('count(*) > 1');
            }, '>=', 1);
        }

        $pesanan = $query->latest()->paginate(15);

        // Calculate shortage info for each paginated pesanan
        $pesanan->getCollection()->transform(function ($po) {
            $shortageTotal = 0;
            $shortageDetails = [];

            foreach ($po->detailPesanan as $detail) {
                $produk = $detail->produk;
                if (! $produk) continue;

                $requested = intval($detail->jumlah);
                $available = intval($produk->stok ?? 0);

                if ($requested > $available) {
                    $kurang = $requested - $available;
                    $shortageTotal += $kurang;
                    $shortageDetails[] = [
                        'produk_id' => $produk->id,
                        'nama_produk' => $produk->nama,
                        'jumlah_dipesan' => $requested,
                        'stok_tersedia' => $available,
                        'kurang' => $kurang,
                    ];
                }
            }

            $po->has_shortage = $shortageTotal > 0;
            $po->shortage_total = $shortageTotal;
            $po->shortage_details = $shortageDetails;

            return $po;
        });

        return view('pesanan.index', compact('pesanan'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $nomorPo = $this->generateNomorPo();
        $produk = Produk::all();

        return view('pesanan.create', compact('nomorPo', 'produk'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        $requestedQuantities = [];
        foreach ($items as $item) {
            $productId = intval($item['produk_id']);
            $requestedQuantities[$productId] = ($requestedQuantities[$productId] ?? 0) + intval($item['jumlah']);
        }

        $detail_kurang = [];
        foreach ($requestedQuantities as $productId => $qty) {
            $produk = Produk::find($productId);
            if (! $produk) {
                return back()->withInput()->withErrors(['items' => 'Produk tidak ditemukan.']);
            }
            if ($qty > $produk->stok) {
                $detail_kurang[] = [
                    'produk_id' => $produk->id,
                    'nama_produk' => $produk->nama,
                    'jumlah_dipesan' => $qty,
                    'stok_tersedia' => $produk->stok,
                    'kurang' => $qty - $produk->stok,
                ];
            }
        }

        $nomorPo = $this->generateNomorPo($validated['tanggal_pesanan']);
        $pesanan = null;
        DB::transaction(function () use ($validated, $items, $pelanggan, &$pesanan, $request, $nomorPo) {
            $pesanan = Pesanan::create([
                'nomor_po' => $nomorPo,
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

            if ($request->boolean('send_shortage_notification') && ! empty($detail_kurang)) {
                NotifikasiController::createStokKurangNotifikasi($pesanan, $detail_kurang);
            }
        });

        $message = 'PO berhasil disimpan.';
        if ($request->boolean('send_shortage_notification')) {
            $message = ! empty($detail_kurang)
                ? 'PO berhasil disimpan dan notifikasi kekurangan stok telah dikirim.'
                : 'PO berhasil disimpan. Tidak ada kekurangan stok.';
        }

        return redirect()->route('pesanan.index')->with('success', $message);
    }

    /**
     * Generate a unique PO number using the provided tanggal_pesanan or current date.
     */
    private function generateNomorPo(string $tanggalPesanan = null): string
    {
        $date = $tanggalPesanan ? Carbon::parse($tanggalPesanan)->format('Ymmd') : now()->format('Ymmd');
        $prefix = 'PO-' . $date . '-';

        $lastPesanan = Pesanan::where('nomor_po', 'like', $prefix . '%')
            ->orderBy('nomor_po', 'desc')
            ->first();

        if (! $lastPesanan) {
            return $prefix . '001';
        }

        $lastSequence = intval(substr($lastPesanan->nomor_po, strrpos($lastPesanan->nomor_po, '-') + 1));
        return $prefix . str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pesanan $pesanan)
    {
        // Use policy authorization
        $this->authorize('view', $pesanan);

        $pesanan->load('pelanggan', 'detailPesanan.produk', 'historiStatus.user', 'creator');

        // Calculate any stock shortages for display in the detail view
        $detail_kurang = [];
        foreach ($pesanan->detailPesanan as $detail) {
            $produk = $detail->produk;
            if ($produk && $produk->stok < $detail->jumlah) {
                $detail_kurang[] = [
                    'produk_id' => $produk->id,
                    'nama_produk' => $produk->nama,
                    'jumlah_dipesan' => $detail->jumlah,
                    'stok_tersedia' => $produk->stok,
                    'kurang' => $detail->jumlah - $produk->stok,
                ];
            }
        }

        $statusOptions = $this->getAllowedStatusOptions($pesanan, auth()->user());

        return view('pesanan.show', compact('pesanan', 'statusOptions', 'detail_kurang'));
    }

    /**
     * Determine which status options are allowed for the current user and order.
     */
    private function getAllowedStatusOptions(Pesanan $pesanan, $user): array
    {
        $statusOptions = [];

        if ($pesanan->status === 'menunggu_konfirmasi') {
            $statusOptions = [
                'dikonfirmasi' => 'Dikonfirmasi',
                'dibatalkan' => 'Dibatalkan',
            ];
        } elseif ($pesanan->status === 'dikonfirmasi') {
            $statusOptions = [
                'dalam_produksi' => 'Dalam Produksi',
                'dibatalkan' => 'Dibatalkan',
            ];
        } elseif ($pesanan->status === 'dalam_produksi') {
            $statusOptions = [
                'siap_kirim' => 'Siap Kirim',
                'dibatalkan' => 'Dibatalkan',
            ];
        } elseif ($pesanan->status === 'siap_kirim') {
            $statusOptions = [
                'selesai' => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
            ];
        }

        return array_filter($statusOptions, function ($label, $status) use ($user, $pesanan) {
            return $this->canChangeStatusTo($user->role, $status, $pesanan);
        }, ARRAY_FILTER_USE_BOTH);
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

        // Check stock for each product in order
        $pesanan->load('detailPesanan.produk');
        $detail_kurang = [];
        foreach ($pesanan->detailPesanan as $detail) {
            if ($detail->produk->stok < $detail->jumlah) {
                $detail_kurang[] = [
                    'nama_produk' => $detail->produk->nama,
                    'jumlah_dipesan' => $detail->jumlah,
                    'stok_tersedia' => $detail->produk->stok,
                    'kurang' => $detail->jumlah - $detail->produk->stok,
                ];
            }
        }

        // Create notification if stock is insufficient
        if (!empty($detail_kurang)) {
            NotifikasiController::createStokKurangNotifikasi($pesanan, $detail_kurang);
        }

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
        $allowedStatuses = ['dalam_produksi', 'siap_kirim', 'selesai', 'dibatalkan'];
        if ($pesanan->status === 'menunggu_konfirmasi') {
            $allowedStatuses[] = 'dikonfirmasi';
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', $allowedStatuses),
            'keterangan' => 'nullable|string|max:500',
            'tanggal_dikirim' => 'nullable|date',
            'nomor_resi' => 'nullable|string|max:255',
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

        $updateData = ['status' => $newStatus];
        if ($newStatus === 'siap_kirim') {
            // save shipping details if provided
            $updateData['tanggal_dikirim'] = $request->input('tanggal_dikirim');
            $updateData['nomor_resi'] = $request->input('nomor_resi');
        } else {
            // clear shipping info if status moved away from siap_kirim
            if (in_array($pesanan->status, ['siap_kirim']) && $newStatus !== 'siap_kirim') {
                $updateData['tanggal_dikirim'] = null;
                $updateData['nomor_resi'] = null;
            }
        }

        $pesanan->update($updateData);

        $historiKeterangan = $request->keterangan ?? 'Perubahan status oleh ' . auth()->user()->name;
        if ($newStatus === 'siap_kirim') {
            $extra = [];
            if ($pesanan->tanggal_dikirim) {
                $extra[] = 'Tgl kirim: ' . $pesanan->tanggal_dikirim->format('d M Y');
            }
            if ($pesanan->nomor_resi) {
                $extra[] = 'Resi: ' . $pesanan->nomor_resi;
            }
            if (!empty($extra)) {
                $historiKeterangan .= ' (' . implode(' | ', $extra) . ')';
            }
        }

        $pesanan->historiStatus()->create([
            'user_id' => auth()->id(),
            'status' => $newStatus,
            'keterangan' => $historiKeterangan,
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
            // Can change to: dikonfirmasi, dalam_produksi, siap_kirim, selesai, dibatalkan
            if ($pesanan->status === 'menunggu_konfirmasi' && $newStatus === 'dikonfirmasi') {
                return true;
            }
            return in_array($newStatus, ['dalam_produksi', 'siap_kirim', 'selesai', 'dibatalkan']);
        }

        if ($userRole === 'operator_gudang') {
            // Can only change to: dalam_produksi
            return $newStatus === 'dalam_produksi';
        }

        if ($userRole === 'staf_penjualan') {
            // Staf Penjualan dapat membatalkan PO yang mereka buat sebelum dikonfirmasi
            if ($pesanan->status === 'menunggu_konfirmasi' && $newStatus === 'dibatalkan') {
                return true;
            }
            return false;
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
