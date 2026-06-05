# HUTCH API INTEGRATION - CONFIGURATION REFERENCE

Generated: 2024
Status: ✅ INTEGRASI SELESAI

---

## 🔧 CONFIGURATION CHECKLIST

### Laravel Backend

#### ✅ routes/api.php

- Public route: POST /api/login
- Protected routes with auth:sanctum middleware:
  - GET /api/dashboard (NEW)
  - GET /api/notifikasi (NEW)
  - DELETE /api/arsip-pdf/{id} (NEW)
  - All other existing routes...

#### ✅ config/cors.php

```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['*'],
'allowed_headers' => ['*'],
```

#### ✅ Controllers Updated

1. **DashboardController**
   - Method: `apiIndex()`
   - Returns: JSON dashboard summary

2. **ArsipController**
   - Methods: `show()`, `destroy()`, `index()` (updated)
   - Returns: JSON archive list

3. **NotifikasiController**
   - Method: `apiIndex()`
   - Returns: JSON notification list

4. **PelangganController**
   - Already supports JSON with `expectsJson()`

5. **PesananController**
   - Already supports JSON with `expectsJson()`

6. **ProdukController**
   - Already supports JSON responses

7. **Api\AuthController**
   - Already has: `login()`, `logout()`, `profile()`

#### ✅ Database

- Ensure migrations are run
- Test user credentials set up
- Models have proper relationships

---

### Flutter Frontend

#### ✅ lib/config/app_config.dart

```dart
// Development (Android Emulator)
case Env.development:
  return 'http://10.0.2.2:8000/api';

// iOS Emulator (use localhost or your Mac's IP)
// case Env.development:
//   return 'http://localhost:8000/api';

// Staging
case Env.staging:
  return 'https://staging.hutchprestige.com/api';

// Production
case Env.production:
  return 'https://api.hutchprestige.com/api';
```

#### ✅ lib/services/api_service.dart

- All endpoints configured
- Token management with SharedPreferences
- Offline fallback enabled
- Bearer token auto-added to headers

#### ✅ Models

- User model with fromJson()
- Pelanggan model with relationships
- All models support JSON serialization

---

## 🧪 TESTING COMMANDS

### 1. Test with PHP Script

```bash
cd c:\xampp\htdocs\hutch-web\hutch_id_Website_OrderFlow
php test_api.php
```

### 2. Test with cURL (Windows PowerShell)

```powershell
# Login
$response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/login" `
  -Method POST `
  -ContentType "application/json" `
  -Body '{"email":"admin@example.com","password":"password"}'

# Extract token
$token = ($response.Content | ConvertFrom-Json).token

# Test dashboard
Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/dashboard" `
  -Method GET `
  -Headers @{"Authorization"="Bearer $token"}
```

### 3. Test with Flutter

```bash
cd c:\xampp\htdocs\hutch-web\hutch_id_Mobile

# Clean and rebuild
flutter clean
flutter pub get

# Run on emulator
flutter run

# View logs
flutter logs
```

### 4. Test with Postman

- Import collection from API_INTEGRATION_GUIDE.md
- Use environment variables for token
- Test all endpoints

---

## 🌐 URL REFERENCE

### Development Servers

#### Laravel (Backend)

- Base URL: `http://127.0.0.1:8000`
- API Base: `http://127.0.0.1:8000/api`
- Artisan: `php artisan serve`

#### Flutter (Frontend)

- Android Emulator: `http://10.0.2.2:8000/api`
- iOS Simulator: `http://localhost:8000/api`
- Physical Device: `http://<YOUR_IP>:8000/api`

### Production URLs

- Staging API: `https://staging.hutchprestige.com/api`
- Production API: `https://api.hutchprestige.com/api`

---

## 📱 DEVICE SETUP

### Android Emulator

1. Use URL: `http://10.0.2.2:8000/api`
2. This maps to localhost of your development machine
3. Verify with: `adb shell ping 10.0.2.2`

### iOS Simulator

1. Use URL: `http://localhost:8000/api`
2. Or use your Mac's IP address: `http://192.168.x.x:8000/api`
3. Find IP with: `ipconfig` or `ifconfig`

### Physical Device

