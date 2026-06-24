<?php
// Test script to verify if WhatsApp message was sent to customer

require_once 'vendor/autoload.php';

$token = 'faoHVamsKosPoHcq6bgD';
$apiUrl = 'https://api.fonnte.com/send';
$customerPhone = '628555554012'; // +62 855-5555-4012 formatted
$senderPhone = '628122436'; // +62 812-2436-0829 formatted

// Get current device status and quota
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.fonnte.com/info');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $token]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

echo "=== CHECKING FONNTE ACCOUNT STATUS ===\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: " . $httpCode . "\n";
echo "Response:\n";
echo json_encode(json_decode($response, true), JSON_PRETTY_PRINT) . "\n\n";

// Try to send a test message
echo "=== SENDING TEST MESSAGE ===\n\n";

$testPayload = [
    'target' => $customerPhone,
    'message' => 'Test: Pesanan Anda (PO-2026060618-001) siap dikirim. Mohon hubungi kami untuk konfirmasi pengiriman.'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testPayload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: ' . $token, 'Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: " . $httpCode . "\n";
echo "Response:\n";
$responseData = json_decode($response, true);
echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";

if (isset($responseData['status']) && $responseData['status']) {
    echo "✅ Message sent successfully!\n";
    echo "Message ID: " . implode(', ', $responseData['id'] ?? []) . "\n";
    if (isset($responseData['quota'])) {
        echo "Remaining quota: " . json_encode($responseData['quota'], JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "❌ Message failed to send\n";
}
