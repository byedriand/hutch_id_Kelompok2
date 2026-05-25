<div align="center">

<img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" />
<img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" />
<img src="https://img.shields.io/badge/Flutter-02569B?style=for-the-badge&logo=flutter&logoColor=white" />

# 👜 Hutch.id — Website OrderFlow

### Sistem Manajemen Pesanan dan Produksi UMKM

_Tugas Besar Rekayasa Sistem Informasi — Kelas-A1 Kelompok 2_  
_Program Studi Sistem Informasi — Universitas Kebangsaan Republik Indonesia (UKRI) 2026_

</div>

---

## 📌 Tentang Proyek

**OrderFlow** adalah sistem manajemen pesanan berbasis web yang dirancang khusus untuk mendukung operasional **hutch.id** — produsen tas konveksi dan brand lokal yang melayani custom production untuk bisnis maupun ready bags untuk umum.

Sistem ini mengelola seluruh siklus pesanan pelanggan, mulai dari:

- Penerimaan dan pencatatan **Purchase Order (PO)** dari pelanggan
- Verifikasi otomatis **ketersediaan bahan baku** berdasarkan BOM produk
- Pelacakan **status produksi** dari konfirmasi hingga selesai
- Penerbitan **dokumen PO resmi** dalam format PDF yang dapat dibagikan
- Notifikasi email otomatis ke pemilik UMKM setiap ada pesanan baru

> Sistem ini dikembangkan berdasarkan kebutuhan operasional **hutch.id** — Bag Manufacturing & In-House Brand yang bergerak di bidang konveksi tas UMKM.

---

## 🔔 Perkembangan Terbaru (Progress)

Versi saat ini menambahkan perbaikan pada alur stok dan notifikasi serta penyempurnaan tampilan:

- Notifikasi `stok_kurang` kini menyimpan detail kekurangan per produk (`data.detail_kurang`) saat PO memiliki item melebihi stok.
- Tombol "Aksi Cepat" pada daftar produk telah dihapus dari UI produk; quick-update stok tetap tersedia melalui modal notifikasi dan halaman edit produk.
- Saat stok ditambahkan (quick-update), sistem otomatis mencoba menyelesaikan atau memperbarui notifikasi `stok_kurang` untuk produk terkait.
- Form edit stok sekarang hanya menyediakan dua aksi: `Tambahkan Stok` dan `Kurangi Stok` (menghilangkan opsi "Set ke nilai baru").
- Daftar Pesanan dan Dashboard menampilkan indikator "Kurang" beserta jumlah unit yang kurang untuk PO yang terkena efek kekurangan stok.

Langkah-langkah ini memperbaiki konsistensi UI dan memastikan notifikasi selalu mencerminkan kondisi stok saat ini.

## ✨ Fitur Utama

| Fitur                        | Deskripsi                                                                                               |
| ---------------------------- | ------------------------------------------------------------------------------------------------------- |
| 📋 Penerimaan Order          | Buat PO baru dengan nomor auto-generate format `PO-YYYYMMDD-XXX`, multi-item, harga dikunci saat simpan |
| 🔍 Cek Bahan Baku            | Verifikasi stok otomatis saat PO disimpan; tampilkan tabel stok tersedia vs kebutuhan vs selisih        |
| 🔄 Manajemen Status Produksi | 6 status terstruktur dengan audit trail lengkap; rollback stok otomatis jika PO dibatalkan              |
| 📄 Cetak Dokumen PO ke PDF   | Generate PDF dokumen PO resmi dalam ≤ 5 detik; link berbagi sementara valid 24 jam                      |
| 👥 Manajemen Pelanggan       | CRUD data master pelanggan dengan autocomplete saat pembuatan PO                                        |
| 🔔 Notifikasi Email          | Kirim email ke pemilik UMKM setiap PO baru masuk beserta link langsung ke detail PO                     |
| 📊 Dashboard PO              | Ringkasan total PO aktif, menunggu konfirmasi, dan siap kirim secara real-time                          |
| 📁 Arsip PO                  | Akses arsip PO yang telah selesai atau dibatalkan untuk Administrator dan Pemilik UMKM                  |

---

## 🔄 Alur Status Produksi

```
Menunggu Konfirmasi → Dikonfirmasi → Dalam Produksi → Siap Kirim → Selesai
                                                    ↘
                                                  Dibatalkan
```

- Pengurangan stok bahan baku dilakukan **satu kali** saat status → `Dalam Produksi`
- PO berstatus `Selesai` atau `Dibatalkan` bersifat **immutable** (tidak dapat diedit/dihapus)
- Rollback stok otomatis jika PO dibatalkan setelah `Dalam Produksi`

---

## 🛠️ Teknologi

### Backend

- **Laravel 10.x** — Framework PHP untuk web development
- **MySQL** — Database utama
- **Blade Templating** — Template engine untuk UI

### Frontend

- **HTML / CSS / JavaScript** — Standard web technologies
- **Bootstrap** — CSS framework untuk responsive design

### Libraries Tambahan

- **DOMPDF** — Library untuk generate PDF dokumen PO
- **Carbon** — Library untuk manipulasi tanggal dan waktu

