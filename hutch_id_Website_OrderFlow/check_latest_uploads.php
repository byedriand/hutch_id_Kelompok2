<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Produk;

echo "=== LATEST 5 PRODUCTS ===\n";
$products = Produk::orderBy('created_at', 'desc')->take(5)->get();

foreach ($products as $p) {
    echo sprintf(
        "ID: %d | Nama: %-30s | Foto: %s\n",
        $p->id,
        $p->nama,
        $p->foto ?? 'NULL'
    );
}

echo "\n=== FILES IN /public/images/ ===\n";
$files = glob(__DIR__ . '/public/images/*.{jpeg,jpg,png,gif}', GLOB_BRACE);
echo "Total: " . count($files) . "\n";
foreach (array_slice($files, -5) as $f) {
    echo basename($f) . "\n";
}
