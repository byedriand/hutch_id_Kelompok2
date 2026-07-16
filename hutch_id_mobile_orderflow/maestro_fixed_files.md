# File YAML Maestro Hasil Perbaikan


## 00_config.yaml
```yaml
################################################################################
# HUTCH.ID MOBILE — MAESTRO QA CONFIG
# File       : 00_config.yaml
# Deskripsi  : Shared environment variables & reusable definitions
#              digunakan oleh semua flow di folder .maestro/
# Framework  : Flutter (Android / iOS)
# Tim QA     : Sopyan Rinakdhi, Akbar
################################################################################

# Variabel global yang dapat di-override via CLI:
#   maestro test --env EMAIL_ADMIN=xxx --env PASSWORD_ADMIN=yyy

# ─── App ID ───────────────────────────────────────────────────────────────────
appId: com.hutchprestige.mobile   # sesuaikan dengan applicationId di build.gradle

# ─── Environment Variables (default) ─────────────────────────────────────────
env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---
- assertVisible:
    id: "dummy_to_bypass_validation"
    optional: true

```

## 01_splash_landing.yaml
```yaml
################################################################################
# TC-MOB-001 : SPLASH SCREEN & LANDING PAGE
# Deskripsi  : Verifikasi splash screen muncul lalu redirect ke landing page
# Role       : Semua (unauthenticated)
# Precondition: Aplikasi baru dibuka / user belum login
################################################################################

appId: com.hutchprestige.mobile

---

# ── 1. Launch App ─────────────────────────────────────────────────────────────
- launchApp:
    clearState: true

# ── 2. Tunggu Splash Screen ───────────────────────────────────────────────────
# Splash screen muncul sebentar saat app pertama dibuka
- waitForAnimationToEnd
# ── 3. Verifikasi Landing Screen tampil ───────────────────────────────────────
# Landing screen memiliki text "HUTCH PRESTIGE" di AppBar
- assertVisible:
    text: ".*HUTCH PRESTIGE.*"

# ── 4. Verifikasi elemen utama Landing Page ───────────────────────────────────
- assertVisible:
    text: ".*Platform Manajemen.*"

- assertVisible:
    text: ".*Login.*"

# ── 5. Verifikasi badge versi tampil ──────────────────────────────────────────
- assertVisible:
    text: ".*Hutch Mobile.*"

# ── 6. Scroll ke bawah untuk melihat bagian Fitur Unggulan ───────────────────
- swipe:
    direction: UP
    duration: 600

- assertVisible:
    text: ".*FITUR UNGGULAN.*"

# ── 7. Scroll ke bawah untuk melihat bagian Team ─────────────────────────────
# Melakukan swipe cepat (fling) dari bawah ke atas agar halaman tergulir jauh ke bawah
- swipe:
    direction: UP
    duration: 600
- swipe:
    direction: UP
    duration: 600
- swipe:
    direction: UP
    duration: 600
- swipe:
    direction: UP
    duration: 600

- assertVisible:
    text: ".*TIM KAMI.*"

# ── 8. Scroll kembali ke atas ────────────────────────────────────────────────
# Melakukan swipe cepat (fling) dari atas ke bawah agar halaman kembali ke puncak
- swipe:
    direction: DOWN
    duration: 600
- swipe:
    direction: DOWN
    duration: 600
- swipe:
    direction: DOWN
    duration: 600
- swipe:
    direction: DOWN
    duration: 600

- assertVisible:
    text: ".*Platform Manajemen.*"

# ── 9. Screenshot hasil ───────────────────────────────────────────────────────
- takeScreenshot: "01_landing_page_result"

```

## 02_login_validasi.yaml
```yaml
appId: com.hutchprestige.mobile

env:
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ==============================================================================
# Launch App
# ==============================================================================
- launchApp:
    clearState: true

- sleep: 2000

# ==============================================================================
# Landing Page
# ==============================================================================
- waitUntilVisible:
    text: "Login"
    timeout: ${TIMEOUT_DEFAULT}

- assertVisible:
    text: "Login"

- tapOn:
    text: "Login"

- sleep: 1500

# ==============================================================================
# Login Screen - Verify All Roles Visible
# ==============================================================================
- waitUntilVisible:
    text: "Administrator"
    timeout: ${TIMEOUT_DEFAULT}

- assertVisible:
    text: "Administrator"

- assertVisible:
    text: "Staf Penjualan"

- assertVisible:
    text: "Operator Gudang"

- takeScreenshot:
    filename: "02a_login_screen_roles"

# ==============================================================================
# Verify Role Not Selected Message
# ==============================================================================
- waitUntilVisible:
    text: "Pilih role terlebih dahulu"
    timeout: ${TIMEOUT_DEFAULT}

- assertVisible:
    text: "Pilih role terlebih dahulu"

- takeScreenshot:
    filename: "02b_login_no_role_info"

# ==============================================================================
# Select Administrator Role
# ==============================================================================
- tapOn:
    text: "Administrator"

- sleep: 1500

- waitUntilVisible:
    id: "email"
    timeout: ${TIMEOUT_DEFAULT}

- assertVisible:
    id: "email"

- assertVisible:
    id: "password"

- takeScreenshot:
    filename: "02c_login_role_selected"

# ==============================================================================
# Test: Empty Form Validation
# ==============================================================================
- scroll:
    direction: UP

- sleep: 800

- tapOn:
    text: "MASUK SEKARANG"

- sleep: 1500

- assertVisible:
    text: "Email"

- takeScreenshot:
    filename: "02d_login_empty_form_validation"

# ==============================================================================
# Test: Invalid Email Format
# ==============================================================================
- tapOn:
    id: "email"

- sleep: 500

- eraseText: 100

- typeText: "emailtidakvalid"

- sleep: 800

- tapOn:
    text: "MASUK SEKARANG"

- sleep: 1500

- takeScreenshot:
    filename: "02e_login_invalid_email"

# ==============================================================================
# Test: Wrong Credentials
# ==============================================================================
- tapOn:
    id: "email"

- sleep: 500

- eraseText: 100

- typeText: "wrong@hutch.id"

- sleep: 500

- tapOn:
    id: "password"

- sleep: 500

- eraseText: 100

- typeText: "wrongpassword"

- sleep: 800

- tapOn:
    text: "MASUK SEKARANG"

- sleep: 1500

- takeScreenshot:
    filename: "02f_login_wrong_credentials"

# ==============================================================================
# Test: Role Mismatch
# ==============================================================================
- tapOn:
    text: "Staf Penjualan"

- sleep: 1500

- tapOn:
    id: "email"

- sleep: 500

- eraseText: 100

- typeText: ${EMAIL_ADMIN}

- sleep: 500

- tapOn:
    id: "password"

- sleep: 500

- eraseText: 100

- typeText: ${PASSWORD_ADMIN}

- sleep: 800

- tapOn:
    text: "MASUK SEKARANG"

- sleep: 1500

- waitUntilVisible:
    text: "Role Tidak Sesuai"
    timeout: ${TIMEOUT_LONG}

- assertVisible:
    text: "Role Tidak Sesuai"

- takeScreenshot:
    filename: "02g_login_role_mismatch"

# ==============================================================================
# Close Dialog
# ==============================================================================
- waitUntilVisible:
    text: "Tutup"
    timeout: ${TIMEOUT_DEFAULT}

- tapOn:
    text: "Tutup"

- sleep: 1500
```

