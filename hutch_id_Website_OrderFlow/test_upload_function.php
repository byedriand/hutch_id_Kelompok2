<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Produk;
use App\Http\Controllers\ProdukController;

// Create test controller instance
$controller = new ProdukController();

// Simulate a request with test photo
$testFile = new \Symfony\Component\HttpFoundation\File\UploadedFile(
    'test-upload.png',
    'test-file-' . time() . '.png',
    'image/png',
    null,
    true
);

// Call helper function using Reflection
$storeMethod = new \ReflectionMethod($controller, 'storeProductPhoto');
$storeMethod->setAccessible(true);

$result = $storeMethod->invoke($controller, $testFile);

echo "=== UPLOAD TEST RESULT ===\n";
echo "Returned path: $result\n";
echo "Files in /public/images/:\n";
$files = glob(__DIR__ . '/public/images/*');
echo "Total: " . count($files) . "\n";
foreach (array_slice($files, -5) as $f) {
    echo "  - " . basename($f) . "\n";
}
