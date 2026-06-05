<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');

$kernel->bootstrap();

// Update password for testing user
$user = \App\Models\User::find(1);
if ($user) {
    $user->password = \Illuminate\Support\Facades\Hash::make('password');
    $user->save();
    echo "Password updated for user: " . $user->email . "\n";
    echo "You can now login with:\n";
    echo "Email: " . $user->email . "\n";
    echo "Password: password\n";
} else {
    echo "User not found\n";
}