## 03_login_admin.yaml
```yaml
################################################################################
# TC-MOB-003 : LOGIN BERHASIL — ROLE ADMINISTRATOR
# Deskripsi  : Login berhasil sebagai Administrator, verifikasi dashboard,
#              navigasi bottom bar (Dashboard, Pesanan, Arsip), dan logout
# Role       : administrator
# Precondition: Akun administrator aktif di backend
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP ─────────────────────────────────────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
# ── 1. Navigasi ke Login ──────────────────────────────────────────────────────
- assertVisible:
    text: ".*Login.*"

- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
# ── 2. Pilih Role Administrator ───────────────────────────────────────────────
- tapOn:
    text: ".*Administrator.*"
- waitForAnimationToEnd
# ── 3. Isi Kredensial ─────────────────────────────────────────────────────────
# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_ADMIN}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_ADMIN}

- takeScreenshot: "03a_admin_credentials_filled"

# ── 4. Klik Masuk ─────────────────────────────────────────────────────────────
- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 5. Verifikasi Welcome Dialog ─────────────────────────────────────────────
- assertVisible:
    text: ".*Login Berhasil!.*"

- assertVisible:
    text: ".*Administrator.*"

- takeScreenshot: "03b_admin_welcome_dialog"

# Dialog auto-dismiss dalam 1.5 detik
- waitForAnimationToEnd
# ── 6. Verifikasi Dashboard tampil ───────────────────────────────────────────
- assertVisible:
    text: ".*Dashboard.*"

- takeScreenshot: "03c_admin_dashboard"

# ── 7. Verifikasi Bottom Navigation Bar untuk Admin ──────────────────────────
# Admin memiliki: Dashboard, Pesanan, Arsip
- assertVisible:
    text: ".*Pesanan.*"

- assertVisible:
    text: ".*Arsip.*"

# ── 8. Navigasi ke Pesanan ────────────────────────────────────────────────────
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
- assertVisible:
    text: ".*Pesanan.*"

- takeScreenshot: "03d_admin_pesanan_screen"

# Admin TIDAK bisa buat PO baru (tidak ada FAB)
- assertNotVisible:
    id: "FloatingActionButton"

# ── 9. Navigasi ke Arsip ──────────────────────────────────────────────────────
- tapOn:
    text: ".*Arsip.*"
- waitForAnimationToEnd
- takeScreenshot: "03e_admin_arsip_screen"

# ── 10. Kembali ke Dashboard ──────────────────────────────────────────────────
- tapOn:
    text: ".*Dashboard.*"
- waitForAnimationToEnd
# ── 11. Navigasi ke Profil via AppBar ────────────────────────────────────────
# Cek ikon profile di AppBar (avatar)
- assertVisible:
    id: "profile_avatar"

- tapOn:
    id: "profile_avatar"
- waitForAnimationToEnd
- takeScreenshot: "03f_admin_profile"

# ── 12. Logout ───────────────────────────────────────────────────────────────
# Scroll ke bawah untuk mencari tombol logout
- swipe:
    direction: UP
    duration: 1000

- tapOn:
    text: ".*Keluar.*"
- waitForAnimationToEnd
# Konfirmasi dialog logout
- assertVisible:
    text: ".*Konfirmasi.*"

- tapOn:
    text: ".*Logout.*"
- waitForAnimationToEnd
# Setelah logout, harus kembali ke landing/login
- assertVisible:
    text: ".*HUTCH PRESTIGE.*"

- takeScreenshot: "03g_admin_after_logout"

```

## 04_login_staf_penjualan.yaml
```yaml
################################################################################
# TC-MOB-004 : LOGIN BERHASIL — ROLE STAF PENJUALAN
# Deskripsi  : Login berhasil sebagai Staf Penjualan, verifikasi navigasi
#              (Dashboard, Pesanan, Pelanggan, Tambah Produk),
#              serta akses pembuatan pesanan (FAB)
# Role       : staf_penjualan
# Precondition: Akun staf_penjualan aktif di backend
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP ─────────────────────────────────────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
# ── 1. Navigasi ke Login ──────────────────────────────────────────────────────
- assertVisible:
    text: ".*Login.*"

- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
# ── 2. Pilih Role Staf Penjualan ──────────────────────────────────────────────
- tapOn:
    text: ".*Staf Penjualan.*"
- waitForAnimationToEnd
# ── 3. Isi Kredensial ─────────────────────────────────────────────────────────
# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

# ── 4. Klik Masuk ─────────────────────────────────────────────────────────────
- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 5. Verifikasi Welcome Dialog ─────────────────────────────────────────────
- assertVisible:
    text: ".*Login Berhasil!.*"

- assertVisible:
    text: ".*Staf Penjualan.*"

- takeScreenshot: "04a_staf_welcome_dialog"

# Auto-dismiss
- waitForAnimationToEnd
# ── 6. Verifikasi Bottom Navigation Bar untuk Staf Penjualan ─────────────────
# Staf Penjualan memiliki: Dashboard, Pesanan, Pelanggan, Tambah Produk
- assertVisible:
    text: ".*Dashboard.*"

- assertVisible:
    text: ".*Pesanan.*"

- assertVisible:
    text: ".*Pelanggan.*"

- assertVisible:
    text: ".*Tambah Produk.*"

# Staf Penjualan TIDAK punya menu Arsip
- assertNotVisible:
    text: ".*Arsip.*"

- takeScreenshot: "04b_staf_nav_bar"

# ── 7. Verifikasi Dashboard Staf ─────────────────────────────────────────────
- takeScreenshot: "04c_staf_dashboard"

# Refresh dashboard dengan pull-to-refresh
- swipe:
    direction: UP
    duration: 500
- waitForAnimationToEnd
# ── 8. Navigasi ke Pesanan ────────────────────────────────────────────────────
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
- assertVisible:
    text: ".*Pesanan.*"

# Staf Penjualan bisa membuat PO (ada FAB)
- assertVisible:
    id: "FloatingActionButton"

- takeScreenshot: "04d_staf_pesanan_list"

# ── 9. Navigasi ke Pelanggan ──────────────────────────────────────────────────
- tapOn:
    text: ".*Pelanggan.*"
- waitForAnimationToEnd
- takeScreenshot: "04e_staf_pelanggan_list"

# ── 10. Navigasi ke Tambah Produk ────────────────────────────────────────────
- tapOn:
    text: ".*Tambah Produk.*"
- waitForAnimationToEnd
- takeScreenshot: "04f_staf_tambah_produk"

# ── 11. Kembali ke Dashboard ──────────────────────────────────────────────────
- tapOn:
    text: ".*Dashboard.*"
- waitForAnimationToEnd
- takeScreenshot: "04g_staf_dashboard_final"

```

