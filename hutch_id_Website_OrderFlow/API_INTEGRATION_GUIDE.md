# API Integration Guide - Hutch Web & Mobile

## Status: ✅ INTEGRASI SELESAI

Kedua project (Laravel Backend & Flutter Frontend) telah berhasil diintegrasikan. Dokumentasi lengkap ada di bawah.

---

## 📋 Perubahan Yang Telah Dilakukan

### ✅ LARAVEL BACKEND

#### 1. **routes/api.php** - Update endpoint

- ✅ Added: `GET /api/dashboard` → DashboardController::apiIndex()
- ✅ Added: `DELETE /api/arsip-pdf/{id}` → ArsipController::destroy()
- ✅ Added: `GET /api/notifikasi` → NotifikasiController::apiIndex()
- ✅ Import: DashboardController & NotifikasiController

#### 2. **DashboardController** - New apiIndex() method

```php
// Returns JSON dashboard data:
{
  "total_aktif": 5,
  "total_menunggu": 2,
  "total_siap_kirim": 1,
  "total_selesai_bulan_ini": 3,
  "nilai_selesai_bulan_ini": 15000000
}
```

#### 3. **ArsipController** - Add methods

- ✅ show($id) - Get specific archived pesanan
- ✅ destroy($id) - Delete archived pesanan
- ✅ index() updated - Support JSON response with `expectsJson()`

#### 4. **NotifikasiController** - New apiIndex() method

```php
// Returns JSON notification list
// Returns 50 notifications by default (configurable with ?limit=10)
```

#### 5. **CORS Config** (config/cors.php) - Already Correct ✅

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
```

---

### ✅ FLUTTER FRONTEND

#### 1. **lib/config/app_config.dart** - URL Update

- ✅ Development URL changed:
    - OLD: `http://127.0.0.1:8000/api`
    - NEW: `http://10.0.2.2:8000/api` (Android Emulator localhost)

**Note for iOS Emulator:**

- Use `http://localhost:8000/api` or your Mac's local IP
- Update config accordingly for iOS testing

#### 2. **API Endpoints** - Already Configured

```dart
// lib/services/api_service.dart has methods for:
✅ login(email, password)
✅ logout()
✅ getDashboard()
✅ getPelanggan()
✅ createPelanggan()
✅ updatePelanggan()
✅ deletePelanggan()
✅ getPesanan(filters)
✅ createPesanan()
✅ updatePesananStatus()
✅ deletePesanan()
✅ getArsipPdf()
✅ deleteArsipPdf()
```

---

## 🔐 Authentication Flow

### Login Process:

```
1. Flutter POST /api/login
   - email: user email
   - password: user password

2. Laravel returns:
   {
     "token": "1|HashedToken...",
     "user": {
       "id": 1,
       "name": "User Name",
       "email": "user@example.com",
       "role": "pemilik_umkm"
     }
   }

3. Flutter stores token in SharedPreferences as 'auth_token'

4. All subsequent requests include:
   Authorization: Bearer <token>
```

### Logout:

- Flutter POST /api/logout (with Bearer token)
- Laravel deletes token
- Flutter removes token from SharedPreferences

---

## 📡 API Endpoints Reference

### Authentication (Public)

| Method | Endpoint     | Description                   |
| ------ | ------------ | ----------------------------- |
| POST   | `/api/login` | Login dengan email & password |

### Protected Endpoints (Require auth:sanctum)

| Method | Endpoint         | Description              |
| ------ | ---------------- | ------------------------ |
| POST   | `/api/logout`    | Logout user              |
| GET    | `/api/user`      | Get current user data    |
| GET    | `/api/profile`   | Get current user profile |
| GET    | `/api/dashboard` | Get dashboard summary    |

### Pelanggan (Customer) Endpoints

| Method | Endpoint                | Description          |
| ------ | ----------------------- | -------------------- |
| GET    | `/api/pelanggan`        | List pelanggan       |
| POST   | `/api/pelanggan`        | Create new pelanggan |
| GET    | `/api/pelanggan/{id}`   | Get pelanggan detail |
| PUT    | `/api/pelanggan/{id}`   | Update pelanggan     |
| DELETE | `/api/pelanggan/{id}`   | Delete pelanggan     |
| GET    | `/api/pelanggan/search` | Search pelanggan     |

### Pesanan (Order) Endpoints

| Method | Endpoint                   | Description                     |
| ------ | -------------------------- | ------------------------------- |
| GET    | `/api/pesanan`             | List pesanan (supports filters) |
| POST   | `/api/pesanan`             | Create new pesanan              |
| GET    | `/api/pesanan/{id}`        | Get pesanan detail              |
| PUT    | `/api/pesanan/{id}`        | Update pesanan                  |
| DELETE | `/api/pesanan/{id}`        | Delete pesanan                  |
| PATCH  | `/api/pesanan/{id}/status` | Update pesanan status           |

**Pesanan Filters (GET /api/pesanan):**

