<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('produk')->where('id', 1)->update(['foto' => 'images/Tas canvas custom.jpeg']);
DB::table('produk')->where('id', 3)->update(['foto' => 'images/Tas laptop.jpeg']);
DB::table('produk')->where('id', 4)->update(['foto' => 'images/Totebag wanita.jpeg']);

echo "✓ Update selesai!\n";