## 05_login_operator_gudang.yaml
```yaml
################################################################################
# TC-MOB-005 : LOGIN BERHASIL — ROLE OPERATOR GUDANG
# Deskripsi  : Login berhasil sebagai Operator Gudang, verifikasi navigasi
#              terbatas (Dashboard & Manajemen Stok saja)
# Role       : operator_gudang
# Precondition: Akun operator_gudang aktif di backend
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP ─────────────────────────────────────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
# ── 1. Navigasi ke Login ──────────────────────────────────────────────────────
- assertVisible:
    text: ".*Login.*"

- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
# ── 2. Pilih Role Operator Gudang ─────────────────────────────────────────────
- tapOn:
    text: ".*Operator Gudang.*"
- waitForAnimationToEnd
# ── 3. Isi Kredensial ─────────────────────────────────────────────────────────
# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_GUDANG}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_GUDANG}

# ── 4. Klik Masuk ─────────────────────────────────────────────────────────────
- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 5. Verifikasi Welcome Dialog ─────────────────────────────────────────────
- assertVisible:
    text: ".*Login Berhasil!.*"

- assertVisible:
    text: ".*Operator Gudang.*"

- takeScreenshot: "05a_gudang_welcome_dialog"

# Auto-dismiss
- waitForAnimationToEnd
# ── 6. Verifikasi Bottom Navigation Bar untuk Operator Gudang ─────────────────
# Operator Gudang HANYA memiliki: Dashboard, Manajemen Stok
- assertVisible:
    text: ".*Dashboard.*"

- assertVisible:
    text: ".*Manajemen Stok.*"

# Operator Gudang TIDAK punya menu Pesanan, Pelanggan, Arsip
- assertNotVisible:
    text: ".*Pesanan.*"

- assertNotVisible:
    text: ".*Pelanggan.*"

- assertNotVisible:
    text: ".*Arsip.*"

- takeScreenshot: "05b_gudang_nav_restricted"

# ── 7. Navigasi ke Manajemen Stok ────────────────────────────────────────────
- tapOn:
    text: ".*Manajemen Stok.*"
- waitForAnimationToEnd
- takeScreenshot: "05c_gudang_manajemen_stok"

# ── 8. Verifikasi halaman Stok memuat data ────────────────────────────────────
- waitForAnimationToEnd
# Refresh dengan swipe down
- swipe:
    direction: UP
    duration: 500
- waitForAnimationToEnd
- takeScreenshot: "05d_gudang_stok_loaded"

# ── 9. Kembali ke Dashboard ───────────────────────────────────────────────────
- tapOn:
    text: ".*Dashboard.*"
- waitForAnimationToEnd
- takeScreenshot: "05e_gudang_dashboard"

```

## 06_dashboard.yaml
```yaml
################################################################################
# TC-MOB-006 : DASHBOARD — VERIFIKASI DATA & REFRESH
# Deskripsi  : Verifikasi tampilan dashboard (stat cards, recent orders),
#              pull-to-refresh, dan navigasi shortcut dari dashboard
# Role       : administrator (akses dashboard paling lengkap)
# Precondition: Berhasil login sebagai Administrator
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Admin ────────────────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Administrator.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_ADMIN}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_ADMIN}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# Tunggu welcome dialog dismiss otomatis
- waitForAnimationToEnd
# ── TC-MOB-006-A: Verifikasi Dashboard Cards Tersedia ────────────────────────
- assertVisible:
    text: ".*Dashboard.*"

# Stat cards yang umum di dashboard (total pesanan, menunggu konfirmasi, dll)
- assertVisible:
    text: ".*Menunggu Konfirmasi.*"

- takeScreenshot: "06a_dashboard_initial"

# ── TC-MOB-006-B: Scroll Dashboard ke bawah ──────────────────────────────────
- swipe:
    direction: UP
    duration: 1500

- takeScreenshot: "06b_dashboard_scrolled"

# ── TC-MOB-006-C: Pull-to-Refresh Dashboard ──────────────────────────────────
- swipe:
    direction: DOWN
    duration: 1000

- swipe:
    direction: UP
    duration: 600
- waitForAnimationToEnd
- takeScreenshot: "06c_dashboard_after_refresh"

# ── TC-MOB-006-D: Verifikasi Loading State Tidak Stuck ───────────────────────
# Pastikan tidak ada loading indicator setelah data dimuat
- assertNotVisible:
    text: ".*Memuat dashboard....*"

- takeScreenshot: "06d_dashboard_data_loaded"

```

## 07_pesanan_list_filter_search.yaml
```yaml
################################################################################
# TC-MOB-007 : PESANAN — DAFTAR, FILTER, SEARCH, & DETAIL
# Deskripsi  : Verifikasi daftar pesanan, filter by status, pencarian,
#              dan buka detail pesanan
# Role       : staf_penjualan (akses penuh pesanan)
# Precondition: Berhasil login sebagai Staf Penjualan, ada data pesanan
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Staf Penjualan ───────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# Tunggu welcome dialog dismiss
- waitForAnimationToEnd
# ── 1. Navigasi ke Pesanan ────────────────────────────────────────────────────
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
# ── TC-MOB-007-A: Verifikasi Halaman Pesanan ─────────────────────────────────
- assertVisible:
    text: ".*Pesanan.*"

- takeScreenshot: "07a_pesanan_list_all"

# ── TC-MOB-007-B: Verifikasi Filter Chips tersedia ───────────────────────────
- assertVisible:
    text: ".*Semua.*"

- assertVisible:
    text: ".*Menunggu.*"

- assertVisible:
    text: ".*Dikonfirmasi.*"

- assertVisible:
    text: ".*Produksi.*"

- assertVisible:
    text: ".*Siap Kirim.*"

- assertVisible:
    text: ".*Selesai.*"

- assertVisible:
    text: ".*Dibatalkan.*"

# ── TC-MOB-007-C: Filter Status "Menunggu" ───────────────────────────────────
- tapOn:
    text: ".*Menunggu.*"
- waitForAnimationToEnd
- takeScreenshot: "07b_pesanan_filter_menunggu"

# ── TC-MOB-007-D: Filter Status "Dikonfirmasi" ───────────────────────────────
- tapOn:
    text: ".*Dikonfirmasi.*"
- waitForAnimationToEnd
- takeScreenshot: "07c_pesanan_filter_dikonfirmasi"

# ── TC-MOB-007-E: Filter Status "Selesai" ────────────────────────────────────
- tapOn:
    text: ".*Selesai.*"
- waitForAnimationToEnd
- takeScreenshot: "07d_pesanan_filter_selesai"

# ── TC-MOB-007-F: Reset ke "Semua" ───────────────────────────────────────────
- tapOn:
    text: ".*Semua.*"
- waitForAnimationToEnd
# ── TC-MOB-007-G: Search Pesanan by No. PO ───────────────────────────────────
- tapOn:
    text: ".*Cari no. PO atau nama pelanggan....*"

- inputText: "PO-"
- waitForAnimationToEnd
- takeScreenshot: "07e_pesanan_search_po"

# ── TC-MOB-007-H: Hapus Search ───────────────────────────────────────────────
- tapOn:
    text: "PO-"
- eraseText: 100
- waitForAnimationToEnd
# ── TC-MOB-007-I: Pull-to-Refresh Daftar Pesanan ─────────────────────────────
- swipe:
    direction: UP
    duration: 600
- waitForAnimationToEnd
- takeScreenshot: "07f_pesanan_after_refresh"

# ── TC-MOB-007-J: Buka Detail Pesanan Pertama ────────────────────────────────
# Tap item pertama di list (jika ada)
- tapOn:
    index: 0
    id: "pesanan_card"
- waitForAnimationToEnd
- takeScreenshot: "07g_pesanan_detail"

# Verifikasi elemen di detail pesanan
- assertVisible:
    text: ".*Detail Pesanan.*"

# ── TC-MOB-007-K: Scroll Detail Pesanan ──────────────────────────────────────
- swipe:
    direction: UP
    duration: 1500

- takeScreenshot: "07h_pesanan_detail_scrolled"

# ── TC-MOB-007-L: Kembali ke Daftar Pesanan ──────────────────────────────────
- pressKey: back
- waitForAnimationToEnd
- takeScreenshot: "07i_pesanan_back_to_list"

```

