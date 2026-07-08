<div align="center">

<img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" />
<img src="https://img.shields.io/badge/Flutter-02569B?style=for-the-badge&logo=flutter&logoColor=white" />
<img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" />
<img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" />
<img src="https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white" />
<img src="https://img.shields.io/badge/Dart-0175C2?style=for-the-badge&logo=dart&logoColor=white" />

<br/><br/>

# 👜 Hutch.id — OrderFlow System

### Sistem Manajemen Pesanan dan Produksi Internal Hutch.id
### Website + Aplikasi Mobile

_Tugas Besar Rekayasa Sistem Informasi — Kelas A1 Kelompok 2_  
_Program Studi Sistem Informasi — Universitas Kebangsaan Republik Indonesia (UKRI) 2026_

</div>

---

## 🖼️ Tampilan Aplikasi

### 🌐 Website — Landing Page

<!-- Upload screenshot website ke docs/screenshots/website-landing.png lalu hapus komentar ini -->
<!-- ![Landing Page Website](docs/screenshots/website-landing.png) -->

> **Cara menambahkan screenshot:**
> 1. Buat folder `docs/screenshots/` di root repository
> 2. Upload screenshot landing page website → `docs/screenshots/website-landing.png`
> 3. Upload screenshot landing page mobile → `docs/screenshots/mobile-landing.png`
> 4. Hapus blok komentar di atas dan ganti dengan tag `![...](...)`

### 📱 Mobile — Landing Page

<!-- ![Landing Page Mobile](docs/screenshots/mobile-landing.png) -->

---

## 📌 Tentang Proyek

**Hutch.id OrderFlow** adalah sistem manajemen pesanan terintegrasi yang terdiri dari **dua platform**:

| Platform | Teknologi | Fungsi |
|---|---|---|
| 🌐 **Website** | Laravel 10 + Bootstrap | Panel manajemen untuk admin, staf, dan operator |
| 📱 **Mobile App** | Flutter (Android) | Akses monitoring & operasional dari smartphone |

Sistem ini mengelola seluruh siklus pesanan pelanggan **hutch.id** — produsen tas konveksi dan brand lokal yang melayani custom production untuk bisnis maupun ready bags untuk umum.

---

## ✨ Fitur Utama

### 🌐 Website (Laravel)

| Fitur | Deskripsi |
|---|---|
| 📋 Penerimaan Order | Buat PO baru dengan nomor auto-generate format `PO-YYYYMMDD-XXX`, multi-item, harga dikunci saat simpan |
| 🔍 Cek Bahan Baku | Verifikasi stok otomatis saat PO disimpan; tampilkan tabel stok tersedia vs kebutuhan vs selisih |
| 🔄 Manajemen Status Produksi | 6 status terstruktur dengan audit trail lengkap; rollback stok otomatis jika PO dibatalkan |
| 📄 Cetak Dokumen PO ke PDF | Generate PDF dokumen PO resmi dalam ≤ 5 detik; link berbagi sementara valid 24 jam |
| 👥 Manajemen Pelanggan | CRUD data master pelanggan dengan autocomplete saat pembuatan PO |
| ✅ Tambah Produk (Staff) | Interface khusus staf penjualan untuk menambah produk baru dengan upload foto & preview |
| 🔔 Notifikasi Real-time | Notifikasi ke semua role saat ada PO baru, perubahan status, atau stok menipis |
| 📊 Dashboard Analitik | Ringkasan total PO aktif, menunggu konfirmasi, dan siap kirim secara real-time |
| 📁 Arsip PO | Akses arsip PO selesai/dibatalkan untuk Administrator  Hutch.id |
| 🔐 RBAC 4 Level | Role-based access control ketat untuk setiap fitur dan halaman |

### 📱 Mobile App (Flutter)

| Fitur | Deskripsi |
|---|---|
| 🏠 Dashboard Mobile | Ringkasan PO aktif, status produksi, dan statistik real-time |
| 📦 Manajemen Pesanan | Lihat, buat, dan update status PO langsung dari smartphone |
| 👤 Manajemen Pelanggan | CRUD data pelanggan dengan pencarian cepat |
| 🏪 Inventori & Stok | Monitoring stok bahan baku dan produk secara real-time |
| 🔔 Notifikasi Push | Notifikasi pesanan baru dan perubahan status produksi |
| 🤖 Asisten AI (Chatbot) | Terintegrasi workflow N8N untuk proses otomatis dan pencarian informasi |
| 📂 Arsip Digital | Akses arsip PO dan dokumen PDF dari mobile |
| 🔑 Multi-role Login | Login dengan 4 role berbeda; tampilan disesuaikan per peran |
| 📥 Unduh APK | APK tersedia langsung dari landing page website |

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

