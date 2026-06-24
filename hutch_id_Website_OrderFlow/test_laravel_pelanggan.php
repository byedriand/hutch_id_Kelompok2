<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/bootstrap/app.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

$pelanggan = \App\Models\Pelanggan::find(7);

if ($pelanggan) {
    echo "=== Pelanggan Data ===\n";
    echo "ID: " . $pelanggan->id . "\n";
    echo "Nama: " . $pelanggan->nama . "\n";
    echo "Telepon: " . var_export($pelanggan->telepon, true) . "\n";
    echo "Nomor WhatsApp: " . var_export($pelanggan->nomor_whatsapp, true) . "\n";
    echo "nomor_whatsapp empty? " . (empty($pelanggan->nomor_whatsapp) ? "YES" : "NO") . "\n";
    echo "nomor_whatsapp null? " . ($pelanggan->nomor_whatsapp === null ? "YES" : "NO") . "\n";
    
    // Test formatting
    require __DIR__ . '/app/Services/WhatsAppService.php';
    
    if (!empty($pelanggan->nomor_whatsapp)) {
        $formatted = \App\Services\WhatsAppService::formatPhoneNumber($pelanggan->nomor_whatsapp);
        echo "Formatted: " . $formatted . "\n";
        echo "Is Valid: " . (\App\Services\WhatsAppService::isValidPhoneNumber($pelanggan->nomor_whatsapp) ? "YES" : "NO") . "\n";
    } else {
        echo "nomor_whatsapp is empty!\n";
        if (!empty($pelanggan->telepon)) {
            $formatted = \App\Services\WhatsAppService::formatPhoneNumber($pelanggan->telepon);
            echo "Formatting telepon instead: " . $formatted . "\n";
            echo "Is Valid: " . (\App\Services\WhatsAppService::isValidPhoneNumber($pelanggan->telepon) ? "YES" : "NO") . "\n";
        }
    }
} else {
    echo "Pelanggan not found\n";
}