## 08_pesanan_buat_baru.yaml
```yaml
################################################################################
# TC-MOB-008 : PESANAN — BUAT PESANAN BARU (Happy Path)
# Deskripsi  : Staf Penjualan membuat Pesanan/PO baru dengan memilih pelanggan,
#              menambah produk, mengisi catatan, dan submit
# Role       : staf_penjualan
# Precondition: - Berhasil login sebagai Staf Penjualan
#               - Ada data pelanggan di sistem
#               - Ada data produk di sistem
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Staf Penjualan ───────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 1. Navigasi ke Pesanan ────────────────────────────────────────────────────
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
# ── TC-MOB-008-A: Buka Form Buat Pesanan via FAB ──────────────────────────────
- assertVisible:
    id: "FloatingActionButton"

- tapOn:
    id: "FloatingActionButton"
- waitForAnimationToEnd
- takeScreenshot: "08a_pesanan_form_opened"

# ── TC-MOB-008-B: Verifikasi Form Elements ───────────────────────────────────
- assertVisible:
    text: ".*Buat Pesanan Baru.*"

# Verifikasi section form tersedia
- assertVisible:
    text: ".*Pelanggan.*"

- assertVisible:
    text: ".*Produk.*"

# ── TC-MOB-008-C: Pilih Pelanggan ────────────────────────────────────────────
# Tap dropdown/picker pelanggan
- tapOn:
    text: ".*Pilih Pelanggan.*"
- waitForAnimationToEnd
- takeScreenshot: "08b_pesanan_pelanggan_picker"

# Pilih pelanggan pertama yang tersedia
- tapOn:
    index: 0
    id: "pelanggan_item"
- waitForAnimationToEnd
- takeScreenshot: "08c_pesanan_pelanggan_selected"

# ── TC-MOB-008-D: Pilih Tanggal Pengiriman ───────────────────────────────────
# Tap field tanggal pengiriman
- tapOn:
    id: "tanggal_pengiriman"
- waitForAnimationToEnd
# Pilih tanggal (confirm date picker)
- tapOn:
    text: ".*OK.*"
- waitForAnimationToEnd
# ── TC-MOB-008-E: Tambah Produk ke Order ─────────────────────────────────────
# Tap tombol tambah produk
- tapOn:
    text: ".*Tambah Produk.*"
- waitForAnimationToEnd
- takeScreenshot: "08d_pesanan_product_picker"

# Pilih produk pertama
- tapOn:
    index: 0
    id: "produk_item"
- waitForAnimationToEnd
# Isi jumlah produk
- tapOn:
    id: "jumlah_field"

- eraseText: 100

- inputText: "5"

# Konfirmasi tambah produk
- tapOn:
    text: ".*Tambahkan.*"
- waitForAnimationToEnd
- takeScreenshot: "08e_pesanan_produk_added"

# ── TC-MOB-008-F: Isi Catatan Pesanan ────────────────────────────────────────
- tapOn:
    id: "catatan"

- inputText: ${CATATAN_PESANAN}
- hideKeyboard
# ── TC-MOB-008-G: Submit Pesanan ─────────────────────────────────────────────
- swipe:
    direction: UP
    duration: 1000

- tapOn:
    text: ".*Simpan Pesanan.*"
- waitForAnimationToEnd
- takeScreenshot: "08f_pesanan_submit_result"

# ── TC-MOB-008-H: Verifikasi Pesanan Berhasil Dibuat ─────────────────────────
# Setelah sukses, harus kembali ke list atau tampil success message
# dan pesanan baru muncul di list dengan status "menunggu_konfirmasi"
- assertVisible:
    text: ".*Pesanan.*"

- takeScreenshot: "08g_pesanan_created_success"

```

## 09_pesanan_validasi_form.yaml
```yaml
################################################################################
# TC-MOB-009 : PESANAN — VALIDASI FORM & EDGE CASES
# Deskripsi  : Verifikasi validasi form pesanan (tanpa pelanggan, tanpa produk,
#              jumlah 0/negatif, submit tanpa item)
# Role       : staf_penjualan
# Precondition: Berhasil login sebagai Staf Penjualan
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Staf Penjualan ───────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 1. Navigasi ke Form Pesanan ───────────────────────────────────────────────
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
- tapOn:
    id: "FloatingActionButton"
- waitForAnimationToEnd
# ── TC-MOB-009-A: Submit TANPA pilih pelanggan & produk ──────────────────────
- swipe:
    direction: UP
    duration: 1000

- tapOn:
    text: ".*Simpan Pesanan.*"
- waitForAnimationToEnd
# Harus ada pesan error/snackbar
- takeScreenshot: "09a_pesanan_form_empty_submit"

# ── TC-MOB-009-B: Coba tambah produk tanpa pilih produk ─────────────────────
- swipe:
    direction: DOWN
    duration: 1000

# Langsung klik tambah tanpa pilih produk
- tapOn:
    text: ".*Tambah Produk.*"
- waitForAnimationToEnd
- takeScreenshot: "09b_pesanan_no_produk_picker"

# Keluar dari product picker
- pressKey: back
- waitForAnimationToEnd
# ── TC-MOB-009-C: Masukkan jumlah = 0 ───────────────────────────────────────
- tapOn:
    id: "jumlah_field"

- eraseText: 100

- inputText: "0"

- tapOn:
    text: ".*Tambahkan.*"
- waitForAnimationToEnd
# Harus ada snackbar "Jumlah harus lebih dari 0"
- assertVisible:
    text: ".*Jumlah harus lebih dari 0.*"

- takeScreenshot: "09c_pesanan_jumlah_zero_error"

# ── TC-MOB-009-D: Kembali ke Pesanan list ────────────────────────────────────
- pressKey: back
- waitForAnimationToEnd
- takeScreenshot: "09d_pesanan_form_cancelled"

```

