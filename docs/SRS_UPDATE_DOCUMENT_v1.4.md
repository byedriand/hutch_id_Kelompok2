# 📄 DOKUMEN UPDATE SRS v1.4 - JUNI 2026

**Tanggal**: 06 Juni 2026  
**Update oleh**: Adrian Ronald Daga (20241320011)  
**Status**: Ready for SRS v1.4 Approval

---

## 📋 RINGKASAN UPDATE

Dokumen ini mendetail perubahan yang dilakukan pada SRS Hutch.id dari **Versi 1.3 (April 2026)** menjadi **Versi 1.4 (Juni 2026)**.

**Fitur utama yang ditambahkan:**

- ✨ Menu "Tambah Produk" untuk Staff Penjualan
- 📸 Upload dan preview foto produk
- 🎨 Grid display produk responsive
- 🔔 Notifikasi otomatis produk baru
- 🐛 Bug fixes untuk image display

---

## 📝 PERUBAHAN DETAIL

### 1. HALAMAN JUDUL & METADATA

**Sebelum:**

```
SOFTWARE REQUIREMENTS SPECIFICATION (SRS)
IEEE 830 · Versi 1.3 · April 2026
```

**Sesudah:**

```
SOFTWARE REQUIREMENTS SPECIFICATION (SRS)
IEEE 830 · Versi 1.4 · Juni 2026
```

---

### 2. RIWAYAT REVISI (Table di awal dokumen)

**Tambahkan baris baru:**

| Versi   | Tanggal      | Deskripsi Perubahan                                 | Penulis         |
| ------- | ------------ | --------------------------------------------------- | --------------- |
| 1.3     | Apr 2026     | Versi dasar - Sistem Manajemen Pesanan (PO)         | Kelompok 2      |
| **1.4** | **Jun 2026** | **Tambah fitur Manajemen Produk Staff & Bug Fixes** | **Adrian Daga** |

---

### 3. BAB I - PENDAHULUAN (1.1 Tujuan Dokumen)

**Tambahkan ke daftar fungsi inti:**

```
Sebelumnya 4 fungsi:
- Penerimaan order dari pelanggan
- Verifikasi ketersediaan bahan baku
- Manajemen status produksi
- Pencetakan dokumen PO ke PDF

MENJADI 5 fungsi:
- Penerimaan order dari pelanggan
- Verifikasi ketersediaan bahan baku
- Manajemen status produksi
- Pencetakan dokumen PO ke PDF
- Manajemen produk oleh Staff Penjualan ✨ (BARU)
```

---

### 4. BAB II - DESKRIPSI UMUM (2.2 Fungsi Utama Modul)

**Update list fungsi:**

Tambahkan fungsi baru setelah "Pencetakan Dokumen PO ke PDF":

```markdown
#### 5. Manajemen Produk oleh Staff Penjualan

- Staff Penjualan dapat menambahkan produk baru ke katalog sistem
- Form input: nama, harga, stok awal, keterangan, upload foto
- Upload foto dengan preview instant (JPG/PNG, max 5MB)
- Produk ditampilkan dalam grid responsive
- Notifikasi otomatis ke semua user roles saat produk baru ditambahkan
- Produk tersedia untuk dipilih saat membuat Purchase Order baru
```

---

### 5. BAB III - ANTARMUKA EKSTERNAL (3.1 Antarmuka Pengguna)

**Tambahkan halaman baru:**

```markdown
#### Halaman Tambah Produk (BARU)

- Route: /produk/staf/tambah
- Access: Staff Penjualan only (role:staf_penjualan)
- Komponen:
  1. Form input produk (kolom kiri - 2 kolom layout)
     - Input nama produk
     - Input harga jual (currency formatting)
     - Input stok awal
     - Textarea keterangan
     - File upload foto (dengan preview live)
     - Submit button
  2. Grid daftar produk (kolom kanan)
     - Card design responsive
     - Tampilkan: foto, nama, harga, stok, keterangan
     - Badge stok dengan warna indicator
     - Hover animation

#### Halaman Edit Produk (BARU)

- Route: /produk/{id}/edit
- Access: Staff Penjualan & Administrator
- Form update produk dengan foto existing
```

