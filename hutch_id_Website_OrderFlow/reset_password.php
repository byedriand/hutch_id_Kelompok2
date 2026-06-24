<?php
require __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Find and update admin user
$user = User::where('email', 'admin@hutch.id')->first();

if ($user) {
    $user->password = bcrypt('password');
    $user->save();
    echo "✅ Password reset for {$user->email}\n";
} else {
    echo "❌ Admin user not found\n";
}
