<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format nomor WhatsApp ke format internasional (62xxxx...)
     */
    public static function formatPhoneNumber($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Hapus karakter non-digit
        $phone = preg_replace('/\D/', '', $phone);

        // Jika dimulai dengan 0, ganti dengan 62
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        // Jika belum dimulai dengan 62, tambahkan
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Validasi format nomor WhatsApp Indonesia
     */
    public static function isValidPhoneNumber($phone)
    {
        if (empty($phone)) {
            return false;
        }

        $formatted = self::formatPhoneNumber($phone);
        
        // Nomor Indonesia harus 62xxxxxxxxxxxxx (9-14 digit setelah 62)
        // Standar: 62 + 9-14 digit = 11-16 total digit
        // Mendukung: 08xx (11 digit), 0xxx (12+ digit), +62xxx (13+ digit), 62xxx (11+ digit)
        if (preg_match('/^62\d{9,14}$/', $formatted)) {
            return true;
        }

        return false;
    }

    /**
     * Kirim pesan WhatsApp menggunakan Fonnte API
     * 
     * @param string $phone Nomor WhatsApp penerima (format bebas, akan diformat otomatis)
     * @param string $message Pesan yang akan dikirim
     * @param string|null $attachment Path file untuk attachment (PDF, dll)
     * @param string|null $senderPhone Nomor WhatsApp pengirim (opsional, gunakan dari config jika tidak ada)
     * @return array
     */
    public static function sendMessage($phone, $message, $attachment = null, $senderPhone = null)
    {
        try {
            // Trim dan validasi nomor penerima
            $phone = trim($phone);
            
            if (!self::isValidPhoneNumber($phone)) {
                Log::warning('Invalid phone number: ' . $phone, [
                    'phone_length' => strlen($phone),
                    'phone_raw' => json_encode($phone)
                ]);
                return [
                    'success' => false,
                    'message' => 'Nomor WhatsApp tidak valid atau tidak terdaftar.',
                    'error' => 'invalid_phone'
                ];
            }

            $formattedPhone = self::formatPhoneNumber($phone);
            $senderPhone = $senderPhone ? self::formatPhoneNumber($senderPhone) : config('services.fonnte.sender_number', '6281224360829');

            // Ambil Fonnte API Token dari .env
            $token = env('FONNTE_API_TOKEN');
            $apiUrl = env('FONNTE_API_URL', 'https://api.fonnte.com/send');

            if (!$token) {
                Log::error('Fonnte API token tidak dikonfigurasi');
                return [
                    'success' => false,
                    'message' => 'Konfigurasi API WhatsApp tidak lengkap',
                    'error' => 'missing_token'
                ];
            }

            // Siapkan payload untuk Fonnte
            if ($attachment && file_exists($attachment)) {
                $fileName = basename($attachment);
                
                // Copy PDF ke public folder untuk accessible URL
                $publicPdfPath = 'files/pesanan/' . $fileName;
                $publicFullPath = public_path($publicPdfPath);
                $publicDir = dirname($publicFullPath);
                
                // Ensure public directory exists
                if (!is_dir($publicDir)) {
                    mkdir($publicDir, 0755, true);
                }
                
                // Copy file to public
                copy($attachment, $publicFullPath);
                
                // Generate public URL
                $pdfUrl = url($publicPdfPath);
                
                // Check if URL is localhost - Fonnte API cannot access localhost URLs
                $isLocalhost = strpos($pdfUrl, 'localhost') !== false || 
                               strpos($pdfUrl, '127.0.0.1') !== false ||
                               strpos($pdfUrl, '::1') !== false;
                
                if ($isLocalhost) {
                    // For local development: Send message without PDF
                    Log::warning('⚠️ PDF URL is localhost - Fonnte cannot access. Sending message only.', [
                        'pdf_url' => $pdfUrl,
                        'file' => $fileName
                    ]);
                    
                    Log::info('📤 Sending WhatsApp message via Fonnte (localhost - PDF skipped)', [
                        'to' => $formattedPhone,
                        'to_length' => strlen($formattedPhone),
                        'from' => $senderPhone,
                        'file' => $fileName,
                        'note' => 'PDF was prepared at: ' . $publicFullPath
                    ]);

                    // Kirim ke Fonnte API tanpa attachment
                    $response = Http::withHeaders([
                        'Authorization' => $token,
                    ])->timeout(30)->post($apiUrl, [
                        'target' => $formattedPhone,
                        'message' => $message,
                    ]);
                    
                    Log::info('📨 Fontre API Response (message only, localhost)', [
                        'status_code' => $response->status(),
                        'status_text' => $response->getReasonPhrase(),
                        'response_body' => $response->body(),
                        'is_success' => $response->successful()
                    ]);
                } else {
                    // Production: Send with PDF URL
                    Log::info('📤 Sending WhatsApp with PDF attachment via Fonnte', [
                        'to' => $formattedPhone,
                        'to_length' => strlen($formattedPhone),
                        'from' => $senderPhone,
                        'file' => $fileName,
                        'size' => filesize($attachment) . ' bytes',
                        'pdf_url' => $pdfUrl,
                        'api_url' => $apiUrl,
                        'has_token' => !empty($token)
                    ]);

                    // Kirim ke Fonnte API dengan PDF URL
                    $response = Http::withHeaders([
                        'Authorization' => $token,
                    ])->timeout(30)->post($apiUrl, [
                        'target' => $formattedPhone,
                        'message' => $message,
                        'url' => $pdfUrl,
                    ]);
                    
                    Log::info('📨 Fontre API Response (with PDF)', [
                        'status_code' => $response->status(),
                        'status_text' => $response->getReasonPhrase(),
                        'response_body' => $response->body(),
                        'is_success' => $response->successful()
                    ]);
                }
            } else {
                // Log request untuk debugging
                Log::info('📤 Sending WhatsApp message via Fonnte (no PDF)', [
                    'to' => $formattedPhone,
                    'to_length' => strlen($formattedPhone),
                    'from' => $senderPhone,
                    'api_url' => $apiUrl,
                    'has_token' => !empty($token)
                ]);

                // Kirim ke Fonnte API tanpa attachment
                $response = Http::withHeaders([
                    'Authorization' => $token,
                ])->timeout(30)->post($apiUrl, [
                    'target' => $formattedPhone,
                    'message' => $message,
                ]);
                
                Log::info('📨 Fonnte API Response (no PDF)', [
                    'status_code' => $response->status(),
                    'status_text' => $response->getReasonPhrase(),
                    'response_body' => $response->body(),
                    'is_success' => $response->successful()
                ]);
            }

            if ($response->successful()) {
                $responseData = $response->json();
                
                // Check if Fonnte actually succeeded (not just HTTP 200)
                // Fontre API returns "status": true for success
                $isSuccess = false;
                if (isset($responseData['status']) && $responseData['status'] === true) {
                    $isSuccess = true;
                }
                // Some API responses use 'message' field to indicate success
                elseif (isset($responseData['data']) && !empty($responseData['data'])) {
                    $isSuccess = true;
                }
                
                Log::info('✅ WhatsApp response from Fonnte', [
                    'phone' => $formattedPhone,
                    'http_status' => $response->status(),
                    'api_status' => $responseData['status'] ?? 'unknown',
                    'api_success' => $isSuccess,
                    'full_response' => $responseData
                ]);

                if (!$isSuccess) {
                    // HTTP 200 tapi API gagal
                    Log::warning('⚠️ Fonnte returned 200 but API failed', [
                        'phone' => $formattedPhone,
                        'response' => $responseData
                    ]);
                    return [
                        'success' => false,
                        'message' => 'API WhatsApp menolak nomor ini. ' . ($responseData['message'] ?? ''),
                        'error' => 'api_rejected',
                        'api_response' => $responseData
                    ];
                }

                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim',
                    'phone' => $formattedPhone,
                    'sender' => $senderPhone,
                    'timestamp' => now(),
                    'fonnte_response' => $responseData
                ];
            } else {
                $errorMsg = $response->body();
                Log::warning('❌ WhatsApp send failed via Fonnte - HTTP Error', [
                    'phone' => $formattedPhone,
                    'status' => $response->status(),
                    'error' => $errorMsg
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal mengirim pesan WhatsApp (HTTP ' . $response->status() . ')',
                    'error' => $errorMsg,
                    'status' => $response->status()
                ];
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengirim pesan',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Kirim pesan dengan attachment
     */
    public static function sendMessageWithAttachment($phone, $message, $attachmentPath, $senderPhone = null)
    {
        if (!file_exists($attachmentPath)) {
            return [
                'success' => false,
                'message' => 'File attachment tidak ditemukan',
                'error' => 'file_not_found'
            ];
        }

        return self::sendMessage($phone, $message, $attachmentPath, $senderPhone);
    }

    /**
     * Kirim notifikasi stok kurang ke pelanggan
     */
    public static function sendStockNotification($pelanggan, $pesanan, $barangKurang, $senderPhone = null)
    {
        $message = sprintf(
            "Halo %s,\n\n" .
            "Pesanan dengan nomor PO %s sedang diproses.\n\n" .
            "Namun saat ini terdapat kekurangan stok bahan baku sehingga produksi belum dapat dimulai.\n\n" .
            "Tim Hutch.id sedang melakukan pengadaan bahan dan akan segera menghubungi Anda kembali setelah stok tersedia. " .
            "Apakah dari %s akan menunggu stok hingga terpenuhi atau mau langsung kirim saja dengan jumlah stok yang tersedia?\n\n" .
            "Terima kasih atas pengertiannya.\n\n" .
            "Salam,\n" .
            "Hutch.id",
            $pelanggan->nama,
            $pesanan->nomor_po,
            $pelanggan->nama
        );

        return self::sendMessage($pelanggan->nomor_whatsapp, $message, null, $senderPhone);
    }

    /**
     * Kirim notifikasi siap kirim dengan PDF attachment
     * Pesan berisi informasi bahwa pesanan siap dikirim dan PDF PO dilampirkan
     */
    public static function sendReadyToShipNotification($pelanggan, $pesanan, $pdfPath = null, $senderPhone = null, $customerPhone = null, $status = 'siap_kirim')
    {
        // Customized message based on order status
        if ($status === 'selesai') {
            // Message for completed orders
            $message = sprintf(
                "Halo %s,\n\n" .
                "Pesanan Anda dengan nomor PO %s telah *SELESAI*.\n\n" .
                "Terima kasih telah mempercayai Hutch.id untuk kebutuhan produksi Anda. Kami bangga dapat melayani pesanan ini dengan sempurna.\n\n" .
                "Dokumen Purchase Order dalam format PDF telah kami lampirkan sebagai arsip pesanan Anda.\n\n" .
                "Jika ada pertanyaan atau memerlukan bantuan lebih lanjut, jangan ragu untuk menghubungi kami.\n\n" .
                "Terima kasih,\n" .
                "Hutch.id",
                $pelanggan->nama,
                $pesanan->nomor_po
            );
        } else {
            // Message for ready to ship orders (default: siap_kirim)
            $message = sprintf(
                "Halo %s,\n\n" .
                "Pesanan Anda dengan nomor PO %s telah selesai diproduksi dan siap dikirim.\n\n" .
                "Dokumen Purchase Order dalam format PDF telah kami lampirkan sebagai arsip pesanan.\n\n" .
                "Terima kasih telah menggunakan layanan Hutch.id.\n\n" .
                "Salam,\n" .
                "Hutch.id",
                $pelanggan->nama,
                $pesanan->nomor_po
            );
        }

        // Use provided customerPhone or fallback to nomor_whatsapp, then telepon
        $phone = $customerPhone ?? (!empty($pelanggan->nomor_whatsapp) ? $pelanggan->nomor_whatsapp : $pelanggan->telepon);

        if ($pdfPath && file_exists($pdfPath)) {
            return self::sendMessageWithAttachment($phone, $message, $pdfPath, $senderPhone);
        }

        return self::sendMessage($phone, $message, null, $senderPhone);
    }
}
