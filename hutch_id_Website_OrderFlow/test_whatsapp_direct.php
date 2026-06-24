<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Http;

// Test data - Token Fonnte yang diberikan user
$token = 'kcMeKzrKX7R9kybq6X4RhHeb9TS9j2iFkAoAuXdZzQSo7yfF';
$apiUrl = 'https://api.fonnte.com/send';
$senderPhone = '62812243608829'; // +62 812-2436-0829
$customerPhone = '628555555401'; // +62 855-5555-4012 (tanpa + dan -)

echo "=== Testing Fonnte WhatsApp API ===\n";
echo "Sender: +62 812-2436-0829\n";
echo "Recipient: +62 855-5555-4012\n";
echo "Token: " . substr($token, 0, 10) . "...\n";
echo "-----------------------------------\n\n";

// Test message
$message = "Halo! Ini test message dari Hutch.id menggunakan Fonnte API. Pesan ini dikirim untuk testing sistem notifikasi WhatsApp.";

// Prepare payload
$payload = [
    'target' => $customerPhone,
    'message' => $message,
];

echo "Sending message...\n\n";

try {
    // Prepare cURL request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $token,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    echo "Response Status: " . $httpCode . "\n";
    echo "Response Body:\n";
    $responseData = json_decode($response, true);
    echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

    if ($httpCode == 200 && isset($responseData['status']) && $responseData['status']) {
        echo "✅ BERHASIL! Pesan dikirim ke " . $customerPhone . "\n";
        echo "Cek WhatsApp di nomor +62 855-5555-4012\n";
    } else {
        echo "❌ GAGAL mengirim pesan\n";
        echo "Reason: " . ($responseData['reason'] ?? 'Unknown error') . "\n";
        echo "Status: " . $httpCode . "\n";
        echo "\n⚠️  TOKEN KEMUNGKINAN TIDAK VALID\n";
        echo "Token diterima: kcMeKzrKX7R9kybq6X4RhHeb9TS9j2iFkAoAuXdZzQSo7yfF\n";
        echo "Silakan verify token di dashboard Fonnte Anda.\n";
    }
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
