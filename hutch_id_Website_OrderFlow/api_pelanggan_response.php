<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Simulate API response untuk mobile app
$pelanggan = DB::table('pelanggan')->orderBy('nama')->select(['id', 'nama', 'email', 'telepon', 'alamat'])->get();

// Format sebagai API response
$response = [
    'value' => $pelanggan->toArray(),
    'total' => count($pelanggan)
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