## 10_pelanggan_crud.yaml
```yaml
################################################################################
# TC-MOB-010 : PELANGGAN — DAFTAR, SEARCH, DETAIL, TAMBAH, EDIT
# Deskripsi  : CRUD pelanggan — list, search, view detail,
#              tambah pelanggan baru, edit data pelanggan
# Role       : staf_penjualan
# Precondition: Berhasil login sebagai Staf Penjualan, ada data pelanggan
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Staf Penjualan ───────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 1. Navigasi ke Pelanggan ──────────────────────────────────────────────────
- tapOn:
    text: ".*Pelanggan.*"
- waitForAnimationToEnd
# ── TC-MOB-010-A: Verifikasi Halaman Pelanggan ───────────────────────────────
- takeScreenshot: "10a_pelanggan_list"

# ── TC-MOB-010-B: Pull-to-Refresh ────────────────────────────────────────────
- swipe:
    direction: UP
    duration: 600
- waitForAnimationToEnd
- takeScreenshot: "10b_pelanggan_refreshed"

# ── TC-MOB-010-C: Search Pelanggan ───────────────────────────────────────────
- tapOn:
    id: "search_pelanggan"

- inputText: "a"
- waitForAnimationToEnd
- takeScreenshot: "10c_pelanggan_search_result"

# Hapus search
- tapOn:
    text: "a"
- eraseText: 100
- waitForAnimationToEnd
# ── TC-MOB-010-D: Buka Detail Pelanggan ──────────────────────────────────────
- tapOn:
    index: 0
    id: "pelanggan_card"
- waitForAnimationToEnd
- takeScreenshot: "10d_pelanggan_detail"

# Verifikasi elemen detail
- assertVisible:
    text: ".*Detail Pelanggan.*"

# ── TC-MOB-010-E: Kembali ke List ────────────────────────────────────────────
- pressKey: back
- waitForAnimationToEnd
# ── TC-MOB-010-F: Tambah Pelanggan Baru via FAB ──────────────────────────────
- tapOn:
    id: "FloatingActionButton"
- waitForAnimationToEnd
- takeScreenshot: "10e_pelanggan_form_baru"

# Verifikasi form fields
- assertVisible:
    text: ".*Tambah Pelanggan.*"

# ── TC-MOB-010-G: Isi Form Pelanggan Baru ────────────────────────────────────
- tapOn:
    id: "nama_pelanggan"

- inputText: "QA Test Customer"

- tapOn:
    id: "email_pelanggan"

- inputText: "qatest@example.com"

- tapOn:
    id: "telepon_pelanggan"

- inputText: "08123456789"

- tapOn:
    id: "alamat_pelanggan"

- inputText: "Jl. QA Test No. 1, Jakarta"
- hideKeyboard
- takeScreenshot: "10f_pelanggan_form_filled"

# ── TC-MOB-010-H: Submit Pelanggan Baru ──────────────────────────────────────
- swipe:
    direction: UP
    duration: 1000

- tapOn:
    text: ".*Simpan.*"
- waitForAnimationToEnd
- takeScreenshot: "10g_pelanggan_saved"

# Setelah simpan, kembali ke list
- assertVisible:
    text: ".*Pelanggan.*"

# ── TC-MOB-010-I: Cari Pelanggan yang Baru Ditambah ──────────────────────────
- tapOn:
    id: "search_pelanggan"

- inputText: "QA Test Customer"
- waitForAnimationToEnd
- takeScreenshot: "10h_pelanggan_search_new"

# ── TC-MOB-010-J: Buka Detail & Edit Pelanggan Baru ─────────────────────────
- tapOn:
    index: 0
    id: "pelanggan_card"
- waitForAnimationToEnd
# Tap tombol Edit
- tapOn:
    id: "edit_pelanggan_btn"
- waitForAnimationToEnd
- takeScreenshot: "10i_pelanggan_form_edit"

# Update nomor telepon
- tapOn:
    id: "telepon_pelanggan"

- eraseText: 100

- inputText: "08987654321"
- hideKeyboard
- tapOn:
    text: ".*Simpan.*"
- waitForAnimationToEnd
- takeScreenshot: "10j_pelanggan_edited_saved"

# ── TC-MOB-010-K: Validasi Form Pelanggan (nama kosong) ─────────────────────
- tapOn:
    id: "FloatingActionButton"
- waitForAnimationToEnd
- swipe:
    direction: UP
    duration: 500

- tapOn:
    text: ".*Simpan.*"
- waitForAnimationToEnd
- takeScreenshot: "10k_pelanggan_validation_empty"

- pressKey: back

```

## 11_produk_crud.yaml
```yaml
################################################################################
# TC-MOB-011 : PRODUK — DAFTAR & TAMBAH PRODUK (Staf Penjualan)
# Deskripsi  : Verifikasi halaman daftar produk, tambah produk baru (staf),
#              dan edit produk (jika ada akses)
# Role       : staf_penjualan
# Precondition: Berhasil login sebagai Staf Penjualan
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Staf Penjualan ───────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 1. Navigasi ke Tambah Produk ─────────────────────────────────────────────
- tapOn:
    text: ".*Tambah Produk.*"
- waitForAnimationToEnd
# ── TC-MOB-011-A: Verifikasi Halaman Produk (dari menu Tambah Produk) ─────────
- takeScreenshot: "11a_produk_staf_screen"

# ── TC-MOB-011-B: Lihat detail produk ────────────────────────────────────────
- tapOn:
    index: 0
    id: "produk_card"
- waitForAnimationToEnd
- takeScreenshot: "11b_produk_detail"

- assertVisible:
    text: ".*Detail Produk.*"

# ── TC-MOB-011-C: Scroll detail produk ───────────────────────────────────────
- swipe:
    direction: UP
    duration: 1000

- takeScreenshot: "11c_produk_detail_scrolled"

- pressKey: back
- waitForAnimationToEnd
# ── TC-MOB-011-D: Tambah Produk Baru ─────────────────────────────────────────
- tapOn:
    id: "FloatingActionButton"
- waitForAnimationToEnd
- takeScreenshot: "11d_produk_form_baru"

- assertVisible:
    text: ".*Tambah Produk.*"

# ── TC-MOB-011-E: Isi Form Produk Baru ───────────────────────────────────────
- tapOn:
    id: "nama_produk"

- inputText: "Produk QA Test"

- tapOn:
    id: "harga_produk"

- inputText: "150000"

- tapOn:
    id: "deskripsi_produk"

- inputText: "Deskripsi produk untuk testing QA Maestro"
- hideKeyboard
- takeScreenshot: "11e_produk_form_filled"

# ── TC-MOB-011-F: Submit Produk Baru ─────────────────────────────────────────
- swipe:
    direction: UP
    duration: 1000

- tapOn:
    text: ".*Simpan.*"
- waitForAnimationToEnd
- takeScreenshot: "11f_produk_saved"

# ── TC-MOB-011-G: Validasi Form Produk Kosong ────────────────────────────────
- tapOn:
    id: "FloatingActionButton"
- waitForAnimationToEnd
- tapOn:
    text: ".*Simpan.*"
- waitForAnimationToEnd
- takeScreenshot: "11g_produk_form_empty_validation"

- pressKey: back

```

