<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$produk = \App\Models\Produk::all(['id', 'nama', 'foto']);
foreach($produk as $p) {
    echo $p->id . ' | ' . $p->nama . ' | ' . ($p->foto ?? 'NULL') . PHP_EOL;
}
