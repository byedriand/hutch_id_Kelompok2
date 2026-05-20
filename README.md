<div align="center">

<img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" />
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

| Peran           | Deskripsi                              | Hak Akses                                            |
| --------------- | -------------------------------------- | ---------------------------------------------------- |
| Staf Penjualan  | Menerima dan mencatat pesanan          | Buat PO, lihat daftar PO, cetak PO PDF               |
| Pemilik UMKM    | Memantau dan mengelola seluruh pesanan | Full access: konfirmasi, ubah status, laporan, cetak |
| Operator Gudang | Memproses bahan baku untuk produksi    | Lihat PO aktif, perbarui status produksi             |
| Administrator   | Konfigurasi sistem dan data master     | Full access + konfigurasi sistem, akses arsip        |

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

## 🔑 Akun Default (Seeder)

| Role            | Email               | Password |
| --------------- | ------------------- | -------- |
| Administrator   | admin@hutchid.com   | password |
| Pemilik UMKM    | pemilik@hutchid.com | password |
| Staf Penjualan  | staf@hutchid.com    | password |
| Operator Gudang | gudang@hutchid.com  | password |

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
| Eka Febryanto          | 20241320014 | Qa Tester                              |
| Julia Habibah          | 20241320020 | Sistem Analyst                         |
| Akbar                  | 20241320017 | Dokumentasi                            |

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
