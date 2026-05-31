<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Mapping produk dengan foto dari images folder
$mapping = [
    2 => 'images/Tas gendong.jpeg',      // Tas Punggung -> use Tas gendong.jpeg
    5 => 'images/Tas punggung mini.jpeg',  // Tas Travel -> use Tas punggung mini.jpeg
    6 => 'images/Tas gendong.jpeg',      // Tas gendong -> Tas gendong.jpeg
    7 => 'images/Tas punggung mini.jpeg', // Tas punggung mini -> Tas punggung mini.jpeg
    8 => 'images/Tas laptop.jpeg',       // Tas laptop -> Tas laptop.jpeg
    9 => 'images/Totebag wanita.jpeg',   // Totebag wanita -> Totebag wanita.jpeg
];

foreach ($mapping as $id => $foto) {
    DB::table('produk')->where('id', $id)->update(['foto' => $foto]);
}

echo "✓ Update selesai! Semua produk sekarang gunakan images folder.\n\n";

// Verify
use App\Models\Produk;
$produk = Produk::all();
echo "Daftar Produk dan Foto:\n";
echo str_repeat("=", 70) . "\n";
foreach ($produk as $p) {
    $foto = $p->foto ? "✓ " . $p->foto : "✗ (tidak ada)";
    echo $p->id . ". " . str_pad($p->nama, 25) . " -> " . $foto . "\n";
}
echo str_repeat("=", 70) . "\n";
