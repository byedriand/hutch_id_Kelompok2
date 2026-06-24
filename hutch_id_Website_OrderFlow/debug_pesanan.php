<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = app();

$pesanan = \App\Models\Pesanan::with('pelanggan')->get();
echo "Total pesanan: " . count($pesanan) . "\n\n";

foreach ($pesanan as $p) {
    echo "PO: " . $p->nomor_po . " | Status: " . $p->status . " | Created by: " . $p->created_by . " | Total: Rp " . number_format($p->total_nilai, 0) . "\n";
}

echo "\n=== STAF USERS ===\n";
$users = \App\Models\User::where('role', 'staf_penjualan')->get();
foreach ($users as $u) {
    echo "ID: " . $u->id . " | Name: " . $u->name . " | Role: " . $u->role . "\n";
}
?>
