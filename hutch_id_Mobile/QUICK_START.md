# 🚀 Hutch ID Mobile - Quick Start Guide

## Langkah-Langkah Menjalankan Aplikasi

### 1️⃣ Persiapan Awal

**Requirements:**

- Flutter SDK 3.11.0 atau lebih tinggi
- Dart SDK
- Android Studio atau Xcode
- Emulator atau Physical Device
- Backend Laravel/Hutch ID Web sudah running di `http://localhost:8000`

### 2️⃣ Setup Aplikasi

```bash
# Masuk ke folder project
cd c:\xampp\htdocs\hutch_id_mobile\hutch_id_mobile_orderflow

# Install dependencies
flutter pub get

# Tunggu proses selesai...
```

### 3️⃣ Konfigurasi API URL

**Untuk Android Emulator:**

- File: `lib/config/app_config.dart`
- Ubah: `http://10.0.2.2:8000/api`
- Ini sudah default, jadi tidak perlu diubah

**Untuk Physical Device:**

- File: `lib/config/app_config.dart`
- Ubah menjadi: `http://[IP-LAPTOP]:8000/api`
- Contoh: `http://192.168.1.10:8000/api`
- Cari IP laptop Anda dengan: `ipconfig` (Windows) atau `ifconfig` (Mac/Linux)

**Untuk iOS Emulator:**

- File: `lib/config/app_config.dart`
- Ubah: `http://localhost:8000/api` atau IP lokal

### 4️⃣ Jalankan Aplikasi

```bash
# List available devices
flutter devices

# Run di emulator/device
flutter run

# Atau dengan mode release
flutter run --release
```

### 5️⃣ Testing Aplikasi

**Login Credentials:**

- Gunakan email dan password dari akun web Hutch ID
- Contoh: `admin@example.com` / `password`

**Testing Fitur:**

1. ✅ Dashboard: Lihat statistik pesanan
2. ✅ Pesanan: List, filter, detail
3. ✅ Pelanggan: List, buat baru, edit
4. ✅ Produk: List dengan grid view
5. ✅ Notifikasi: List notifikasi

## 🔧 Troubleshooting

### Error: "Cannot connect to API"

```
❌ Masalah: Koneksi ke backend gagal

✅ Solusi:
1. Pastikan backend Laravel running: http://localhost:8000
2. Cek IP configuration di app_config.dart
3. Pastikan no firewall blocking
4. Test API dengan Postman: http://localhost:8000/api/login
```

### Error: "Connection refused"

```
❌ Masalah: Emulator/device tidak bisa akses localhost

✅ Solusi:
1. Gunakan 10.0.2.2 untuk Android Emulator
2. Gunakan IP lokal untuk physical device
3. Cek IP dengan: ipconfig (Windows)
```

### Error: "Dependencies conflict"

```bash
# Solution:
flutter clean
flutter pub get
flutter pub upgrade
```

### Aplikasi crash saat login

```bash
# Cek logs:
flutter run -v

# atau gunakan Flutter DevTools:
flutter pub global activate devtools
devtools
```

## 📊 Struktur Aplikasi

```
┌─────────────────────────────────┐
│        LoginScreen              │  ← Entry point
└──────────────┬──────────────────┘
               │ (Login success)
               ↓
        ┌────────────────┐
        │   HomeScreen   │  ← Main navigation
        └────┬───┬───┬───┤
             │   │   │   └─ Notifikasi
             │   │   └───── Produk
             │   └───────── Pelanggan
             └───────────── Pesanan
                     │
                     ↓
             Dashboard (Default)
```

## 🔌 API Integration Points

### Setiap Screen Terhubung ke API:

```
LoginScreen
  └─ POST /api/login

DashboardScreen
  └─ GET /api/dashboard

PesananListScreen
  └─ GET /api/pesanan
  └─ PATCH /api/pesanan/{id}/status

PelangganListScreen
  └─ GET /api/pelanggan
  └─ POST /api/pelanggan
  └─ PUT /api/pelanggan/{id}
  └─ DELETE /api/pelanggan/{id}

ProdukListScreen
  └─ GET /api/produk

NotifikasiScreen
  └─ GET /api/notifikasi
```

## 🎯 Testing dengan Postman

### 1. Login

```
POST http://localhost:8000/api/login
Body (raw JSON):
{
  "email": "admin@example.com",
  "password": "password"
}
Response: { "token": "...", "user": {...} }
```

### 2. Get Dashboard (dengan token)

```
GET http://localhost:8000/api/dashboard
Header: Authorization: Bearer [TOKEN]
```

### 3. Get Pesanan

```
GET http://localhost:8000/api/pesanan
Header: Authorization: Bearer [TOKEN]
```

## 📱 Fitur yang Sudah Siap

✅ **Authentication**

- Login dengan email & password
- Token management
- Logout

✅ **Dashboard**

- Statistik pesanan
- Total nilai penjualan
- 4 status pesanan

✅ **Pesanan**

- List pesanan dengan filter status
- Pull-to-refresh
- Status badge dengan warna
- Detail view

✅ **Pelanggan**

- List pelanggan
- Create pelanggan
- View detail
- Edit pelanggan
- Delete pelanggan

✅ **Produk**

- List produk dengan grid view
- Product cards
- Image support

✅ **Notifikasi**

- List notifikasi
- Type indicators
- Timestamp
- New badge

## 🛠️ Customization

### Mengubah warna aplikasi

File: `lib/app.dart`

```dart
primarySwatch: Colors.blue,  // Ubah ke Colors.green, dll
```

### Mengubah URL API

File: `lib/config/app_config.dart`

```dart
static const String apiBaseUrl = 'http://10.0.2.2:8000/api';
```

### Menambah screen baru

1. Buat file di `screens/`
2. Tambahkan route di `app.dart`
3. Tambahkan tab di `home_screen.dart` (jika perlu)

### Menambah API call

1. Tambahkan method di `services/api_service.dart`
2. Buat provider di `providers/`
3. Consume provider di screen

## 📝 Development Tips

1. **Hot Reload**: Tekan `r` di terminal untuk quick reload
2. **Hot Restart**: Tekan `R` untuk restart aplikasi
3. **DevTools**: `flutter pub global activate devtools`
4. **Debugging**: Gunakan `print()` atau DevTools
5. **Testing**: Test setiap fitur dengan berbagai data

## ⚡ Performance Tips

- Gunakan `const` constructor
- Implement proper state management
- Cache data dengan SharedPreferences
- Optimize list dengan lazy loading
- Use images dengan proper sizing

## 🐛 Common Issues & Solutions

| Masalah           | Penyebab                | Solusi                    |
| ----------------- | ----------------------- | ------------------------- |
| API unreachable   | IP config salah         | Check app_config.dart     |
| Login fails       | Credentials salah       | Use web login credentials |
| Blank screen      | Provider initialization | Check app.dart setup      |
| Image not showing | URL invalid             | Check network image       |
| Slow performance  | Large list              | Implement pagination      |

## 📞 Next Steps

1. ✅ Run aplikasi dan test login
2. ✅ Explore setiap screen
3. ✅ Test create/edit/delete fitur
4. ✅ Check API integration di network tab
5. ✅ Customize sesuai kebutuhan

---

**Happy Coding! 🚀**
