# API Structure Analysis: Orders (Pesanan) & Customers (Pelanggan)

## Summary

This document provides a comprehensive analysis of the API structure for Orders and Customers endpoints, including database schemas, relationships, controllers, and response structures for ensuring consistency between mobile and web applications.

---

## 1. DATABASE STRUCTURE & RELATIONSHIPS

### A. Pelanggan (Customers) Table

**Table: `pelanggan`**

| Column     | Type         | Nullable | Notes                         |
| ---------- | ------------ | -------- | ----------------------------- |
| id         | BIGINT (PK)  | No       | Auto-incrementing primary key |
| nama       | VARCHAR(255) | No       | Customer name                 |
| alamat     | TEXT         | No       | Customer address              |
| telepon    | VARCHAR      | No       | Phone number                  |
| email      | VARCHAR(255) | No       | Unique email                  |
| catatan    | TEXT         | Yes      | Optional notes                |
| created_at | TIMESTAMP    | No       | Creation timestamp            |
| updated_at | TIMESTAMP    | No       | Last update timestamp         |

**Relationship:**

```
Pelanggan (1) ──────> (Many) Pesanan
```

- One customer can have many orders

### B. Pesanan (Orders) Table

**Table: `pesanan`**

| Column             | Type             | Nullable | Notes                                                                                                  |
| ------------------ | ---------------- | -------- | ------------------------------------------------------------------------------------------------------ |
| id                 | BIGINT (PK)      | No       | Auto-incrementing primary key                                                                          |
| nomor_po           | VARCHAR (UNIQUE) | No       | Order number (e.g., PO-20260605-001)                                                                   |
| tanggal_pesanan    | DATE             | No       | Order date                                                                                             |
| tanggal_pengiriman | DATE             | No       | Expected delivery date                                                                                 |
| pelanggan_id       | BIGINT (FK)      | No       | References pelanggan.id                                                                                |
| total_nilai        | DECIMAL(14,2)    | No       | Total order value                                                                                      |
| status             | ENUM             | No       | One of: `menunggu_konfirmasi`, `dikonfirmasi`, `dalam_produksi`, `siap_kirim`, `selesai`, `dibatalkan` |
| catatan            | TEXT             | Yes      | Optional order notes                                                                                   |
| created_by         | BIGINT (FK)      | No       | References users.id (who created the order)                                                            |
| tanggal_dikirim    | DATE             | Yes      | Actual shipping date                                                                                   |
| nomor_resi         | VARCHAR          | Yes      | Shipping tracking number                                                                               |
| alasan_pembatalan  | TEXT             | Yes      | Cancellation reason                                                                                    |
| created_at         | TIMESTAMP        | No       | Creation timestamp                                                                                     |
| updated_at         | TIMESTAMP        | No       | Last update timestamp                                                                                  |

**Relationships:**

```
Pesanan (1) ──────> (Many) DetailPesanan
Pesanan (Many) <──── (1) Pelanggan
Pesanan (Many) <──── (1) User (created_by)
Pesanan (1) ──────> (Many) HistoriStatus
```

### C. DetailPesanan (Order Items) Table

**Table: `detail_pesanan`**

| Column       | Type          | Nullable | Notes                         |
| ------------ | ------------- | -------- | ----------------------------- |
| id           | BIGINT (PK)   | No       | Auto-incrementing primary key |
| pesanan_id   | BIGINT (FK)   | No       | References pesanan.id         |
| produk_id    | BIGINT (FK)   | No       | References produk.id          |
| jumlah       | INTEGER       | No       | Quantity ordered              |
| spesifikasi  | TEXT          | Yes      | Special specifications/notes  |
| harga_satuan | DECIMAL(12,2) | No       | Unit price at time of order   |
| created_at   | TIMESTAMP     | No       | Creation timestamp            |
| updated_at   | TIMESTAMP     | No       | Last update timestamp         |

**Relationships:**

```
DetailPesanan (Many) <──── (1) Pesanan
DetailPesanan (Many) <──── (1) Produk
```

### D. Produk (Products) Table (Relevant Fields)

**Table: `produk`**

