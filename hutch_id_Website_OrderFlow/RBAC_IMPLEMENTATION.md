# RBAC Implementation Guide - Hutch ID System

## 📋 Overview

This document outlines the complete Role-Based Access Control (RBAC) system implemented for the Hutch ID Order Flow management system.

---

## 👥 Four User Roles

### 1️⃣ Administrator (`administrator`)

**Email Example**: admin@hutch.id

- **Access Level**: SUPERUSER - Full system access
- **PO Rights**:
    - ✅ Create, view, edit, confirm, cancel any PO
    - ✅ Update to ANY status (menunggu_konfirmasi → dikonfirmasi → dalam_produksi → siap_kirim → selesai)
    - ✅ Update dibatalkan status
- **Customer Rights**: ✅ Full CRUD
- **Dashboard**: ✅ Admin dashboard + regular dashboard
- **Archive**: ✅ Full access

### 2️⃣ Pemilik UMKM (Owner) - `pemilik_umkm`

**Email Example**: pemilik@hutch.id

- **Access Level**: MANAGER - Almost full like Admin
- **PO Rights**:
    - ✅ Create and confirm PO
    - ✅ Update status to: `dalam_produksi`, `siap_kirim`, `selesai`
    - ✅ Cancel PO (set to dibatalkan)
    - ❌ Cannot change to other statuses
- **Customer Rights**: ✅ Full CRUD
- **Dashboard**: ✅ Regular dashboard
- **Archive**: ✅ Full access

### 3️⃣ Staf Penjualan (Sales Staff) - `staf_penjualan`

**Email Example**: staf@hutch.id

- **Access Level**: LIMITED - PO creation & viewing
- **PO Rights**:
    - ✅ Create new PO
    - ✅ View/print own created PO
    - ❌ Cannot confirm PO
    - ❌ Cannot change status
    - ❌ Cannot cancel PO
    - ❌ Cannot edit PO
- **Customer Rights**: ✅ Full CRUD
- **Dashboard**: ✅ Regular dashboard (only own PO)
- **PDF Download**: ✅ Own PO only

### 4️⃣ Operator Gudang (Warehouse Operator) - `operator_gudang`

**Email Example**: operator@hutch.id

- **Access Level**: MINIMAL - Production status updates only
- **PO Rights**:
    - ✅ View confirmed PO list (dikonfirmasi, dalam_produksi, siap_kirim, selesai)
    - ✅ Update status to: `dalam_produksi` ONLY
    - ❌ Cannot create PO
    - ❌ Cannot confirm PO
    - ❌ Cannot change to other statuses
    - ❌ Cannot cancel PO
- **Customer Rights**: ❌ NO access to customer management
- **Dashboard**: ✅ Regular dashboard (confirmed PO only)
- **Archive**: ❌ No access

---

## 🔧 Implementation Details

### Files Created/Modified

#### 1. New Policy Class

**File**: `app/Policies/PesananPolicy.php`

```
Handles authorization for PO operations:
- view()          : Who can view this PO
- create()        : Who can create PO
- update()        : Who can edit PO details
- confirm()       : Who can confirm PO
- changeStatus()  : Who can change status
- cancel()        : Who can cancel PO
- delete()        : Who can delete PO
- downloadPdf()   : Who can download PDF
- canChangeStatusTo() : Role-based status validation
```

#### 2. Updated Controller

**File**: `app/Http/Controllers/PesananController.php`

```
New Methods:
- confirm()              : Confirm PO (menunggu_konfirmasi → dikonfirmasi)

Updated Methods:
- show()                 : Uses policy authorization
- edit()                 : Uses policy authorization
- update()               : Uses policy authorization
- destroy()              : Uses policy authorization
- updateStatus()         : Improved validation, role checking
- index()                : Operator Gudang sees confirmed PO only

Helper Methods:
- canChangeStatusTo()    : Validates status transitions by role
```

#### 3. Migration

**File**: `database/migrations/2026_05_13_120000_add_role_to_users_table.php`

```
Adds:
- role column (ENUM: administrator, pemilik_umkm, staf_penjualan, operator_gudang)
- Default value: staf_penjualan
```

#### 4. Service Provider

**File**: `app/Providers/AuthServiceProvider.php`

```
Registers PesananPolicy with Pesanan model
```

#### 5. Routes

**File**: `routes/web.php`

```
Organized by functionality:
- PO Creation routes
- PO Confirmation routes
- Status Update routes
- PO Cancel/Delete routes
- PO View & Download routes
- PO Edit & Share routes
- Customer Management routes
- Archive routes
- API routes
- Admin Dashboard routes
```

---

## 🚀 Usage Examples

### In Controller

```php
// Check authorization using policy
$this->authorize('confirm', $pesanan);

// Or specific action check
$this->authorize('canChangeStatusTo', [$pesanan, 'dalam_produksi']);
```

### In Routes

