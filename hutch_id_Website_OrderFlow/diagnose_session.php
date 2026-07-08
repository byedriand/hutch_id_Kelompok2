<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$path = storage_path('framework/sessions');
echo "storage_path=$path\n";
echo is_dir($path) ? "is_dir=1\n" : "is_dir=0\n";
echo is_writable($path) ? "writable=1\n" : "writable=0\n";
$file = $path . DIRECTORY_SEPARATOR . 'test_write.txt';
$result = @file_put_contents($file, 'ok');
echo "file_put_contents=" . ($result === false ? 'false' : $result) . "\n";
if (file_exists($file)) {
    echo "file_exists=1\n";
    echo "file_content=" . file_get_contents($file) . "\n";
    unlink($file);
} else {
    echo "file_exists=0\n";
}
