<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Produk;

echo "=== DATABASE PRODUCTS ===\n";
$products = Produk::all();
foreach($products as $p) {
    echo "ID: {$p->id} | Nama: {$p->nama} | Foto: {$p->foto}\n";
}

echo "\n=== IMAGES IN /public/images/ ===\n";
$images = glob(__DIR__ . '/public/images/*');
foreach($images as $img) {
    echo basename($img) . "\n";
}