1. Find your machine's local IP: `ipconfig getifaddr en0`
2. Use URL: `http://<YOUR_IP>:8000/api`
3. Ensure device is on same WiFi network
4. Check Windows Firewall allows port 8000

---

## 🔑 DEFAULT TEST USER

**Email:** admin@example.com
**Password:** password
**Role:** administrator

To change credentials:

1. Create seeder or migration
2. Update in `.env` or database directly
3. Update test_api.php with new credentials

---

## 📊 API RESPONSE EXAMPLES

### Login Success (200)

```json
{
  "token": "1|HashedTokenString...",
  "user": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@example.com",
    "role": "administrator"
  }
}
```

### Dashboard (200)

```json
{
  "total_aktif": 5,
  "total_menunggu": 2,
  "total_siap_kirim": 1,
  "total_selesai_bulan_ini": 3,
  "nilai_selesai_bulan_ini": 15000000
}
```

### Pelanggan List (200)

```json
[
  {
    "id": 1,
    "nama": "PT Mitra Jaya",
    "telepon": "021-123-4567",
    "alamat": "Jl. Merdeka 123",
    "email": "contact@mitrajaya.com"
  }
]
```

### Error Response (401/404/500)

```json
{
  "message": "Error description"
}
```

---

## 🛡️ SECURITY NOTES

### Production Checklist

- [ ] Change CORS origins from '\*' to specific domains
- [ ] Enable HTTPS/SSL certificates
- [ ] Setup rate limiting
- [ ] Add request validation/sanitization
- [ ] Enable query logging for debugging
- [ ] Setup error monitoring (Sentry, etc)
- [ ] Implement proper error messages (hide DB errors)
- [ ] Add API versioning (v1, v2, etc)
- [ ] Setup API key rotation schedule
- [ ] Document API security requirements

### CORS Production Example

```php
'allowed_origins' => [
    'https://hutchprestige.com',
    'https://app.hutchprestige.com',
    'https://admin.hutchprestige.com',
],
'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'],
'max_age' => 86400,
'supports_credentials' => true,
```

---

## 🐛 TROUBLESHOOTING

### Issue: 401 Unauthorized

**Cause:** Token missing or invalid
**Solution:**

- Check Authorization header is present
- Verify token format: `Bearer <token>`
- Login again to get fresh token

### Issue: 403 Forbidden

**Cause:** User doesn't have permission
**Solution:**

- Check user role
- Verify role-based access rules
- May need higher role access

### Issue: 500 Internal Server Error

**Cause:** Backend error
**Solution:**

- Check Laravel logs: `storage/logs/laravel.log`
- Verify database connection
- Check model relationships

### Issue: Connection Refused

**Cause:** Server not running
**Solution:**

- Start Laravel: `php artisan serve`
- Check port 8000 is available
- Verify firewall settings

### Issue: CORS Error

**Cause:** CORS configuration issue
**Solution:**

- Verify `config/cors.php` has `'api/*'`
- Check `'allowed_origins'` includes your origin
- Review browser console for exact error

### Issue: Request Timeout

**Cause:** Server slow response
**Solution:**

- Check server logs for slow queries
- Optimize database queries
- Increase timeout in Flutter if needed

---

## 📞 SUPPORT

### Resources

- Laravel Docs: https://laravel.com/docs
- Sanctum: https://laravel.com/docs/sanctum
- Flutter HTTP: https://pub.dev/packages/http
- REST API: https://restfulapi.net

### Getting Help

1. Check logs first
2. Review this guide
3. Run test_api.php
4. Check model relationships
5. Verify database migrations

---

## 🎯 QUICK START

### First Time Setup

1. Clone/sync both projects
2. Run Laravel migrations: `php artisan migrate`
3. Start Laravel server: `php artisan serve`
4. Update Flutter config if needed
5. Run test_api.php to verify
6. Start Flutter app: `flutter run`

### Daily Development

```bash
# Terminal 1 - Laravel Server
cd hutch_id_Website_OrderFlow
php artisan serve

# Terminal 2 - Flutter
cd hutch_id_Mobile
flutter run

# Terminal 3 - Monitoring (optional)
tail -f hutch_id_Website_OrderFlow/storage/logs/laravel.log
```

---

**Last Updated:** 2024
**Version:** 1.0.0
**Status:** Production Ready ✅
