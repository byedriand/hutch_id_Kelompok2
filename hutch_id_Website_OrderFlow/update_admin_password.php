<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

DB::table('users')->where('email', 'admin@hutch.id')->update([
    'password' => Hash::make('password123')
]);

echo "✓ Admin password updated to 'password123'\n";