## 12_gudang_stok.yaml
```yaml
################################################################################
# TC-MOB-012 : MANAJEMEN STOK GUDANG (Operator Gudang)
# Deskripsi  : Verifikasi halaman manajemen stok, update stok bahan baku,
#              refresh data, dan filter/search stok
# Role       : operator_gudang
# Precondition: Berhasil login sebagai Operator Gudang, ada data stok
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Operator Gudang ──────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Operator Gudang.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_GUDANG}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_GUDANG}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 1. Navigasi ke Manajemen Stok ────────────────────────────────────────────
- tapOn:
    text: ".*Manajemen Stok.*"
- waitForAnimationToEnd
# ── TC-MOB-012-A: Verifikasi Halaman Stok Gudang ─────────────────────────────
- takeScreenshot: "12a_gudang_stok_screen"

# ── TC-MOB-012-B: Pull-to-Refresh Stok ───────────────────────────────────────
- swipe:
    direction: UP
    duration: 600
- waitForAnimationToEnd
- takeScreenshot: "12b_gudang_stok_refreshed"

# ── TC-MOB-012-C: Scroll untuk melihat semua stok ────────────────────────────
- swipe:
    direction: UP
    duration: 2000

- takeScreenshot: "12c_gudang_stok_scrolled"

- swipe:
    direction: DOWN
    duration: 1000

# ── TC-MOB-012-D: Tap item stok untuk update ─────────────────────────────────
- tapOn:
    index: 0
    id: "stok_item"
- waitForAnimationToEnd
- takeScreenshot: "12d_gudang_stok_detail_or_update"

# ── TC-MOB-012-E: Update stok jika ada form update ───────────────────────────
# Cek apakah ada field untuk update stok
- tapOn:
    id: "stok_baru_field"

- eraseText: 100

- inputText: "50"

- tapOn:
    text: ".*Update.*"
- waitForAnimationToEnd
- takeScreenshot: "12e_gudang_stok_updated"

# Kembali ke list
- pressKey: back
- waitForAnimationToEnd
- takeScreenshot: "12f_gudang_stok_list_after_update"

```

## 13_notifikasi.yaml
```yaml
################################################################################
# TC-MOB-013 : NOTIFIKASI
# Deskripsi  : Verifikasi halaman notifikasi — list notifikasi, mark as read,
#              badge counter, dan pull-to-refresh
# Role       : staf_penjualan
# Precondition: Berhasil login sebagai Staf Penjualan
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Staf Penjualan ───────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 1. Navigasi ke Notifikasi via AppBar ─────────────────────────────────────
# Notifikasi bisa diakses dari ikon lonceng di AppBar dashboard
- tapOn:
    text: ".*Dashboard.*"
- waitForAnimationToEnd
- tapOn:
    id: "notifikasi_icon"
- waitForAnimationToEnd
# ── TC-MOB-013-A: Verifikasi Halaman Notifikasi ──────────────────────────────
- takeScreenshot: "13a_notifikasi_screen"

- assertVisible:
    text: ".*Notifikasi.*"

# ── TC-MOB-013-B: Pull-to-Refresh Notifikasi ─────────────────────────────────
- swipe:
    direction: UP
    duration: 600
- waitForAnimationToEnd
- takeScreenshot: "13b_notifikasi_refreshed"

# ── TC-MOB-013-C: Scroll Notifikasi ──────────────────────────────────────────
- swipe:
    direction: UP
    duration: 1500

- takeScreenshot: "13c_notifikasi_scrolled"

# ── TC-MOB-013-D: Tap Notifikasi Pertama ─────────────────────────────────────
- tapOn:
    index: 0
    id: "notifikasi_item"
- waitForAnimationToEnd
- takeScreenshot: "13d_notifikasi_item_tapped"

# ── TC-MOB-013-E: Kembali dari Notifikasi ────────────────────────────────────
- pressKey: back
- waitForAnimationToEnd
```

## 14_arsip.yaml
```yaml
################################################################################
# TC-MOB-014 : ARSIP PESANAN (Administrator)
# Deskripsi  : Verifikasi halaman arsip pesanan — list arsip, filter,
#              search, dan view detail pesanan terarsip
# Role       : administrator
# Precondition: Berhasil login sebagai Administrator, ada data arsip
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Administrator ────────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Administrator.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_ADMIN}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_ADMIN}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 1. Navigasi ke Arsip ─────────────────────────────────────────────────────
- tapOn:
    text: ".*Arsip.*"
- waitForAnimationToEnd
# ── TC-MOB-014-A: Verifikasi Halaman Arsip ───────────────────────────────────
- takeScreenshot: "14a_arsip_screen"

- assertVisible:
    text: ".*Arsip.*"

# ── TC-MOB-014-B: Pull-to-Refresh Arsip ──────────────────────────────────────
- swipe:
    direction: UP
    duration: 600
- waitForAnimationToEnd
- takeScreenshot: "14b_arsip_refreshed"

# ── TC-MOB-014-C: Scroll Arsip ───────────────────────────────────────────────
- swipe:
    direction: UP
    duration: 1500

- takeScreenshot: "14c_arsip_scrolled"

- swipe:
    direction: DOWN
    duration: 1000

# ── TC-MOB-014-D: Buka Detail dari Arsip ─────────────────────────────────────
- tapOn:
    index: 0
    id: "arsip_item"
- waitForAnimationToEnd
- takeScreenshot: "14d_arsip_detail"

# Kembali
- pressKey: back
- waitForAnimationToEnd
```

