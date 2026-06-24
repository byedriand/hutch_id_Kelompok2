<?php
// Check order status and history

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Pesanan;

$pesanan = Pesanan::with('historiStatus')->findOrFail(23);
echo "Order ID: 23\n";
echo "PO Number: " . $pesanan->nomor_po . "\n";
echo "Current Status: " . $pesanan->status . "\n";
echo "Customer: " . $pesanan->pelanggan->nama . " (" . $pesanan->pelanggan->nomor_telepon . ")\n\n";

echo "Status History:\n";
foreach ($pesanan->historiStatus as $hist) {
    echo "  • " . strtoupper(str_replace('_', ' ', $hist->status)) . "\n";
    echo "    Time: " . $hist->created_at->format('Y-m-d H:i:s') . "\n";
    echo "    By: " . ($hist->user->name ?? 'Unknown') . "\n";
    echo "    Note: " . $hist->keterangan . "\n\n";
}
