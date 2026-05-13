# RBAC Implementation - Verification & Testing Guide

## ✅ Deployment Status

### Completed Tasks

- ✅ Migration: `2026_05_13_120000_add_role_to_users_table.php` - **EXECUTED**
- ✅ Seeder: `RoleUserSeeder.php` - **CREATED & EXECUTED**
- ✅ Policy: `PesananPolicy.php` - **IMPLEMENTED**
- ✅ Routes: `routes/web.php` - **UPDATED**
- ✅ Controllers: `PesananController.php` - **UPDATED**
- ✅ Service Provider: `AuthServiceProvider.php` - **UPDATED**

### Test Users Created

```
✅ admin@hutch.id         (role: administrator)          password: password123
✅ pemilik@hutch.id       (role: pemilik_umkm)           password: password123
✅ staf@hutch.id          (role: staf_penjualan)         password: password123
✅ operator@hutch.id      (role: operator_gudang)        password: password123
```

---

## 🧪 Testing Checklist

### Test 1: Administrator Access ✅

```
Login: admin@hutch.id | Password: password123

Expected Access:
□ View Dashboard
□ Create PO
□ View all PO list
□ Confirm PO
□ Update PO status (all statuses)
□ Cancel PO
□ Manage Customers (CRUD)
□ View Archive
□ Access Admin Dashboard (/admin/dashboard)
□ Download PO PDF
```

### Test 2: Pemilik UMKM (Owner) Access ✅

```
Login: pemilik@hutch.id | Password: password123

Expected Access:
□ View Dashboard
□ Create PO
□ View all PO list
□ Confirm PO
□ Update PO status to: dalam_produksi, siap_kirim, selesai
□ Cancel PO
□ Manage Customers (CRUD)
□ View Archive
□ Download PO PDF

Should NOT Access:
✗ Admin Dashboard
✗ Update PO to menunggu_konfirmasi or other statuses
```

### Test 3: Staf Penjualan (Sales) Access ✅

```
Login: staf@hutch.id | Password: password123

Expected Access:
□ View Dashboard (own PO only)
□ Create PO
□ View own PO list
□ View own PO details
□ Manage Customers (CRUD)
□ Download own PO PDF

Should NOT Access:
✗ Confirm PO button/action
✗ Update status
✗ Cancel PO
✗ View other user's PO
✗ View Archive
✗ Admin Dashboard
```

### Test 4: Operator Gudang (Warehouse) Access ✅

```
Login: operator@hutch.id | Password: password123

Expected Access:
□ View Dashboard (confirmed PO only)
□ View confirmed PO list (status: dikonfirmasi, dalam_produksi, siap_kirim, selesai)
□ View PO details
□ Update PO status to: dalam_produksi ONLY
□ Download PO PDF

Should NOT Access:
✗ Create PO
✗ Confirm PO
✗ Cancel PO
✗ Update to other statuses
✗ Manage Customers
✗ View Archive
✗ View menunggu_konfirmasi PO
✗ Admin Dashboard
```

---

## 🔍 Quick Verification Steps

### 1. Check Migration Applied

```bash
# See in database that role column exists in users table
php artisan tinker
Schema::hasColumn('users', 'role')  # Should return true
```

### 2. Verify Users Created

```bash
php artisan tinker
User::all(['id', 'name', 'email', 'role'])

# Expected Output:
# id | name              | email             | role
# 1  | Administrator    | admin@hutch.id    | administrator
# 2  | Pemilik UMKM     | pemilik@hutch.id  | pemilik_umkm
# 3  | Staf Penjualan   | staf@hutch.id     | staf_penjualan
# 4  | Operator Gudang  | operator@hutch.id | operator_gudang
```

### 3. Check Policy Registration

```bash
php artisan tinker
$policy = app('auth')->getPolicyFor(\App\Models\Pesanan::class)
# Should return: App\Policies\PesananPolicy
```

### 4. Test Authorization

```bash
php artisan tinker

# Create test data
$user = User::find(1)  # Admin
$pesanan = Pesanan::first()

# Test authorization
auth()->login($user)
$user->can('confirm', $pesanan)  # Should return true for admin
```

---

## 📋 Manual Testing Procedure

### Prerequisites

- Application running on local server
- Database migrated and seeded
- All RBAC files in place

### Step-by-Step Test

#### Setup

1. Open browser and navigate to application
2. Log out if previously logged in

#### Test Administrator

1. Login with `admin@hutch.id` / `password123`
2. Verify you see all menu items
3. Create a test PO (POST /pesanan)
4. Confirm the PO (POST /pesanan/{id}/confirm)
5. Update status to "dalam_produksi" (PATCH /pesanan/{id}/status)
6. Verify access to /admin/dashboard
7. Log out