### DevOps & Hosting

- **XAMPP** — Local development environment
- **Git/GitHub** — Version control dan repository hosting

---

## 👥 Kelas Pengguna & Hak Akses

| Peran           | Deskripsi singkat             | Hak Akses (ringkas)                                                                                                                                    |
| --------------- | ----------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------ |
| Staf Penjualan  | Menerima dan mencatat pesanan | Buat PO, edit PO sebelum konfirmasi, lihat/print PO. Tidak boleh mengonfirmasi atau mengubah status produksi.                                          |
| Pemilik UMKM    | Pemilik / manajer bisnis      | Akses penuh untuk konfirmasi PO, ubah status produksi, batalkan PO, dan lihat laporan.                                                                 |
| Operator Gudang | Petugas gudang / produksi     | Lihat PO aktif, verifikasi bahan, tambah/kurangi stok, mulai produksi (ubah status ke "Dalam Produksi"). Tidak mengelola user atau konfigurasi sistem. |
| Administrator   | Admin sistem                  | Akses penuh: manajemen user, konfigurasi, arsip, dan semua aksi operasional.                                                                           |

RBAC diimplementasikan melalui `PesananPolicy` dan middleware role-based; sesuaikan kebijakan di `app/Policies` bila diperlukan.

---

## 📁 Struktur Repository

```
hutch_id_Website_OrderFlow/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php
│   │   ├── DashboardController.php
│   │   ├── PesananController.php
│   │   ├── PelangganController.php
│   │   └── ArsipController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Pesanan.php
│   │   ├── DetailPesanan.php
│   │   ├── Pelanggan.php
│   │   └── HistoriStatus.php
│   ├── Policies/
│   │   └── PesananPolicy.php
│   └── Middleware/
│       └── CheckRole.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
├── routes/
│   ├── web.php
│   └── api.php
├── public/
├── config/
├── storage/
├── tests/
├── composer.json
├── package.json
├── vite.config.js
└── README.md
```

---

## 🚀 Cara Menjalankan (Local Development)

### Prasyarat

- Git terinstall
- PHP 8.1+ & Composer terinstall
- MySQL terinstall
- Node.js & npm terinstall (untuk Vite)

### Langkah

**1. Clone repository**

```bash
git clone https://github.com/byedriand/hutch_id_Kelompok2.git
cd hutch_id_Kelompok2
```

**2. Install dependensi PHP**

```bash
composer install
```

**3. Install dependensi Node.js**

```bash
npm install
```

**4. Salin file environment**

```bash
cp .env.example .env
```

**5. Generate application key**

```bash
php artisan key:generate
```

**6. Konfigurasi database**
Edit file `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hutch_id_db
DB_USERNAME=root
DB_PASSWORD=
```

**7. Jalankan migrasi & seeder**

```bash
php artisan migrate --seed
```

**8. Build assets**

```bash
npm run build
# atau untuk development
npm run dev
```

**9. Jalankan server**

```bash
php artisan serve
```

**10. Akses aplikasi**

```
Web: http://localhost:8000
```

---

## 🐳 Menjalankan dengan Docker

Untuk menjalankan aplikasi menggunakan Docker Compose (direkomendasikan untuk pengujian cepat atau lingkungan terisolasi):

1. Pastikan Docker & Docker Compose terinstall.
2. Salin file `.env.example` menjadi `.env` dan sesuaikan variabel bila perlu.

Jalankan:

```bash
docker compose up --build -d
```

Service utama akan berjalan (mis. web server dan database). Akses aplikasi di:

```
http://localhost:8080
```

Catatan:

- Jika Anda ingin menggunakan port lain atau mengatur variable DB, edit `docker-compose.yml` atau `.env` sebelum `up`.
- Untuk melihat log container gunakan `docker compose logs -f`.

## 🔑 Akun Default (Seeder)

| Role            | Email               | Password |
| --------------- | ------------------- | -------- |
| Administrator   | admin@hutch.id   | password123 |
| Pemilik UMKM    | pemilik@hutch.id | password123 |
| Staf Penjualan  | staf@hutch.id    | password123 |
| Operator Gudang | gudang@hutch.id  | password123 |

---

## 🛣️ Web Routes

Base URL: `/`  
Authentication: Laravel session-based

| Method              | Route                  | Deskripsi              | Middleware               |
| ------------------- | ---------------------- | ---------------------- | ------------------------ |
| GET                 | `/dashboard`           | Dashboard utama        | auth                     |
| GET/POST            | `/pesanan`             | List / buat PO baru    | auth, role               |
| GET                 | `/pesanan/{id}`        | Detail PO              | auth, role               |
| PUT                 | `/pesanan/{id}`        | Update PO              | auth, role               |
| DELETE              | `/pesanan/{id}`        | Batalkan PO            | auth, role               |
| PATCH               | `/pesanan/{id}/status` | Update status produksi | auth, role               |
| GET                 | `/pesanan/{id}/pdf`    | Download PDF PO        | auth, role               |
| POST                | `/pesanan/{id}/share`  | Generate link berbagi  | auth, role               |
| GET/POST/PUT/DELETE | `/pelanggan`           | CRUD pelanggan         | auth, role               |
| GET                 | `/arsip`               | Arsip PO               | auth, role               |
| GET                 | `/admin/dashboard`     | Admin dashboard        | auth, role:administrator |

