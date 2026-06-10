# 🌐 Web Backend - Testing & API Endpoints Guide

## 🚀 Running the Website Backend

### Prerequisites

- XAMPP running with MySQL on port 3307
- Laravel environment configured
- Database migrations completed

### Start the Laravel Server

```bash
cd c:\xampp\htdocs\hutch-web\hutch_id_Website_OrderFlow

# Start development server (port 8000)
php artisan serve

# OR start on specific port (8082 for API)
php artisan serve --port=8082

# OR using Docker (if configured)
docker-compose up
```

### Access the Dashboard

```
Web URL: http://localhost:8082/dashboard
API Base: http://localhost:8082/api
```

---

## 📡 Testing API Endpoints

All endpoints require authentication header:

```
Authorization: Bearer {token}
Content-Type: application/json
```

### Authentication

**Login (Get Token)**

```bash
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}

Response:
{
  "token": "your_token_here",
  "user": {...}
}
```

**Profile**

```bash
GET /api/profile
Authorization: Bearer {token}
```

### Pesanan (Orders) Endpoints

**List All Orders (Returns Full Data)**

```bash
GET /api/pesanan
Authorization: Bearer {token}

Response: Array of pesanan with detail_pesanan included
[
  {
    "id": 18,
    "nomor_po": "PO-2026050531-001",
    "tanggal_pesanan": "2026-05-31",
    "pelanggan": {...},
    "detail_pesanan": [
      {
        "id": 1,
        "produk_id": 1,
        "jumlah": 5,
        "harga_satuan": 150000,
        "produk": {...}
      }
    ],
    ...
  }
]
```

**Get Order Detail**

```bash
GET /api/pesanan/{id}
Authorization: Bearer {token}

Response: Full pesanan with nested detail_pesanan and produk data
```

**Create Order**

```bash
POST /api/pesanan
Authorization: Bearer {token}
Content-Type: application/json

{
  "tanggal_pesanan": "2026-06-09",
  "tanggal_pengiriman": "2026-06-16",
  "pelanggan_id": 9,
  "items": [
    {
      "produk_id": 1,
      "jumlah": 10,
      "spesifikasi": "Custom packaging"
    }
  ]
}
```

**Update Order Status**

```bash
PATCH /api/pesanan/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
  "status": "dikonfirmasi"
}

Valid statuses:
- menunggu_konfirmasi
- dikonfirmasi
- dalam_produksi
- siap_kirim
- selesai
- dibatalkan
```

**Delete Order**

```bash
DELETE /api/pesanan/{id}
Authorization: Bearer {token}
```

### Pelanggan (Customers) Endpoints

**List All Customers**

```bash
GET /api/pelanggan
Authorization: Bearer {token}

Response:
[
  {
    "id": 9,
    "nama": "PT.Inti",
    "telepon": "08184374927252",
    "email": "inti@gmail.com",
    "alamat": "Jl. Moch. Toha No.77...",
    "catatan": null,
    "created_at": "2026-05-31T08:39:22Z",
    "updated_at": "2026-05-31T08:39:22Z"
  }
]
```

**Get Customer Detail**

```bash
GET /api/pelanggan/{id}
Authorization: Bearer {token}
```

**Create Customer**

```bash
POST /api/pelanggan
Authorization: Bearer {token}
Content-Type: application/json

{
  "nama": "Customer Name",
  "alamat": "Customer Address",
  "telepon": "08xxxxxxxxxx",
  "email": "customer@example.com"
}
```

**Update Customer**

```bash
PUT /api/pelanggan/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "nama": "Updated Name",
  "alamat": "Updated Address",
  "telepon": "08xxxxxxxxxx",
  "email": "newemail@example.com"
}
```

**Delete Customer**

```bash
DELETE /api/pelanggan/{id}
Authorization: Bearer {token}
```

### Produk (Products) Endpoints

**List All Products**