### 🌐 Website

| Layer | Teknologi |
|---|---|
| Backend | Laravel 10.x, PHP 8.1+ |
| Frontend | Blade Templating, Bootstrap, HTML/CSS/JS |
| Database | MySQL |
| PDF | DOMPDF |
| Auth | Laravel Sanctum |
| DevOps | Docker, Docker Compose |

### 📱 Mobile

| Layer | Teknologi |
|---|---|
| Framework | Flutter (Dart) |
| State Management | Provider |
| Storage Lokal | SharedPreferences, SQLite (sqflite) |
| Auth | Token-based (Laravel Sanctum API) |
| Build | Android APK (Release) |

---

## 👥 Kelas Pengguna & Hak Akses

| Peran | Deskripsi | Hak Akses (ringkas) |
|---|---|---|
| Staf Penjualan | Menerima dan mencatat pesanan | Buat PO, edit PO sebelum konfirmasi, lihat/print PO, tambah produk baru |
| Operator Gudang | Petugas gudang / produksi | Lihat PO aktif, verifikasi bahan, tambah/kurangi stok, mulai produksi |
| Administrator | Admin sistem | Akses penuh: manajemen user, konfigurasi, arsip, dan semua aksi operasional |

---

## 📁 Struktur Repository

```
hutch_id_Kelompok2/
├── hutch_id_Website_OrderFlow/        # 🌐 Project Laravel (Website)
│   ├── app/
│   │   ├── Http/Controllers/
│   │   ├── Models/
│   │   ├── Policies/
│   │   └── Middleware/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── resources/views/
│   ├── routes/
│   │   ├── web.php
│   │   └── api.php
│   ├── public/
│   │   └── downloads/
│   │       └── Hutch-mobile.apk       # APK hasil build Flutter
│   ├── docker-compose.yml
│   ├── .env.example
│   └── .env.production
│
├── hutch_id_mobile_orderflow/         # 📱 Project Flutter (Mobile)
│   ├── lib/
│   │   ├── config/
│   │   │   └── app_config.dart        # Konfigurasi URL API
│   │   ├── models/
│   │   ├── providers/
│   │   └── screens/
│   │       ├── landing/
│   │       ├── auth/
│   │       ├── home/
│   │       ├── pesanan/
│   │       ├── pelanggan/
│   │       ├── gudang/
│   │       ├── notifikasi/
│   │       ├── arsip/
│   │       └── chatbot/
│   ├── android/
│   └── pubspec.yaml
│
└── docs/
    └── screenshots/                   # Screenshot untuk README
        ├── website-landing.png
        └── mobile-landing.png
```

---

## 🚀 Cara Menjalankan

### 🌐 Website (Docker — Direkomendasikan)

**Prasyarat:** Docker & Docker Compose terinstall

```bash
# 1. Clone repository
git clone https://github.com/byedriand/hutch_id_Kelompok2.git
cd hutch_id_Kelompok2/hutch_id_Website_OrderFlow

# 2. Salin file environment
cp .env.example .env

# 3. Jalankan dengan Docker
docker compose up --build -d

# 4. Generate app key & setup database
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

Akses aplikasi di: **`http://localhost:8082`**

---

### 🖥️ Website (XAMPP / Lokal)

```bash
cd hutch_id_Website_OrderFlow

composer install
npm install
cp .env.example .env

# Edit .env sesuaikan DB_HOST, DB_PORT, DB_DATABASE, dll

php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Akses aplikasi di: **`http://localhost:8000`**

---

### 📱 Mobile (Flutter)

**Prasyarat:** Flutter SDK & Android SDK terinstall

```bash
cd hutch_id_mobile_orderflow

# Sesuaikan URL API di lib/config/app_config.dart:
# Lokal  → 'http://localhost:8082/api'
# Hosting → 'https://domain-kamu.com/api'

flutter pub get

# Jalankan di emulator (development)
flutter run

# Build APK release (distribusi)
flutter build apk --release
# Output: build/app/outputs/flutter-apk/app-release.apk
```

---

## 🔑 Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Administrator | admin@hutch.id | password123 |
| Staf Penjualan | staf@hutch.id | password123 |
| Operator Gudang | gudang@hutch.id | password123 |

---

## 🛣️ API Routes (Mobile ↔ Website)

Base URL: `/api` | Auth: Bearer Token (Laravel Sanctum)