---

## 📊 Spesifikasi Dokumen PO PDF

Setiap dokumen PO yang di-generate memuat 8 elemen wajib:

| No  | Elemen            | Konten                                                     |
| --- | ----------------- | ---------------------------------------------------------- |
| 1   | Header Perusahaan | Logo hutch.id, nama, alamat, telepon, email                |
| 2   | Informasi PO      | Nomor PO, tanggal pesanan, tanggal pengiriman, status      |
| 3   | Data Pelanggan    | Nama, alamat lengkap, telepon, email                       |
| 4   | Tabel Produk      | No · Nama produk · Spesifikasi · Jumlah · Harga · Subtotal |
| 5   | Ringkasan Biaya   | Subtotal, PPN (jika berlaku), Total Nilai PO               |
| 6   | Catatan Khusus    | Instruksi produksi / permintaan spesifik pelanggan         |
| 7   | Tanda Tangan      | Dibuat oleh (Staf Penjualan) & Disetujui oleh (Pemilik)    |
| 8   | Footer            | Nomor halaman, tanggal cetak, keterangan validitas         |

> Format nama file: `PO-[NomorPO]-[NamaPelanggan].pdf`  
> Contoh: `PO-20260413-001-BudiBagStore.pdf`

---

## 🗄️ Desain Basis Data

| Tabel               | Deskripsi                                        |
| ------------------- | ------------------------------------------------ |
| `users`             | Data pengguna dengan role RBAC                   |
| `pesanan`           | Master seluruh PO — nomor_po bersifat UNIQUE     |
| `detail_pesanan`    | Item produk per PO; harga dikunci saat PO dibuat |
| `pelanggan`         | Master data pelanggan mitra                      |
| `histori_status_po` | Audit trail setiap perubahan status PO           |

---

## ⚡ Persyaratan Kinerja

| Kode           | Persyaratan                              | Target                              |
| -------------- | ---------------------------------------- | ----------------------------------- |
| REQ-NFR-PO-001 | Waktu simpan PO baru ke database         | ≤ 2 detik                           |
| REQ-NFR-PO-002 | Waktu verifikasi ketersediaan bahan baku | ≤ 3 detik                           |
| REQ-NFR-PO-003 | Waktu generate dan unduh dokumen PDF     | ≤ 5 detik                           |
| REQ-NFR-PO-004 | Jumlah pengguna konkuren                 | Minimal 10 pengguna tanpa degradasi |

---

## 🔒 Keamanan

- **Role-Based Access Control (RBAC)** dengan 4 level pengguna
- Hanya **Staf Penjualan, Pemilik UMKM, atau Administrator** yang dapat membuat PO baru
- Hanya **Pemilik UMKM atau Administrator** yang dapat mengonfirmasi atau membatalkan PO
- Link berbagi PDF menggunakan **token acak** dan kedaluwarsa dalam 24 jam
- Seluruh data pelanggan dikelola sesuai **UU PDP No. 27 Tahun 2022**
- Autentikasi menggunakan **Laravel Sanctum** untuk session management

---

## 📋 Referensi Dokumen

- IEEE Std 830-1998 — IEEE Recommended Practice for Software Requirements Specifications
- UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)
- SAK EMKM — IAI 2018
- Peraturan Pemerintah RI No. 7 Tahun 2021 tentang UMKM

---

## 👥 Tim Pengembang

| Nama                   | NPM         | Role                                   |
| ---------------------- | ----------- | -------------------------------------- |
| Nayla Rabia Gustari    | 20241320034 | Project Manager                        |
| Adrian Ronald Daga     | 20241320011 | Frontend/Backend Developer . (Website) |
| Muhamad Alvin Ramadhan | 20241320035 | Frontend Developer · (Mobile)          |
| Sopyan Rinaldhi        | 20241320028 | Backend Developer · (Mobile)           |
| Eka Febryanto          | 20241320014 | Qa Tester (Website)                              |
| Julia Habibah          | 20241320020 | Sistem Analyst                         |
| Akbar                  | 20241320017 | Qa Tester (Mobile)                            |

---

## 🏫 Informasi Akademik

|               |                                      |
| ------------- | ------------------------------------ |
| Mata Kuliah   | Rekayasa Sistem Informasi            |
| Program Studi | Sistem Informasi                     |
| Universitas   | Kebangsaan Republik Indonesia (UKRI) |
| Kelas         | A1 — Kelompok 2                      |
| Versi SRS     | 1.2 — Disetujui (13 April 2026)      |
| Tahun         | 2026                                 |

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik. Seluruh hak cipta milik Kelompok 2 — Program Studi Sistem Informasi UKRI 2026.

---

<div align="center">

Made with ❤️ by **Kelompok 2 — UKRI 2026**

_Hutch.id · Custom Production for Businesses, Ready Bags for Everyone_

</div>
