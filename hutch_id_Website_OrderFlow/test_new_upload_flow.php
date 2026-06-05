<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Produk;

// Simulate creating test file in images folder
$testContent = file_get_contents(__DIR__ . '/test-upload.png');
$uniqueName = \Str::random(32) . '.png';
$testPath = public_path('images/' . $uniqueName);

file_put_contents($testPath, $testContent);

echo "=== TEST UPLOAD SIMULATION ===\n";
echo "Created test file: " . basename($testPath) . "\n";
echo "Full path: " . $testPath . "\n";
echo "File exists: " . (file_exists($testPath) ? 'YES' : 'NO') . "\n\n";

// Create product with the new path format
$produk = Produk::create([
    'nama' => 'TEST PRODUK UPLOAD BARU ' . date('Y-m-d H:i:s'),
    'harga_jual' => 99000,
    'stok' => 10,
    'keterangan' => 'Test produk dengan foto yang di-upload ke /public/images/',
    'foto' => 'images/' . $uniqueName,  // New format!
]);

echo "=== PRODUCT CREATED ===\n";
echo "ID: " . $produk->id . "\n";
echo "Nama: " . $produk->nama . "\n";
echo "Foto DB: " . $produk->foto . "\n";
echo "Foto URL (via accessor): " . $produk->fotoUrl . "\n\n";

echo "=== FILE VERIFICATION ===\n";
$files = array_slice(glob(__DIR__ . '/public/images/*'), -5);
echo "Latest 5 files in /public/images/:\n";
foreach ($files as $f) {
    echo "  - " . basename($f) . " (" . filesize($f) . " bytes)\n";
}