---

### 6. BAB IV - FITUR DAN PERSYARATAN FUNGSIONAL (Section 4.6 BARU)

**Tambahkan section baru setelah 4.5:**

```markdown
## 4.6 Manajemen Produk oleh Staff Penjualan

### 4.6.1 Deskripsi dan Prioritas

Modul Manajemen Produk memungkinkan Staff Penjualan menambahkan produk baru
ke katalog sistem. Produk yang ditambahkan akan tersedia untuk dipilih saat
membuat Purchase Order baru dan ditampilkan dalam daftar produk untuk referensi.

Fitur ini meningkatkan fleksibilitas sistem dalam mengelola katalog produk
tanpa perlu intervensi Administrator.

**Prioritas**: SEDANG (dilaksanakan setelah core PO features)

### 4.6.2 Alur Stimulus / Respons

**Alur Utama:**
```

1. Staff Penjualan membuka menu "Tambah Produk" dari sidebar
2. Sistem menampilkan halaman dengan form + grid daftar produk existing
3. Staff mengisi form:
   - Nama produk (text, unique validation)
   - Harga jual (currency format Rp)
   - Stok awal (integer ≥ 0)
   - Keterangan (textarea)
   - Upload foto (JPG/PNG, max 5MB)
4. Staff melihat preview foto secara real-time
5. Staff submit form
6. Sistem validasi data:
   - Nama unique di database
   - Harga > 0
   - Foto format dan ukuran valid
7. Jika valid:
   - Sistem simpan produk ke database
   - Upload foto ke storage/public/produk
   - Generate notifikasi "produk_baru" ke semua roles
   - Log action di audit trail
   - Tampilkan success message
   - Refresh grid produk
8. Jika tidak valid:
   - Tampilkan error message per field
   - Fokus ke field dengan error

```

**Alur Alternatif:**
- Staff upload foto terlalu besar → sistem tampilkan peringatan + reject upload
- Staff input nama duplikat → sistem tampilkan error "Produk sudah terdaftar"

### 4.6.3 Spesifikasi Konten Form

| Field | Tipe | Validasi | Deskripsi |
|-------|------|----------|-----------|
| Nama Produk | Text | Required, Unique, Max 100 char | Nama unik produk di katalog |
| Harga Jual | Currency | Required, > 0, Rp format | Harga jual ke pelanggan |
| Stok Awal | Integer | Required, ≥ 0 | Stok awal saat produk ditambahkan |
| Keterangan | Textarea | Optional, Max 500 char | Deskripsi detail produk |
| Foto Produk | File | JPG/PNG, Max 5MB | Gambar produk untuk display |

### 4.6.4 Spesifikasi Grid Display Produk

| Aspek | Spesifikasi |
|-------|-------------|
| Layout | Responsive grid (auto-columns) |
| Card Size | Min 200px, Max 280px per card |
| Per Card | Foto (300px), Nama, Harga, Stok (badge), Keterangan |
| Foto Fallback | Placeholder image jika tidak ada foto |
| Interaktif | Hover effect, Shadow animation |
| Sorting | Default: newest first |

### 4.6.5 Persyaratan Fungsional

**REQ-FR-020: Input Produk Baru**
- Staff penjualan dapat membuka form tambah produk
- Form memiliki validasi real-time per field
- Submit button disabled hingga semua required field terisi
- Clear button untuk reset form
- Maximum 1 form submission per 2 detik (prevent duplicate submit)

**REQ-FR-021: Notifikasi Produk Baru**
- Saat produk berhasil disimpan, sistem buat notifikasi tipe "produk_baru"
- Notifikasi dikirim ke ALL USER ROLES
- Isi notifikasi: Nama produk, harga, stok, foto thumbnail
- Notifikasi disimpan di table notifications untuk history

**REQ-FR-022: Grid Display Produk Responsif**
- Grid menampilkan semua produk di sistem (pagination optional jika > 20 items)
- Responsive: 1 kolom (mobile), 2 kolom (tablet), 3-4 kolom (desktop)
- Each card menampilkan: foto + nama + harga + stok badge + 50 karakter keterangan
- Fallback image untuk produk tanpa foto
- Loading animation saat grid pertama kali load
- Refresh grid setiap kali produk baru ditambahkan tanpa page reload

**REQ-FR-023: Upload Foto Produk**
- Support format: JPG, JPEG, PNG
- Max file size: 5MB
- Validasi dilakukan client-side dan server-side
- Foto disimpan dengan nama random (security)
- Thumbnail 300x300px untuk display di grid

**REQ-FR-024: Audit Logging Produk**
- Setiap penambahan produk di-log di audit trail
- Log include: user ID, timestamp, nama produk, harga, stok
- Log disimpan untuk compliance dan troubleshooting

```

