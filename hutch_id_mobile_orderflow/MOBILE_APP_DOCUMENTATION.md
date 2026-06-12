# Hutch ID Mobile - Dokumentasi Aplikasi

## 📱 Deskripsi Aplikasi

Hutch ID Mobile adalah aplikasi Flutter yang dikembangkan untuk melengkapi Hutch ID Web dengan fitur manajemen pesanan, pelanggan, dan produk yang dapat diakses dari perangkat mobile.

## ✨ Fitur Utama

- **📊 Dashboard**: Ringkasan statistik pesanan dan penjualan
- **📦 Pesanan**: Lihat, buat, dan kelola pesanan dengan filter status
- **👥 Pelanggan**: Kelola data pelanggan dengan CRUD operations
- **🛍️ Produk**: Lihat daftar produk dengan detail lengkap
- **🔔 Notifikasi**: Terima notifikasi real-time tentang pesanan dan aktivitas
- **🔐 Authentication**: Sistem login yang aman menggunakan token Sanctum

## 🏗️ Struktur Proyek

```
lib/
├── main.dart                 # Entry point aplikasi
├── app.dart                  # Konfigurasi app dan providers
├── config/
│   └── app_config.dart      # Konfigurasi API dan konstanta
├── models/
│   ├── user.dart            # Model User
│   ├── pesanan.dart         # Model Pesanan (Order)
│   ├── pelanggan.dart       # Model Pelanggan (Customer)
│   ├── produk.dart          # Model Produk
│   ├── arsip_pdf.dart       # Model Arsip PDF
│   ├── notifikasi.dart      # Model Notifikasi
│   └── dashboard.dart       # Model Dashboard
├── services/
│   └── api_service.dart     # Service untuk komunikasi API
├── providers/
│   ├── auth_provider.dart            # State management auth
│   ├── dashboard_provider.dart       # State management dashboard
│   ├── pesanan_provider.dart         # State management pesanan
│   ├── pelanggan_provider.dart       # State management pelanggan
│   ├── produk_provider.dart          # State management produk
│   └── notifikasi_provider.dart      # State management notifikasi
├── screens/
│   ├── auth/
│   │   └── login_screen.dart         # Layar login
│   ├── home/
│   │   ├── home_screen.dart          # Layar utama dengan bottom nav
│   │   └── dashboard_screen.dart     # Layar dashboard
│   ├── pesanan/
│   │   └── pesanan_list_screen.dart  # Daftar pesanan
│   ├── pelanggan/
│   │   └── pelanggan_list_screen.dart # Daftar pelanggan
│   ├── produk/
│   │   └── produk_list_screen.dart   # Daftar produk
│   ├── notifikasi/
│   │   └── notifikasi_screen.dart    # Daftar notifikasi
│   └── arsip/                        # (Untuk ekspansi di masa depan)
└── widgets/
    └── custom_widgets.dart  # Widget reusable
```

## 🔧 Teknologi yang Digunakan

- **Flutter 3.11+**: Framework UI cross-platform
- **Provider**: State management
- **HTTP**: Request API
- **Shared Preferences**: Local storage
- **Intl**: Formatting tanggal dan angka
- **Shimmer**: Loading animation
- **Image Picker**: Pemilihan gambar

## 🚀 Instalasi dan Setup

### 1. Prerequisites

- Flutter SDK 3.11.0 atau lebih tinggi
- Dart SDK yang sesuai dengan Flutter
- Android Studio / Xcode untuk emulator

### 2. Clone Repository

```bash
cd hutch_id_mobile_orderflow
```

### 3. Install Dependencies

```bash
flutter pub get
```

### 4. Konfigurasi API URL

Edit `lib/config/app_config.dart`:

- Untuk **Android Emulator**: Gunakan `http://10.0.2.2:8000/api`
- Untuk **Physical Device**: Ganti dengan IP lokal server: `http://192.168.1.X:8000/api`
- Untuk **iOS Emulator**: Gunakan `http://localhost:8000/api` atau IP lokal

### 5. Run Aplikasi

```bash
flutter run
```

## 📱 Fitur Detil

### 🔐 Login

- Email dan password dari akun web Hutch ID
- Token disimpan secara aman di local storage
- Auto-logout jika token expired

### 📊 Dashboard

- Total pesanan aktif
- Total pesanan menunggu
- Total pesanan siap kirim
- Total pesanan selesai bulan ini
- Nilai total penjualan bulan ini

### 📦 Pesanan

- **List View**: Lihat semua pesanan dengan filter status
- **Filter**: Aktif, Menunggu, Siap Kirim, Selesai, Batal
- **Status Badge**: Tampilan status dengan warna berbeda
- **Refresh**: Pull-to-refresh untuk update data terbaru
- **Detail**: Lihat detail lengkap setiap pesanan

### 👥 Pelanggan

- **CRUD Operations**: Create, Read, Update, Delete pelanggan
- **Contact Info**: Nomor HP, email, alamat
- **Location**: Kota, provinsi, kode pos
- **Tracking**: Tanggal pembuatan data
- **Actions**: View, Edit, Delete dengan mudah

