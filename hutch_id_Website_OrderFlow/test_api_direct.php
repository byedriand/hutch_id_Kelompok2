<?php
// Quick API test script
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test if API routes are accessible
echo "=== API TEST ===\n\n";

// Test pelanggan endpoint directly
$pelanggan = DB::table('pelanggan')->get();
echo "Pelanggan Count: " . count($pelanggan) . "\n";
echo json_encode(['value' => $pelanggan->toArray(), 'total' => count($pelanggan)], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