| Column     | Type        | Nullable | Notes                         |
| ---------- | ----------- | -------- | ----------------------------- |
| id         | BIGINT (PK) | No       | Auto-incrementing primary key |
| nama       | VARCHAR     | No       | Product name                  |
| foto       | VARCHAR     | Yes      | Product image path            |
| harga_jual | DECIMAL     | No       | Selling price                 |
| stok       | INTEGER     | No       | Current stock                 |
| keterangan | TEXT        | Yes      | Product description           |

---

## 2. API ENDPOINTS & RESPONSE STRUCTURES

### A. `/api/pesanan` - List Orders

**HTTP Method:** `GET`
**Authentication:** Required (Sanctum)
**Query Parameters:**

- `cari` (string): Search by PO number, customer name, or product name
- `status` (string): Filter by order status
- `dari` (date): Filter orders from this date
- `sampai` (date): Filter orders until this date
- `min_total` (numeric): Filter by minimum order value
- `max_total` (numeric): Filter by maximum order value
- `produk` (string): Filter by product name
- `multi_item` (boolean): Filter orders with multiple items

**Response Structure (API):**

```json
[
  {
    "id": 1,
    "no": "PO-20260605-001",
    "pelanggan": "PT Mitra Usaha",
    "pelanggan_id": 1,
    "tanggal": "05 Jun 2026",
    "status": "dikonfirmasi",
    "total_nilai": 500000,
    "total_item": 2,
    "deskripsi": "5x Produk A, 10x Produk B",
    "created_at": "2026-06-05T10:00:00Z",
    "updated_at": "2026-06-05T10:30:00Z"
  }
]
```

**Data Transformation Logic:**

```
Input: Raw Pesanan model with relationships
├─ Load: pelanggan, detailPesanan.produk
├─ Transform Fields:
│  ├─ id → id
│  ├─ nomor_po → "no"
│  ├─ pelanggan.nama → "pelanggan"
│  ├─ pelanggan_id → "pelanggan_id"
│  ├─ tanggal_pesanan → format to "d M Y" → "tanggal"
│  ├─ status → "status"
│  ├─ total_nilai → cast to int → "total_nilai"
│  ├─ detailPesanan.count() → "total_item"
│  ├─ detailPesanan items → join "jumlah x nama_produk" → "deskripsi"
│  ├─ created_at → "created_at"
│  └─ updated_at → "updated_at"
└─ Return: Transformed array
```

**Web Response:** Paginated (15 items per page) with additional `shortage_details` calculation

---

### B. `/api/pesanan/{id}` - Get Order Details

**HTTP Method:** `GET`
**Authentication:** Required (Sanctum)
**Path Parameters:**

- `id` (integer): Order ID

**Response Structure:**

```json
{
  "id": 1,
  "nomor_po": "PO-20260605-001",
  "tanggal_pesanan": "2026-06-05",
  "tanggal_pengiriman": "2026-06-10",
  "pelanggan_id": 1,
  "total_nilai": "500000.00",
  "status": "dikonfirmasi",
  "catatan": "Order notes here",
  "created_by": 1,
  "tanggal_dikirim": "2026-06-10",
  "nomor_resi": "JNE123456789",
  "alasan_pembatalan": null,
  "created_at": "2026-06-05T10:00:00Z",
  "updated_at": "2026-06-05T10:30:00Z",
  "pelanggan": {
    "id": 1,
    "nama": "PT Mitra Usaha",
    "alamat": "Jl. Example No. 123",
    "telepon": "08123456789",
    "email": "contact@mitra.com",
    "catatan": null,
    "created_at": "2026-05-15T00:00:00Z",
    "updated_at": "2026-05-15T00:00:00Z"
  },
  "detail_pesanan": [
    {
      "id": 1,
      "pesanan_id": 1,
      "produk_id": 1,
      "jumlah": 5,
      "spesifikasi": "Custom spec",
      "harga_satuan": "100000.00",
      "created_at": "2026-06-05T10:00:00Z",
      "updated_at": "2026-06-05T10:00:00Z",
      "produk": {
        "id": 1,
        "nama": "Produk A",
        "foto": "images/produk_a.jpg",
        "harga_jual": "100000.00",
        "stok": 50,
        "keterangan": "Product description",
        "created_at": "2026-05-13T00:00:00Z",
        "updated_at": "2026-05-13T00:00:00Z"
      }
    }
  ],
  "histori_status": [
    {
      "id": 1,
      "pesanan_id": 1,
      "user_id": 1,
      "status": "menunggu_konfirmasi",
      "keterangan": "Order created and waiting for approval",
      "created_at": "2026-06-05T10:00:00Z",
      "updated_at": "2026-06-05T10:00:00Z",
      "user": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "role": "staf_penjualan"
      }
    }
  ],
  "creator": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "staf_penjualan"
  }
}
```