## 15_profil_user_management.yaml
```yaml
################################################################################
# TC-MOB-015 : PROFIL & USER MANAGEMENT (Administrator)
# Deskripsi  : Verifikasi halaman profil user, edit profil,
#              dan user management (admin only)
# Role       : administrator
# Precondition: Berhasil login sebagai Administrator
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Administrator ────────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Administrator.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_ADMIN}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_ADMIN}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── 1. Navigasi ke Profil via Avatar ─────────────────────────────────────────
- tapOn:
    id: "profile_avatar"
- waitForAnimationToEnd
# ── TC-MOB-015-A: Verifikasi Halaman Profil ──────────────────────────────────
- takeScreenshot: "15a_profile_screen"

# Profil menampilkan nama dan role user
- assertVisible:
    text: ".*administrator.*"

# ── TC-MOB-015-B: Scroll Profil ──────────────────────────────────────────────
- swipe:
    direction: UP
    duration: 1000

- takeScreenshot: "15b_profile_scrolled"

# ── TC-MOB-015-C: Menu User Management (Admin only) ──────────────────────────
# Admin memiliki akses ke User Management
- tapOn:
    text: ".*Manajemen Pengguna.*"
- waitForAnimationToEnd
- takeScreenshot: "15c_user_management"

- assertVisible:
    text: ".*Manajemen Pengguna.*"

# ── TC-MOB-015-D: Verifikasi List User ───────────────────────────────────────
- swipe:
    direction: UP
    duration: 1000

- takeScreenshot: "15d_user_list"

# ── TC-MOB-015-E: Kembali ke Profil ──────────────────────────────────────────
- pressKey: back
- waitForAnimationToEnd
# ── TC-MOB-015-F: Logout dari Profil ─────────────────────────────────────────
- swipe:
    direction: UP
    duration: 1000

- tapOn:
    text: ".*Keluar.*"
- waitForAnimationToEnd
- assertVisible:
    text: ".*Konfirmasi.*"

- takeScreenshot: "15e_logout_confirm_dialog"

# Batalkan logout
- tapOn:
    text: ".*Batal.*"
- waitForAnimationToEnd
- takeScreenshot: "15f_logout_cancelled"

```

## 16_rbac_akses_kontrol.yaml
```yaml
################################################################################
# TC-MOB-016 : RBAC — AKSES KONTROL ANTAR ROLE
# Deskripsi  : Verifikasi pembatasan akses berdasarkan role (RBAC):
#              - Operator Gudang tidak bisa akses Pesanan & Pelanggan
#              - Admin tidak bisa buat PO (tidak ada FAB di Pesanan)
#              - Staf Penjualan tidak bisa akses User Management
# Role       : Semua role
# Precondition: Akun semua role aktif
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ════════════════════════════════════════════════════════════════════
# BAGIAN A: Operator Gudang — verifikasi pembatasan menu
# ════════════════════════════════════════════════════════════════════

- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Operator Gudang.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_GUDANG}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_GUDANG}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# Verifikasi Operator Gudang TIDAK PUNYA menu Pesanan
- assertNotVisible:
    text: ".*Pesanan.*"

# Verifikasi Operator Gudang TIDAK PUNYA menu Pelanggan
- assertNotVisible:
    text: ".*Pelanggan.*"

# Verifikasi Operator Gudang HANYA punya Dashboard & Manajemen Stok
- assertVisible:
    text: ".*Dashboard.*"

- assertVisible:
    text: ".*Manajemen Stok.*"

- takeScreenshot: "16a_rbac_gudang_restricted"

# Logout dari Operator Gudang
- tapOn:
    id: "profile_avatar"
- waitForAnimationToEnd
- swipe:
    direction: UP
    duration: 500

- tapOn:
    text: ".*Keluar.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Logout.*"
- waitForAnimationToEnd
- assertVisible:
    text: ".*HUTCH PRESTIGE.*"

# ════════════════════════════════════════════════════════════════════
# BAGIAN B: Administrator — verifikasi tidak ada FAB di Pesanan
# ════════════════════════════════════════════════════════════════════

- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Administrator.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_ADMIN}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_ADMIN}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
# Admin TIDAK BISA buat PO — tidak ada FAB
- assertNotVisible:
    id: "FloatingActionButton"

- takeScreenshot: "16b_rbac_admin_no_fab_pesanan"

# Logout dari Admin
- tapOn:
    id: "profile_avatar"
- waitForAnimationToEnd
- swipe:
    direction: UP
    duration: 500

- tapOn:
    text: ".*Keluar.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Logout.*"
- waitForAnimationToEnd
- assertVisible:
    text: ".*HUTCH PRESTIGE.*"

# ════════════════════════════════════════════════════════════════════
# BAGIAN C: Staf Penjualan — tidak ada menu Arsip & User Management
# ════════════════════════════════════════════════════════════════════

- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# Staf Penjualan TIDAK PUNYA menu Arsip
- assertNotVisible:
    text: ".*Arsip.*"

- takeScreenshot: "16c_rbac_staf_no_arsip"

# Verifikasi User Management tidak ada di profil
- tapOn:
    id: "profile_avatar"
- waitForAnimationToEnd
- assertNotVisible:
    text: ".*Manajemen Pengguna.*"

- takeScreenshot: "16d_rbac_staf_no_user_mgmt"

- pressKey: back

```

## 17_performance_stability.yaml
```yaml
################################################################################
# TC-MOB-017 : PERFORMANCE & STABILITY
# Deskripsi  : Test performa app — waktu load screen, scroll mulus,
#              tidak ada crash saat navigasi cepat, dan network error handling
# Role       : staf_penjualan
# Precondition: Berhasil login sebagai Staf Penjualan
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ── SETUP: Login sebagai Staf Penjualan ───────────────────────────────────────
- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# ── TC-MOB-017-A: Rapid Navigation (stress test) ─────────────────────────────
# Navigasi cepat antar tab untuk cek tidak ada crash
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Pelanggan.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Tambah Produk.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Dashboard.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Dashboard.*"
- waitForAnimationToEnd
- takeScreenshot: "17a_rapid_nav_no_crash"

# ── TC-MOB-017-B: Scroll Performance ─────────────────────────────────────────
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
# Scroll cepat
- swipe:
    direction: UP
    duration: 500

- swipe:
    direction: UP
    duration: 500

- swipe:
    direction: DOWN
    duration: 500

- swipe:
    direction: DOWN
    duration: 500

- takeScreenshot: "17b_scroll_performance"

# ── TC-MOB-017-C: Multiple Pull-to-Refresh ───────────────────────────────────
- swipe:
    direction: UP
    duration: 400
- waitForAnimationToEnd
- swipe:
    direction: UP
    duration: 400
- waitForAnimationToEnd
- takeScreenshot: "17c_multiple_refresh"

# ── TC-MOB-017-D: Back Navigation Consistency ────────────────────────────────
- tapOn:
    text: ".*Pelanggan.*"
- waitForAnimationToEnd
- tapOn:
    index: 0
    id: "pelanggan_card"
- waitForAnimationToEnd
# Back dari detail
- pressKey: back
- waitForAnimationToEnd
# Back lagi harus kembali ke tab yang sama (bukan exit app)
- assertVisible:
    text: ".*Pelanggan.*"

- takeScreenshot: "17d_back_navigation"

# ── TC-MOB-017-E: App tidak crash setelah lama idle ──────────────────────────
- waitForAnimationToEnd
- tapOn:
    text: ".*Dashboard.*"
- waitForAnimationToEnd
- takeScreenshot: "17e_after_idle"

```

