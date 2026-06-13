<?php
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->handle(
    $request = Illuminate\Http\Request::capture()
);

$products = DB::table('produk')->whereNotNull('foto')->get(['id', 'nama', 'foto']);

foreach ($products as $p) {
    echo "{$p->id} | {$p->nama} | {$p->foto}\n";
}