**Eager Loaded Relations:**

- `pelanggan` (Customer details)
- `detailPesanan.produk` (Order items with product details)
- `historiStatus.user` (Status history with user info)
- `creator` (User who created the order)

---

### C. `/api/pesanan` - Create Order

**HTTP Method:** `POST`
**Authentication:** Required (Sanctum)

**Request Payload:**

```json
{
  "tanggal_pesanan": "2026-06-05",
  "tanggal_pengiriman": "2026-06-10",
  "pelanggan_id": 1,
  "total_nilai": 500000,
  "items": [
    {
      "produk_id": 1,
      "jumlah": 5,
      "spesifikasi": "Custom spec"
    },
    {
      "produk_id": 2,
      "jumlah": 10,
      "spesifikasi": null
    }
  ],
  "catatan": "Order notes",
  "send_shortage_notification": true
}
```

**Validation Rules:**

```
tanggal_pesanan          → required, date
tanggal_pengiriman       → required, date, after_or_equal:tanggal_pesanan
pelanggan_id             → required, exists:pelanggan,id
total_nilai              → required, numeric, min:0
items                    → required, array, min:1
items.*.produk_id        → required, exists:produk,id
items.*.jumlah           → required, integer, min:1
items.*.spesifikasi      → nullable, string, max:500
```

**Processing Logic:**

1. Validate input data
2. Check stock availability for each product
3. Generate unique PO number: `PO-YYYYMMDD-###`
4. Create Pesanan record in transaction
5. Create DetailPesanan records for each item
6. Create initial HistoriStatus entry
7. Send stock shortage notification if requested
8. Return response (JSON for API, redirect for web)

**Response (Success):**

```json
{
  "id": 1,
  "nomor_po": "PO-20260605-001",
  "tanggal_pesanan": "2026-06-05",
  "tanggal_pengiriman": "2026-06-10",
  "pelanggan_id": 1,
  "total_nilai": "500000.00",
  "status": "menunggu_konfirmasi",
  "catatan": "Order notes",
  "created_by": 1,
  "created_at": "2026-06-05T10:00:00Z",
  "updated_at": "2026-06-05T10:00:00Z"
}
```

---

### D. `/api/pesanan/{id}` - Update Order

**HTTP Method:** `PUT`
**Authentication:** Required (Sanctum)
**Authorization:** Policy-based (only certain statuses allow edits)

**Request Payload:**

```json
{
  "tanggal_pengiriman": "2026-06-12",
  "catatan": "Updated notes"
}
```

**Validation Rules:**

```
tanggal_pengiriman → required, date, after_or_equal:created_tanggal_pesanan
catatan            → nullable, string, max:1000
```

---

### E. `/api/pesanan/{id}/status` - Update Order Status

**HTTP Method:** `PATCH`
**Authentication:** Required (Sanctum)
**Authorization:** Role-based restrictions

**Request Payload:**

```json
{
  "status": "siap_kirim",
  "keterangan": "Status update notes",
  "tanggal_dikirim": "2026-06-10",
  "nomor_resi": "JNE123456789"
}
```

**Validation Rules:**

```
status          → required, in:dalam_produksi,siap_kirim,selesai,dibatalkan
keterangan      → nullable, string, max:500
tanggal_dikirim → nullable, date (required if status=siap_kirim)
nomor_resi      → nullable, string, max:255
```

**Status Transition Flow:**

```
menunggu_konfirmasi  →  dikonfirmasi / dibatalkan
         ↓
    dikonfirmasi      →  dalam_produksi / dibatalkan
         ↓
  dalam_produksi     →  siap_kirim / dibatalkan
         ↓
    siap_kirim        →  selesai / dibatalkan
         ↓
      selesai         [FINAL - No transitions allowed]
      dibatalkan      [FINAL - No transitions allowed]
```

