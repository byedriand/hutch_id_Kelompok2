# 📋 Panduan Login & Setup Sistem hutch.id

## 🚀 Setup Database & Seeder

Jalankan command berikut untuk setup database dengan users dan data test:

```bash
php artisan migrate
php artisan db:seed
```

## 🔐 Test Credentials

Gunakan email dan password berikut untuk login. Pastikan memilih **role yang sesuai** saat login:

| Email            | Password    | Role            | Deskripsi                   |
| ---------------- | ----------- | --------------- | --------------------------- |
| admin@hutch.id   | password123 | Administrator   | Akses penuh ke semua fitur  |
| pemilik@hutch.id | password123 | Pemilik UMKM    | Manajer PO, owner           |
| staf@hutch.id    | password123 | Staf Penjualan  | Buat & lihat PO mereka      |
| gudang@hutch.id  | password123 | Operator Gudang | Update status produksi saja |

## 🎯 Fitur Login Baru

✨ **Halaman Login Modern & Responsif**

- Animated background dengan geometric shapes
- Role selection dropdown (4 role tersedia)
- Validasi role vs email otomatis
- Error message yang jelas dan helpful
- Fully responsive untuk mobile, tablet, desktop

## 🔑 Role-Based Permissions

### Administrator

- ✅ Akses penuh ke semua halaman
- ✅ Dashboard dengan data lengkap
- ✅ Buat, lihat, edit, update, batalkan PO
- ✅ Kelola pelanggan (CRUD)
- ✅ Lihat arsip PDF

### Pemilik UMKM

- ✅ Dashboard dengan data lengkap
- ✅ Buat PO baru
- ✅ Lihat & edit PO
- ✅ Update status: dalam_produksi → siap_kirim → selesai
- ✅ Batalkan PO
- ✅ Kelola pelanggan (CRUD)
- ✅ Lihat arsip PDF

### Staf Penjualan

- ✅ Dashboard (PO mereka saja)
- ✅ Buat PO baru
- ✅ Lihat PO yang mereka buat
- ✅ Download PDF
- ❌ Edit/batalkan PO
- ❌ Update status
- ❌ Kelola pelanggan
- ❌ Lihat arsip

### Operator Gudang

- ✅ Dashboard (PO yang dikonfirmasi ke atas saja)
- ✅ Lihat daftar & detail PO
- ✅ Update status ke "dalam_produksi" SAJA
- ❌ Buat/edit PO
- ❌ Batalkan PO
- ❌ Kelola pelanggan

## 🔄 Login Flow

1. Buka halaman login: `http://localhost:8000/login`
2. **Pilih role** yang sesuai dengan akun Anda
3. Masukkan **email**
4. Masukkan **password**
5. Klik **Masuk Sekarang**

⚠️ **PENTING**: Role yang dipilih HARUS sesuai dengan role di database!

Jika role tidak sesuai, sistem akan menampilkan error:

```
"Role tidak sesuai dengan akun [email]. Akun ini adalah: [Role Sebenarnya]"
```

## 📱 Responsive Design

Halaman login sudah optimized untuk:

- ✅ Desktop (1920px+)
- ✅ Laptop (1024px)
- ✅ Tablet (768px - 1023px)
- ✅ Mobile (320px - 767px)

## 🎨 UI Improvements

**Login Page Features:**

- Modern gradient background
- Animated floating shapes
- Smooth transitions & animations
- Icon integration (Font Awesome)
- Error message dengan animation
- Responsive padding & spacing
- Touch-friendly buttons untuk mobile
- Larger font size pada mobile untuk accessibility

## 🔒 Security Notes

- Passwords hashed dengan bcrypt
- CSRF protection aktif
- Role validation di server-side
- Session berbasis cookie
- Logout manual tersedia

## 📝 Troubleshooting

**Q: Login gagal padahal email & password benar**
A: Pastikan role yang dipilih SESUAI dengan role user di database. Cek table users untuk confirm.

**Q: Migrasi gagal**
A: Pastikan sudah jalankan `php artisan migrate` terlebih dahulu

**Q: Seeder tidak membuat data**
A: Jalankan `php artisan db:seed` atau `php artisan migrate:fresh --seed`

## 🚀 Next Steps

1. Customize warna/branding sesuai kebutuhan (edit CSS di login.blade.php)
2. Tambah logo custom di folder `public/images/`
3. Configure email settings untuk password reset (optional)
4. Set up backup automation untuk database

---

**Created:** May 13, 2026  
**Version:** 1.0  
**Last Updated:** Modern Login UI + Role Validation
