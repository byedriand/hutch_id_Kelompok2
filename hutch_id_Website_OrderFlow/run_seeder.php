<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Run seeder
$seeder = new \Database\Seeders\ProdukSeeder();
$seeder->run();

echo "✓ Produk berhasil dibuat dengan nama dari gambar Anda!\n";
echo "\nProduk yang dibuat:\n";
$produk = \App\Models\Produk::orderBy('id', 'desc')->limit(5)->get();
foreach($produk as $p) {
    echo "- {$p->nama} (ID: {$p->id})\n";
}
echo "\nSekarang silakan upload gambar untuk setiap produk melalui halaman edit produk.\n";