**Role-Based Status Restrictions:**

- `staf_penjualan`: Can only create and confirm orders
- `operator_gudang`: Can manage production and shipping statuses
- `pemilik_umkm` / `administrator`: Can manage all statuses

---

### F. `/api/pesanan/{id}` - Delete Order

**HTTP Method:** `DELETE`
**Authentication:** Required (Sanctum)
**Authorization:** Policy-based

---

## 3. CUSTOMERS (PELANGGAN) API

### A. `/api/pelanggan` - List Customers

**HTTP Method:** `GET`
**Authentication:** Required (Sanctum)
**Query Parameters:**

- `cari` (string): Search by customer name

**Response Structure:**

```json
[
  {
    "id": 1,
    "nama": "PT Mitra Usaha",
    "alamat": "Jl. Example No. 123",
    "telepon": "08123456789",
    "email": "contact@mitra.com",
    "catatan": null,
    "created_at": "2026-05-15T00:00:00Z",
    "updated_at": "2026-05-15T00:00:00Z",
    "pesanan_count": 5
  }
]
```

**Data Transformation:**

```
Input: Pelanggan model with withCount('pesanan')
├─ Load all fields from pelanggan table
├─ Count related pesanan records
└─ Return raw model
```

**Key Differences from Web:**

- API returns all results without pagination
- Web returns paginated results (12 items per page)
- API includes `pesanan_count` (count of orders)

---

### B. `/api/pelanggan/{id}` - Get Customer Details

**HTTP Method:** `GET`
**Authentication:** Required (Sanctum)

**Response Structure:**

```json
{
  "id": 1,
  "nama": "PT Mitra Usaha",
  "alamat": "Jl. Example No. 123",
  "telepon": "08123456789",
  "email": "contact@mitra.com",
  "catatan": null,
  "created_at": "2026-05-15T00:00:00Z",
  "updated_at": "2026-05-15T00:00:00Z"
}
```

---

### C. `/api/pelanggan` - Create Customer

**HTTP Method:** `POST`
**Authentication:** Required (Sanctum)

**Request Payload:**

```json
{
  "nama": "PT Mitra Usaha",
  "alamat": "Jl. Example No. 123",
  "telepon": "08123456789",
  "email": "contact@mitra.com"
}
```

**Validation Rules:**

```
nama     → required, string, max:255
alamat   → required, string, max:500
telepon  → required, string, max:50
email    → nullable, email, max:255
```

**Response (Success):**

```json
{
  "id": 1,
  "nama": "PT Mitra Usaha",
  "alamat": "Jl. Example No. 123",
  "telepon": "08123456789",
  "email": "contact@mitra.com",
  "catatan": null,
  "created_at": "2026-06-05T10:00:00Z",
  "updated_at": "2026-06-05T10:00:00Z"
}
```

---

### D. `/api/pelanggan/{id}` - Update Customer

**HTTP Method:** `PUT`
**Authentication:** Required (Sanctum)

**Request Payload:**

```json
{
  "nama": "PT Mitra Usaha Updated",
  "alamat": "Jl. New Address No. 456",
  "telepon": "08198765432",
  "email": "newemail@mitra.com"
}
```

---

### E. `/api/pelanggan/{id}` - Delete Customer

**HTTP Method:** `DELETE`
**Authentication:** Required (Sanctum)
**Authorization:** Only `pemilik_umkm` and `administrator` roles

**Response (Success):**

```json
{
  "message": "Pelanggan berhasil dihapus"
}
```

---

### F. `/api/pelanggan/search` - Search Customers

**HTTP Method:** `GET`
**Authentication:** Required (Sanctum)
**Query Parameters:**

- `q` (string): Search query

**Response Structure:**

```json
[
  {
    "id": 1,
    "nama": "PT Mitra Usaha",
    "alamat": "Jl. Example No. 123",
    "telepon": "08123456789",
    "email": "contact@mitra.com"
  }
]
```

**Limit:** 10 results maximum

---

## 4. DATA CONSISTENCY CHECKLIST FOR MOBILE/WEB

### A. API Response Field Names & Types

**Pesanan List Response:**

