<div align="center">

<img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
<img src="https://img.shields.io/badge/Flutter-02569B?style=for-the-badge&logo=flutter&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/Vercel-000000?style=for-the-badge&logo=vercel&logoColor=white" />
<img src="https://img.shields.io/badge/JWT-000000?style=for-the-badge&logo=jsonwebtokens&logoColor=white" />

# 👜 Hutch.id — OrderFlow
### Sistem Manajemen Pesanan dan Produksi UMKM

*Tugas Besar Rekayasa Sistem Informasi — Kelas-A1 Kelompok 2*  
*Program Studi Sistem Informasi — Universitas Kebangsaan Republik Indonesia (UKRI) 2026*

</div>

---

## 📌 Tentang Proyek

**OrderFlow** adalah sistem manajemen pesanan berbasis web dan mobile yang dirancang khusus untuk mendukung operasional **hutch.id** — produsen tas konveksi dan brand lokal yang melayani custom production untuk bisnis maupun ready bags untuk umum.

Sistem ini mengelola seluruh siklus pesanan pelanggan, mulai dari:

- Penerimaan dan pencatatan **Purchase Order (PO)** dari pelanggan
- Verifikasi otomatis **ketersediaan bahan baku** berdasarkan BOM produk
- Pelacakan **status produksi** dari konfirmasi hingga selesai
- Penerbitan **dokumen PO resmi** dalam format PDF yang dapat dibagikan
- Notifikasi email otomatis ke pemilik UMKM setiap ada pesanan baru

> Sistem ini dikembangkan berdasarkan kebutuhan operasional **hutch.id** — Bag Manufacturing & In-House Brand yang bergerak di bidang konveksi tas UMKM.

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|---|---|
| 📋 Penerimaan Order | Buat PO baru dengan nomor auto-generate format `PO-YYYYMMDD-XXX`, multi-item, harga dikunci saat simpan |
| 🔍 Cek Bahan Baku | Verifikasi stok otomatis saat PO disimpan; tampilkan tabel stok tersedia vs kebutuhan vs selisih |
| 🔄 Manajemen Status Produksi | 6 status terstruktur dengan audit trail lengkap; rollback stok otomatis jika PO dibatalkan |
| 📄 Cetak Dokumen PO ke PDF | Generate PDF dokumen PO resmi dalam ≤ 5 detik; link berbagi sementara valid 24 jam |
| 👥 Manajemen Pelanggan | CRUD data master pelanggan dengan autocomplete saat pembuatan PO |
| 🔔 Notifikasi Email | Kirim email ke pemilik UMKM setiap PO baru masuk beserta link langsung ke detail PO |
| 📊 Dashboard PO | Ringkasan total PO aktif, menunggu konfirmasi, dan siap kirim secara real-time |
| 📱 Mobile App | Aplikasi Flutter untuk akses pesanan dan pembaruan status dari lapangan |

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
- **Laravel** — REST API (tidak render HTML Blade)
- **MySQL** — Database utama
- **JWT (JSON Web Token)** — Autentikasi berbasis sesi

### Frontend Web
- **HTML / CSS / JavaScript** (Blade Laravel)

### Mobile
- **Flutter / Dart** — Android
- **Dio** — HTTP client

### DevOps & Hosting
- **Vercel** — Hosting aplikasi
- **HTTPS / TLS 1.2+** — Seluruh komunikasi client-server

---

## 👥 Kelas Pengguna & Hak Akses

| Peran | Deskripsi | Hak Akses |
|---|---|---|
| Staf Penjualan | Menerima dan mencatat pesanan | Buat PO, lihat daftar PO, cetak PO PDF |
| Pemilik UMKM | Memantau dan mengelola seluruh pesanan | Full access: konfirmasi, ubah status, laporan, cetak |
| Operator Gudang | Memproses bahan baku untuk produksi | Lihat PO aktif, perbarui status produksi |
| Administrator | Konfigurasi sistem dan data master | Full access + konfigurasi sistem |

---

## 📁 Struktur Repository

```
hutch_id/
├── backend/                      # Laravel REST API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   ├── Models/
│   │   └── Services/
│   ├── routes/api.php
│   └── .env.example
├── mobile/                       # Flutter Android
│   └── lib/
├── dokumen/                      # SRS & dokumentasi
│   ├── srs_hutch_id_Kel2_REAL.docx
│   └── notulensi/
└── README.md
```

---

## 🚀 Cara Menjalankan (Local Development)

### Prasyarat
- Git terinstall
- PHP & Composer terinstall
- MySQL terinstall
- Flutter SDK terinstall (untuk mobile)

### Langkah

**1. Clone repository**
```bash
git clone https://github.com/byedriand/hutch_id.git
cd hutch_id
```

**2. Salin file environment**
```bash
cp backend/.env.example backend/.env
```

**3. Sesuaikan konfigurasi `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hutch_id_db
DB_USERNAME=root
DB_PASSWORD=secret
JWT_SECRET=your_jwt_secret
```

