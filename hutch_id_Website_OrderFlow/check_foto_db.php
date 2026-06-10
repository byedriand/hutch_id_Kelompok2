<?php
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';

$results = DB::select('SELECT id, nama, foto FROM produk WHERE foto IS NOT NULL ORDER BY id DESC LIMIT 5');

foreach ($results as $r) {
    echo $r->id . ' | ' . $r->nama . ' | ' . $r->foto . "\n";
}
