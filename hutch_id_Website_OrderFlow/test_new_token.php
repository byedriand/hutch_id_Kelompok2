<?php

// Test Fonnte dengan token baru dari user

$token = 'faoHVamsKosPoHcq6bgD';
$apiUrl = 'https://api.fonnte.com/send';

// Format nomor user: 081224360829 → 62 + 1224360829
$senderPhone = '6212243608829'; // 081224360829 → 62xxx format
$customerPhone = '628555555401';  // +62 855-5555-4012

echo "=== Test Fonnte API dengan Token BARU ===\n\n";
echo "Token: " . $token . "\n";
echo "Token Length: " . strlen($token) . " characters\n";
echo "Nomor Pengirim (user): 081224360829 → " . $senderPhone . "\n";
echo "Nomor Penerima: +62 855-5555-4012 → " . $customerPhone . "\n";
echo "-------------------------------------------\n\n";

$message = "Halo! Ini test WhatsApp dari Hutch.id menggunakan API Fonnte. Sistem notifikasi pesanan sudah siap digunakan.";

echo "Sending test message...\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'target' => $customerPhone,
    'message' => $message,
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $token,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "Response Status: " . $httpCode . "\n";
echo "Response Body:\n";
$responseData = json_decode($response, true);
echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n\n";

if (isset($responseData['status']) && $responseData['status']) {
    echo "✅ BERHASIL! Pesan dikirim ke +62 855-5555-4012\n";
    echo "Cek WhatsApp customer Anda sekarang!\n";
    echo "\n🎉 Sistem WhatsApp siap digunakan!\n";
} else {
    echo "❌ Gagal mengirim pesan\n";
    if (isset($responseData['reason'])) {
        echo "Reason: " . $responseData['reason'] . "\n";
    }
    if ($error) {
        echo "cURL Error: " . $error . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "Status: " . ($responseData['status'] ? "✅ VALID TOKEN" : "❌ INVALID TOKEN") . "\n";
echo "Status Code: " . $httpCode . "\n";