```bash
GET /api/produk
Authorization: Bearer {token}

Response:
[
  {
    "id": 1,
    "nama": "Tas Kanvas Custom",
    "foto": "images/filename.jpeg",
    "harga_jual": 150000,
    "stok": 92,
    "keterangan": null
  }
]
```

**Get Product Detail**

```bash
GET /api/produk/{id}
Authorization: Bearer {token}
```

### Dashboard Endpoint

**Get Dashboard Data**

```bash
GET /api/dashboard
Authorization: Bearer {token}

Response: Summary of orders, customers, products, etc.
```

### Notifikasi (Notifications) Endpoints

**List Notifications**

```bash
GET /api/notifikasi
Authorization: Bearer {token}
```

### Arsip (Archive) Endpoints

**List Archive PDFs**

```bash
GET /api/arsip-pdf
Authorization: Bearer {token}
```

---

## 🧪 Testing with Tools

### Using Postman

1. Create collection with base URL: `http://localhost:8082/api`
2. Add "Authorization" header: `Bearer {token}`
3. Test each endpoint listed above

### Using cURL

```bash
# Get token
curl -X POST http://localhost:8082/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# List orders (using token from above)
curl http://localhost:8082/api/pesanan \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Create order
curl -X POST http://localhost:8082/api/pesanan \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "tanggal_pesanan": "2026-06-09",
    "tanggal_pengiriman": "2026-06-16",
    "pelanggan_id": 9,
    "items": [{"produk_id": 1, "jumlah": 5}]
  }'
```

### Using Browser DevTools

1. Log in to web dashboard
2. Open Network tab
3. Make API calls from web dashboard
4. Inspect request/response format

---

## 🔍 Database Direct Query

```sql
-- Check customers
SELECT * FROM pelanggan;

-- Check orders with details
SELECT p.*, d.* FROM pesanan p
LEFT JOIN detail_pesanan d ON p.id = d.pesanan_id;

-- Check products
SELECT * FROM produk;

-- Count data
SELECT COUNT(*) FROM pelanggan;    -- Shows: 2
SELECT COUNT(*) FROM pesanan;      -- Shows: 2
SELECT COUNT(*) FROM produk;       -- Shows: 8
```

---

## ⚠️ Common Issues & Fixes

### Issue: 401 Unauthorized

**Solution**: Make sure token is included in header

```
Authorization: Bearer {valid_token}
```

### Issue: 403 Forbidden

**Solution**: Check user role permissions for the action

### Issue: 404 Not Found

**Solution**: Verify resource ID exists (e.g., pelanggan id 9, pesanan id 18)

### Issue: 422 Unprocessable Entity

**Solution**: Check request body validation:

- All required fields present
- Correct data types
- Valid relationships

### Issue: Empty Response

**Solution**:

- Check if resource has no data
- Verify filter parameters
- Check pagination limits

---

## 📊 Expected Test Data

### Existing Customers (2)

1. PT. Inti (ID: 9) - inti@gmail.com
2. Kadut (ID: 13) - kadut@gmail.com

### Existing Orders (2)

1. PO-2026050531-001 (ID: 18) - Status: selesai
2. PO-2026060605-001 (ID: 20) - Status: selesai

### Existing Products (8)

- Tas Kanvas Custom (ID: 1)
- Tas gendong (ID: 6)
- Tas punggung mini (ID: 7)
- ...and 5 more

---

## ✅ Verification Checklist

- [ ] Server starts on port 8082
- [ ] Dashboard accessible at localhost:8082/dashboard
- [ ] API login endpoint works and returns token
- [ ] Pesanan list returns 2 orders with detail_pesanan
- [ ] Pelanggan list returns 2 customers
- [ ] Produk list returns 8 products
- [ ] Can create new customer
- [ ] Can create new order with items
- [ ] Can update order status
- [ ] Can delete resources

---

## 🔗 Related Resources

- [Laravel API Documentation](https://laravel.com/docs)
- [HTTP Status Codes](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status)
- [Postman Documentation](https://learning.postman.com/)

---

Generated: 2026-06-09
Status: ✅ Complete
