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

<br/>

[![🌐 Live Demo](https://img.shields.io/badge/🌐_Live_Demo-hutch--prestige.my.id-02569B?style=for-the-badge)](https://hutch-prestige.my.id)

</div>

---

### 🌐 Website — Landing Page

<div align="center">
  <img src="docs/screenshots/website-landing.png" width="80%" />
</div>

### 📱 Mobile — Landing Page

<div align="center">
  <img src="docs/screenshots/mobile-landing.png" width="40%" />
</div>

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
| Staf Penjualan | Menerima dan mencatat pesanan | Buat PO, lihat/print PO, tambah produk baru (via menu khusus), kelola pelanggan |
| Operator Gudang | Petugas gudang / produksi | Lihat PO aktif, tambah/kurangi stok, update status produksi |
| Administrator | Admin sistem | Akses penuh: manajemen user, konfirmasi PO, edit PO, batalkan PO, arsip, dan semua aksi operasional |

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

## 📖 Cara Penggunaan

---

### 🌐 Website — Panduan Lengkap

#### 1️⃣ Akses & Login

1. Buka browser (Chrome / Firefox / Edge) dan kunjungi **[https://hutch-prestige.my.id](https://hutch-prestige.my.id)**
2. Anda akan disambut oleh **Landing Page** — berisi profil produk, fitur unggulan, dan tombol unduh APK mobile
3. Klik tombol **"Masuk ke Sistem"** atau **"Login"** di pojok kanan atas
4. Masukkan **email** dan **password** sesuai role Anda
5. Klik **"Login"** — sistem akan otomatis mengarahkan ke **dashboard** sesuai peran

> 💡 Setiap role memiliki tampilan dan menu yang berbeda. Lihat tabel akun default di bagian bawah.

---

#### 2️⃣ Panduan Per Role — Website

##### 📋 Staf Penjualan

<details>
<summary><b>Klik untuk expand — Panduan Staf Penjualan</b></summary>

**A. Membuat Purchase Order (PO) Baru**
| Langkah | Aksi |
|---|---|
| 1 | Login sebagai Staf Penjualan |
| 2 | Di dashboard, klik **"Buat PO Baru"** atau buka menu **Pesanan → Buat PO** |
| 3 | Pilih pelanggan dari dropdown (ketik nama untuk autocomplete). Jika pelanggan belum terdaftar, klik **"Tambah Pelanggan Baru"** |
| 4 | Isi **Tanggal PO** dan **Catatan** (opsional) |
| 5 | Klik **"Tambah Item"** — pilih produk, isi **jumlah** dan **harga satuan** |
| 6 | Ulangi langkah 5 untuk setiap item produk yang dipesan |
| 7 | Sistem akan menampilkan **total nilai PO** secara otomatis |
| 8 | Klik **"Simpan PO"** — nomor PO di-generate otomatis format `PO-YYYYMMDD-XXX` |
| 9 | Sistem langsung melakukan **verifikasi stok bahan baku** |
| 10 | ✅ Jika stok cukup → PO masuk status **"Menunggu Konfirmasi"**, notifikasi terkirim ke semua role |
| 11 | ⚠️ Jika stok **tidak mencukupi** → halaman menampilkan **tabel selisih stok** (tersedia vs kebutuhan vs kekurangan) → klik **"Kirim Notifikasi Stok Kurang"** untuk menginformasikan Operator Gudang |

> **Catatan:** Setelah PO tersimpan, PO hanya bisa **diedit atau dikonfirmasi** oleh **Administrator**. Staf Penjualan tidak dapat mengubah PO yang sudah dibuat.

**B. Menambah Produk Baru (khusus Staf)**
| Langkah | Aksi |
|---|---|
| 1 | Buka menu **Produk** → klik **"Tambah Produk"** (tersedia di menu khusus staf: `/produk/staf/tambah`) |
| 2 | Isi nama produk, kategori, harga, dan deskripsi |
| 3 | Upload foto produk — preview foto akan tampil sebelum disimpan |
| 4 | Klik **"Simpan"** — notifikasi otomatis terkirim ke semua role |

**C. Mencetak & Membagikan Dokumen PO**
| Langkah | Aksi |
|---|---|
| 1 | Buka detail PO → klik **"Cetak PDF"** |
| 2 | PDF dokumen PO resmi di-generate dalam ≤ 5 detik |
| 3 | Untuk berbagi: klik **"Salin Link"** — link sementara valid **24 jam** |
| 4 | Kirimkan link ke pelanggan atau tim internal |

</details>

---

##### 🏭 Operator Gudang

<details>
<summary><b>Klik untuk expand — Panduan Operator Gudang</b></summary>

**A. Mengelola Stok Bahan Baku**
| Langkah | Aksi |
|---|---|
| 1 | Login sebagai Operator Gudang |
| 2 | Cek menu **Notifikasi** — jika ada notifikasi **"Stok Kurang"** dari Staf Penjualan, klik untuk melihat detail bahan baku yang kurang beserta jumlah kekurangannya |
| 3 | Buka menu **Inventori / Gudang** → pilih bahan baku yang perlu ditambah |
| 4 | Klik **"Tambahkan Stok"** → isi jumlah penambahan → klik **Simpan** |
| 5 | Stok langsung diperbarui — notifikasi stok kurang akan otomatis hilang jika stok sudah mencukupi |
| 6 | Untuk **mengurangi stok**: klik **"Kurangi Stok"** → isi jumlah pengurangan → Simpan |

**B. Update Status Produksi**
| Langkah | Aksi |
|---|---|
| 1 | Buka menu **Pesanan** → lihat PO dengan status **"Dikonfirmasi"** |
| 2 | Buka detail PO → di bagian **"Ubah Status"** (kanan bawah halaman), pilih status baru dari dropdown |
| 3 | Pilih **"Dalam Produksi"** → isi keterangan singkat → klik **"Simpan Status"** |
| 4 | Sistem otomatis **mengurangi stok bahan baku** sesuai kebutuhan PO saat status berubah ke "Dalam Produksi" |
| 5 | Setelah produksi selesai: ulangi langkah yang sama → pilih **"Siap Kirim"** → simpan |

**C. Memantau Dashboard Gudang**
| Langkah | Aksi |
|---|---|
| 1 | Dashboard menampilkan ringkasan stok bahan baku dan PO dalam produksi |
| 2 | Indikator merah **"Kurang"** muncul pada bahan baku yang stoknya di bawah minimum |
| 3 | Klik bahan baku untuk melihat detail stok dan riwayat perubahan |

</details>

---

##### 🔐 Administrator

<details>
<summary><b>Klik untuk expand — Panduan Administrator</b></summary>

**A. Mengelola Seluruh Pesanan (PO)**
| Langkah | Aksi |
|---|---|
| 1 | Login sebagai Administrator |
| 2 | Dashboard menampilkan ringkasan: PO aktif, menunggu konfirmasi, siap kirim, selesai bulan ini |
| 3 | Buka menu **Pesanan** untuk melihat semua PO dari seluruh role |
| 4 | Klik PO untuk melihat detail lengkap termasuk **audit trail** perubahan status |
| 5 | Untuk **mengkonfirmasi PO**: buka detail PO → pilih status **"Dikonfirmasi"** di form **"Ubah Status"** → klik **"Simpan Status"** |
| 6 | Untuk **edit PO**: klik tombol **"Edit"** → ubah item/harga → simpan |
| 7 | Untuk **menyelesaikan PO**: ubah status ke **"Selesai"** setelah pengiriman dikonfirmasi |
| 8 | Untuk **membatalkan PO**: klik **"Batalkan Pesanan"** → isi alasan pembatalan (wajib, min. 5 karakter) → konfirmasi → stok bahan baku otomatis di-**rollback** jika PO sudah "Dalam Produksi" |

**B. Mengelola Data Pengguna** *(khusus Administrator)*
| Langkah | Aksi |
|---|---|
| 1 | Buka menu **Manajemen User** → akses ke `/admin/users` |
| 2 | Klik **"Tambah User"** → isi nama, email, password, dan pilih **role** |
| 3 | Untuk edit user: klik ikon edit pada baris user yang diinginkan |
| 4 | Untuk hapus user: klik ikon hapus |

**C. Mengakses Arsip**
| Langkah | Aksi |
|---|---|
| 1 | Buka menu **Arsip** |
| 2 | Lihat seluruh PO dengan status **"Selesai"** atau **"Dibatalkan"** |
| 3 | Klik PO untuk melihat detail lengkap dan histori status |
| 4 | Download PDF PO dari halaman arsip |

**D. Memantau Notifikasi Sistem**
| Langkah | Aksi |
|---|---|
| 1 | Buka menu **Notifikasi** — semua notifikasi sistem ditampilkan |
| 2 | Notifikasi meliputi: PO baru, perubahan status, stok menipis, produk baru |
| 3 | Klik notifikasi untuk langsung menuju halaman terkait |

</details>

---

### 📱 Mobile App — Panduan Lengkap

#### 1️⃣ Download & Instalasi APK

| Langkah | Aksi |
|---|---|
| 1 | Buka browser HP Android dan kunjungi **[https://hutch-prestige.my.id](https://hutch-prestige.my.id)** |
| 2 | Scroll ke bawah, cari tombol **"Unduh Aplikasi"** atau **"Download APK"** |
| 3 | Klik tombol unduh — file `Hutch-mobile.apk` akan otomatis terunduh |
| 4 | Buka notifikasi unduhan atau cari file di **folder Downloads** HP |
| 5 | Ketuk file `Hutch-mobile.apk` untuk memulai instalasi |
| 6 | Jika muncul peringatan **"Sumber tidak dikenal"** → buka **Pengaturan HP → Keamanan (atau Privasi) → Aktifkan "Izinkan instalasi dari sumber tidak dikenal"** |
| 7 | Kembali ke file APK dan ketuk **"Instal"** |
| 8 | Tunggu hingga proses instalasi selesai → ketuk **"Buka"** |

> **Catatan:** APK hanya kompatibel dengan perangkat **Android 6.0 (Marshmallow) ke atas**.

---

#### 2️⃣ Login ke Aplikasi Mobile

1. Buka aplikasi **Hutch Prestige** dari layar utama HP
2. Anda akan melihat **Landing Screen** — berisi informasi aplikasi dan tombol "Masuk"
3. Ketuk **"Masuk"** → masukkan **email** dan **password** sesuai role Anda
4. Ketuk tombol **"Login"**
5. Setelah login berhasil, muncul **pop-up selamat datang** (auto-dismiss 1,5 detik)
6. Anda langsung masuk ke **Dashboard** sesuai role

---

#### 3️⃣ Panduan Per Role — Mobile

##### 📋 Staf Penjualan (Mobile)

<details>
<summary><b>Klik untuk expand — Panduan Staf Penjualan (Mobile)</b></summary>

**Menu Navigasi Bawah:** Dashboard | Pesanan | Pelanggan | Tambah Produk

**A. Dashboard**
- Menampilkan ringkasan statistik: total PO aktif, PO menunggu konfirmasi, dan PO siap kirim
- Tap kartu statistik untuk langsung menuju daftar PO terkait

**B. Membuat PO Baru**
| Langkah | Aksi |
|---|---|
| 1 | Tap menu **"Pesanan"** di navbar bawah |
| 2 | Tap tombol **"+"** (FAB) di pojok kanan bawah |
| 3 | Pilih pelanggan dari daftar atau cari dengan **search bar** |
| 4 | Tap **"Tambah Item"** → pilih produk, isi jumlah dan harga |
| 5 | Ulangi untuk setiap item produk |
| 6 | Tap **"Simpan PO"** |

**C. Melihat & Memantau PO**
| Langkah | Aksi |
|---|---|
| 1 | Buka menu **"Pesanan"** |
| 2 | List PO tampil lengkap dengan status, nomor PO, nama pelanggan, dan total nilai |
| 3 | Gunakan **filter status** di bagian atas untuk menyaring PO |
| 4 | Tap PO untuk melihat **detail lengkap** termasuk item dan histori status |

**D. Mengelola Pelanggan**
| Langkah | Aksi |
|---|---|
| 1 | Buka menu **"Pelanggan"** |
| 2 | Gunakan **search bar** untuk mencari pelanggan |
| 3 | Tap **"+"** untuk tambah pelanggan baru → isi nama, telepon, alamat |
| 4 | Tap pelanggan untuk melihat detail dan riwayat pesanan |
| 5 | Swipe kiri atau tap ikon edit/hapus untuk mengelola data pelanggan |

**E. Tambah Produk**
| Langkah | Aksi |
|---|---|
| 1 | Buka menu **"Tambah Produk"** |
| 2 | Isi nama produk, kategori, harga, dan stok |
| 3 | Tap ikon kamera untuk **upload foto produk** |
| 4 | Tap **"Simpan"** |

</details>

---

##### 🏭 Operator Gudang (Mobile)

<details>
<summary><b>Klik untuk expand — Panduan Operator Gudang (Mobile)</b></summary>

**Menu Navigasi Bawah:** Dashboard | Manajemen Stok

**A. Dashboard**
- Ringkasan stok bahan baku dan PO dalam proses produksi
- Indikator merah pada stok yang hampir habis

**B. Manajemen Stok**
| Langkah | Aksi |
|---|---|
| 1 | Tap menu **"Manajemen Stok"** |
| 2 | Lihat daftar seluruh bahan baku beserta jumlah stok saat ini |
| 3 | Tap bahan baku yang ingin dikelola |
| 4 | Tap **"Tambahkan Stok"** → isi jumlah penambahan → **Simpan** |
| 5 | Tap **"Kurangi Stok"** → isi jumlah pengurangan → **Simpan** |
| 6 | Perubahan stok langsung tersinkron dengan website secara real-time |

</details>

---

##### 🔐 Administrator (Mobile)

<details>
<summary><b>Klik untuk expand — Panduan Administrator (Mobile)</b></summary>

**Menu Navigasi Bawah:** Dashboard | Pesanan | Arsip

**A. Dashboard**
- Ringkasan seluruh PO aktif, menunggu konfirmasi, dan siap kirim
- Tap kartu untuk langsung menuju list PO terkait

**B. Memantau & Mengelola PO**
| Langkah | Aksi |
|---|---|
| 1 | Tap menu **"Pesanan"** |
| 2 | Filter PO berdasarkan status menggunakan tab filter |
| 3 | Tap PO untuk melihat detail lengkap |
| 4 | Di halaman detail PO: tap **"Ubah Status"** untuk mengupdate status produksi |
| 5 | Pilih status baru dari opsi yang tersedia |

**C. Arsip PO**
| Langkah | Aksi |
|---|---|
| 1 | Tap menu **"Arsip"** |
| 2 | Lihat seluruh PO selesai dan dibatalkan |
| 3 | Tap PO untuk detail dan download PDF |

</details>

---

#### 4️⃣ Fitur Tambahan Mobile

**🤖 Chatbot AI (N8N)**
1. Tap ikon **chatbot** (💬) di pojok kanan bawah layar (tersedia di semua halaman)
2. Ketik pertanyaan tentang pesanan, stok, atau informasi sistem
3. Asisten AI akan memberikan jawaban berdasarkan data real-time

**🔔 Notifikasi Push**
- Notifikasi otomatis muncul saat ada PO baru, perubahan status, atau stok menipis
- Tap notifikasi untuk langsung menuju halaman terkait

**👤 Profil & Logout**
1. Tap ikon **profil** di pojok kanan atas layar
2. Lihat informasi akun dan role aktif
3. Tap **"Logout"** untuk keluar dari aplikasi

> 📶 **Catatan:** Pastikan HP terhubung ke internet. Aplikasi terhubung langsung ke server **hutch-prestige.my.id** secara real-time. Data akan diperbarui otomatis setiap beberapa detik.

---

### 📄 Cetak & Bagikan Dokumen PO (Website)

1. Buka detail PO yang ingin dicetak
2. Klik tombol **"Cetak PDF"** — dokumen PDF di-generate dalam ≤ 5 detik
3. Browser akan langsung mengunduh file PDF
4. Untuk berbagi via link: klik **"Salin Link"** — link sementara valid selama **24 jam**
5. Kirimkan link ke pelanggan atau tim internal — siapapun dapat membuka link tanpa perlu login

---

## 🏗️ Infrastruktur

Sistem **Hutch.id OrderFlow** dibangun dengan arsitektur berlapis yang memisahkan antara **client layer**, **server layer**, **data layer**, dan **layanan eksternal**.

---

### 🌐 Arsitektur Deployment

```
┌─────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                       │
│  🌐 Web Browser          📱 Flutter App (Android)       │
│  Chrome / Firefox / Edge  Hutch Prestige APK            │
└──────────────┬───────────────────────┬──────────────────┘
               │ HTTPS (TLS 1.2+)      │ HTTPS (TLS 1.2+)
               ▼                       ▼
┌─────────────────────────────────────────────────────────┐
│               SERVER LAYER — hutch-prestige.my.id       │
│                                                         │
│  ┌──────────────────────────────────────────────────┐   │
│  │           Nginx 1.24+ (Reverse Proxy)            │   │
│  │  HTTPS Termination · Static Files · Load Balance │   │
│  └─────────────────────┬────────────────────────────┘   │
│                        │                                │
│  ┌─────────────────────▼────────────────────────────┐   │
│  │         PHP-FPM 8.1+ · Laravel 10 (MVC)          │   │
│  │  Route Middleware · Eloquent ORM · Auth Sanctum  │   │
│  │  Session Auth (Web) · Bearer Token Auth (API)    │   │
│  └─────────────────────┬────────────────────────────┘   │
│                        │                                │
│  ┌─────────────────────▼────────────────────────────┐   │
│  │               MySQL 8.0+ (InnoDB)                │   │
│  │     users · pesanan · detail_pesanan             │   │
│  │     pelanggan · produk · histori_status_po       │   │
│  │     notifikasi                                   │   │
│  └──────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────────────────────┐
│                   EXTERNAL SERVICES                     │
│  📨 SMTP Email (Laravel Mail)                           │
│  🤖 N8N Workflow (Chatbot AI)                           │
│  📄 DomPDF — Generate PDF PO (Signed URL 24 jam)       │
└─────────────────────────────────────────────────────────┘
```

---

### 🖥️ Spesifikasi Server (VPS / Cloud)

| Komponen | Spesifikasi |
|---|---|
| **CPU** | Minimum 2 vCore |
| **RAM** | Minimum 2 GB |
| **Storage** | Minimum 20 GB SSD |
| **OS** | Ubuntu 22.04 LTS |
| **Web Server** | Nginx 1.24+ |
| **Runtime** | PHP 8.1+ dengan PHP-FPM |
| **Database** | MySQL 8.0+ |
| **SSL/TLS** | Let's Encrypt (TLS 1.2+) |
| **Domain** | hutch-prestige.my.id |
| **Uptime Target** | ≥ 99% (jam 08.00–22.00 WIB) |

---

### 🐳 Kontainerisasi (Docker)

Project menyediakan konfigurasi **Docker Compose** untuk kemudahan deployment:

```yaml
# Layanan yang dikontainerisasi:
services:
  app:    # PHP 8.1-FPM + Laravel (port 9000)
  nginx:  # Nginx reverse proxy (port 8082 → 80)
  db:     # MySQL 8.0 (port 3306)
```

| Container | Image | Fungsi |
|---|---|---|
| `app` | `php:8.1-fpm` | Runtime Laravel + Artisan |
| `nginx` | `nginx:alpine` | Web server & reverse proxy |
| `db` | `mysql:8.0` | Database MySQL |

---

### 🔗 Lapisan Teknologi Lengkap

| Layer | Teknologi | Versi |
|---|---|---|
| **📱 Mobile** | Flutter, Dart, Material Design 3 | Flutter 3.x · Dart 3.0+ |
| **🖥 Frontend Web** | HTML5, CSS3, JavaScript ES6+, Bootstrap, Blade | Laravel Blade |
| **⚙ Backend** | PHP, Laravel, RESTful API, Eloquent ORM | PHP 8.1+ · Laravel 10+ |
| **🗄 Database** | MySQL, InnoDB, Laravel Migration | MySQL 8.0+ |
| **📄 PDF Engine** | DomPDF (barryvdh/laravel-dompdf) | Signed URL 24 jam |
| **🔒 Auth (Web)** | Laravel Session Auth, bcrypt | Timeout 8 jam |
| **🔒 Auth (API)** | Laravel Sanctum, Bearer Token | Stateless |
| **☁ Infra** | VPS, Nginx, Docker, Docker Compose | Ubuntu 22.04 |
| **🤖 AI/Chatbot** | N8N Workflow Automation | Cloud-hosted |
| **📨 Email** | SMTP via Laravel Mail | Retry 3× |

---

### 📡 Integrasi API Internal

Komunikasi antara **Mobile App** ↔ **Website Backend** menggunakan **REST API** dengan autentikasi **Bearer Token (Laravel Sanctum)**:

| Kode | Method | Endpoint | Fungsi |
|---|---|---|---|
| API-01 | `GET` | `/api/inventory/check` | Verifikasi stok bahan baku berdasarkan kebutuhan PO |
| API-02 | `POST` | `/api/inventory/deduct` | Kurangi stok saat PO → "Dalam Produksi" |
| API-03 | `POST` | `/api/inventory/rollback` | Kembalikan stok jika PO dibatalkan dari "Dalam Produksi" |
| API-04 | `GET` | `/api/products/{id}/price` | Ambil harga jual produk dari Modul HPP |
| API-05 | `POST` | `SMTP (Laravel Mail)` | Notifikasi email PO baru ke Administrator |
| API-06 | `GET` | `/po/{token}/pdf` | Generate & serve PDF via Signed URL valid 24 jam |

---

### 🔐 Keamanan Infrastruktur

| Aspek | Implementasi |
|---|---|
| **Transport Security** | HTTPS dengan TLS 1.2+ pada semua komunikasi |
| **Auth Web** | Session-based login dengan hash bcrypt, timeout 8 jam |
| **Auth API** | Bearer Token via Laravel Sanctum (stateless) |
| **RBAC** | 4 level peran — setiap route dilindungi middleware `role` |
| **PDF Sharing** | Signed URL acak dengan expiry 24 jam |
| **APK Distribution** | Hanya tersedia dari website resmi hutch-prestige.my.id |
| **Database** | Foreign key constraint, InnoDB transaction |

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
| Adrian Ronald Daga | 20241320011 | Backend |
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

🌐 [hutch-prestige.my.id](https://hutch-prestige.my.id) · 📱 Mobile App

</div>
