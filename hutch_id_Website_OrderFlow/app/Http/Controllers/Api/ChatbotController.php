<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        try {
            $message = $request->input('message');
            $model = 'n8n';

            if (empty($message)) {
                return response()->json([
                    'success' => false,
                    'reply' => 'Pesan tidak boleh kosong'
                ], 400);
            }

            $reply = $this->getResponse($message, auth()->user(), $model);

            return response()->json([
                'success' => true,
                'reply' => $reply
            ]);
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'reply' => 'Terjadi kesalahan saat memproses pesan Anda. Silakan coba lagi.'
            ], 500);
        }
    }

    private function getResponse($message, $user, $model = 'n8n')
    {
        return $this->getN8NResponse($message, $user);
    }

    private function getN8NResponse($message, $user)
    {
        $n8nResponse = $this->tryN8NResponse($message, $user);

        if ($n8nResponse) {
            return $n8nResponse;
        }

        Log::warning('N8N tidak tersedia, menggunakan Local Response sebagai fallback');
        return $this->getLocalResponse($message, $user);
    }

    private function tryN8NResponse($message, $user)
    {
        try {
            $webhookUrl = env('N8N_CHATBOT_WEBHOOK_URL');

            if (empty($webhookUrl)) {
                return null;
            }

            $systemPrompt = 'Kamu adalah AI Assistant profesional untuk Hutch.id - Platform Order Flow untuk UMKM Indonesia. Jawab semua pertanyaan tentang fitur Hutch.id dengan ramah dan akurat. Gunakan data real-time yang diberikan sistem untuk menjawab pertanyaan tentang jumlah pelanggan, pesanan, produk, dan stok. Jawab dalam Bahasa Indonesia.';

            $payload = [
                'message' => $message,
                'system_prompt' => $systemPrompt,
                'context' => 'hutch_id_platform',
                'language' => 'id',
                'version' => '2.0'
            ];

            $client = new \GuzzleHttp\Client();
            $response = $client->post($webhookUrl, [
                'json' => $payload,
                'timeout' => 30,
                'verify' => false
            ]);

            $body = json_decode($response->getBody(), true);

            if (isset($body['reply']) && !empty($body['reply'])) {
                return $body['reply'];
            } elseif (isset($body['message']) && !empty($body['message'])) {
                return $body['message'];
            }

            return null;
        } catch (\Exception $e) {
            Log::debug('N8N not available: ' . $e->getMessage());
            return null;
        }
    }

    private function getLocalResponse($message, $user)
    {
        $msgLower = strtolower($message);

        if (preg_match('/^\s*(halo|hi|hello|pagi|siang|malam|hei)\s*\??$/i', $message)) {
            return '👋 Halo! Saya adalah AI Assistant Hutch.id. Ada yang bisa saya bantu hari ini?';
        }

        if (preg_match('/\b(terima kasih|makasih|thanks)\b/i', $message)) {
            return '😊 Sama-sama! Ada pertanyaan lain tentang Hutch.id?';
        }

        if (preg_match('/\b(pesanan|order|po)\b/i', $msgLower)) {
            return "📋 **Manajemen Pesanan Hutch.id:**\n\n1. Buka menu Daftar Pesanan\n2. Klik Buat PO untuk pesanan baru\n3. Isi form: pelanggan, produk, qty, tanggal\n4. Klik Simpan\n\nStatus: Draft > Dikonfirmasi > Dalam Produksi > Siap Kirim > Selesai";
        }

        if (preg_match('/\b(stok|produk|inventory|barang)\b/i', $msgLower)) {
            return $this->getProductListResponse();
        }

        if (preg_match('/\b(pelanggan|customer)\b/i', $msgLower)) {
            return "👥 **Manajemen Pelanggan:**\n\n1. Buka menu Pelanggan\n2. Klik Tambah Pelanggan\n3. Isi nama, email, telepon, alamat\n4. Klik Simpan\n\nAnda bisa lihat riwayat pembelian setiap pelanggan.";
        }

        if (preg_match('/\b(laporan|report|analytics)\b/i', $msgLower)) {
            return "📈 **Laporan Hutch.id:**\n\n- Sales Report: Penjualan & revenue\n- Product Report: Performa produk\n- Customer Report: Analisis pelanggan\n- Inventory Report: Status stok\n\nBuka menu Laporan untuk export PDF/Excel.";
        }

        return "🤖 Saya AI Assistant Hutch.id. Saya bisa membantu tentang:\n- Membuat & manage pesanan\n- Cek stok produk\n- Manage pelanggan\n- Lihat laporan\n\nTanyakan sesuatu tentang Hutch.id!";
    }

    private function getProductListResponse()
    {
        try {
            $products = Produk::select('nama', 'stok', 'harga_jual')->limit(10)->get();

            if ($products->isEmpty()) {
                return "📦 Belum ada produk yang terdaftar di sistem.";
            }

            $response = "📦 **Daftar Produk:**\n\n";
            foreach ($products as $i => $product) {
                $stokStatus = $product->stok > 0 ? "✅" : "❌";
                $response .= ($i + 1) . ". {$product->nama} | Stok: {$product->stok} {$stokStatus} | Rp " . number_format($product->harga_jual, 0, ',', '.') . "\n";
            }

            $totalProducts = Produk::count();
            if ($totalProducts > 10) {
                $response .= "\n... dan " . ($totalProducts - 10) . " produk lainnya";
            }

            return $response;
        } catch (\Exception $e) {
            Log::error('Error getting products: ' . $e->getMessage());
            return "📦 Maaf, terjadi kesalahan saat mengambil data produk.";
        }
    }

    private function getRandomResponse($responses)
    {
        return $responses[array_rand($responses)];
    }
}