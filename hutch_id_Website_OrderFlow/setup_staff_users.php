<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');

$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create or update staff users
$users = [
    [
        'email' => 'admin@hutch.id',
        'name' => 'Administrator',
        'password' => 'password123',
        'role' => 'administrator',
    ],
    [
        'email' => 'pemilik@hutch.id',
        'name' => 'Pemilik UMKM',
        'password' => 'password123',
        'role' => 'pemilik_umkm',
    ],
    [
        'email' => 'staf@hutch.id',
        'name' => 'Staf Penjualan',
        'password' => 'password123',
        'role' => 'staf_penjualan',
    ],
    [
        'email' => 'gudang@hutch.id',
        'name' => 'Operator Gudang',
        'password' => 'password123',
        'role' => 'operator_gudang',
    ],
];

foreach ($users as $userData) {
    $user = User::where('email', $userData['email'])->first();
    
    if ($user) {
        // Update existing user
        $user->name = $userData['name'];
        $user->password = Hash::make($userData['password']);
        $user->role = $userData['role'];
        $user->email_verified_at = now();
        $user->save();
        echo "✅ Updated user: " . $userData['email'] . "\n";
    } else {
        // Create new user
        User::create([
            'email' => $userData['email'],
            'name' => $userData['name'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'],
            'email_verified_at' => now(),
        ]);
        echo "✅ Created user: " . $userData['email'] . "\n";
    }
}

echo "\n✅ All users setup completed!\n";
echo "You can login with:\n";
echo "Email: staf@hutch.id\n";
echo "Password: password123\n";
echo "Role: Staf Penjualan\n";