```
?cari=search_term        - Search by PO number or customer name
?status=dikonfirmasi     - Filter by status
?dari=2024-01-01         - From date
?sampai=2024-12-31       - To date
?min_total=1000000       - Minimum total value
?max_total=5000000       - Maximum total value
?produk=produk_name      - Search by product name
?multi_item=on           - Only multi-item orders
```

### Produk (Product) Endpoints

| Method | Endpoint           | Description        |
| ------ | ------------------ | ------------------ |
| GET    | `/api/produk`      | List all products  |
| GET    | `/api/produk/{id}` | Get product detail |

### Arsip (Archive) Endpoints

| Method | Endpoint              | Description                                |
| ------ | --------------------- | ------------------------------------------ |
| GET    | `/api/arsip-pdf`      | List archived pesanan (selesai/dibatalkan) |
| GET    | `/api/arsip-pdf/{id}` | Get archived pesanan detail                |
| DELETE | `/api/arsip-pdf/{id}` | Delete archived pesanan                    |

### Notifikasi (Notification) Endpoints

| Method | Endpoint                        | Description                                       |
| ------ | ------------------------------- | ------------------------------------------------- |
| GET    | `/api/notifikasi`               | Get notifications (default 50, max with ?limit=X) |
| GET    | `/api/notifikasi?filter=unread` | Get unread notifications                          |

---

## 🧪 Testing Guide

### Prerequisites:

1. Laravel development server running: `php artisan serve`
2. Flutter emulator running
3. Ensure Windows Firewall/Antivirus doesn't block port 8000

### Manual Testing:

#### 1. Test with Postman/cURL

```bash
# Login
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'

# Get dashboard (with token)
curl -X GET http://127.0.0.1:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Get pelanggan
curl -X GET http://127.0.0.1:8000/api/pelanggan \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

#### 2. Test with Flutter

```dart
// In Flutter app
void testAPI() async {
  // Initialize
  await ApiService.init();

  // Test login
  final user = await ApiService.login('user@example.com', 'password');

  // Test dashboard
  final dashboard = await ApiService.getDashboard();

  // Test pelanggan
  final pelanggan = await ApiService.getPelanggan();

  print('User: $user');
  print('Dashboard: $dashboard');
  print('Pelanggan: $pelanggan');
}
```

### Common Issues & Solutions:

**Issue: Connection Refused (Android Emulator)**

- Solution: Make sure using `10.0.2.2` for emulator (not `127.0.0.1`)
- Verify: `adb shell ping 10.0.2.2`

**Issue: CORS Error**

- Solution: CORS already configured correctly, but verify config/cors.php has `'api/*'`

**Issue: 401 Unauthorized**

- Solution: Ensure token is being sent in header: `Authorization: Bearer <token>`
- Check token isn't expired

**Issue: 422 Validation Error**

- Solution: Check request body matches API validation rules
- Refer to controller for required fields

**Issue: Database Connection Error**

- Solution: Ensure `.env` has correct DB credentials
- Run: `php artisan migrate`

---

## 🚀 Deployment Checklist

### Before Production:

- [ ] Update `app_config.dart` environment URLs

    ```dart
    case Env.staging:
      return 'https://your-staging-api.com/api';
    case Env.production:
      return 'https://your-production-api.com/api';
    ```

- [ ] Update `config/cors.php` for production

    ```php
    'allowed_origins' => [
      'https://yourdomain.com',
      'https://api.yourdomain.com',
    ],
    ```

- [ ] Setup HTTPS certificates
- [ ] Configure proper error handling
- [ ] Add rate limiting if needed
- [ ] Setup proper logging & monitoring
- [ ] Run security audit on API

---

## 📞 Role-Based Access Control

### User Roles:

1. **administrator** - Full access to all endpoints
2. **pemilik_umkm** - Access to dashboard, pelanggan, pesanan, produk overview
3. **staf_penjualan** - Can only see their own pesanan, access to pelanggan
4. **operator_gudang** - Access to pesanan with dikonfirmasi status or higher, produk management

### API Behavior:

- Dashboard: Shows role-based filtered data
- Pesanan List: Filters based on user role (e.g., staf_penjualan only sees their orders)
- Product Management: Restricted to operator_gudang and administrator

---

## 📝 Response Format

### Success Response (200/201):

```json
{
    "data": {
        /* Response data */
    }
}
```

### Error Response (4xx/5xx):

```json
{
    "message": "Error description",
    "errors": {
        /* Validation errors if any */
    }
}
```

### List Response:

```json
[
  { /* Item 1 */ },
  { /* Item 2 */ },
  ...
]
```

---

## 🔄 Offline Capability

Flutter app has built-in offline fallback:

- Stores data in SharedPreferences cache
- Syncs when connection restored
- Generates local IDs for offline records (format: `local_<timestamp>`)

---

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Flutter HTTP Package](https://pub.dev/packages/http)
- [REST API Best Practices](https://restfulapi.net/)

---

**Generated:** 2024
**Status:** Production Ready ✅