**4. Install dependensi & jalankan migrasi**
```bash
cd backend
composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

**5. Akses aplikasi**
```
Web     : http://localhost:8000
API     : http://localhost:8000/api
```

---

## 🔑 Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Administrator | admin@hutchid.com | password |
| Pemilik UMKM | pemilik@hutchid.com | password |
| Staf Penjualan | staf@hutchid.com | password |
| Operator Gudang | gudang@hutchid.com | password |

---

## 📡 API Endpoints

Base URL: `/api`  
Auth: `Authorization: Bearer {token}`

| Method | Endpoint | Deskripsi |
|---|---|---|
| POST | `/api/auth/login` | Login, return JWT token |
| POST | `/api/auth/logout` | Logout, invalidate token |
| GET | `/api/dashboard` | Data ringkasan dashboard PO |
| GET/POST | `/api/pelanggan` | List / tambah pelanggan |
| GET/POST | `/api/pesanan` | List / buat PO baru |
| GET | `/api/pesanan/{id}` | Detail satu PO |
| PUT | `/api/pesanan/{id}/status` | Update status produksi PO |
| DELETE | `/api/pesanan/{id}` | Batalkan PO (jika belum Selesai) |
| GET | `/api/pesanan/{id}/cek-bahan` | Verifikasi ketersediaan bahan baku |
| GET | `/api/pesanan/{id}/pdf` | Generate & unduh dokumen PO PDF |
| POST | `/api/pesanan/{id}/bagikan` | Buat link berbagi PDF sementara (24 jam) |
| GET/PUT | `/api/pelanggan/{id}` | Detail / update data pelanggan |

---

## 📊 Spesifikasi Dokumen PO PDF

Setiap dokumen PO yang di-generate memuat 8 elemen wajib:

| No | Elemen | Konten |
|---|---|---|
| 1 | Header Perusahaan | Logo hutch.id, nama, alamat, telepon, email |
| 2 | Informasi PO | Nomor PO, tanggal pesanan, tanggal pengiriman, status |
| 3 | Data Pelanggan | Nama, alamat lengkap, telepon, email |
| 4 | Tabel Produk | No · Nama produk · Spesifikasi · Jumlah · Harga · Subtotal |
| 5 | Ringkasan Biaya | Subtotal, PPN (jika berlaku), Total Nilai PO |
| 6 | Catatan Khusus | Instruksi produksi / permintaan spesifik pelanggan |
| 7 | Tanda Tangan | Dibuat oleh (Staf Penjualan) & Disetujui oleh (Pemilik) |
| 8 | Footer | Nomor halaman, tanggal cetak, keterangan validitas |

> Format nama file: `PO-[NomorPO]-[NamaPelanggan].pdf`  
> Contoh: `PO-20260413-001-BudiBagStore.pdf`

---

## 🗄️ Desain Basis Data

| Tabel | Deskripsi |
|---|---|
| `pesanan` | Master seluruh PO — nomor_po bersifat UNIQUE |
| `detail_pesanan` | Item produk per PO; harga dikunci saat PO dibuat |
| `pelanggan` | Master data pelanggan mitra |
| `histori_status_po` | Audit trail setiap perubahan status PO |
| `pdf_token_po` | Token link berbagi PDF (64 karakter, UNIQUE, kedaluwarsa 24 jam) |

---

## ⚡ Persyaratan Kinerja

| Kode | Persyaratan | Target |
|---|---|---|
| REQ-NFR-PO-001 | Waktu simpan PO baru ke database | ≤ 2 detik |
| REQ-NFR-PO-002 | Waktu verifikasi ketersediaan bahan baku | ≤ 3 detik |
| REQ-NFR-PO-003 | Waktu generate dan unduh dokumen PDF | ≤ 5 detik |
| REQ-NFR-PO-004 | Jumlah pengguna konkuren | Minimal 10 pengguna tanpa degradasi |

---

## 🔒 Keamanan

- Hanya **Staf Penjualan, Pemilik UMKM, atau Administrator** yang dapat membuat PO baru
- Hanya **Pemilik UMKM atau Administrator** yang dapat mengonfirmasi atau membatalkan PO
- Link berbagi PDF menggunakan **token acak minimal 64 karakter** dan kedaluwarsa dalam 24 jam
- Seluruh data pelanggan dikelola sesuai **UU PDP No. 27 Tahun 2022**
- Seluruh komunikasi menggunakan **HTTPS / TLS 1.2+**

---

## 📋 Referensi Dokumen

- IEEE Std 830-1998 — IEEE Recommended Practice for Software Requirements Specifications
- UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)
- SAK EMKM — IAI 2018
- Peraturan Pemerintah RI No. 7 Tahun 2021 tentang UMKM

---

## 👥 Tim Pengembang

| Nama | NPM | Role |
|---|---|---|
| Nayla Rabia Gustari | 20241320034 | Project Manager · Functional Analyst |
| Adrian Ronald Daga | 20241320011 | Backend Developer · DevOps |
| Muhamad Alvin Ramadhan | 20241320035 | Frontend Developer · UI Designer |
| Akbar | 20241320017 | Database Designer · ERD |
| Eka Febryanto | 20241320014 | Mobile Developer (Flutter) |
| Sopyan Rinaldhi | 20241320028 | Use Case · Activity Diagram |
| Julia Habibah | 20241320020 | Documentation & Secretary |

---

## 🏫 Informasi Akademik

| | |
|---|---|
| Mata Kuliah | Rekayasa Sistem Informasi |
| Program Studi | Sistem Informasi |
| Universitas | Kebangsaan Republik Indonesia (UKRI) |
| Kelas | A1 — Kelompok 2 |
| Versi SRS | 1.2 — Disetujui (13 April 2026) |
| Tahun | 2026 |

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik. Seluruh hak cipta milik Kelompok 2 — Program Studi Sistem Informasi UKRI 2026.

---

<div align="center">

Made with ❤️ by **Kelompok 2 — UKRI 2026**

*Hutch.id · Custom Production for Businesses, Ready Bags for Everyone*

</div>
