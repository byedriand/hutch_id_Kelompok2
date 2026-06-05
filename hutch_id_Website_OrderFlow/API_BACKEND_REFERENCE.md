# Backend API - Data Format Reference

## 📊 Dashboard API Endpoint

**Route**: `GET /api/dashboard`  
**Authentication**: Sanctum Bearer Token Required  
**Status**: ✅ Tested & Working

### Response Format

```json
{
    "total_aktif": 42,
    "total_menunggu": 8,
    "total_siap_kirim": 12,
    "total_selesai_bulan_ini": 15,
    "nilai_selesai_bulan_ini": 52500000
}
```

### Field Mapping

- `total_aktif`: Count of pesanan NOT in [selesai, dibatalkan]
- `total_menunggu`: Count of pesanan with status = menunggu_konfirmasi
- `total_siap_kirim`: Count of pesanan with status = siap_kirim
- `total_selesai_bulan_ini`: Count of selesai pesanan in current month
- `nilai_selesai_bulan_ini`: Sum of total_nilai for selesai pesanan in current month

---

## 🎫 Pesanan (Orders) List API

**Route**: `GET /api/pesanan`  
**Authentication**: Sanctum Bearer Token Required  
**Query Parameters**:

- `cari`: Search keyword (PO number, customer name, product)
- `status`: Filter by status (menunggu_konfirmasi, dikonfirmasi, dalam_produksi, siap_kirim, selesai, dibatalkan)
- `dari`: Date from (format: YYYY-MM-DD)
- `sampai`: Date to (format: YYYY-MM-DD)
- `min_total`: Minimum order value
- `max_total`: Maximum order value
- `produk`: Product name filter
- `multi_item`: Boolean flag for multi-item orders

### Response Array Item

```json
{
    "id": 1,
    "no": "PO-20260529-001",
    "tanggal": "29 Mei 2026",
    "total_nilai": 12500000,
    "pelanggan": "CV. Indo Makmur",
    "status": "menunggu_konfirmasi"
}
```

### Status Transitions

```
draft → menunggu_konfirmasi → dikonfirmasi → dalam_produksi → siap_kirim → selesai
                                                          ↘ (can cancel to dibatalkan at any point)
```

---

## 👥 Pelanggan (Customer) List API

**Route**: `GET /api/pelanggan`  
**Authentication**: Sanctum Bearer Token Required  
**Pagination**: Not paginated (returns all)

### Response Array Item

```json
{
    "id": 1,
    "nama": "CV. Indo Makmur",
    "telepon": "08123456789",
    "alamat": "Jl. Industri No. 45, Jakarta",
    "email": "indomakmur@mail.com",
    "jumlahPO": 5
}
```

---

## 📋 Role-Based Data Filtering

### Staf Penjualan (Sales Staff)

- **Pesanan**: Only sees their own orders (`where created_by = auth()->id()`)
- **Dashboard**: Shows stats filtered to own orders only

### Operator Gudang (Warehouse Operator)

- **Pesanan**: Only sees confirmed orders and beyond (status IN [dikonfirmasi, dalam_produksi, siap_kirim, selesai])
- **Dashboard**: Shows stats for confirmed orders only

### Administrator / Pemilik UMKM (Owner/Admin)

- **Pesanan**: Sees all orders
- **Dashboard**: Shows stats for all orders

---

## ⚙️ Implementation Details

### Field Name Transformations (API Response)

The API transforms database field names for consistency:

| Database Field    | API Field                            |
| ----------------- | ------------------------------------ |
| `nomor_po`        | `no`                                 |
| `tanggal_pesanan` | `tanggal`                            |
| `total_nilai`     | `total_nilai` (integer, not decimal) |
| `status`          | `status` (unchanged)                 |

### Offline Sync Strategy

- Mobile app caches all responses to SharedPreferences
- Cache keys:
    - `cached_pesanan`: Order list
    - `cached_pelanggan`: Customer list
    - `cached_pdf`: PDF archive list
- When offline or API fails, app loads from cache
- Cache is automatically invalidated when online and fresh data is fetched

### Stock Shortage Detection

For each pesanan detail item, the backend calculates:

- `has_shortage`: Boolean (requested qty > available stock)
- `shortage_total`: Sum of shortage quantities
- `shortage_details`: List of items with shortage

---

## 🔍 Database Status Values (Ground Truth)

```sql
-- Pesanan table status column
ALTER TABLE pesanan ADD CONSTRAINT check_status
CHECK (status IN ('draft', 'menunggu_konfirmasi', 'dikonfirmasi', 'dalam_produksi', 'siap_kirim', 'selesai', 'dibatalkan'))
```

**Valid Status Values**:

1. `draft` - Initial state, not submitted
2. `menunggu_konfirmasi` - Submitted, awaiting approval
3. `dikonfirmasi` - Approved by warehouse
4. `dalam_produksi` - Currently being produced
5. `siap_kirim` - Ready for shipment
6. `selesai` - Completed/delivered
7. `dibatalkan` - Cancelled

---

## 📲 Mobile App Compatibility

The mobile app is designed to work with this exact API structure. Key integration points:

1. **ApiService.getPesanan()** handles both:
    - Response with `value` key: `{value: [...]}`
    - Raw array: `[...]`

2. **Status mapping** in mobile handles:
    - API values: menunggu_konfirmasi → Display "Pending"
    - API values: dalam_produksi → Display "Proses"

3. **Caching** ensures:
    - Online/offline consistency
    - Immediate UI updates
    - Seamless transitions

---

## ✅ Verification Steps

1. **Dashboard API**:

    ```bash
    curl -H "Authorization: Bearer <token>" http://localhost:8000/api/dashboard
    ```

2. **Pesanan List**:

    ```bash
    curl -H "Authorization: Bearer <token>" "http://localhost:8000/api/pesanan?status=menunggu_konfirmasi"
    ```

3. **Pelanggan List**:
    ```bash
    curl -H "Authorization: Bearer <token>" http://localhost:8000/api/pelanggan
    ```

---

Generated: 2024-12-XX | Status: Complete
