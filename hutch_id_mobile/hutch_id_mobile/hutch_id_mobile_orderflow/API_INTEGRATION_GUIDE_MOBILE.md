# 🔌 API Integration Guide - Hutch ID Mobile

## 📋 Daftar Isi

1. [Konfigurasi API](#konfigurasi-api)
2. [Authentication Flow](#authentication-flow)
3. [API Endpoints](#api-endpoints)
4. [Request & Response Format](#request--response-format)
5. [Error Handling](#error-handling)
6. [Testing API](#testing-api)

---

## Konfigurasi API

### File Konfigurasi

**Location**: `lib/config/app_config.dart`

```dart
class AppConfig {
  // API URL Configuration
  static const String apiBaseUrl = 'http://10.0.2.2:8000/api';

  // Untuk environment berbeda:
  // Development: http://10.0.2.2:8000/api (Android Emulator)
  // Physical Device: http://[IP-LOKAL]:8000/api
  // iOS Emulator: http://localhost:8000/api
}
```

### Setup untuk Berbagai Environment

```dart
// Android Emulator
static const String apiBaseUrl = 'http://10.0.2.2:8000/api';

// Physical Device (Windows)
// Cari IP: ipconfig -> IPv4 Address
static const String apiBaseUrl = 'http://192.168.1.10:8000/api';

// Physical Device (Mac)
// Cari IP: ifconfig -> inet
static const String apiBaseUrl = 'http://192.168.1.20:8000/api';

// iOS Emulator
static const String apiBaseUrl = 'http://localhost:8000/api';
```

---

## Authentication Flow

### 1. Login Process

```
┌─────────────────┐
│  User Input     │
│  Email/Password │
└────────┬────────┘
         │
         ↓
┌──────────────────────────────────┐
│  POST /api/login                 │
│  Body: {email, password}         │
└────────┬─────────────────────────┘
         │
         ↓
┌──────────────────────────────────┐
│  Laravel Backend                 │
│  - Hash password check           │
│  - Generate token                │
│  - Return token + user data      │
└────────┬─────────────────────────┘
         │
         ↓
┌──────────────────────────────────┐
│  Mobile App                      │
│  - Save token ke SharedPref      │
│  - Set isLoggedIn = true         │
│  - Navigate ke HomeScreen        │
└──────────────────────────────────┘
```

### 2. Token Management

```dart
// Token disimpan di SharedPreferences
await _prefs.setString(AppConfig.tokenKey, token);

// Token diambil saat startup
_token = _prefs.getString(AppConfig.tokenKey);

// Token ditambahkan ke header setiap request
headers['Authorization'] = 'Bearer $_token';
```

### 3. Logout Process

```dart
// Clear token dari storage
await http.post('/api/logout', headers: {'Authorization': 'Bearer $token'});
await _prefs.remove(AppConfig.tokenKey);
_token = null;

// Redirect ke LoginScreen
```

---

## API Endpoints

### 📊 Authentication Endpoints

```
┌─────────────┬──────────────────────────────────┐
│ Method      │ Endpoint                         │
├─────────────┼──────────────────────────────────┤
│ POST        │ /login                           │
│ POST        │ /logout                          │
│ GET         │ /profile                         │
│ GET         │ /user                            │
└─────────────┴──────────────────────────────────┘

POST /api/login
├─ Email: required|email
├─ Password: required|min:6
└─ Response: { token, user, message }

GET /api/profile
├─ Headers: Authorization: Bearer {token}
└─ Response: { user data }
```

### 📦 Pesanan (Orders) Endpoints

```
┌─────────────┬──────────────────────────────────┐
│ Method      │ Endpoint                         │
├─────────────┼──────────────────────────────────┤
│ GET         │ /pesanan                         │
│ POST        │ /pesanan                         │
│ GET         │ /pesanan/{id}                    │
│ PUT         │ /pesanan/{id}                    │
│ DELETE      │ /pesanan/{id}                    │
│ PATCH       │ /pesanan/{id}/status             │
└─────────────┴──────────────────────────────────┘

GET /api/pesanan
├─ Query Params:
│  ├─ status: [aktif|menunggu|siap_kirim|selesai|batal]
│  ├─ page: integer
│  └─ limit: integer
├─ Headers: Authorization: Bearer {token}
└─ Response: [{ pesanan_data }]

POST /api/pesanan
├─ Body: {
│   "pelanggan_id": integer,
│   "produk_id": integer,
│   "jumlah": integer,
│   "harga": integer,
│   "tanggal_pesanan": date,
│   "catatan": string
│ }
├─ Headers: Authorization: Bearer {token}
└─ Response: { pesanan_data }

PATCH /api/pesanan/{id}/status
├─ Body: { "status": "selesai" }
├─ Headers: Authorization: Bearer {token}
└─ Response: { success: true/false }
```

### 👥 Pelanggan (Customers) Endpoints

```
┌─────────────┬──────────────────────────────────┐
│ Method      │ Endpoint                         │
├─────────────┼──────────────────────────────────┤
│ GET         │ /pelanggan                       │
│ POST        │ /pelanggan                       │
│ GET         │ /pelanggan/{id}                  │
│ PUT         │ /pelanggan/{id}                  │
│ DELETE      │ /pelanggan/{id}                  │
│ GET         │ /pelanggan/search                │
└─────────────┴──────────────────────────────────┘

GET /api/pelanggan
├─ Query Params: page, limit
├─ Headers: Authorization: Bearer {token}
└─ Response: [{ pelanggan_data }]

POST /api/pelanggan
├─ Body: {
│   "nama": string,
│   "email": email,
│   "nohp": string,
│   "alamat": string,
│   "kota": string,
│   "provinsi": string,
│   "kodepos": string
│ }
├─ Headers: Authorization: Bearer {token}
└─ Response: { pelanggan_data }

PUT /api/pelanggan/{id}
├─ Body: { sama seperti POST }
├─ Headers: Authorization: Bearer {token}
└─ Response: { pelanggan_data }

DELETE /api/pelanggan/{id}
├─ Headers: Authorization: Bearer {token}
└─ Response: { success: true/false }
```

### 🛍️ Produk (Products) Endpoints

```
┌─────────────┬──────────────────────────────────┐
│ Method      │ Endpoint                         │
├─────────────┼──────────────────────────────────┤
│ GET         │ /produk                          │
│ GET         │ /produk/{id}                     │
└─────────────┴──────────────────────────────────┘

GET /api/produk
├─ Query Params: page, limit
├─ Headers: Authorization: Bearer {token}
└─ Response: [{ produk_data }]

GET /api/produk/{id}
├─ Headers: Authorization: Bearer {token}
└─ Response: { produk_data }
```

### 📊 Dashboard Endpoints

```
┌─────────────┬──────────────────────────────────┐
│ Method      │ Endpoint                         │
├─────────────┼──────────────────────────────────┤
│ GET         │ /dashboard                       │
└─────────────┴──────────────────────────────────┘

GET /api/dashboard
├─ Headers: Authorization: Bearer {token}
└─ Response: {
     "total_aktif": integer,
     "total_menunggu": integer,
     "total_siap_kirim": integer,
     "total_selesai_bulan_ini": integer,
     "nilai_selesai_bulan_ini": integer
   }
```

### 🔔 Notifikasi (Notifications) Endpoints

```
┌─────────────┬──────────────────────────────────┐
│ Method      │ Endpoint                         │
├─────────────┼──────────────────────────────────┤
│ GET         │ /notifikasi                      │
└─────────────┴──────────────────────────────────┘

GET /api/notifikasi
├─ Query Params: page, limit
├─ Headers: Authorization: Bearer {token}
└─ Response: [{ notifikasi_data }]
```

### 📁 Arsip PDF Endpoints

```
┌─────────────┬──────────────────────────────────┐
│ Method      │ Endpoint                         │
├─────────────┼──────────────────────────────────┤
│ GET         │ /arsip-pdf                       │
│ GET         │ /arsip-pdf/{id}                  │
│ DELETE      │ /arsip-pdf/{id}                  │
└─────────────┴──────────────────────────────────┘

GET /api/arsip-pdf
├─ Query Params: page, limit
├─ Headers: Authorization: Bearer {token}
└─ Response: [{ arsip_data }]

DELETE /api/arsip-pdf/{id}
├─ Headers: Authorization: Bearer {token}
└─ Response: { success: true/false }
```

---

## Request & Response Format

### Standard Request Headers

```dart
{
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'Authorization': 'Bearer {token}'  // Untuk protected routes
}
```

### Standard Response Format

#### Success Response (200, 201)

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  }
}
```

#### Error Response (4xx, 5xx)

```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Error detail"]
  }
}
```

### Example: Login Request & Response

**Request:**

```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response (200 OK):**

```json
{
  "token": "1|abcdefghijklmnopqrstuvwxyz",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com",
    "phone": "08123456789",
    "role": "admin",
    "avatar": "https://...",
    "created_at": "2024-01-01T10:00:00Z"
  }
}
```

**Response (401 Unauthorized):**

```json
{
  "message": "The provided credentials are incorrect."
}
```

---

## Error Handling

### API Service Error Handling

```dart
Future<List<Pesanan>> getPesanan({String? status}) async {
  try {
    final response = await http.get(
      Uri.parse(url),
      headers: await _getHeaders(),
    );

    // Check status code
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      return pesananList;
    } else if (response.statusCode == 401) {
      // Token expired - redirect to login
      clearToken();
      return [];
    } else {
      // Handle other errors
      return [];
    }
  } catch (e) {
    // Network error
    print('Error: $e');
    return [];
  }
}
```

### Provider Error Handling

```dart
Future<void> fetchPesanan({String? status}) async {
  _isLoading = true;
  _errorMessage = null;
  notifyListeners();

  try {
    _pesananList = await _apiService.getPesanan(status: status);
    _isLoading = false;
  } catch (e) {
    _errorMessage = 'Error: $e';
    _isLoading = false;
  }
  notifyListeners();
}
```

### Screen Error Handling

```dart
Consumer<PesananProvider>(
  builder: (context, provider, _) {
    if (provider.isLoading) {
      return LoadingWidget();
    }

    if (provider.errorMessage != null) {
      return EmptyStateWidget(
        message: provider.errorMessage!,
        onRetry: () => provider.fetchPesanan(),
      );
    }

    return ListView(...);
  }
)
```

---

## Testing API

### Testing dengan Postman

**1. Setup Postman**

```
- Base URL: http://localhost:8000/api
- Headers (untuk setiap request):
  - Content-Type: application/json
  - Accept: application/json
```

**2. Login Test**

```
POST http://localhost:8000/api/login
Body (raw JSON):
{
  "email": "admin@example.com",
  "password": "password"
}

Response:
{
  "token": "1|...",
  "user": {...}
}
```

**3. Authenticated Request Test**

```
GET http://localhost:8000/api/pesanan

Headers:
- Authorization: Bearer 1|... (copy token dari login response)

Response: List pesanan
```

### Testing dengan Flutter Debugging

```dart
// Tambahkan logging di api_service.dart
print('Request: $url');
print('Status: ${response.statusCode}');
print('Response: ${response.body}');

// Atau gunakan Flutter DevTools
flutter pub global activate devtools
devtools
```

### Testing Network Calls

```dart
// Inspect network calls di Flutter DevTools
// Database tab -> Show Network calls
// Lihat semua HTTP request/response
```

---

## 🔍 Debugging Tips

### 1. Check API Connectivity

```bash
# Dari terminal, test API langsung
curl http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### 2. Enable HTTP Logging

```dart
// Di api_service.dart, tambahkan:
import 'dart:developer' as developer;

developer.log('Request to: $url');
developer.log('Status: ${response.statusCode}');
```

### 3. Monitor Network Requests

```
Flutter DevTools → DevTools → Network tab
Lihat semua API requests dan responses secara real-time
```

### 4. Test Token Expiry

```dart
// Modify app_config.dart untuk test expired token:
static const String apiBaseUrl = 'http://localhost:8000/api';

// Maka app akan redirect ke login saat token expired
```

---

## Summary

- ✅ API fully integrated dengan Flutter
- ✅ Token-based authentication (Sanctum)
- ✅ Proper error handling
- ✅ Request/response logging
- ✅ All CRUD operations supported
- ✅ Real-time data sync dengan web

---

**Next**: Baca [MOBILE_APP_DOCUMENTATION.md](MOBILE_APP_DOCUMENTATION.md) untuk fitur lengkap.
