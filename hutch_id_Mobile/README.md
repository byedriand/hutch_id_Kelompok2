# hutch.id — Modul Manajemen Pesanan (Flutter)

Aplikasi mobile Flutter untuk Modul Purchase Order (PO) hutch.id.
Dibuat sesuai spesifikasi SRS Kelompok 2.

---

## Struktur Proyek

```
lib/
├── main.dart                          # Entry point
├── theme/
│   └── app_theme.dart                 # Design tokens, ThemeData
├── models/
│   └── models.dart                    # Data class + dummy data
├── widgets/
│   └── widgets.dart                   # Komponen reusable
└── screens/
    ├── login_screen.dart              # UI-01 — Login
    ├── main_screen.dart               # Bottom Navigation Shell
    ├── dashboard_screen.dart          # UI-02 — Dashboard PO
    ├── order_list_screen.dart         # UI-04 — Daftar Pesanan
    ├── order_detail_screen.dart       # UI-05 — Detail Pesanan + Histori Status
    ├── combined_screens.dart          # UI-03, UI-06, UI-07 (Create PO, Pelanggan, PDF)
    ├── create_po_screen.dart          # UI-03 — Formulir Buat PO Baru
    ├── customer_screen.dart           # UI-06 — Manajemen Pelanggan
    └── pdf_preview_screen.dart        # UI-07 — Preview & Unduh PDF
```

---

## Halaman yang Tersedia

| Kode  | Halaman                | File                         | REQ SRS                  |
|-------|------------------------|------------------------------|--------------------------|
| UI-01 | Login                  | login_screen.dart            | 3.3, 3.4                 |
| UI-02 | Dashboard PO           | dashboard_screen.dart        | REQ-PO-023, REQ-PO-024   |
| UI-03 | Formulir Buat PO Baru  | create_po_screen.dart        | REQ-PO-001 s/d 007       |
| UI-04 | Daftar Pesanan         | order_list_screen.dart       | REQ-PO-023               |
| UI-05 | Detail Pesanan         | order_detail_screen.dart     | REQ-PO-009, 013, 016     |
| UI-06 | Manajemen Pelanggan    | customer_screen.dart         | REQ-PO-026               |
| UI-07 | Preview & Unduh PDF    | pdf_preview_screen.dart      | REQ-PO-019 s/d 022       |

---

## Cara Menjalankan

### Prasyarat
- Flutter SDK 3.x (https://flutter.dev/docs/get-started/install)
- Dart SDK 3.0+
- Android Studio / VS Code dengan Flutter extension
- Android emulator / iOS simulator / perangkat fisik

### Langkah

```bash
# 1. Masuk ke folder proyek
cd hutch_po

# 2. Install dependencies
flutter pub get

# 3. Jalankan di emulator/device
flutter run

# 4. Build APK release (Android)
flutter build apk --release

# 5. Build untuk iOS (hanya di macOS)
flutter build ios --release
```

---

## Dependencies

```yaml
google_fonts: ^6.1.0      # Plus Jakarta Sans + Fira Code
flutter_svg:  ^2.0.9      # SVG support
intl:         ^0.19.0     # Format tanggal & currency
```

---

## Desain

- **Primary font**: Plus Jakarta Sans (Google Fonts)
- **Monospace font**: Fira Code (untuk nomor PO, kode)
- **Color palette**:
  - Navy `#0F2744` — sidebar, appbar, PDF header
  - Accent Blue `#2D7DD2` — CTA, links, focus
  - Background `#F0F4FA` — scaffold background
- **Design pattern**: Material 3 + custom card style
- **Target device**: Mobile portrait (360dp minimum)

---

## Catatan Developer

1. Data dummy ada di `lib/models/models.dart` — `dummyOrders` dan `dummyCustomers`
2. Untuk integrasi API nyata, ganti data dummy dengan HTTP calls ke endpoint yang sudah didefinisikan di BAB 3.3 SRS
3. Autentikasi menggunakan session-based (lihat BAB 3.3 SRS) — implementasikan dengan `shared_preferences` atau `flutter_secure_storage`
4. PDF generation dapat menggunakan package `pdf` (pub.dev/packages/pdf) + `printing`
5. Semua warna dan typography terpusat di `lib/theme/app_theme.dart`