| Method | Route | Deskripsi | Auth |
|---|---|---|---|
| POST | `/login` | Login & dapat token | - |
| POST | `/logout` | Logout & hapus token | ✅ |
| GET | `/dashboard` | Data ringkasan dashboard | ✅ |
| GET/POST | `/pesanan` | List & buat PO baru | ✅ |
| GET/PUT | `/pesanan/{id}` | Detail & update PO | ✅ |
| PATCH | `/pesanan/{id}/status` | Update status produksi | ✅ |
| GET | `/pesanan/{id}/pdf` | Download PDF PO | ✅ |
| GET/POST/PUT/DELETE | `/pelanggan` | CRUD data pelanggan | ✅ |
| GET/POST/PUT/DELETE | `/produk` | CRUD data produk | ✅ |
| GET | `/notifikasi` | List notifikasi user | ✅ |
| GET | `/arsip` | Arsip PO selesai/batal | ✅ |
| GET | `/user` | Data profil user login | ✅ |

---

## 🗄️ Desain Basis Data

| Tabel | Deskripsi |
|---|---|
| `users` | Data pengguna dengan role RBAC |
| `pesanan` | Master seluruh PO — `nomor_po` bersifat UNIQUE |
| `detail_pesanan` | Item produk per PO; harga dikunci saat PO dibuat |
| `pelanggan` | Master data pelanggan mitra |
| `produk` | Data produk beserta stok dan foto |
| `histori_status_po` | Audit trail setiap perubahan status PO |
| `notifikasi` | Notifikasi per role/user |

---

## 🔒 Keamanan

- **Role-Based Access Control (RBAC)** dengan 4 level pengguna di website dan mobile
- Autentikasi API menggunakan **Laravel Sanctum** (Bearer Token)
- Link berbagi PDF menggunakan **token acak** dan kedaluwarsa dalam 24 jam
- Seluruh data pelanggan dikelola sesuai **UU PDP No. 27 Tahun 2022**
- APK mobile hanya tersedia via unduhan langsung dari website resmi hutch.id

---

## 🔔 Perkembangan Terbaru

### Versi 1.4 — Juli 2026 *(Current)*

- ✅ **Aplikasi Mobile Flutter** selesai dikembangkan dan siap distribusi
- ✅ **APK Release** (`Hutch-mobile.apk`) tersedia untuk diunduh dari landing page website
- ✅ **Landing Page Mobile** dengan 4 pilar keunggulan sistem, fitur unggulan, tim, dan info aplikasi
- ✅ **Navbar Mobile** diperbaiki — judul HUTCH PRESTIGE kini tetap (pinned) saat scroll
- ✅ **Card 4 Pilar** diperbaiki — teks tidak lagi terpotong, tampil penuh dan rapi
- ✅ **Integrasi API** website ↔ mobile menggunakan Laravel Sanctum
- ✅ **Asisten AI/Chatbot** terintegrasi workflow N8N
- ✅ **Persiapan hosting** — `.env.production` siap, struktur file lengkap untuk deployment

### Versi 1.3 — Juni 2026

- ✅ Menu "Tambah Produk" khusus role `staf_penjualan`
- ✅ Grid display produk responsive dengan upload foto & preview
- ✅ Notifikasi otomatis ke semua role saat produk baru ditambahkan
- ✅ Fix image display APP_URL mismatch (port 8000 vs 8082)

### Versi 1.2 — Sebelumnya

- ✅ Notifikasi `stok_kurang` menyimpan detail kekurangan per produk
- ✅ Auto-update notifikasi saat stok ditambahkan
- ✅ Form edit stok: Tambahkan Stok & Kurangi Stok
- ✅ Dashboard menampilkan indikator "Kurang" dengan jumlah unit

---

## 👥 Tim Pengembang

| Nama | NPM | Role |
|---|---|---|
| Nayla Rabia Gustari | 20241320034 | Project Manager |
| Adrian Ronald Daga | 20241320011 | ) |
| Muhamad Alvin Ramadhan | 20241320035 | Frontend  |
| Sopyan Rinaldhi | 20241320028 | QA Tester (Mobile) |
| Eka Febryanto | 20241320014 | QA Tester (Website) |
| Julia Habibah | 20241320020 | Sistem Analis |
| Akbar | 20241320017 | QA Tester (Mobile) |

---

## 🏫 Informasi Akademik

| | |
|---|---|
| Mata Kuliah | Rekayasa Sistem Informasi |
| Program Studi | Sistem Informasi |
| Universitas | Kebangsaan Republik Indonesia (UKRI) |
| Kelas | A1 — Kelompok 2 |
| Tahun | 2026 |

---

## 📄 Lisensi

Proyek ini dibuat untuk keperluan akademik. Seluruh hak cipta milik Kelompok 2 — Program Studi Sistem Informasi UKRI 2026.

---

<div align="center">


_Hutch.id · Custom Production for Businesses, Ready Bags for Everyone_

🌐 Website · 📱 Mobile App

</div>
