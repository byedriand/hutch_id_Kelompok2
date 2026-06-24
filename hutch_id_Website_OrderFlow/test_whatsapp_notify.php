<?php
// Test WhatsApp notification with new order
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

use App\Models\Pesanan;
use App\Models\Pelanggan;

$app = require __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get pelanggan
$pelanggan = Pelanggan::find(12);
if (!$pelanggan) {
    die("Pelanggan not found");
}

// Create new order
$order = Pesanan::create([
    'pelanggan_id' => 12,
    'nomor_po' => 'PO-' . date('YmdHi') . '-TEST',
    'status' => 'dalam_produksi',
    'total_nilai' => 100000,
    'tgl_pesanan' => now(),
    'tgl_kirim_target' => now()->addDays(7),
]);

echo "Order created: " . $order->nomor_po . " (ID: " . $order->id . ")\n";
echo "URL: http://localhost:8082/pesanan/" . $order->id . "\n";
