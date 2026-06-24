<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Pelanggan;
use Illuminate\Support\Facades\DB;

// Start Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Update Nomor WhatsApp Pelanggan ===\n\n";

// Find pelanggan by name
$pelanggan = Pelanggan::where('nama', 'like', '%Sopyan%')->first();

if (!$pelanggan) {
    echo "❌ Pelanggan tidak ditemukan.\n";
    echo "\nDaftar Pelanggan:\n";
    $allPelanggan = Pelanggan::all();
    foreach ($allPelanggan as $p) {
        echo "  - " . $p->id . ". " . $p->nama . " (Telepon: " . $p->telepon . ")\n";
    }
    exit;
}

echo "✅ Pelanggan ditemukan:\n";
echo "   ID: " . $pelanggan->id . "\n";
echo "   Nama: " . $pelanggan->nama . "\n";
echo "   Telepon Lama: " . $pelanggan->telepon . "\n";
echo "   WhatsApp Lama: " . ($pelanggan->nomor_whatsapp ?? 'Belum terdaftar') . "\n";
echo "\n";

// Update nomor WhatsApp
$newPhone = '+62 855-5555-401';
$pelanggan->update(['nomor_whatsapp' => $newPhone]);

// Format for verification
$formatted = preg_replace('/\D/', '', $newPhone);
if (substr($formatted, 0, 1) === '0') {
    $formatted = '62' . substr($formatted, 1);
}
if (substr($formatted, 0, 2) !== '62') {
    $formatted = '62' . $formatted;
}

echo "✅ Update Berhasil!\n";
echo "   Nomor WhatsApp Baru: " . $newPhone . "\n";
echo "   Format Terekam: " . $formatted . "\n\n";

// Verify
$verified = Pelanggan::find($pelanggan->id);
echo "📋 Verifikasi Data:\n";
echo "   ID: " . $verified->id . "\n";
echo "   Nama: " . $verified->nama . "\n";
echo "   Telepon: " . $verified->telepon . "\n";
echo "   WhatsApp: " . $verified->nomor_whatsapp . "\n";
echo "\n✅ Data pelanggan berhasil diupdate! Sekarang coba kirim notifikasi WhatsApp.\n";
