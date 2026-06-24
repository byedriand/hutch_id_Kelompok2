<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;

// Boot the application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Now check products
$produkCount = DB::table('produk')->count();
$produk = DB::table('produk')->get();

echo "Total Produk: " . $produkCount . "\n";
echo json_encode($produk, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
?>
