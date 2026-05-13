# RBAC Setup Checklist & Quick Reference

## ✅ Implementation Complete

### Files Created

- ✅ `app/Policies/PesananPolicy.php` - Authorization policy
- ✅ `database/migrations/2026_05_13_120000_add_role_to_users_table.php` - Add role column
- ✅ `RBAC_IMPLEMENTATION.md` - Full documentation

### Files Updated

- ✅ `app/Providers/AuthServiceProvider.php` - Register policy
- ✅ `app/Http/Controllers/PesananController.php` - Add confirm() method, improve authorization
- ✅ `routes/web.php` - Organize routes with role-based middleware

---

## 🚀 Next Steps to Finalize

### 1. Run Migration

```bash
cd c:\xampp\htdocs\hutch_id_Kelompok2\hutch_id_Website_OrderFlow
php artisan migrate
```

### 2. Create Test Users (Optional but Recommended)

```bash
php artisan tinker

# Then run:
User::create(['name'=>'Admin','email'=>'admin@hutch.id','password'=>bcrypt('password123'),'role'=>'administrator']);
User::create(['name'=>'Pemilik','email'=>'pemilik@hutch.id','password'=>bcrypt('password123'),'role'=>'pemilik_umkm']);
User::create(['name'=>'Sales','email'=>'staf@hutch.id','password'=>bcrypt('password123'),'role'=>'staf_penjualan']);
User::create(['name'=>'Operator','email'=>'operator@hutch.id','password'=>bcrypt('password123'),'role'=>'operator_gudang']);
```

### 3. Update Views (Optional but Important)

Update your blade templates to show/hide buttons based on user role:

```blade
<!-- In pesanan.show view -->
@can('confirm', $pesanan)
    <form action="{{ route('pesanan.confirm', $pesanan) }}" method="POST">
        @csrf
        <button class="btn btn-success">Confirm PO</button>
    </form>
@endcan

@can('changeStatus', $pesanan)
    <form action="{{ route('pesanan.updateStatus', $pesanan) }}" method="POST">
        @csrf
        @method('PATCH')
        <select name="status" class="form-control">
            @if(auth()->user()->role === 'operator_gudang')
                <option value="dalam_produksi">Dalam Produksi</option>
            @elseif(auth()->user()->role === 'pemilik_umkm')
                <option value="dalam_produksi">Dalam Produksi</option>
                <option value="siap_kirim">Siap Kirim</option>
                <option value="selesai">Selesai</option>
            @elseif(auth()->user()->role === 'administrator')
                <option value="dalam_produksi">Dalam Produksi</option>
                <option value="siap_kirim">Siap Kirim</option>
                <option value="selesai">Selesai</option>
                <option value="dibatalkan">Batalkan</option>
            @endif
        </select>
        <button class="btn btn-primary">Update Status</button>
    </form>
@endcan

@can('cancel', $pesanan)
    <form action="{{ route('pesanan.batalkan', $pesanan) }}" method="POST" style="display:inline;">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger" onclick="return confirm('Cancel this PO?')">Cancel PO</button>
    </form>
@endcan
```

### 4. Update Menu/Navigation (Optional)

```blade
@auth
    @if(auth()->user()->role === 'administrator')
        <li><a href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
    @endif

    @if(in_array(auth()->user()->role, ['administrator', 'pemilik_umkm']))
        <li><a href="{{ route('arsip.index') }}">Archive</a></li>
    @endif

    @if(in_array(auth()->user()->role, ['staf_penjualan', 'pemilik_umkm', 'administrator']))
        <li><a href="{{ route('pesanan.create') }}">Create PO</a></li>
        <li><a href="{{ route('pelanggan.index') }}">Customers</a></li>
    @endif
@endauth
```

---

## 🧪 Testing Procedures

### Test 1: Administrator Access

- Login as `admin@hutch.id`
- ✓ Can see all menus
- ✓ Can create PO
- ✓ Can confirm PO
- ✓ Can update to any status
- ✓ Can cancel PO
- ✓ Can access admin dashboard
- ✓ Can manage customers

### Test 2: Pemilik UMKM Access

- Login as `pemilik@hutch.id`
- ✓ Can create PO
- ✓ Can confirm PO
- ✓ Can update status (hanya: dalam_produksi, siap_kirim, selesai)
- ✓ Can cancel PO
- ✓ Can manage customers
- ✓ Cannot access admin dashboard
- ✓ Can access archive

