<?php
// Test WhatsApp notification API directly
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate a request to the updateStatus endpoint
use Illuminate\Http\Request;
use App\Http\Controllers\PesananController;
use App\Models\Pesanan;

// Find an order to test
$order = Pesanan::whereIn('status', ['dalam_produksi', 'menunggu', 'dikonfirmasi'])->first();

if (!$order) {
    die("No suitable order found for testing\n");
}

echo "Found order: " . $order->nomor_po . " (ID: " . $order->id . ", Current status: " . $order->status . ")\n";
echo "URL: http://localhost:8082/pesanan/" . $order->id . "\n";

// Get available status options for this order
$pesananController = app(PesananController::class);
$statusOptions = app(\App\Services\StatusService::class)->getAvailableTransitions($order->status);

echo "Available transitions:\n";
foreach ($statusOptions as $value => $label) {
    echo "  - $value: $label\n";
}

// Check if siap_kirim is available
if (in_array('siap_kirim', array_keys($statusOptions))) {
    echo "\n✅ Order can transition to 'Siap Kirim' - ready for WhatsApp test\n";
} else {
    echo "\n❌ Order cannot transition to 'Siap Kirim'\n";
}
