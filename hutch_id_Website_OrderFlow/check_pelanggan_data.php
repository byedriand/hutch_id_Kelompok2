<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Get all pelanggan data
$pelanggan = DB::table('pelanggan')->orderBy('id')->get();

echo "=== DATA PELANGGAN TERBARU ===\n\n";
echo "Total Pelanggan: " . count($pelanggan) . "\n\n";

foreach($pelanggan as $p) {
    echo "[ID: " . $p->id . "] " . $p->nama . "\n";
    echo "  Telepon: " . $p->telepon . "\n";
    echo "  Email: " . $p->email . "\n";
    echo "  Alamat: " . substr($p->alamat, 0, 50) . "...\n";
    
    // Count pesanan
    $pesananCount = DB::table('pesanan')->where('pelanggan_id', $p->id)->count();
    echo "  Total PO: " . $pesananCount . "\n";
    echo "\n";
}
?>
