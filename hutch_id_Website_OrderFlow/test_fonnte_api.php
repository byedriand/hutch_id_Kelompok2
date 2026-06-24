<?php

// Bootstrap Laravel application
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$request = Illuminate\Http\Request::capture();
$kernel->handle($request);

use App\Services\WhatsAppService;

// Test WhatsApp message
echo "=== Testing Fonnte WhatsApp API ===\n\n";

$result = WhatsAppService::sendMessage(
    '+62 896-0129-2957',
    'Test notifikasi dari Fonnte API - Hutch.id WhatsApp Integration',
    null,
    '+62 812-2436-0829'
);

echo "Result:\n";
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
echo "\n\n";

if ($result['success']) {
    echo "✅ Pesan berhasil dikirim!\n";
} else {
    echo "❌ Gagal mengirim pesan: " . ($result['error'] ?? 'Unknown error') . "\n";
}