### 🛍️ Produk

- **Grid View**: Tampilan produk dalam bentuk grid
- **Product Card**: Nama, kategori, harga, stok
- **Image**: Tampil gambar produk (jika ada)
- **Refresh**: Update data produk terbaru

### 🔔 Notifikasi

- **Real-time**: Notifikasi terbaru di atas
- **Type Badge**: Indikator tipe notifikasi
- **Timestamp**: Waktu notifikasi
- **New Badge**: Tanda notifikasi baru

## 🔌 API Integration

### Authentication Endpoints

```
POST   /api/login              # Login
POST   /api/logout             # Logout
GET    /api/profile            # Ambil profile user
GET    /api/user               # Ambil data user
```

### Pesanan Endpoints

```
GET    /api/pesanan            # List pesanan (dengan filter)
POST   /api/pesanan            # Buat pesanan
GET    /api/pesanan/{id}       # Detail pesanan
PUT    /api/pesanan/{id}       # Update pesanan
DELETE /api/pesanan/{id}       # Hapus pesanan
PATCH  /api/pesanan/{id}/status # Update status pesanan
```

### Pelanggan Endpoints

```
GET    /api/pelanggan          # List pelanggan
POST   /api/pelanggan          # Buat pelanggan
GET    /api/pelanggan/{id}     # Detail pelanggan
PUT    /api/pelanggan/{id}     # Update pelanggan
DELETE /api/pelanggan/{id}     # Hapus pelanggan
GET    /api/pelanggan/search   # Cari pelanggan
```

### Produk Endpoints

```
GET    /api/produk             # List produk
GET    /api/produk/{id}        # Detail produk
```

### Arsip PDF Endpoints

```
GET    /api/arsip-pdf          # List arsip
GET    /api/arsip-pdf/{id}     # Detail arsip
DELETE /api/arsip-pdf/{id}     # Hapus arsip
```

### Dashboard Endpoints

```
GET    /api/dashboard          # Data dashboard
```

### Notifikasi Endpoints

```
GET    /api/notifikasi         # List notifikasi
```

## 🔐 Authentication Flow

1. User memasukkan email & password di login screen
2. Aplikasi mengirim request POST ke `/api/login`
3. Backend mengembalikan token Sanctum
4. Token disimpan di SharedPreferences
5. Token otomatis ditambahkan ke header setiap request selanjutnya
6. Jika token expired, user perlu login kembali

## 📦 State Management dengan Provider

Aplikasi menggunakan Provider untuk state management dengan pola:

```dart
// Provider deklarasi
ChangeNotifierProvider(create: (_) => AuthProvider())

// Consume provider
Consumer<AuthProvider>(
  builder: (context, authProvider, _) {
    // Build UI berdasarkan state
  }
)

// Update state
Provider.of<AuthProvider>(context, listen: false).login(email, password)
```

## 🎨 UI/UX Design

- **Material Design 3**: Menggunakan komponen Material terbaru
- **Color Scheme**: Primary blue dengan aksen warna untuk status
- **Responsive**: Adaptif di berbagai ukuran layar
- **Loading States**: Shimmer loading untuk UX yang baik
- **Error Handling**: Pesan error yang jelas dan actionable

## 🐛 Error Handling

- Try-catch di setiap API call
- Error message ditampilkan ke user
- Retry button untuk refresh data
- Graceful fallback jika data tidak tersedia

## 📱 Build untuk Production

### Android

```bash
flutter build apk --release
```

### iOS

```bash
flutter build ios --release
```

## 🔄 Sync Data dengan Web

Data di mobile app selalu sync dengan web backend:

- Setiap aksi di mobile langsung update di database
- Pull-to-refresh untuk update data terbaru
- Real-time notification jika ada perubahan

## ⚠️ Troubleshooting

### API Connection Error

- Cek konfigurasi IP di `app_config.dart`
- Pastikan backend Laravel running
- Cek CORS configuration di Laravel

### Build Error

```bash
flutter clean
flutter pub get
flutter pub upgrade
```

### Plugin Issues

```bash
flutter pub cache repair
flutter pub get
```

## 📝 Notes untuk Development

- Semua API calls menggunakan `ApiService` singleton
- Provider pattern digunakan untuk state management
- Models menggunakan JSON serialization
- Setiap screen adalah StatefulWidget untuk lifecycle management
- Custom widgets di `custom_widgets.dart` untuk reusability

## 🚀 Future Enhancements

- [ ] Detail screen untuk Pesanan (dengan edit & status update)
- [ ] Form screen untuk Pelanggan (dengan image upload)
- [ ] Detail screen untuk Produk
- [ ] Download PDF dari Arsip
- [ ] Push notifications
- [ ] Offline mode dengan local database
- [ ] Search dan filter advanced
- [ ] Export data ke Excel/PDF
- [ ] Dark mode support
- [ ] Multi-language support

## 📞 Support

Jika ada pertanyaan atau issue, silakan hubungi developer.

---

**Version**: 1.0.0  
**Last Updated**: 2024  
**Status**: ✅ Ready for Development
