# 🎭 Hutch.id Mobile — Maestro QA Test Suite

Folder ini berisi script **Maestro** untuk pengujian otomatis aplikasi Flutter **Hutch.id Mobile**.

---

## 📁 Struktur File

```
.maestro/
├── 00_config.yaml                    # Environment variables & app config
├── 01_splash_landing.yaml            # TC-MOB-001: Splash & Landing Page
├── 02_login_validasi.yaml            # TC-MOB-002: Login - Validasi Form & Role
├── 03_login_admin.yaml               # TC-MOB-003: Login Administrator
├── 04_login_staf_penjualan.yaml      # TC-MOB-004: Login Staf Penjualan
├── 05_login_operator_gudang.yaml     # TC-MOB-005: Login Operator Gudang
├── 06_dashboard.yaml                 # TC-MOB-006: Dashboard
├── 07_pesanan_list_filter_search.yaml # TC-MOB-007: Pesanan List, Filter, Search
├── 08_pesanan_buat_baru.yaml         # TC-MOB-008: Buat Pesanan Baru (Happy Path)
├── 09_pesanan_validasi_form.yaml     # TC-MOB-009: Validasi Form Pesanan
├── 10_pelanggan_crud.yaml            # TC-MOB-010: Pelanggan CRUD
├── 11_produk_crud.yaml               # TC-MOB-011: Produk CRUD
├── 12_gudang_stok.yaml               # TC-MOB-012: Manajemen Stok Gudang
├── 13_notifikasi.yaml                # TC-MOB-013: Notifikasi
├── 14_arsip.yaml                     # TC-MOB-014: Arsip Pesanan
├── 15_profil_user_management.yaml    # TC-MOB-015: Profil & User Management
├── 16_rbac_akses_kontrol.yaml        # TC-MOB-016: RBAC Akses Kontrol
├── 17_performance_stability.yaml     # TC-MOB-017: Performance & Stability
├── 18_e2e_full_order_flow.yaml       # TC-MOB-018: E2E Full Order Workflow
└── run_all_tests.yaml                # Runner: Smoke Test Suite
```

---

## 🚀 Cara Menjalankan

### Prasyarat
1. Install Maestro CLI:
   ```bash
   curl -Ls "https://get.maestro.mobile.dev" | bash
   ```
2. Pastikan device Android terhubung (USB) atau emulator berjalan
3. Verifikasi koneksi device:
   ```bash
   adb devices
   ```
4. Pastikan APK sudah terinstal di device dengan `appId`: `com.hutchprestige.mobile`

### Jalankan Satu Test
```bash
maestro test .maestro/01_splash_landing.yaml
```

### Jalankan Smoke Test Suite
```bash
maestro test .maestro/run_all_tests.yaml
```

### Jalankan Semua Test (Full Suite)
```bash
maestro test .maestro/
```

### Jalankan dengan Credentials Custom
```bash
maestro test .maestro/03_login_admin.yaml \
  --env EMAIL_ADMIN=myadmin@hutch.id \
  --env PASSWORD_ADMIN=mypassword
```

### Jalankan dengan Screenshot Output
```bash
maestro test .maestro/ --output output/
```

---

## 🧪 Daftar Test Case

| No  | File                                  | Deskripsi                              | Role           |
|-----|---------------------------------------|----------------------------------------|----------------|
| 001 | `01_splash_landing.yaml`              | Splash screen & landing page           | All            |
| 002 | `02_login_validasi.yaml`              | Validasi form login & role selection   | All            |
| 003 | `03_login_admin.yaml`                 | Login Administrator + navigasi         | Administrator  |
| 004 | `04_login_staf_penjualan.yaml`        | Login Staf Penjualan + navigasi        | Staf Penjualan |
| 005 | `05_login_operator_gudang.yaml`       | Login Operator Gudang + nav terbatas   | Op. Gudang     |
| 006 | `06_dashboard.yaml`                   | Dashboard data & refresh               | Administrator  |
| 007 | `07_pesanan_list_filter_search.yaml`  | Pesanan: list, filter status, search   | Staf Penjualan |
| 008 | `08_pesanan_buat_baru.yaml`           | Buat pesanan baru (happy path)         | Staf Penjualan |
| 009 | `09_pesanan_validasi_form.yaml`       | Validasi form pesanan & edge cases     | Staf Penjualan |
| 010 | `10_pelanggan_crud.yaml`              | CRUD pelanggan (list, tambah, edit)    | Staf Penjualan |
| 011 | `11_produk_crud.yaml`                 | CRUD produk (list, tambah)             | Staf Penjualan |
| 012 | `12_gudang_stok.yaml`                 | Manajemen stok gudang                  | Op. Gudang     |
| 013 | `13_notifikasi.yaml`                  | Halaman notifikasi                     | Staf Penjualan |
| 014 | `14_arsip.yaml`                       | Arsip pesanan                          | Administrator  |
| 015 | `15_profil_user_management.yaml`      | Profil & user management               | Administrator  |
| 016 | `16_rbac_akses_kontrol.yaml`          | RBAC — kontrol akses semua role        | All            |
| 017 | `17_performance_stability.yaml`       | Performa & stabilitas                  | Staf Penjualan |
| 018 | `18_e2e_full_order_flow.yaml`         | E2E: buat PO → konfirmasi              | Staf + Admin   |

---

## ⚙️ Environment Variables

| Variable         | Default          | Keterangan             |
|-----------------|------------------|------------------------|
| `EMAIL_ADMIN`   | admin@hutch.id   | Email akun Administrator|
| `PASSWORD_ADMIN`| password123      | Password Administrator  |
| `EMAIL_STAF`    | staf@hutch.id    | Email Staf Penjualan    |
| `PASSWORD_STAF` | password123      | Password Staf Penjualan |
| `EMAIL_GUDANG`  | gudang@hutch.id  | Email Operator Gudang   |
| `PASSWORD_GUDANG`| password123     | Password Operator Gudang|

> ⚠️ **PENTING**: Sesuaikan `appId` di `00_config.yaml` dengan `applicationId` yang ada di `android/app/build.gradle`

---

## 📋 Konfigurasi App ID

Cek `applicationId` di file:
```
android/app/build.gradle
```

Lalu update `00_config.yaml`:
```yaml
appId: com.hutchprestige.mobile  # ← ubah sesuai applicationId
```

---

## 📸 Screenshot Output

Semua test menghasilkan screenshot yang tersimpan di folder `output/` dengan nama deskriptif.
Gunakan `--output` flag untuk menentukan lokasi:

```bash
maestro test .maestro/ --output ./test-results/
```

---

## 👥 Tim QA

| Nama             | Role              |
|-----------------|-------------------|
| Sopyan Rinakdhi  | QA Tester Mobile  |
| Akbar            | QA Tester Mobile  |

---

## 🔗 Referensi

- [Maestro Documentation](https://maestro.mobile.dev)
- [Maestro CLI Reference](https://maestro.mobile.dev/cli/reference)
- [Flutter Testing Guide](https://docs.flutter.dev/testing)