```php
// Specific roles only
Route::post('/pesanan/{pesanan}/confirm', ...)
    ->middleware(['role:pemilik_umkm,administrator']);

// Multiple actions grouped
Route::middleware(['role:staf_penjualan,pemilik_umkm,administrator'])->group(function () {
    Route::resource('pelanggan', PelangganController::class);
});
```

### In Views (Blade)

```blade
@can('confirm', $pesanan)
    <button>Confirm PO</button>
@endcan

@can('changeStatus', $pesanan)
    <button>Update Status</button>
@endcan
```

---

## 📊 PO Status Workflow

```
NORMAL FLOW:
┌─────────────────────┐
│ menunggu_konfirmasi │ (Created by: staf_penjualan/pemilik_umkm/admin)
└──────────┬──────────┘
           │ confirm (pemilik_umkm/admin)
           ▼
      ┌──────────────┐
      │ dikonfirmasi │ (Ready for production)
      └──────┬───────┘
             │ updateStatus (pemilik_umkm/admin/operator)
             ▼
      ┌────────────────┐
      │ dalam_produksi │ (In progress)
      └──────┬─────────┘
             │ updateStatus (pemilik_umkm/admin)
             ▼
      ┌─────────────┐
      │ siap_kirim  │ (Ready to ship)
      └──────┬──────┘
             │ updateStatus (pemilik_umkm/admin)
             ▼
      ┌────────┐
      │ selesai│ (Completed - Final)
      └────────┘

CANCELLATION (any status):
Any status → dibatalkan (cancel by pemilik_umkm/admin)
```

---

## 🛡️ Security Features

✅ Route-level middleware checking  
✅ Policy-based authorization  
✅ Role validation on every action  
✅ Proper error handling (403 Forbidden)  
✅ Audit trail (all changes logged)  
✅ Data isolation (users see only authorized data)

---

## ⚙️ Setup Instructions

### 1. Run Migration

```bash
php artisan migrate
```

### 2. Create Test Users

```bash
php artisan tinker

# Administrator
User::create([
    'name' => 'Admin',
    'email' => 'admin@hutch.id',
    'password' => bcrypt('password123'),
    'role' => 'administrator'
]);

# Owner
User::create([
    'name' => 'Pemilik',
    'email' => 'pemilik@hutch.id',
    'password' => bcrypt('password123'),
    'role' => 'pemilik_umkm'
]);

# Sales Staff
User::create([
    'name' => 'Sales',
    'email' => 'staf@hutch.id',
    'password' => bcrypt('password123'),
    'role' => 'staf_penjualan'
]);

# Warehouse Operator
User::create([
    'name' => 'Operator',
    'email' => 'operator@hutch.id',
    'password' => bcrypt('password123'),
    'role' => 'operator_gudang'
]);
```

### 3. Test Each Role

- Login with each email and verify dashboard/menu access
- Test PO creation with each role
- Test status transitions per role

---

## 📝 API Endpoints Reference

### For Authenticated Users (All Roles)

```
GET  /dashboard              - User dashboard (filtered by role)
GET  /pesanan                - PO list (filtered by role)
GET  /pesanan/{id}           - PO details
GET  /pesanan/{id}/pdf       - Download PO PDF
```

### For Staf Penjualan, Pemilik UMKM, Administrator

```
GET  /pesanan/create         - Create form
POST /pesanan                - Store new PO
GET  /pelanggan              - Customer list
GET  /pelanggan/create       - Create customer form
POST /pelanggan              - Store customer
```

### For Pemilik UMKM, Administrator

```
GET  /pesanan/{id}/edit      - Edit form
PUT  /pesanan/{id}           - Update PO
POST /pesanan/{id}/confirm   - Confirm PO
DELETE /pesanan/{id}         - Cancel PO
PATCH /pesanan/{id}/status   - Update status
GET  /arsip                   - Archive
```

### For Pemilik UMKM, Operator Gudang, Administrator

```
PATCH /pesanan/{id}/status   - Update status (role-limited)
```

### For Administrator Only

```
GET  /admin/dashboard        - Admin dashboard
```

---

## 🐛 Troubleshooting

**Issue**: User getting 403 error

- **Solution**: Check user role in users table, verify policy rules

**Issue**: Status update not working

- **Solution**: Check current PO status, verify transition rules in policy

**Issue**: Users seeing other's PO

- **Solution**: Check role-based filtering in index() method

**Issue**: Operator Gudang can't see PO

- **Solution**: PO must be in "dikonfirmasi" or later status

---

## 📚 References

- **User Model**: `app/Models/User.php`
- **Pesanan Model**: `app/Models/Pesanan.php`
- **Routes**: `routes/web.php`
- **Middleware**: `app/Http/Middleware/CheckRole.php`
- **Policy**: `app/Policies/PesananPolicy.php`

---

**Implementation Date**: May 13, 2026  
**Last Updated**: May 13, 2026