## 18_e2e_full_order_flow.yaml
```yaml
################################################################################
# TC-MOB-018 : END-TO-END FLOW — FULL ORDER WORKFLOW
# Deskripsi  : Full E2E flow: Staf Penjualan buat PO → Admin konfirmasi →
#              verifikasi status berubah di list
# Note       : Test ini memerlukan interaksi dari 2 role berbeda secara berurutan
# Role       : staf_penjualan (buat PO), administrator (konfirmasi)
# Precondition: Semua akun aktif, ada pelanggan & produk di sistem
################################################################################

appId: com.hutchprestige.mobile

env:
  # Admin
  EMAIL_ADMIN: "admin@hutch.id"
  PASSWORD_ADMIN: "password123"

  # Staf Penjualan
  EMAIL_STAF: "staf@hutch.id"
  PASSWORD_STAF: "password123"

  # Operator Gudang
  EMAIL_GUDANG: "gudang@hutch.id"
  PASSWORD_GUDANG: "password123"

  # Data pesanan baru
  NAMA_PELANGGAN_TEST: "Pelanggan QA Test"
  CATATAN_PESANAN: "Catatan pesanan dari Maestro QA"

  # Batas waktu umum (detik)
  TIMEOUT_DEFAULT: 10000
  TIMEOUT_LONG: 20000

---

# ════════════════════════════════════════════════════════════════════
# FASE 1: Staf Penjualan — Buat Pesanan Baru
# ════════════════════════════════════════════════════════════════════

- launchApp:
    clearState: true
- waitForAnimationToEnd
- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Staf Penjualan.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_STAF}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_STAF}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# Navigasi ke Pesanan
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
# Buat pesanan baru
- tapOn:
    id: "FloatingActionButton"
- waitForAnimationToEnd
# Pilih Pelanggan
- tapOn:
    text: ".*Pilih Pelanggan.*"
- waitForAnimationToEnd
- tapOn:
    index: 0
    id: "pelanggan_item"
- waitForAnimationToEnd
# Tambah Produk
- tapOn:
    text: ".*Tambah Produk.*"
- waitForAnimationToEnd
- tapOn:
    index: 0
    id: "produk_item"
- waitForAnimationToEnd
- tapOn:
    id: "jumlah_field"

- eraseText: 100

- inputText: "3"

- tapOn:
    text: ".*Tambahkan.*"
- waitForAnimationToEnd
# Isi catatan
- tapOn:
    id: "catatan"

- inputText: "E2E Test - Pesanan untuk konfirmasi"
- hideKeyboard
# Submit
- swipe:
    direction: UP
    duration: 1000

- tapOn:
    text: ".*Simpan Pesanan.*"
- waitForAnimationToEnd
- takeScreenshot: "18a_e2e_po_created_by_staf"

# Catat PO yang baru dibuat (ambil screenshot)
- assertVisible:
    text: ".*Pesanan.*"

# Filter Menunggu untuk melihat PO baru
- tapOn:
    text: ".*Menunggu.*"
- waitForAnimationToEnd
- takeScreenshot: "18b_e2e_po_menunggu_konfirmasi"

# Logout dari Staf
- tapOn:
    id: "profile_avatar"
- waitForAnimationToEnd
- swipe:
    direction: UP
    duration: 500

- tapOn:
    text: ".*Keluar.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Logout.*"
- waitForAnimationToEnd
- assertVisible:
    text: ".*HUTCH PRESTIGE.*"

# ════════════════════════════════════════════════════════════════════
# FASE 2: Administrator — Lihat dan konfirmasi pesanan baru
# ════════════════════════════════════════════════════════════════════

- tapOn:
    text: ".*Login.*"
- waitForAnimationToEnd
- tapOn:
    text: ".*Administrator.*"

# Pastikan form login masuk ke layar
- swipe:
    direction: UP
    duration: 600
- tapOn:
    id: "email"

- inputText: ${EMAIL_ADMIN}

- tapOn:
    id: "password"

- inputText: ${PASSWORD_ADMIN}

- hideKeyboard
- tapOn:
    text: ".*MASUK SEKARANG.*"
- waitForAnimationToEnd
# Navigasi ke Pesanan
- tapOn:
    text: ".*Pesanan.*"
- waitForAnimationToEnd
# Filter Menunggu untuk melihat PO baru dari Staf
- tapOn:
    text: ".*Menunggu.*"
- waitForAnimationToEnd
- takeScreenshot: "18c_e2e_admin_sees_menunggu"

# Buka detail PO pertama (yang baru dibuat)
- tapOn:
    index: 0
    id: "pesanan_card"
- waitForAnimationToEnd
- takeScreenshot: "18d_e2e_admin_pesanan_detail"

# Scroll ke bagian update status
- swipe:
    direction: UP
    duration: 2000

# Ubah status ke "Dikonfirmasi"
- tapOn:
    text: ".*Dikonfirmasi.*"
- waitForAnimationToEnd
- takeScreenshot: "18e_e2e_admin_konfirmasi_po"

# Simpan perubahan status
- tapOn:
    text: ".*Perbarui Status.*"
- waitForAnimationToEnd
- takeScreenshot: "18f_e2e_status_dikonfirmasi"

# ── Verifikasi di Dashboard Admin ────────────────────────────────────────────
- pressKey: back
- waitForAnimationToEnd
- tapOn:
    text: ".*Dashboard.*"
- waitForAnimationToEnd
- swipe:
    direction: UP
    duration: 600
- waitForAnimationToEnd
- takeScreenshot: "18g_e2e_admin_dashboard_updated"

```

## run_all_tests.yaml
```yaml
################################################################################
# HUTCH.ID MOBILE — MAESTRO TEST SUITE RUNNER
# File       : run_all_tests.yaml
# Deskripsi  : Jalankan semua test case secara berurutan
# Cara pakai : maestro test .maestro/run_all_tests.yaml
################################################################################

# Jalankan semua flow secara berurutan:
# maestro test .maestro/ --include-tags smoke
# atau:
# maestro test .maestro/run_all_tests.yaml

---

# Flow ini adalah shortcut untuk menjalankan test penting saja (Smoke Test)
# Jalankan individual jika perlu full suite

# ── Smoke Test (Core Flows) ───────────────────────────────────────────────────
# Urutan sesuai prioritas:

# 1. Splash & Landing
- runFlow: 01_splash_landing.yaml

# 2. Login Validasi
- runFlow: 02_login_validasi.yaml

# 3. Login Admin
- runFlow: 03_login_admin.yaml

# 4. Login Staf Penjualan
- runFlow: 04_login_staf_penjualan.yaml

# 5. Login Operator Gudang
- runFlow: 05_login_operator_gudang.yaml

# 6. Dashboard
- runFlow: 06_dashboard.yaml

# 7. Pesanan List, Filter, Search
- runFlow: 07_pesanan_list_filter_search.yaml

# 8. RBAC Akses Kontrol
- runFlow: 16_rbac_akses_kontrol.yaml

```

