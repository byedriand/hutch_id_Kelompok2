<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\HistoriStatus;
use Illuminate\Support\Facades\Hash;

class HutchidSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users
        User::updateOrCreate(
            ['email' => 'staf@hutch.id'],
            ['name' => 'Staf Penjualan', 'password' => Hash::make('password123'), 'role' => 'staf_penjualan']
        );

        User::updateOrCreate(
            ['email' => 'pemilik@hutch.id'],
            ['name' => 'Pemilik UMKM', 'password' => Hash::make('password123'), 'role' => 'pemilik_umkm']
        );

        User::updateOrCreate(
            ['email' => 'gudang@hutch.id'],
            ['name' => 'Operator Gudang', 'password' => Hash::make('password123'), 'role' => 'operator_gudang']
        );

        User::updateOrCreate(
            ['email' => 'admin@hutch.id'],
            ['name' => 'Administrator', 'password' => Hash::make('password123'), 'role' => 'administrator']
        );

        // Create Pelanggan
        $pelangganData = [
            ['nama' => 'Budi Bag Store', 'alamat' => 'Jl. Merdeka No. 123, Jakarta', 'telepon' => '0812345678', 'email' => 'budi@bagstore.com'],
            ['nama' => 'Toko Maju Jaya', 'alamat' => 'Jl. Sudirman No. 456, Bandung', 'telepon' => '0898765432', 'email' => 'toko@majujaya.com'],
            ['nama' => 'CV Sinar Baru', 'alamat' => 'Jl. Ahmad Yani No. 789, Yogyakarta', 'telepon' => '0822111222', 'email' => 'cv@sinarbaru.com'],
            ['nama' => 'BagWorld Indonesia', 'alamat' => 'Jl. Gatot Subroto No. 321, Surabaya', 'telepon' => '0833444555', 'email' => 'bagworld@indo.com'],
            ['nama' => 'Tiga Bintang Store', 'alamat' => 'Jl. Kuningan No. 654, Medan', 'telepon' => '0856666777', 'email' => 'tigabintang@store.com'],
            ['nama' => 'Indo Bag Co', 'alamat' => 'Jl. Cikini No. 987, Jakarta', 'telepon' => '0878888999', 'email' => 'indo@bagco.com'],
        ];

        $pelangganIds = [];
        foreach ($pelangganData as $data) {
            $pelangganIds[] = Pelanggan::firstOrCreate(['email' => $data['email']], $data)->id;
        }

        // Create Produk
        $produkData = [
            ['nama' => 'Tas Kanvas Custom', 'harga_jual' => 150000, 'stok' => 50],
            ['nama' => 'Tas Punggung', 'harga_jual' => 140000, 'stok' => 75],
            ['nama' => 'Tas Selempang', 'harga_jual' => 120000, 'stok' => 100],
            ['nama' => 'Dompet Kulit', 'harga_jual' => 50000, 'stok' => 200],
            ['nama' => 'Tas Travel', 'harga_jual' => 300000, 'stok' => 30],
        ];

        $produkIds = [];
        foreach ($produkData as $data) {
            $produkIds[] = Produk::firstOrCreate(['nama' => $data['nama']], $data)->id;
        }

        // Create Pesanan with different statuses
        $statuses = ['menunggu_konfirmasi', 'dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai', 'dibatalkan'];
        $poCounter = 1;

        foreach ($statuses as $status) {
            for ($i = 0; $i < 2; $i++) {
                $nomorPo = 'PO-' . now()->format('Ymmd') . '-' . str_pad($poCounter++, 3, '0', STR_PAD_LEFT);
                $pesanan = Pesanan::create([
                    'nomor_po' => $nomorPo,
                    'tanggal_pesanan' => now()->subDays(rand(1, 30)),
                    'tanggal_pengiriman' => now()->addDays(rand(1, 30)),
                    'pelanggan_id' => $pelangganIds[array_rand($pelangganIds)],
                    'total_nilai' => 0,
                    'status' => $status,
                    'catatan' => 'Catatan untuk PO ini',
                    'created_by' => User::where('role', 'staf_penjualan')->first()->id,
                ]);

                // Create Detail Pesanan
                $totalNilai = 0;
                for ($j = 0; $j < rand(1, 3); $j++) {
                    $produkId = $produkIds[array_rand($produkIds)];
                    $jumlah = rand(1, 20);
                    $hargaSatuan = Produk::find($produkId)->harga_jual;

                    DetailPesanan::create([
                        'pesanan_id' => $pesanan->id,
                        'produk_id' => $produkId,
                        'jumlah' => $jumlah,
                        'spesifikasi' => 'Spesifikasi produk custom',
                        'harga_satuan' => $hargaSatuan,
                    ]);

                    $totalNilai += $jumlah * $hargaSatuan;
                }

                // Update total nilai
                $pesanan->update(['total_nilai' => $totalNilai]);

                // Create Histori Status
                HistoriStatus::create([
                    'pesanan_id' => $pesanan->id,
                    'user_id' => User::where('role', 'staf_penjualan')->first()->id,
                    'status' => 'menunggu_konfirmasi',
                    'keterangan' => 'PO dibuat oleh staf penjualan',
                ]);

                if ($status !== 'menunggu_konfirmasi') {
                    HistoriStatus::create([
                        'pesanan_id' => $pesanan->id,
                        'user_id' => User::where('role', 'pemilik_umkm')->first()->id,
                        'status' => $status,
                        'keterangan' => 'Status diubah menjadi ' . $status,
                    ]);
                }
            }
        }
    }
}
