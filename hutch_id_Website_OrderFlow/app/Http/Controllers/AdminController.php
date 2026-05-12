<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Dummy data untuk dashboard
        $stats = [
            'total_po' => 24,
            'menunggu_konfirmasi' => 3,
            'siap_kirim' => 7,
            'selesai_bulan' => 14,
            'nilai_selesai' => 48500000,
        ];

        $po_menunggu = [
            [
                'nomor' => 'PO-20260421-003',
                'pelanggan' => 'Budi Bag Store',
                'produk' => 'Tas Kanvas Custom (50 pcs)',
                'nilai' => 7500000,
                'tgl_kirim' => '28 Apr',
                'stok' => 'tersedia',
            ],
            [
                'nomor' => 'PO-20260421-002',
                'pelanggan' => 'Toko Maju Jaya',
                'produk' => 'Tas Punggung (30 pcs)',
                'nilai' => 4200000,
                'tgl_kirim' => '25 Apr',
                'stok' => 'kurang',
            ],
            [
                'nomor' => 'PO-20260420-005',
                'pelanggan' => 'CV Sinar Baru',
                'produk' => 'Dompet Kulit (100 pcs)',
                'nilai' => 5000000,
                'tgl_kirim' => '30 Apr',
                'stok' => 'tersedia',
            ],
        ];

        $po_produksi = [
            [
                'nomor' => 'PO-20260418-001',
                'pelanggan' => 'BagWorld ID',
                'produk' => 'Tas Travel (20 pcs)',
                'nilai' => 6000000,
                'target_selesai' => '23 Apr',
            ],
            [
                'nomor' => 'PO-20260417-003',
                'pelanggan' => 'Outlet Kota',
                'produk' => 'Tas Selempang (80 pcs)',
                'nilai' => 9600000,
                'target_selesai' => '22 Apr',
            ],
        ];

        // Data untuk daftar PO
        $daftar_po = [
            ['nomor' => 'PO-20260421-003', 'tanggal' => '21 Apr 2026', 'pelanggan' => 'Budi Bag Store', 'produk' => 'Tas Kanvas (50 pcs)', 'nilai' => 7500000, 'tgl_kirim' => '28 Apr', 'status' => 'wait'],
            ['nomor' => 'PO-20260421-002', 'tanggal' => '21 Apr 2026', 'pelanggan' => 'Toko Maju Jaya', 'produk' => 'Tas Punggung (30 pcs)', 'nilai' => 4200000, 'tgl_kirim' => '25 Apr', 'status' => 'wait'],
            ['nomor' => 'PO-20260418-001', 'tanggal' => '18 Apr 2026', 'pelanggan' => 'BagWorld ID', 'produk' => 'Tas Travel (20 pcs)', 'nilai' => 6000000, 'tgl_kirim' => '23 Apr', 'status' => 'prod'],
            ['nomor' => 'PO-20260415-004', 'tanggal' => '15 Apr 2026', 'pelanggan' => 'CV Sinar Baru', 'produk' => 'Dompet Kulit (100 pcs)', 'nilai' => 5000000, 'tgl_kirim' => '30 Apr', 'status' => 'conf'],
            ['nomor' => 'PO-20260410-007', 'tanggal' => '10 Apr 2026', 'pelanggan' => 'Outlet Kota', 'produk' => 'Tas Selempang (80 pcs)', 'nilai' => 9600000, 'tgl_kirim' => '18 Apr', 'status' => 'ready'],
            ['nomor' => 'PO-20260405-002', 'tanggal' => '5 Apr 2026', 'pelanggan' => 'Tiga Bintang Store', 'produk' => 'Tas Pinggang (60 pcs)', 'nilai' => 3600000, 'tgl_kirim' => '12 Apr', 'status' => 'done'],
            ['nomor' => 'PO-20260401-001', 'tanggal' => '1 Apr 2026', 'pelanggan' => 'Indo Bag Co', 'produk' => 'Tas Laptop (25 pcs)', 'nilai' => 6250000, 'tgl_kirim' => '8 Apr', 'status' => 'cancel'],
        ];

        // Data pelanggan
        $pelanggan = [
            ['nama' => 'Budi Bag Store', 'telepon' => '0812-3456-7890', 'email' => 'budi@bagstore.id', 'alamat' => 'Jl. Sudirman No. 45, Jakarta Selatan'],
            ['nama' => 'Toko Maju Jaya', 'telepon' => '0878-9012-3456', 'email' => 'majujaya@gmail.com', 'alamat' => 'Jl. Gatot Subroto No. 12, Bandung'],
            ['nama' => 'CV Sinar Baru', 'telepon' => '0856-7890-1234', 'email' => 'sinarbaru@company.id', 'alamat' => 'Jl. Asia Afrika No. 8, Surabaya'],
            ['nama' => 'BagWorld Indonesia', 'telepon' => '0821-4567-8901', 'email' => 'bagworld@id.co', 'alamat' => 'Jl. Thamrin No. 99, Jakarta Pusat'],
            ['nama' => 'Tiga Bintang Store', 'telepon' => '0813-2222-3333', 'email' => 'tigabintang@store.id', 'alamat' => 'Jl. Malioboro No. 5, Yogyakarta'],
            ['nama' => 'Indo Bag Co', 'telepon' => '0877-5555-6666', 'email' => 'indo@bagco.id', 'alamat' => 'Jl. Pemuda No. 33, Semarang'],
        ];

        return view('admin.dashboard', compact('stats', 'po_menunggu', 'po_produksi', 'daftar_po', 'pelanggan'));
    }
}