---

### 7. BAB V - PERSYARATAN NON-FUNGSIONAL (5.2 Persyaratan Keamanan - UPDATE)

**Tambahkan requirement baru:**

```markdown
### 5.2 Persyaratan Keamanan (UPDATE)

[... existing requirements ...]

• REQ-NFR-PO-010: Upload foto produk terbatas pada format JPG/PNG dengan ukuran
maksimal 5MB. Sistem harus validasi file di server sebelum disimpan.

• REQ-NFR-PO-011: Nama file foto produk harus di-randomize untuk security.
Mapping asli nama file disimpan di database untuk referensi.

• REQ-NFR-PO-012: Akses halaman "Tambah Produk" hanya untuk role staf_penjualan.
Middleware role-based harus aktif untuk enforce restriction ini.
```

---

### 8. BAB V - PERSYARATAN NON-FUNGSIONAL (5.1 Persyaratan Kinerja - UPDATE)

**Tambahkan requirement baru:**

```markdown
### 5.1 Persyaratan Kinerja (UPDATE)

| Kode               | Persyaratan                              | Target                     |
| ------------------ | ---------------------------------------- | -------------------------- |
| REQ-NFR-PO-001     | Waktu simpan PO baru ke database         | ≤ 2 detik                  |
| REQ-NFR-PO-002     | Waktu verifikasi ketersediaan bahan baku | ≤ 3 detik                  |
| REQ-NFR-PO-003     | Waktu generate dan unduh dokumen PDF     | ≤ 5 detik                  |
| REQ-NFR-PO-004     | Jumlah pengguna konkuren                 | Minimal 10 tanpa degradasi |
| **REQ-NFR-PO-013** | **Waktu upload foto produk (max 5MB)**   | **≤ 3 detik**              |
| **REQ-NFR-PO-014** | **Waktu generate thumbnail foto**        | **≤ 1 detik**              |
| **REQ-NFR-PO-015** | **Waktu load grid produk (50+ items)**   | **≤ 2 detik**              |
```

---

### 9. BAB VI - DESAIN BASIS DATA (Update)

**Tambahkan tabel baru pada 6.1 Entitas dan Atribut:**

```markdown
### Tabel PRODUK (NEW)

| Kolom              | Tipe          | Constraint                          | Deskripsi                 |
| ------------------ | ------------- | ----------------------------------- | ------------------------- |
| id                 | INT           | PRIMARY KEY, AUTO_INCREMENT         | Primary key               |
| nama               | VARCHAR(100)  | NOT NULL, UNIQUE                    | Nama produk               |
| harga_jual         | DECIMAL(12,2) | NOT NULL, > 0                       | Harga jual (Rp)           |
| stok               | INT           | NOT NULL, DEFAULT 0, >= 0           | Stok saat ini             |
| keterangan         | TEXT          | NULLABLE                            | Deskripsi produk          |
| foto_url           | VARCHAR(255)  | NULLABLE                            | Path foto produk          |
| foto_original_name | VARCHAR(255)  | NULLABLE                            | Nama file asli foto       |
| created_at         | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP           | Waktu dibuat              |
| updated_at         | TIMESTAMP     | DEFAULT CURRENT_TIMESTAMP ON UPDATE | Waktu update              |
| created_by         | INT           | FK to users                         | User yang menambah produk |
| is_active          | BOOLEAN       | DEFAULT TRUE                        | Status aktif/inactive     |

**Relasi:**

- produk.created_by → referensi ke users.id
- detail_pesanan.produk_id → referensi ke produk.id
```

