<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@hutchprestige.com'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123'),
                'role' => 'Administrator',
                'deskripsi' => 'Akses Penuh',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'sales@hutchprestige.com'],
            [
                'name' => 'Staf Penjualan',
                'password' => bcrypt('sales123'),
                'role' => 'Staf Penjualan',
                'deskripsi' => 'Sales',
            ]
        );

        \App\Models\User::updateOrCreate(
            ['email' => 'warehouse@hutchprestige.com'],
            [
                'name' => 'Operator Gudang',
                'password' => bcrypt('warehouse123'),
                'role' => 'Operator Gudang',
                'deskripsi' => 'Warehouse',
            ]
        );

        // 2. Seed Pelanggans
        \App\Models\Pelanggan::updateOrCreate(
            ['email' => 'indomakmur@mail.com'],
            [
                'nama' => 'CV. Indo Makmur',
                'telepon' => '08123456789',
                'alamat' => 'Jl. Industri No. 45, Jakarta',
                'jumlah_po' => 5,
            ]
        );

        \App\Models\Pelanggan::updateOrCreate(
            ['email' => 'bagussentosa@mail.com'],
            [
                'nama' => 'PT. Bagus Sentosa',
                'telepon' => '08777654321',
                'alamat' => 'Kawasan Ruko Harmoni Blok B/12, Surabaya',
                'jumlah_po' => 2,
            ]
        );

        \App\Models\Pelanggan::updateOrCreate(
            ['email' => 'berkahjaya@mail.com'],
            [
                'nama' => 'Toko Berkah Jaya',
                'telepon' => '08998877665',
                'alamat' => 'Jl. Pasar Baru No. 8, Bandung',
                'jumlah_po' => 0,
            ]
        );

        // 3. Seed Pesanans
        \App\Models\Pesanan::updateOrCreate(
            ['no' => 'PO-001'],
            [
                'pelanggan' => 'CV. Indo Makmur',
                'deskripsi' => 'Pesanan Backpack Kanvas Hitam',
                'jumlah' => 100,
                'harga' => 125000,
                'status' => 'Selesai',
            ]
        );

        \App\Models\Pesanan::updateOrCreate(
            ['no' => 'PO-002'],
            [
                'pelanggan' => 'CV. Indo Makmur',
                'deskripsi' => 'Pesanan Tote Bag Premium',
                'jumlah' => 250,
                'harga' => 45000,
                'status' => 'Proses',
            ]
        );

        \App\Models\Pesanan::updateOrCreate(
            ['no' => 'PO-003'],
            [
                'pelanggan' => 'PT. Bagus Sentosa',
                'deskripsi' => 'Pesanan Duffle Bag Travel',
                'jumlah' => 50,
                'harga' => 210000,
                'status' => 'Pending',
            ]
        );

        \App\Models\Pesanan::updateOrCreate(
            ['no' => 'PO-004'],
            [
                'pelanggan' => 'PT. Bagus Sentosa',
                'deskripsi' => 'Pesanan Pouch Kulit Minimalis',
                'jumlah' => 500,
                'harga' => 25000,
                'status' => 'Draft',
            ]
        );

        // 4. Seed ArsipPdfs
        \App\Models\ArsipPdf::updateOrCreate(
            ['filename' => 'PO-001_CV_Indo_Makmur.pdf'],
            [
                'path' => 'storage/arsip/PO-001_CV_Indo_Makmur.pdf',
                'size' => '1.2 MB',
            ]
        );

        \App\Models\ArsipPdf::updateOrCreate(
            ['filename' => 'PO-002_CV_Indo_Makmur.pdf'],
            [
                'path' => 'storage/arsip/PO-002_CV_Indo_Makmur.pdf',
                'size' => '845 KB',
            ]
        );
    }
}
