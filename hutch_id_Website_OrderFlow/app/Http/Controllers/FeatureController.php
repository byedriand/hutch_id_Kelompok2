<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeatureController extends Controller
{
    public function show($slug)
    {
        $features = [
            'manajemen-pesanan' => [
                'title' => 'Manajemen Pesanan',
            'description' => 'Modul ini memungkinkan staf penjualan membuat PO baru dengan nomor otomatis, menambahkan beberapa produk dalam satu pesanan, serta melihat dan mencetak dokumen PO dalam format PDF. Harga produk akan dikunci saat pesanan disimpan untuk menjaga konsistensi data.',
            'image' => '/images/features/manajemen-pesanan.jpeg',
            'cta_label' => 'Pelajari Manajemen Pesanan',
            'cta_link' => '/features/manajemen-pesanan#learn-more',
                'bullets' => [
                    'Pembuatan PO otomatis',
                    'Cetak dokumen PDF',
                    'Pelacakan status pesanan',
                ],
            ],
            'inventori-pintar' => [
                'title' => 'Inventori Pintar',
                'description' => 'Sistem secara otomatis memverifikasi stok bahan baku berdasarkan BOM produk ketika PO dibuat. Pengguna dapat melihat jumlah stok tersedia, kebutuhan produksi, dan selisih kekurangan, serta menerima notifikasi saat persediaan menipis.',
                'image' => '/images/features/inventori-pintar.jpeg',
                'cta_label' => 'Lihat Inventori Pintar',
                'cta_link' => '/features/inventori-pintar#learn-more',
                'bullets' => [
                    'Verifikasi bahan baku otomatis',
                    'Monitoring stok real-time',
                    'Notifikasi stok menipis',
                ],
            ],
            'manajemen-pelanggan' => [
                'title' => 'Manajemen Pelanggan',
                'description' => 'Menyediakan fitur CRUD data pelanggan lengkap dengan pencarian otomatis saat membuat PO. Riwayat data pelanggan tersimpan dengan baik sehingga memudahkan pengelolaan pesanan berulang dan menjaga akurasi informasi pelanggan.',
                'image' => '/images/features/manajemen-pelanggan.jpeg',
                'cta_label' => 'Kelola Pelanggan',
                'cta_link' => '/features/manajemen-pelanggan#learn-more',
                'bullets' => [
                    'CRUD data pelanggan',
                    'Pencarian otomatis',
                    'Riwayat pemesanan tersimpan',
                ],
            ],
            'dashboard-analitik' => [
                'title' => 'Dashboard Analitik',
                'description' => 'Dashboard menampilkan ringkasan jumlah PO aktif, pesanan yang menunggu konfirmasi, status produksi, serta pesanan yang siap dikirim. Informasi diperbarui secara real-time untuk membantu pengambilan keputusan operasional.',
                'image' => '/images/features/dashboard-analitik.jpeg',
                'cta_label' => 'Buka Dashboard',
                'cta_link' => '/features/dashboard-analitik#learn-more',
                'bullets' => [
                    'Ringkasan pesanan aktif',
                    'Monitoring status produksi',
                    'Data real-time',
                ],
            ],
            'asisten-ai' => [
                'title' => 'Asisten AI',
                'description' => 'Terintegrasi dengan workflow N8N untuk menjalankan proses otomatis, membantu pencarian informasi sistem, serta mendukung pengembangan fitur notifikasi dan automasi operasional agar pekerjaan menjadi lebih efisien.',
                'image' => '/images/features/chatbot.jpeg',
                'cta_label' => 'Gunakan Asisten AI',
                'cta_link' => '/features/asisten-ai#learn-more',
                'bullets' => [
                    'Workflow otomatis',
                    'Pencarian informasi sistem',
                    'Dukungan notifikasi pintar',
                ],
            ],
            'keamanan-enterprise' => [
                'title' => 'Keamanan Enterprise',
                'description' => 'Sistem menerapkan Role-Based Access Control (RBAC) dengan empat tingkat pengguna, audit trail perubahan status pesanan, autentikasi berbasis sesi, serta tautan berbagi dokumen PDF yang memiliki masa berlaku terbatas untuk menjaga keamanan informasi.',
                'image' => '/images/features/keamanan-enterprise.jpeg',
                'cta_label' => 'Pelajari Keamanan',
                'cta_link' => '/features/keamanan-enterprise#learn-more',
                'bullets' => [
                    'RBAC 4 tingkat pengguna',
                    'Audit trail aktivitas',
                    'Tautan PDF terbatas waktu',
                ],
            ],
        ];

        if (!isset($features[$slug])) {
            abort(404);
        }

        $feature = $features[$slug];

        return view('landing.feature', array_merge(['slug' => $slug], $feature));
    }
}