**Update pada Tabel NOTIFIKASI:**

Tambahkan tipe notifikasi baru:

```markdown
• Tipe: "produk_baru"
• Deskripsi: Notifikasi saat produk baru ditambahkan
• Data JSON:
{
"produk_id": 123,
"nama": "Tas Canvas Custom",
"harga": 150000,
"stok": 50,
"foto_url": "/images/...",
"created_by": "Staf Penjualan",
"message": "Produk baru telah ditambahkan: Tas Canvas Custom (Rp150.000)"
}
```

---

### 10. LAMPIRAN A - MATRIKS PERSYARATAN FUNGSIONAL (UPDATE)

**Tambahkan requirements baru:**

| Code       | Fitur         | Requirement                                    | Priority | Status         |
| ---------- | ------------- | ---------------------------------------------- | -------- | -------------- |
| REQ-FR-020 | Tambah Produk | Input produk baru oleh staff                   | SEDANG   | ✅ Implemented |
| REQ-FR-021 | Notifikasi    | Notifikasi otomatis produk baru ke semua roles | SEDANG   | ✅ Implemented |
| REQ-FR-022 | Grid Display  | Tampilkan produk dalam grid responsive         | SEDANG   | ✅ Implemented |
| REQ-FR-023 | Upload Foto   | Upload & store foto produk dengan validasi     | SEDANG   | ✅ Implemented |
| REQ-FR-024 | Audit Log     | Log setiap penambahan produk                   | RENDAH   | ✅ Implemented |

---

## 📊 STATUS IMPLEMENTASI

### ✅ Selesai (Juni 2026)

- [x] Menu "Tambah Produk" di sidebar
- [x] Form input dengan validasi
- [x] Upload foto dengan preview live
- [x] Grid display responsive
- [x] Notifikasi otomatis ke semua roles
- [x] Audit logging
- [x] Bug fix: image display APP_URL mismatch
- [x] Bug fix: photo preview enhancement
- [x] Database table: produk
- [x] Database table update: notifications (new type produk_baru)

### 🚀 Siap untuk Production

Semua fitur baru sudah tested dan ready untuk di-deploy ke production.

---

## 📌 TESTING NOTES

### Functional Testing

- ✅ Form validation works correctly
- ✅ Unique validation for product name
- ✅ Photo upload accepts only JPG/PNG
- ✅ Photo size limited to 5MB
- ✅ Live preview shows uploaded photo
- ✅ Grid displays all products responsive
- ✅ Notification created on product add
- ✅ Audit log recorded for each add

### Performance Testing

- ✅ Product add time: < 2 sec
- ✅ Photo upload: < 3 sec
- ✅ Grid load (50+ items): < 2 sec
- ✅ Notification delivery: < 1 sec

### Security Testing

- ✅ Only staff_penjualan role can access
- ✅ File validation server-side
- ✅ File names randomized
- ✅ No path traversal vulnerabilities
- ✅ CSRF protection active

---

## 📝 CATATAN UNTUK REVIEWER

1. **Backward Compatibility**: Update ini tidak merusak fitur existing. Semua fitur PO lama tetap berfungsi normal.

2. **Database Migration**: Diperlukan migration file baru untuk membuat tabel `produk` dan menambah kolom baru ke `notifikasi`.

3. **Bug Fixes**: Image display issue dan photo preview sudah diperbaiki dan tested di development.

4. **Version**: SRS officially updated dari v1.3 (April 2026) menjadi v1.4 (Juni 2026).

---

**Prepared by:** Adrian Ronald Daga (Frontend/Backend Developer)  
**Date:** 06 Juni 2026  
**Status:** ✅ Ready for approval and SRS update
