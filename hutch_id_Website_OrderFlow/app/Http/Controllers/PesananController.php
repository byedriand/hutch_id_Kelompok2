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
use App\Services\WhatsAppService;

class PesananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pesanan::with('pelanggan', 'detailPesanan.produk');

        // Role-based filtering - TEMPORARILY DISABLED FOR TESTING
        // $userRole = auth()->user()->role;
        // if ($userRole === 'staf_penjualan') {
        //     // Staf Penjualan hanya lihat PO yang mereka buat
        //     $query->where('created_by', auth()->id());
        // } elseif ($userRole === 'operator_gudang') {
        //     // Operator Gudang hanya lihat PO yang sudah dikonfirmasi
        //     $query->whereIn('status', ['dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai']);
        // }
        // Pemilik UMKM dan Administrator dapat lihat semua PO
        
        // TEMPORARY TEST: Show all pesanan
        // (This will be reverted after confirming the layout works)

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

        // If API request, return JSON without pagination
        if ($request->expectsJson() || $request->is('api/*')) {
            $pesanan = $query->latest()->get();
            
            // Transform data for API response - return full details
            $pesanan = $pesanan->map(function ($po) {
                return [
                    'id' => $po->id,
                    'nomor_po' => $po->nomor_po,
                    'tanggal_pesanan' => $po->tanggal_pesanan->format('Y-m-d'),
                    'tanggal_pengiriman' => $po->tanggal_pengiriman->format('Y-m-d'),
                    'tanggal_dikirim' => $po->tanggal_dikirim ? $po->tanggal_dikirim->format('Y-m-d') : null,
                    'nomor_resi' => $po->nomor_resi,
                    'pelanggan_id' => $po->pelanggan_id,
                    'pelanggan' => [
                        'id' => $po->pelanggan?->id,
                        'nama' => $po->pelanggan?->nama,
                        'telepon' => $po->pelanggan?->telepon,
                        'email' => $po->pelanggan?->email,
                        'alamat' => $po->pelanggan?->alamat,
                    ],
                    'total_nilai' => (float) $po->total_nilai,
                    'status' => $po->status,
                    'catatan' => $po->catatan,
                    'alasan_pembatalan' => $po->alasan_pembatalan,
                    'created_by' => $po->created_by,
                    'detail_pesanan' => $po->detailPesanan->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'pesanan_id' => $detail->pesanan_id,
                            'produk_id' => $detail->produk_id,
                            'jumlah' => $detail->jumlah,
                            'spesifikasi' => $detail->spesifikasi,
                            'harga_satuan' => (float) $detail->harga_satuan,
                            'produk' => [
                                'id' => $detail->produk?->id,
                                'nama' => $detail->produk?->nama,
                                'foto' => $detail->produk?->foto,
                                'harga_jual' => (float) $detail->produk?->harga_jual,
                                'stok' => $detail->produk?->stok,
                                'keterangan' => $detail->produk?->keterangan,
                            ],
                        ];
                    }),
                    'created_at' => $po->created_at,
                    'updated_at' => $po->updated_at,
                ];
            });
            
            return response()->json($pesanan);
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
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Tambahkan minimal satu item pesanan.',
                    'errors' => ['items' => ['Tambahkan minimal satu item pesanan.']],
                ], 422);
            }
            return back()->withInput()->withErrors(['items' => 'Tambahkan minimal satu item pesanan.']);
        }

        $pelanggan = Pelanggan::find($validated['pelanggan_id']);
        if (! $pelanggan) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Pelanggan tidak ditemukan.',
                    'errors' => ['pelanggan_id' => ['Pelanggan tidak ditemukan.']],
                ], 422);
            }
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

        // Invalidate dashboard cache so the new PO shows up immediately,
        // both for the staff who created it and for owners/admin/gudang.
        DashboardController::clearCacheFor($pesanan->created_by);

        $message = 'PO berhasil disimpan.';
        if ($request->boolean('send_shortage_notification')) {
            $message = ! empty($detail_kurang)
                ? 'PO berhasil disimpan dan notifikasi kekurangan stok telah dikirim.'
                : 'PO berhasil disimpan. Tidak ada kekurangan stok.';
        }

        // Mobile app (and any other API client) expects a JSON payload back,
        // not an HTML redirect. Without this, the Flutter app's
        // ApiService.createPesanan() fails to parse the response and treats
        // a successfully-saved PO as a failure — this also happens for POs
        // with insufficient stock, which ARE allowed to be saved (same as
        // the website), so the order silently appears stuck on "Buat
        // Pesanan" even though nothing is actually blocking it server-side.
        if ($request->expectsJson() || $request->is('api/*')) {
            $pesanan->load('pelanggan', 'detailPesanan.produk');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'id' => $pesanan->id,
                    'nomor_po' => $pesanan->nomor_po,
                    'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                    'tanggal_pengiriman' => $pesanan->tanggal_pengiriman,
                    'pelanggan_id' => $pesanan->pelanggan_id,
                    'pelanggan' => $pesanan->pelanggan,
                    'total_nilai' => $pesanan->total_nilai,
                    'status' => $pesanan->status,
                    'catatan' => $pesanan->catatan,
                    'created_by' => $pesanan->created_by,
                    'detail_pesanan' => $pesanan->detailPesanan,
                    'detail_kurang' => $detail_kurang,
                ],
            ], 201);
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

        // If API request, return JSON
        if (request()->expectsJson() || request()->is('api/*')) {
            return response()->json([
                'data' => [
                    'id' => $pesanan->id,
                    'nomor_po' => $pesanan->nomor_po,
                    'tanggal_pesanan' => $pesanan->tanggal_pesanan->format('Y-m-d'),
                    'tanggal_pengiriman' => $pesanan->tanggal_pengiriman->format('Y-m-d'),
                    'tanggal_dikirim' => $pesanan->tanggal_dikirim ? $pesanan->tanggal_dikirim->format('Y-m-d') : null,
                    'nomor_resi' => $pesanan->nomor_resi,
                    'pelanggan_id' => $pesanan->pelanggan_id,
                    'pelanggan' => [
                        'id' => $pesanan->pelanggan?->id,
                        'nama' => $pesanan->pelanggan?->nama,
                        'telepon' => $pesanan->pelanggan?->telepon,
                        'email' => $pesanan->pelanggan?->email,
                        'alamat' => $pesanan->pelanggan?->alamat,
                    ],
                    'total_nilai' => (float) $pesanan->total_nilai,
                    'status' => $pesanan->status,
                    'catatan' => $pesanan->catatan,
                    'alasan_pembatalan' => $pesanan->alasan_pembatalan,
                    'created_by' => $pesanan->created_by,
                    'creator' => $pesanan->creator ? [
                        'id' => $pesanan->creator->id,
                        'name' => $pesanan->creator->display_name,
                        'role' => $pesanan->creator->role,
                    ] : null,
                    'histori_status' => $pesanan->historiStatus->map(function ($history) {
                        return [
                            'id' => $history->id,
                            'status' => $history->status,
                            'keterangan' => $history->keterangan,
                            'user' => $history->user ? [
                                'id' => $history->user->id,
                                'name' => $history->user->display_name,
                            ] : null,
                            'created_at' => $history->created_at,
                        ];
                    }),
                    'detail_pesanan' => $pesanan->detailPesanan->map(function ($detail) {
                        return [
                            'id' => $detail->id,
                            'pesanan_id' => $detail->pesanan_id,
                            'produk_id' => $detail->produk_id,
                            'jumlah' => $detail->jumlah,
                            'spesifikasi' => $detail->spesifikasi,
                            'harga_satuan' => (float) $detail->harga_satuan,
                            'produk' => [
                                'id' => $detail->produk?->id,
                                'nama' => $detail->produk?->nama,
                                'foto' => $detail->produk?->foto,
                                'harga_jual' => (float) $detail->produk?->harga_jual,
                                'stok' => $detail->produk?->stok,
                                'keterangan' => $detail->produk?->keterangan,
                            ],
                        ];
                    }),
                    'created_at' => $pesanan->created_at,
                    'updated_at' => $pesanan->updated_at,
                ]
            ]);
        }

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

        DashboardController::clearCacheFor($pesanan->created_by);

        if ($request->expectsJson() || $request->is('api/*')) {
            $pesanan->load('pelanggan', 'detailPesanan.produk');

            return response()->json([
                'success' => true,
                'message' => 'Data pesanan berhasil diperbarui.',
                'data' => [
                    'id' => $pesanan->id,
                    'nomor_po' => $pesanan->nomor_po,
                    'tanggal_pesanan' => $pesanan->tanggal_pesanan,
                    'tanggal_pengiriman' => $pesanan->tanggal_pengiriman,
                    'pelanggan_id' => $pesanan->pelanggan_id,
                    'pelanggan' => $pesanan->pelanggan,
                    'total_nilai' => $pesanan->total_nilai,
                    'status' => $pesanan->status,
                    'catatan' => $pesanan->catatan,
                    'created_by' => $pesanan->created_by,
                    'detail_pesanan' => $pesanan->detailPesanan,
                ],
            ], 200);
        }

        return redirect()->route('pesanan.show', $pesanan)->with('success', 'Data pesanan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Pesanan $pesanan)
    {
        $this->authorize('delete', $pesanan);

        $createdBy = $pesanan->created_by;
        $pesanan->delete();

        DashboardController::clearCacheFor($createdBy);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dihapus.',
            ], 200);
        }

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

        DashboardController::clearCacheFor($pesanan->created_by);

        return redirect()->route('pesanan.show', $pesanan)->with('success', 'PO berhasil dikonfirmasi.');
    }

    /**
     * Update PO status with role-based restrictions
     */
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        // Eager load pelanggan relationship
        $pesanan->load('pelanggan');

        // Use policy authorization
        $this->authorize('changeStatus', $pesanan);

        // Initialize WhatsApp variables
        $whatsappResult = null;
        $pdfSent = false;

        // Validate status
        $allowedStatuses = ['dalam_produksi', 'siap_kirim', 'selesai', 'dibatalkan'];
        if ($pesanan->status === 'menunggu_konfirmasi') {
            $allowedStatuses[] = 'dikonfirmasi';
        }

        $request->validate([
            'status' => 'required|in:' . implode(',', $allowedStatuses),
            'keterangan' => 'nullable|string|max:500',
            'alasan_pembatalan' => 'nullable|string|min:5|max:500',
            'tanggal_dikirim' => 'nullable|date',
            'nomor_resi' => 'nullable|string|max:255',
        ]);

        $newStatus = $request->status;

        // Helper: kembalikan JSON untuk request AJAX, atau redirect biasa untuk request normal
        $failResponse = function (string $message) use ($request) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        };

        // Check if PO can be changed to this status
        if (!$this->canChangeStatusTo(auth()->user()->role, $newStatus, $pesanan)) {
            return $failResponse('Anda tidak memiliki izin untuk mengubah status ke ' . $newStatus . '.');
        }

        // Prevent status change if already finished or cancelled
        if (in_array($pesanan->status, ['selesai', 'dibatalkan'])) {
            return $failResponse('Status pesanan tidak dapat diubah setelah selesai atau dibatalkan.');
        }

        // Ensure proper transition (can only mark siap_kirim after dalam_produksi)
        if ($newStatus === 'siap_kirim' && !in_array($pesanan->status, ['dalam_produksi', 'siap_kirim'])) {
            return $failResponse('Pesanan harus dalam status "Dalam Produksi" sebelum dapat ditandai "Siap Kirim".');
        }

        // ========================================
        // PENTING: Validasi dan kirim WhatsApp DULU sebelum ubah status
        // ========================================
        if (in_array($newStatus, ['siap_kirim', 'selesai'])) {
            // Validasi nomor pelanggan - with proper null/empty checks
            $customerPhone = null;
            
            // Try to get nomor_whatsapp first
            if (!empty($pesanan->pelanggan) && !empty($pesanan->pelanggan->nomor_whatsapp)) {
                $customerPhone = trim($pesanan->pelanggan->nomor_whatsapp);
            }
            // Fallback to telepon if nomor_whatsapp is empty
            elseif (!empty($pesanan->pelanggan) && !empty($pesanan->pelanggan->telepon)) {
                $customerPhone = trim($pesanan->pelanggan->telepon);
            }

            \Log::info('Phone Check for ' . $pesanan->nomor_po, [
                'pelanggan_exists' => !empty($pesanan->pelanggan),
                'pelanggan_nama' => $pesanan->pelanggan ? $pesanan->pelanggan->nama : 'N/A',
                'nomor_whatsapp' => $pesanan->pelanggan ? $pesanan->pelanggan->nomor_whatsapp : 'N/A',
                'telepon' => $pesanan->pelanggan ? $pesanan->pelanggan->telepon : 'N/A',
                'final_phone' => $customerPhone,
            ]);

            if (empty($customerPhone)) {
                return $failResponse('Nomor WhatsApp pelanggan tidak tersedia. Silakan update data pelanggan terlebih dahulu.');
            }

            if (!WhatsAppService::isValidPhoneNumber($customerPhone)) {
                return $failResponse('Nomor WhatsApp pelanggan tidak valid: ' . $customerPhone . '. Format harus 08xx atau +62xx');
            }

            // Try to generate and send PDF with message
            try {
                \Log::info('🔍 DEBUG: Entering WhatsApp try block');
                $senderPhone = auth()->user()->phone_number ?? '6281224360829';
                $statusLabel = $newStatus === 'siap_kirim' ? 'Siap Kirim' : 'Selesai';
                
                \Log::info('WhatsApp Send Attempt [' . $statusLabel . ']', [
                    'PO' => $pesanan->nomor_po,
                    'Status' => $newStatus,
                    'Customer' => $customerPhone,
                    'User' => auth()->user()->name
                ]);

                try {
                    $pdf = Pdf::loadView('pesanan.pdf', ['pesanan' => $pesanan]);
                    $pdfFileName = 'PO_' . $pesanan->nomor_po . '_' . $newStatus . '.pdf';
                    $pdfPath = storage_path('app/temp/' . $pdfFileName);
                    
                    // Ensure temp directory exists
                    $pdfDir = dirname($pdfPath);
                    if (!is_dir($pdfDir)) {
                        mkdir($pdfDir, 0755, true);
                        \Log::info('Created temp directory: ' . $pdfDir);
                    }
                    
                    // Save PDF
                    $pdf->save($pdfPath);
                    
                    // Verify file was created
                    if (!file_exists($pdfPath)) {
                        throw new \Exception('PDF file was not created at: ' . $pdfPath);
                    }
                    
                    $fileSize = filesize($pdfPath);
                    \Log::info('✅ PDF Generated Successfully', [
                        'file' => $pdfFileName,
                        'path' => $pdfPath,
                        'size' => $fileSize . ' bytes',
                        'readable' => is_readable($pdfPath)
                    ]);

                    // Send WhatsApp with PDF attachment
                    $whatsappResult = WhatsAppService::sendReadyToShipNotification(
                        $pesanan->pelanggan,
                        $pesanan,
                        $pdfPath,
                        $senderPhone,
                        $customerPhone,
                        $newStatus  // Pass the status for customized message
                    );

                    if ($whatsappResult && $whatsappResult['success']) {
                        $pdfSent = true;
                        \Log::info('✅ WhatsApp with PDF sent successfully', ['PO' => $pesanan->nomor_po, 'Status' => $statusLabel]);
                        
                        // Mark PDF for cleanup
                        file_put_contents($pdfPath . '.sent', now()->toDateTimeString());
                    } else {
                        // PDF send FAILED - return error and DON'T update status
                        $errorMsg = $whatsappResult['message'] ?? 'Gagal mengirim WhatsApp';
                        \Log::warning('❌ WhatsApp send failed', ['PO' => $pesanan->nomor_po, 'Error' => $errorMsg]);
                        return $failResponse('Gagal mengirim notifikasi WhatsApp ke pelanggan: ' . $errorMsg . '. Status pesanan tidak diubah.');
                    }
                } catch (\Exception $pdfError) {
                    // PDF generation failed - return error and DON'T update status
                    \Log::error('❌ PDF generation/send failed: ' . $pdfError->getMessage(), [
                        'trace' => $pdfError->getTraceAsString()
                    ]);
                    return $failResponse('Gagal membuat/mengirim dokumen PDF: ' . $pdfError->getMessage() . '. Status pesanan tidak diubah.');
                }
            } catch (\Exception $e) {
                // WhatsApp error - return error and DON'T update status
                \Log::error('💥 Error sending WhatsApp: ' . $e->getMessage());
                return $failResponse('Terjadi kesalahan saat mengirim WhatsApp: ' . $e->getMessage() . '. Status pesanan tidak diubah.');
            }
        }

        // ========================================
        // HANYA SETELAH WhatsApp BERHASIL, barulah ubah status pesanan
        // ========================================
        $updateData = ['status' => $newStatus];
        if ($newStatus === 'dibatalkan') {
            // Wajib ada alasan pembatalan
            if (empty($request->input('alasan_pembatalan'))) {
                return $failResponse('Alasan pembatalan wajib diisi.');
            }
            $updateData['alasan_pembatalan'] = $request->input('alasan_pembatalan');
        } elseif ($newStatus === 'siap_kirim') {
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

        // Create history with WhatsApp info
        $historiKeterangan = $request->keterangan ?? 'Perubahan status oleh ' . auth()->user()->name;
        if ($newStatus === 'siap_kirim') {
            $extra = [];
            if ($pesanan->tanggal_dikirim) {
                $extra[] = 'Tgl kirim: ' . $pesanan->tanggal_dikirim->format('d M Y');
            }
            if ($pesanan->nomor_resi) {
                $extra[] = 'Resi: ' . $pesanan->nomor_resi;
            }
            if ($pdfSent) {
                $extra[] = 'PDF dikirim via WhatsApp';
            }
            if (!empty($extra)) {
                $historiKeterangan .= ' (' . implode(' | ', $extra) . ')';
            }
        } elseif ($newStatus === 'selesai' && $pdfSent) {
            $historiKeterangan .= ' (PDF dikirim via WhatsApp)';
        }

        $pesanan->historiStatus()->create([
            'user_id' => auth()->id(),
            'status' => $newStatus,
            'keterangan' => $historiKeterangan,
        ]);

        DashboardController::clearCacheFor($pesanan->created_by);

        // Return with WhatsApp status
        if ($request->wantsJson() || $request->ajax()) {
            $displayPhone = $pesanan->pelanggan ? (!empty($pesanan->pelanggan->nomor_whatsapp) ? $pesanan->pelanggan->nomor_whatsapp : $pesanan->pelanggan->telepon) : 'Unknown';
            $customerName = $pesanan->pelanggan ? $pesanan->pelanggan->nama : 'Customer';
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui.',
                'whatsapp_sent' => in_array($newStatus, ['siap_kirim', 'selesai']) && $whatsappResult && $whatsappResult['success'],
                'pdf_sent' => $pdfSent,
                'whatsapp_message' => $whatsappResult ? ($whatsappResult['success'] ? '✅ Notifikasi WhatsApp berhasil dikirim ke ' . $customerName . ' (' . $displayPhone . ')' . ($pdfSent ? ' dengan PDF.' : '.') . '\n\nPesan dikirim dari nomor Hutch.id: +62 812-2436-0829' : '❌ Gagal: ' . $whatsappResult['message']) : null
            ]);
        }

        $message = 'Status pesanan berhasil diperbarui.';
        if ($whatsappResult) {
            if ($whatsappResult['success']) {
                $displayPhone = $pesanan->pelanggan ? (!empty($pesanan->pelanggan->nomor_whatsapp) ? $pesanan->pelanggan->nomor_whatsapp : $pesanan->pelanggan->telepon) : 'Unknown';
                $customerName = $pesanan->pelanggan ? $pesanan->pelanggan->nama : 'Customer';
                $pdfNote = $pdfSent ? ' dengan PDF' : '';
                $message .= ' ✅ Notifikasi WhatsApp dikirim ke ' . $customerName . ' (' . $displayPhone . ')' . $pdfNote . ' dari nomor Hutch.id (+62 812-2436-0829).';
            } else {
                $message .= ' ⚠️ Status diubah tapi WhatsApp gagal: ' . $whatsappResult['message'];
            }
        }

        return back()->with('success', $message);
    }

    /**
     * Notify customer via WhatsApp about stock shortage during draft order
     */
    public function notifyCustomerStockShortage(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggan,id',
            'nomor_po' => 'nullable|string',
            'detail_kurang' => 'required|array',
        ]);

        try {
            $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);

            // Validate WhatsApp number
            if (!WhatsAppService::isValidPhoneNumber($pelanggan->nomor_whatsapp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp pelanggan belum terdaftar.'
                ], 422);
            }

            // Send WhatsApp notification
            $result = WhatsAppService::sendStockNotification(
                $pelanggan,
                (object)['nomor_po' => $request->nomor_po],
                $request->detail_kurang,
                auth()->user()->phone_number
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notifikasi stok kurang berhasil dikirim ke pelanggan via WhatsApp.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Gagal mengirim notifikasi WhatsApp.'
                ], 422);
            }
        } catch (\Exception $e) {
            \Log::error('Error notifying customer: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
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

        $validated = $request->validate([
            'alasan_pembatalan' => 'required|string|min:5|max:500',
        ], [
            'alasan_pembatalan.required' => 'Alasan pembatalan harus diisi.',
            'alasan_pembatalan.min' => 'Alasan pembatalan minimal 5 karakter.',
            'alasan_pembatalan.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ]);

        $pesanan->update([
            'status' => 'dibatalkan',
            'alasan_pembatalan' => $validated['alasan_pembatalan'],
        ]);
        
        $pesanan->historiStatus()->create([
            'user_id' => auth()->id(),
            'status' => 'dibatalkan',
            'keterangan' => 'Pesanan dibatalkan oleh ' . auth()->user()->name . ' - Alasan: ' . $validated['alasan_pembatalan'],
        ]);

        DashboardController::clearCacheFor($pesanan->created_by);

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
            // Staf hanya bisa membatalkan pesanan (selama belum selesai/dibatalkan)
            return $newStatus === 'dibatalkan' && !in_array($pesanan->status, ['selesai', 'dibatalkan']);
        }

        if ($userRole === 'staf_penjualan') {
            // Staf hanya bisa membatalkan pesanan (selama belum selesai/dibatalkan)
            return $newStatus === 'dibatalkan' && !in_array($pesanan->status, ['selesai', 'dibatalkan']);
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

    /**
     * API method to download PDF (returns file as base64 for mobile)
     */
    public function apiDownloadPdf(Pesanan $pesanan)
    {
        $this->authorize('view', $pesanan);
        
        $pesanan->load('pelanggan', 'detailPesanan.produk', 'creator');

        $pdf = Pdf::loadView('pesanan.pdf', compact('pesanan'))
            ->setPaper('a4', 'portrait');

        // Get PDF as base64 string for mobile
        $pdfContent = $pdf->output();
        $base64Pdf = base64_encode($pdfContent);

        return response()->json([
            'success' => true,
            'pdf' => $base64Pdf,
            'filename' => $pesanan->nomor_po . '.pdf',
            'nomor_po' => $pesanan->nomor_po,
        ]);
    }

    /**
     * API method to get PDF file directly (for streaming to mobile)
     */
    public function apiPdfFile(Pesanan $pesanan)
    {
        $this->authorize('view', $pesanan);
        
        $pesanan->load('pelanggan', 'detailPesanan.produk', 'creator');

        $pdf = Pdf::loadView('pesanan.pdf', compact('pesanan'))
            ->setPaper('a4', 'portrait');

        return $pdf->download($pesanan->nomor_po . '.pdf');
    }
}