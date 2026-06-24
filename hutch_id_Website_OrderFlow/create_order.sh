php artisan tinker << 'EOF'
use App\Models\Pesanan;
use App\Models\Pelanggan;
use App\Models\DetailPesanan;
use App\Models\HistoriStatus;
use Illuminate\Support\Carbon;

$pelanggan = Pelanggan::firstOrCreate(
    ['nama' => 'Pt.Sopyan'],
    ['nomor_telepon' => '+62 855-5555-4012', 'alamat' => 'Bandung', 'email' => 's@gmail.com']
);

$pesanan = Pesanan::create([
    'nomor_po' => 'PO-TEST-' . Carbon::now()->format('YmdHis'),
    'pelanggan_id' => $pelanggan->id,
    'status' => 'dalam_produksi',
    'total_nilai' => 10000,
    'tanggal_pesanan' => Carbon::now(),
    'tanggal_pengiriman' => Carbon::now()->addDay(),
    'created_by' => 1,
]);

DetailPesanan::create([
    'pesanan_id' => $pesanan->id,
    'produk_id' => 7,
    'jumlah' => 1,
    'harga' => 10000,
    'subtotal' => 10000,
]);

HistoriStatus::create([
    'pesanan_id' => $pesanan->id,
    'user_id' => 1,
    'status' => 'dalam_produksi',
    'keterangan' => 'Order created for WhatsApp testing',
]);

echo "✅ Order " . $pesanan->nomor_po . " (ID: " . $pesanan->id . ") created! Navigate to /pesanan/" . $pesanan->id . "\n";
EOF
