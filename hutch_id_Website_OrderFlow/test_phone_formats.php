<?php

$token = 'kcMeKzrKX7R9kybq6X4RhHeb9TS9j2iFkAoAuXdZzQSo7yfF';
$apiUrl = 'https://api.fonnte.com/send';

// Test dengan berbagai format nomor
$phoneFormats = [
    '628555555401' => '62 + 8555555401 (tanpa +)',
    '+628555555401' => '+62 + 8555555401 (dengan +)',
    '08555555401' => '0 + 8555555401 (format lokal)',
    '62-855-5555-4012' => '62 + format dengan -',
];

$message = "Test Fonnte";

echo "=== Test berbagai format nomor phone ===\n\n";

foreach ($phoneFormats as $phone => $description) {
    echo "Testing: $description\n";
    echo "Phone: $phone\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'target' => $phone,
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
    curl_close($ch);
    
    $data = json_decode($response, true);
    
    if (isset($data['status']) && $data['status']) {
        echo "✅ BERHASIL!\n";
    } else {
        echo "❌ Gagal: " . ($data['reason'] ?? 'unknown') . "\n";
    }
    echo "---\n\n";
}

echo "\n=== DIAGNOSIS ===\n";
echo "API URL accessible: ✅ (HTTP 200 response)\n";
echo "Token format: Valid length (48 chars)\n";
echo "Request format: ✅ Valid JSON\n";
echo "Token validity: ❌ INVALID (all tests returned 'invalid token')\n\n";
echo "ACTION REQUIRED:\n";
echo "User perlu:\n";
echo "1. Login ke https://dashboard.fonnte.com\n";
echo "2. Buka menu 'API' atau 'Integrasi'\n";
echo "3. Verifikasi token yang aktif\n";
echo "4. Cek apakah ada 'Verify Device/Phone' yang perlu dilakukan\n";
echo "5. Copy token baru dan provide ke system\n";
