<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\DetailPesanan;
use App\Models\HistoriStatus;
use Illuminate\Support\Carbon;

// Get or create customer
$pelanggan = Pelanggan::firstOrCreate(
    ['nama' => 'Pt.Sopyan'],
    [
        'nomor_telepon' => '+62 855-5555-4012',
        'alamat' => 'Bandung',
        'email' => 's@gmail.com'
    ]
);

// Create order
$pesanan = Pesanan::create([
    'nomor_po' => 'PO-' . Carbon::now()->format('YmdHis') . '-' . rand(100, 999),
    'pelanggan_id' => $pelanggan->id,
    'status' => 'menunggu_konfirmasi',
    'total_nilai' => 10000,
    'tanggal_pesanan' => Carbon::now(),
    'tanggal_pengiriman' => Carbon::now()->addDay(),
    'created_by' => 1, // admin user
]);

// Add product detail
DetailPesanan::create([
    'pesanan_id' => $pesanan->id,
    'produk_id' => 7, // Tas Gendong
    'jumlah' => 1,
    'harga' => 10000,
    'subtotal' => 10000,
]);

// Add initial status history
HistoriStatus::create([
    'pesanan_id' => $pesanan->id,
    'user_id' => 1,
    'status' => 'menunggu_konfirmasi',
    'keterangan' => 'Pesanan dibuat oleh Administrator',
]);

// Update to dikonfirmasi
$pesanan->update(['status' => 'dikonfirmasi']);
HistoriStatus::create([
    'pesanan_id' => $pesanan->id,
    'user_id' => 1,
    'status' => 'dikonfirmasi',
    'keterangan' => 'Pesanan dikonfirmasi oleh Administrator',
]);

// Update to dalam_produksi
$pesanan->update(['status' => 'dalam_produksi']);
HistoriStatus::create([
    'pesanan_id' => $pesanan->id,
    'user_id' => 1,
    'status' => 'dalam_produksi',
    'keterangan' => 'Pesanan sedang dalam produksi',
]);

echo "✅ Order created successfully!\n\n";
echo "Order Details:\n";
echo "- PO Number: " . $pesanan->nomor_po . "\n";
echo "- Customer: " . $pelanggan->nama . " (" . $pelanggan->nomor_telepon . ")\n";
echo "- Status: " . $pesanan->status . "\n";
echo "- Total: Rp " . number_format($pesanan->total_nilai, 0, ',', '.') . "\n";
echo "- ID: " . $pesanan->id . "\n\n";

echo "Next: Navigate to http://localhost:8082/pesanan/" . $pesanan->id . "\n";
echo "Then change status to 'Siap Kirim' to trigger WhatsApp notification.\n";
