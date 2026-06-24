<?php
/**
 * Test script untuk mengirim WhatsApp via Fonnte API
 * Jalankan dari terminal: php test_whatsapp_send.php
 */

require 'vendor/autoload.php';
require 'app/Models/Pesanan.php';
require 'app/Models/Pelanggan.php';
require 'app/Services/WhatsAppService.php';

use Illuminate\Support\Facades\Http;
use App\Services\WhatsAppService;

// Setup basic config
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

echo "=== TEST FONNTE WhatsApp API ===\n\n";

// Test 1: Cek API credentials
echo "1. Checking API Credentials:\n";
$token = env('FONNTE_API_TOKEN');
$apiUrl = env('FONNTE_API_URL', 'https://api.fonnte.com/send');
echo "   API URL: $apiUrl\n";
echo "   Token exists: " . (!empty($token) ? "YES" : "NO") . "\n";
echo "   Token: " . substr($token, 0, 10) . "...\n\n";

// Test 2: Format phone numbers
echo "2. Testing Phone Number Formatting:\n";
$testNumbers = [
    '6285555555401',      // Already formatted
    '08555555540',        // With leading 0
    '628555555540',       // Already formatted without leading 0
    '+6285555555401',     // With +62
];

foreach ($testNumbers as $num) {
    $formatted = WhatsAppService::formatPhoneNumber($num);
    $isValid = WhatsAppService::isValidPhoneNumber($num);
    echo "   Input: $num\n";
    echo "   Formatted: $formatted\n";
    echo "   Valid: " . ($isValid ? "YES" : "NO") . "\n";
    echo "   ---\n";
}

// Test 3: Try to send a real message
echo "\n3. Attempting to send test message:\n";
$testPhone = '6285555555401';  // Ganti dengan nomor test yang valid
$testMessage = "Halo, ini adalah pesan test dari Hutch.id.\n\nJika Anda menerima pesan ini, berarti WhatsApp API sudah berfungsi dengan baik.\n\nTerima kasih!";

echo "   Target: $testPhone\n";
echo "   Message: $testMessage\n\n";

// Test dengan curl langsung
$payload = [
    'target' => $testPhone,
    'message' => $testMessage
];

echo "   Sending request...\n";
echo "   Payload: " . json_encode($payload) . "\n\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: ' . $token,
    'Content-Type: application/x-www-form-urlencoded',
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
echo "   Response: $response\n";
echo "   Error: " . ($error ?: "None") . "\n\n";

// Parse response
if ($httpCode == 200 && !empty($response)) {
    $responseData = json_decode($response, true);
    echo "4. Response Analysis:\n";
    echo "   Status: " . ($responseData['status'] ?? 'N/A') . "\n";
    echo "   Message: " . ($responseData['message'] ?? 'N/A') . "\n";
    echo "   Data: " . json_encode($responseData['data'] ?? []) . "\n";
} else {
    echo "4. ERROR SENDING MESSAGE!\n";
    echo "   Please check:\n";
    echo "   - API Token is correct\n";
    echo "   - Phone number format is valid\n";
    echo "   - Fonnte API is accessible\n";
}

echo "\n=== END TEST ===\n";
?>