- ✅ `id` (integer)
- ✅ `no` (string) - PO number, NOT `nomor_po`
- ✅ `pelanggan` (string) - Customer name, NOT object
- ✅ `pelanggan_id` (integer)
- ✅ `tanggal` (string, formatted "d M Y") - Order date, NOT `tanggal_pesanan`
- ✅ `status` (string)
- ✅ `total_nilai` (integer, NOT decimal string)
- ✅ `total_item` (integer)
- ✅ `deskripsi` (string) - Concatenated product list
- ✅ `created_at` (timestamp)
- ✅ `updated_at` (timestamp)

**Critical Differences:**

- Web list uses transformed `no`, `tanggal`, `deskripsi` fields
- API list returns INT for `total_nilai`, not string decimal
- `pelanggan` is a string name, NOT an object
- Dates are formatted for display, not raw database format

---

### B. Order Detail Response

**Must Include:**

- ✅ Full Pesanan object with all fields
- ✅ Nested `pelanggan` object (full customer details)
- ✅ Nested `detail_pesanan` array with products
- ✅ Each item in `detail_pesanan` must include nested `produk` object
- ✅ `histori_status` array with user information
- ✅ `creator` object (user who created the order)

**Field Type Consistency:**

- ✅ Dates as ISO 8601 strings (YYYY-MM-DD)
- ✅ Timestamps with timezone (ISO 8601)
- ✅ Decimals as strings (e.g., "500000.00", "100000.00")
- ✅ IDs as integers

---

### C. Customer Response

**List Response:**

- ✅ Must include `pesanan_count` for web list functionality
- ✅ No pagination in API (return all results)
- ✅ Include all customer fields

**Detail Response:**

- ✅ Return raw customer object (no nested relations in basic endpoint)

---

### D. Error Response Structure

**Expected Format:**

```json
{
  "message": "Error description",
  "errors": {
    "field_name": ["error message 1", "error message 2"]
  }
}
```

**Common Validation Errors:**

- Stock shortage warnings (not hard errors)
- Duplicate email addresses
- Invalid date ranges
- Missing required fields

---

## 5. IMPORTANT BEHAVIOR NOTES

### A. Authorization & Role-Based Access

**Pesanan (Orders):**

- `staf_penjualan`: Only see orders they created, can create/confirm
- `operator_gudang`: Only see confirmed/production orders
- `pemilik_umkm` / `administrator`: Can see all orders

**Pelanggan (Customers):**

- All roles can view
- Only `pemilik_umkm` and `administrator` can delete

### B. Stock Management

**On Order Creation:**

- Stock is NOT automatically deducted
- Stock shortage is detected and flagged
- Optional notification can be sent if stock is insufficient
- Order proceeds even if stock is short

**On Order Confirmation:**

- Stock is checked again
- Notification sent if stock insufficient

### C. Status Validation

**Strict Transition Rules:**

