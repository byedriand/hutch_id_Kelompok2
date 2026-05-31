<?php
require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Delete the specified products
$deleted = \App\Models\Produk::whereIn('nama', ['Dompet Kulit', 'Tas Selempang', 'Tas Punggung', 'Tas Travel'])->delete();

echo "Deleted $deleted products successfully\n";
