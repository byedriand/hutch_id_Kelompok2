<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Produk;

// Update produk dengan foto dari images folder
DB::table('produk')->where('id', 1)->update(['foto' => 'images/Tas canvas-custom.jpeg']);
DB::table('produk')->where('id', 3)->update(['foto' => 'images/Tas laptop.jpeg']);
DB::table('produk')->where('id', 4)->update(['foto' => 'images/Totebag wanita.jpeg']);

echo "✓ Update selesai!\n\n";

// Verify the updates
$produk = Produk::all();
echo "Daftar Produk dan Foto:\n";
echo str_repeat("=", 70) . "\n";
foreach ($produk as $p) {
    $foto = $p->foto ? "✓ " . $p->foto : "✗ (tidak ada)";
    echo $p->id . ". " . str_pad($p->nama, 25) . " -> " . $foto . "\n";
}
echo str_repeat("=", 70) . "\n";
