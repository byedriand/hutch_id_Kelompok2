<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';

$kernel = $app->make('Illuminate\Contracts\Console\Kernel');

$kernel->bootstrap();

$users = \App\Models\User::all();

if ($users->isEmpty()) {
    echo "No users found in database\n";
} else {
    foreach ($users as $user) {
        echo "ID: " . $user->id . ", Email: " . $user->email . ", Name: " . $user->name . "\n";
    }
}