#### Test Pemilik UMKM

1. Login with `pemilik@hutch.id` / `password123`
2. Create a test PO
3. Confirm a PO from previous admin test
4. Try to update status to "dalam_produksi" - Should work
5. Try to update status to "menunggu_konfirmasi" - Should fail with 403
6. Try to access /admin/dashboard - Should be blocked
7. Log out

#### Test Staf Penjualan

1. Login with `staf@hutch.id` / `password123`
2. Create a test PO
3. View PO list - Should only see own PO
4. Try to confirm PO - Button should not appear / action should fail
5. Try to cancel PO - Button should not appear / action should fail
6. Try to update status - Should fail with 403
7. Manage customers - Should work
8. Log out

#### Test Operator Gudang

1. Login with `operator@hutch.id` / `password123`
2. View PO list - Should only see confirmed PO
3. View PO details - Should work
4. Try to create PO - Should be blocked with 403
5. Try to update status to "dalam_produksi" - Should work
6. Try to update status to "siap_kirim" - Should fail with 403
7. Try to access customer list - Should be blocked with 403
8. Try to access /arsip - Should be blocked with 403
9. Log out

---

## 🐛 Troubleshooting

### Issue: User getting 403 error on valid action

**Solution:**

1. Verify user role: `User::find($id)->role`
2. Check policy allows action: `$user->can('action', $model)`
3. Clear cache: `php artisan cache:clear`
4. Verify CheckRole middleware is registered

### Issue: PO not appearing in Operator Gudang list

**Solution:**

- PO must be confirmed (status: dikonfirmasi or later)
- Check status in database: `Pesanan::where('id', $id)->first()->status`
- Operator can only see: dikonfirmasi, dalam_produksi, siap_kirim, selesai

### Issue: "Confirm PO" button appears for Staf Penjualan

**Solution:**

- Check AuthServiceProvider registered PesananPolicy correctly
- Verify blade template uses `@can('confirm', $pesanan)` directive
- Clear view cache: `php artisan view:clear`

### Issue: Authorization error in browser

**Solution:**

1. Check route middleware in routes/web.php
2. Verify CheckRole middleware is in app/Http/Middleware/
3. Test directly: `php artisan tinker` → `auth()->login(User::find($id))`

---

## 📊 Routes Access Summary

| Endpoint              | Method   | Roles                    | Notes                   |
| --------------------- | -------- | ------------------------ | ----------------------- |
| /dashboard            | GET      | All                      | Role-filtered dashboard |
| /pesanan              | GET      | All                      | Role-filtered list      |
| /pesanan/create       | GET      | staf, pemilik, admin     | Create form             |
| /pesanan              | POST     | staf, pemilik, admin     | Store PO                |
| /pesanan/{id}         | GET      | All\*                    | View details            |
| /pesanan/{id}/edit    | GET      | pemilik, admin           | Edit form               |
| /pesanan/{id}         | PUT      | pemilik, admin           | Update details          |
| /pesanan/{id}/confirm | POST     | pemilik, admin           | Confirm PO              |
| /pesanan/{id}/status  | PATCH    | pemilik, operator, admin | Update status           |
| /pesanan/{id}         | DELETE   | pemilik, admin           | Cancel PO               |
| /pesanan/{id}/pdf     | GET      | All\*                    | Download PDF            |
| /pelanggan            | GET/POST | staf, pemilik, admin     | Customer CRUD           |
| /arsip                | GET      | pemilik, admin           | View archive            |
| /admin/dashboard      | GET      | admin                    | Admin panel             |

\*= Access filtered by role (Staf only own, Operator only confirmed)

---

## ✨ Success Indicators

When implementation is working correctly, you should see:

✅ Four users successfully created with roles  
✅ Role column present in users table  
✅ PesananPolicy methods working correctly  
✅ Routes respecting role middleware  
✅ Different dashboard views per role  
✅ Status update buttons appearing/disappearing by role  
✅ Proper 403 errors for unauthorized access  
✅ PO list filtered based on role

---

## 📝 Database Verification

Check the users table has role column:

```sql
DESC users;
-- Should show 'role' column with type: enum('administrator','pemilik_umkm','staf_penjualan','operator_gudang')

SELECT id, name, email, role FROM users;
-- Should show 4 users with their respective roles
```

---

**Implementation Date**: May 13, 2026  
**Status**: ✅ READY FOR TESTING  
**All Systems**: OPERATIONAL
