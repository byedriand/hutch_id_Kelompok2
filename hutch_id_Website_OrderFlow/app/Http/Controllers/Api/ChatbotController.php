<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Handle incoming chatbot message.
     * Menggunakan local rule-based response engine (tanpa N8N / layanan eksternal).
     */
    public function sendMessage(Request $request)
    {
        try {
            $message = $request->input('message');

            if (empty($message)) {
                return response()->json([
                    'success' => false,
                    'reply'   => 'Pesan tidak boleh kosong',
                ], 400);
            }

            $reply = $this->getLocalResponse(trim($message), auth()->user());

            return response()->json([
                'success' => true,
                'reply'   => $reply,
            ]);
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply'   => 'Terjadi kesalahan saat memproses pesan Anda. Silakan coba lagi.',
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Local rule-based engine (no external services required)
    // ─────────────────────────────────────────────────────────────────────────

    private function getLocalResponse(string $message, $user): string
    {
        $msg = strtolower($message);

        // ── Sapaan ────────────────────────────────────────────────────────────
        if (preg_match('/^\s*(halo|hi|hello|pagi|siang|sore|malam|hei)\s*[!?.]?\s*$/i', $msg)) {
            $name = $user ? $user->name : 'Kak';
            return "👋 Halo, {$name}! Saya adalah AI Assistant Hutch.id. Ada yang bisa saya bantu hari ini? 😊";
        }

        // ── Terima kasih ─────────────────────────────────────────────────────
        if (preg_match('/\b(terima kasih|makasih|thanks|thank you)\b/i', $msg)) {
            return '😊 Sama-sama! Senang bisa membantu. Ada pertanyaan lain tentang Hutch.id?';
        }

        // ── Stok / Produk ─────────────────────────────────────────────────────
        if (preg_match('/\b(stok|produk|barang|inventory|katalog)\b/i', $msg)) {
            return $this->getProductListResponse();
        }

        // ── Pesanan / Order ───────────────────────────────────────────────────
        if (preg_match('/\b(pesanan|order|po|purchase order)\b/i', $msg)) {
            return $this->getOrderInfo();
        }

        // ── Pelanggan / Customer ──────────────────────────────────────────────
        if (preg_match('/\b(pelanggan|customer|klien)\b/i', $msg)) {
            return $this->getCustomerInfo();
        }

        // ── Laporan / Statistik ───────────────────────────────────────────────
        if (preg_match('/\b(laporan|report|statistik|analytics|ringkasan|summary|dashboard)\b/i', $msg)) {
            return $this->getDashboardSummary();
        }

        // ── Status pesanan ────────────────────────────────────────────────────
        if (preg_match('/\b(status|progress|tracking)\b/i', $msg)) {
            return "📋 **Status Pesanan Hutch.id:**\n\n"
                . "• **Draft** – Pesanan dibuat, belum dikonfirmasi\n"
                . "• **Dikonfirmasi** – Pesanan telah disetujui\n"
                . "• **Dalam Produksi** – Sedang diproduksi\n"
                . "• **Siap Kirim** – Siap dikirim ke pelanggan\n"
                . "• **Selesai** – Pesanan selesai\n"
                . "• **Dibatalkan** – Pesanan dibatalkan\n\n"
                . "Perbarui status di menu **Daftar Pesanan → Detail PO**.";
        }

        // ── WhatsApp / Notifikasi ─────────────────────────────────────────────
        if (preg_match('/\b(whatsapp|wa|notif|notifikasi)\b/i', $msg)) {
            return "📲 **Notifikasi WhatsApp:**\n\n"
                . "Hutch.id mengirim notifikasi otomatis via WhatsApp ke pelanggan pada:\n"
                . "• Pesanan dikonfirmasi\n"
                . "• Pesanan siap kirim (+ attachment PDF PO)\n"
                . "• Pesanan selesai\n\n"
                . "Pastikan nomor WhatsApp pelanggan sudah terdaftar di profil pelanggan.";
        }

        // ── Cara / How-to ─────────────────────────────────────────────────────
        if (preg_match('/\b(cara|bagaimana|gimana|how|langkah|tutorial)\b/i', $msg)) {
            return "📖 **Panduan Cepat Hutch.id:**\n\n"
                . "**Buat Pesanan Baru:**\n"
                . "1. Menu **Daftar Pesanan** → **Buat PO**\n"
                . "2. Pilih pelanggan & tambah produk\n"
                . "3. Isi qty & tanggal kirim → **Simpan**\n\n"
                . "**Tambah Produk:**\n"
                . "1. Menu **Produk** → **Tambah Produk**\n"
                . "2. Isi nama, harga, stok, upload foto\n\n"
                . "**Tambah Pelanggan:**\n"
                . "1. Menu **Pelanggan** → **Tambah Pelanggan**\n"
                . "2. Isi data lengkap termasuk nomor WhatsApp";
        }

        // ── Default ───────────────────────────────────────────────────────────
        return "🤖 Saya AI Assistant Hutch.id. Saya siap membantu tentang:\n\n"
            . "• 📦 **Produk & Stok** – cek inventory\n"
            . "• 📋 **Pesanan** – buat & kelola PO\n"
            . "• 👥 **Pelanggan** – manajemen customer\n"
            . "• 📊 **Laporan** – ringkasan bisnis\n"
            . "• 📲 **Notifikasi WhatsApp** – setup notif\n\n"
            . "Silakan tanyakan sesuatu tentang Hutch.id! 😊";
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Data helpers — pull live data from DB
    // ─────────────────────────────────────────────────────────────────────────

    private function getProductListResponse(): string
    {
        try {
            $products = Produk::select('nama', 'stok', 'harga_jual')
                ->orderBy('nama')
                ->limit(10)
                ->get();

            if ($products->isEmpty()) {
                return '📦 Belum ada produk yang terdaftar di sistem.';
            }

            $response = "📦 **Daftar Produk (top 10):**\n\n";
            foreach ($products as $i => $p) {
                $stokBadge = $p->stok > 0 ? '✅' : '❌ Habis';
                $harga     = 'Rp ' . number_format($p->harga_jual, 0, ',', '.');
                $response .= ($i + 1) . ". **{$p->nama}** – Stok: {$p->stok} {$stokBadge} | {$harga}\n";
            }

            $total = Produk::count();
            if ($total > 10) {
                $response .= "\n_... dan " . ($total - 10) . " produk lainnya. Buka menu **Produk** untuk melihat semua._";
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Chatbot getProductListResponse error: ' . $e->getMessage());
            return '📦 Maaf, terjadi kesalahan saat mengambil data produk.';
        }
    }

    private function getOrderInfo(): string
    {
        try {
            $total          = Pesanan::count();
            $berjalan       = Pesanan::whereIn('status', ['draft', 'dikonfirmasi', 'dalam_produksi'])->count();
            $siapKirim      = Pesanan::where('status', 'siap_kirim')->count();

            return "📋 **Ringkasan Pesanan:**\n\n"
                . "• Total PO: **{$total}**\n"
                . "• Sedang berjalan: **{$berjalan}**\n"
                . "• Siap kirim: **{$siapKirim}**\n\n"
                . "**Cara buat PO baru:**\n"
                . "1. Menu **Daftar Pesanan** → **Buat PO**\n"
                . "2. Pilih pelanggan & tambah produk\n"
                . "3. Isi qty & tanggal → **Simpan**\n\n"
                . "Buka menu **Daftar Pesanan** untuk detail selengkapnya.";
        } catch (\Exception $e) {
            Log::error('Chatbot getOrderInfo error: ' . $e->getMessage());
            return "📋 **Manajemen Pesanan:**\n\nBuat & kelola Purchase Order (PO) di menu **Daftar Pesanan**.\n\nStatus: Draft → Dikonfirmasi → Dalam Produksi → Siap Kirim → Selesai.";
        }
    }

    private function getCustomerInfo(): string
    {
        try {
            $total = Pelanggan::count();

            return "👥 **Manajemen Pelanggan:**\n\n"
                . "• Total pelanggan terdaftar: **{$total}**\n\n"
                . "**Fitur:**\n"
                . "• Tambah & edit data pelanggan\n"
                . "• Simpan nomor WhatsApp untuk notifikasi otomatis\n"
                . "• Lihat riwayat pesanan per pelanggan\n\n"
                . "Buka menu **Pelanggan** untuk manajemen lengkap.";
        } catch (\Exception $e) {
            Log::error('Chatbot getCustomerInfo error: ' . $e->getMessage());
            return "👥 **Manajemen Pelanggan:**\n\nKelola data pelanggan di menu **Pelanggan**. Tambah, edit, dan lihat riwayat pesanan.";
        }
    }

    private function getDashboardSummary(): string
    {
        try {
            $totalProduk     = Produk::count();
            $totalPelanggan  = Pelanggan::count();
            $totalPesanan    = Pesanan::count();
            $pesananBerjalan = Pesanan::whereIn('status', ['dikonfirmasi', 'dalam_produksi', 'siap_kirim'])->count();

            return "📊 **Ringkasan Bisnis Hutch.id:**\n\n"
                . "• 📦 Total Produk: **{$totalProduk}**\n"
                . "• 👥 Total Pelanggan: **{$totalPelanggan}**\n"
                . "• 📋 Total Pesanan: **{$totalPesanan}**\n"
                . "• ⚙️ Pesanan Berjalan: **{$pesananBerjalan}**\n\n"
                . "Buka menu **Dashboard** untuk grafik & laporan lengkap.";
        } catch (\Exception $e) {
            Log::error('Chatbot getDashboardSummary error: ' . $e->getMessage());
            return "📊 Buka menu **Dashboard** untuk melihat statistik & laporan bisnis Hutch.id.";
        }
    }
}