### Test 3: Staf Penjualan Access

- Login as `staf@hutch.id`
- ✓ Can create PO
- ✓ Can only see own PO
- ✓ Can download own PO PDF
- ✓ Can manage customers
- ✗ Cannot confirm PO
- ✗ Cannot change status
- ✗ Cannot cancel PO
- ✗ Cannot access archive

### Test 4: Operator Gudang Access

- Login as `operator@hutch.id`
- ✓ Can see PO list (confirmed only)
- ✓ Can see PO details
- ✓ Can update status to "dalam_produksi" only
- ✓ Can download PDF
- ✗ Cannot create PO
- ✗ Cannot confirm PO
- ✗ Cannot access customers
- ✗ Cannot access archive

---

## 🔍 Access Control Matrix

| Action           | Admin    | Pemilik     | Staf | Operator            |
| ---------------- | -------- | ----------- | ---- | ------------------- |
| Create PO        | ✅       | ✅          | ✅   | ❌                  |
| View Own PO      | ✅       | ✅          | ✅\* | ✅\*                |
| View All PO      | ✅       | ✅          | ❌   | ✅ (confirmed only) |
| Confirm PO       | ✅       | ✅          | ❌   | ❌                  |
| Update Status    | ✅ (all) | ✅ (3 only) | ❌   | ✅ (1 only)         |
| Cancel PO        | ✅       | ✅          | ❌   | ❌                  |
| Manage Customers | ✅       | ✅          | ✅   | ❌                  |
| View Archive     | ✅       | ✅          | ❌   | ❌                  |
| Admin Dashboard  | ✅       | ❌          | ❌   | ❌                  |

\*Own PO / Confirmed PO only

---

## 📞 Troubleshooting

### Issue: "This action is unauthorized" (403 error)

**Possible causes:**

- User doesn't have required role
- Check user role: `auth()->user()->role`
- Verify policy allows this action

**Solution:**

```bash
# Check user in database
php artisan tinker
User::where('email', 'user@email.com')->first()
# Update role if needed
User::find(1)->update(['role' => 'administrator'])
```

### Issue: User can't see "Confirm PO" button

**Possible cause:**

- User role is `staf_penjualan` (only pemilik_umkm/admin can confirm)
- OR the policy isn't registered

**Solution:**

- Check role is correct
- Verify `AuthServiceProvider` has policy registration
- Clear cache: `php artisan cache:clear`

### Issue: Operator Gudang sees "menunggu_konfirmasi" PO

**Possible cause:**

- PO hasn't been confirmed yet
- They should only see confirmed PO

**Solution:**

- Confirm the PO first (change status to "dikonfirmasi")
- Then it will appear in operator's list

### Issue: Staf sees other user's PO

**Possible cause:**

- Policy might not be checking `created_by`

**Solution:**

- Verify in `PesananController::index()` role-based filtering
- Check `created_by` filter for staf_penjualan

---

## 📚 Files Reference

```
App Structure:
├── app/
│   ├── Models/
│   │   └── User.php                    (has role field)
│   ├── Http/
│   │   └── Controllers/
│   │       ├── PesananController.php   (UPDATED - confirm, authorize)
│   │       └── PelangganController.php (CHECKED - OK)
│   ├── Policies/
│   │   └── PesananPolicy.php          (NEW)
│   └── Providers/
│       └── AuthServiceProvider.php    (UPDATED - register policy)
├── database/
│   └── migrations/
│       └── 2026_05_13_120000_add_role_to_users_table.php (NEW)
├── routes/
│   └── web.php                        (UPDATED - organized middleware)
└── RBAC_IMPLEMENTATION.md             (NEW - documentation)
```

---

## ✨ Key Points Summary

✅ **4 Roles Implemented**: admin, pemilik_umkm, staf_penjualan, operator_gudang  
✅ **Route-level Protection**: Role middleware on all sensitive routes  
✅ **Policy-level Authorization**: Fine-grained control per action  
✅ **PO Confirmation Flow**: New confirm() method for pemilik_umkm/admin  
✅ **Status Update Control**: Role-specific allowed status transitions  
✅ **Data Filtering**: Users see only authorized data  
✅ **Audit Trail**: All changes recorded in histori_status

---

**Status**: ✅ IMPLEMENTATION COMPLETE  
**Ready for**: Migration and testing  
**Deployment Date**: May 13, 2026