- Status can only move forward in the defined flow
- `selesai` and `dibatalkan` are final states (no transitions)
- Cannot skip status levels (e.g., can't jump from `menunggu_konfirmasi` directly to `siap_kirim`)

### D. Timestamps

**Created_at vs Tanggal_pesanan:**

- `tanggal_pesanan`: Business date for the order (can be historical)
- `created_at`: Database timestamp when record was created
- These may differ - use `tanggal_pesanan` for order dates

---

## 6. KEY MIGRATION POINTS

### Schema Fields Added Over Time:

1. **Base Tables:** `pelanggan`, `pesanan`, `detail_pesanan`
2. **Shipping Fields:** `tanggal_dikirim`, `nomor_resi` (added 2026-05-25)
3. **Cancellation:** `alasan_pembatalan` (added 2026-05-29)
4. **Product Images:** `foto` column in produk (added 2026-05-31)

### For Mobile Development:

- Ensure all these fields are handled, even if nullable
- Display conditionally based on status (e.g., only show `nomor_resi` if `status = siap_kirim`)

---

## 7. API AUTHENTICATION

**Mechanism:** Laravel Sanctum Token-Based
**Flow:**

1. POST `/api/login` with credentials → Returns API token
2. Include token in header: `Authorization: Bearer {token}`
3. All subsequent API requests require this token
4. POST `/api/logout` to invalidate token

---

## 8. IMPLEMENTATION RECOMMENDATIONS

### For Mobile App to Match Web:

1. **List Responses:** Transform data to match web format (use `no`, `tanggal`, `deskripsi`)
2. **Data Types:** Ensure integers, strings, and timestamps are handled correctly
3. **Pagination:** Web uses pagination (15 for orders, 12 for customers), but API returns all
4. **Error Handling:** Properly parse validation errors from `errors` object
5. **Authorization:** Respect role-based restrictions on your UI
6. **Status Flow:** Don't allow invalid status transitions in UI
7. **Date Formatting:** Display dates consistently (consider user locale)
8. **Timestamps:** Handle timezone properly (recommended: UTC with local display conversion)

---

## 9. DATABASE RELATIONSHIP DIAGRAM

```
┌─────────────────┐
│   users         │
│                 │
│ id (PK)         │
│ name            │
│ email           │
│ role            │
└────────┬────────┘
         │
         │ created_by (1:N)
         │
    ┌────┴─────┐
    │ pesanan   │ ──── pelanggan_id (M:1) ────┐
    │           │                              │
    │ id (PK)   │                         ┌────▼──────────┐
    │ nomor_po  │                         │  pelanggan     │
    │ status    │                         │                │
    │ total_nilai                         │  id (PK)       │
    │ pelanggan_id (FK)                   │  nama          │
    │ created_by (FK)                     │  alamat        │
    └────┬──────────┘                     │  telepon       │
         │                                │  email         │
         │ (1:N)                          └────────────────┘
         │
    ┌────▼─────────────────┐
    │ detail_pesanan        │
    │                       │
    │ id (PK)               │
    │ pesanan_id (FK)       │
    │ produk_id (FK) ───┐   │
    │ jumlah            │   │
    │ harga_satuan      │   │
    └───────────────────┼───┘
                        │
                   ┌────▼──────────┐
                   │   produk       │
                   │                │
                   │ id (PK)        │
                   │ nama           │
                   │ harga_jual     │
                   │ stok           │
                   │ foto           │
                   └────────────────┘

    ┌────────────────────────┐
    │ histori_status          │
    │                         │
    │ id (PK)                 │
    │ pesanan_id (FK) ──────┐ │
    │ user_id (FK) ────────┐│ │
    │ status                ││ │
    │ keterangan            ││ │
    └──────────────────────┼┼─┘
                           ││
                    pesanan ││
                           ││
                    users ──┘
```

---

## SUMMARY TABLE: What Mobile Should Expect

| Endpoint                   | Method | Auth | Returns            | Pagination | Main Fields                                                 |
| -------------------------- | ------ | ---- | ------------------ | ---------- | ----------------------------------------------------------- |
| `/api/pesanan`             | GET    | Yes  | List (transformed) | No         | id, no, pelanggan, status, total_nilai, total_item, tanggal |
| `/api/pesanan/{id}`        | GET    | Yes  | Full detail        | N/A        | All fields + nested relations                               |
| `/api/pesanan`             | POST   | Yes  | Created object     | N/A        | Standard Pesanan response                                   |
| `/api/pesanan/{id}`        | PUT    | Yes  | Updated object     | N/A        | Updated Pesanan                                             |
| `/api/pesanan/{id}/status` | PATCH  | Yes  | Updated object     | N/A        | Updated Pesanan with new status                             |
| `/api/pesanan/{id}`        | DELETE | Yes  | 204 or message     | N/A        | Success/error response                                      |
| `/api/pelanggan`           | GET    | Yes  | List + count       | No         | id, nama, pesanan_count                                     |
| `/api/pelanggan/{id}`      | GET    | Yes  | Single object      | N/A        | All customer fields                                         |
| `/api/pelanggan`           | POST   | Yes  | Created object     | N/A        | Standard Pelanggan response                                 |
| `/api/pelanggan/{id}`      | PUT    | Yes  | Updated object     | N/A        | Updated Pelanggan                                           |
| `/api/pelanggan/{id}`      | DELETE | Yes  | Message            | N/A        | Success/error response                                      |

---

**Last Updated:** 2026-06-05
**API Version:** v1 (part of Laravel Sanctum auth)
**Status:** Active in Production
