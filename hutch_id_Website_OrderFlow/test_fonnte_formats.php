<?php

// Test Fonnte API dengan berbagai format request

$token = 'kcMeKzrKX7R9kybq6X4RhHeb9TS9j2iFkAoAuXdZzQSo7yfF';
$apiUrl = 'https://api.fonnte.com/send';
$senderPhone = '62812243608829'; // Format: 62xxxxxxxxxx
$customerPhone = '628555555401';  // Format: 62xxxxxxxxxx

echo "=== Test Fonnte API dengan berbagai format ===\n\n";
echo "Token Length: " . strlen($token) . " characters\n";
echo "Token: " . $token . "\n";
echo "Nomor Pengirim: +62 812-2436-0829 → " . $senderPhone . "\n";
echo "Nomor Penerima: +62 855-5555-4012 → " . $customerPhone . "\n";
echo "-------------------------------------------\n\n";

// Test message
$message = "Test dari Hutch.id - Fonnte API Integration";

// ============================================
// TEST 1: Format dengan Authorization header
// ============================================
echo "TEST 1: Authorization Header (Current method)\n";

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

$response1 = curl_exec($ch);
$httpCode1 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: " . $httpCode1 . "\n";
echo "Response: " . $response1 . "\n\n";

// ============================================
// TEST 2: Format dengan token dalam POST body
// ============================================
echo "TEST 2: Token dalam POST body\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'token' => $token,
    'target' => $customerPhone,
    'message' => $message,
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response2 = curl_exec($ch);
$httpCode2 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: " . $httpCode2 . "\n";
echo "Response: " . $response2 . "\n\n";

// ============================================
// TEST 3: Format URL-encoded
// ============================================
echo "TEST 3: Format URL-encoded dengan token\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'token' => $token,
    'target' => $customerPhone,
    'message' => $message,
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response3 = curl_exec($ch);
$httpCode3 = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: " . $httpCode3 . "\n";
echo "Response: " . $response3 . "\n\n";

// ============================================
// RESULT SUMMARY
// ============================================
echo "=== SUMMARY ===\n";
$responses = [
    'Test 1 (Auth Header)' => json_decode($response1, true),
    'Test 2 (Token in body)' => json_decode($response2, true),
    'Test 3 (URL-encoded)' => json_decode($response3, true),
];

$success = false;
foreach ($responses as $test => $data) {
    if (isset($data['status']) && $data['status']) {
        echo "✅ $test: BERHASIL\n";
        $success = true;
    }
}

if (!$success) {
    echo "❌ Semua test gagal\n";
    echo "\nKemungkinan masalah:\n";
    echo "1. Token belum activated di Fonnte dashboard\n";
    echo "2. Token sudah expired\n";
    echo "3. Token tidak terdaftar di sistem Fonnte\n";
    echo "\nSilakan:\n";
    echo "- Login ke https://dashboard.fonnte.com\n";
    echo "- Verify token di menu API/Integrasi\n";
    echo "- Copy token yang baru/aktif\n";
}
