# 🚀 Hutch Mobile App - Quick Start & Testing Guide

## ✅ Completed Synchronization

The mobile app has been updated to match the website functionality. Here's what was fixed:

### 📦 Data Model Fixes

- **Pelanggan Model**: Now uses correct database fields (telepon, alamat, catatan)
- **Pesanan Model**: Updated field names (nomor_po, tanggal_pengiriman, total_nilai)
- **DetailPesanan**: New model added for order line items
- **Produk Model**: Corrected field names (harga_jual, keterangan)

### 🔧 API Integration Fixes

- API responses now properly parsed in both list and detail views
- All endpoints return complete data with relationships
- Database has real data: 2 customers, 2 orders, 8 products

### 🎯 Navigation Changes

- ✂️ **Profile menu removed** (doesn't exist on website)
- Navigation now matches website exactly
- Role-based menus: Operator Gudang (3 items), Staff (5 items), Admin (5 items)

---

## 🧪 How to Test the Mobile App

### Option 1: Android Emulator

```bash
cd c:\xampp\htdocs\hutch_id_mobile\hutch_id_mobile_orderflow
flutter emulators --launch <emulator_name>
flutter run
```

### Option 2: Physical Device (with USB debugging)

```bash
cd c:\xampp\htdocs\hutch_id_mobile\hutch_id_mobile_orderflow
flutter run
```

### Option 3: Web (for quick testing)

```bash
cd c:\xampp\htdocs\hutch_id_mobile\hutch_id_mobile_orderflow
flutter run -d chrome --web-renderer=html
```

---

## 🧑‍💻 Test Login Credentials

Use any of these accounts created in the system:

- **Email**: Check the database users table
- **Backend**: http://localhost:8082/api
- **Default port**: 8082 (already configured in AppConfig)

---

## ✨ What to Verify

### 1. **Data Display** ✓

- [ ] Dashboard loads without errors
- [ ] Pesanan (Orders) shows 2 items
- [ ] Pelanggan (Customers) shows 2 items
- [ ] Produk (Products) shows items with images and prices

### 2. **Navigation** ✓

- [ ] Bottom navigation has correct items per role
- [ ] No "Profile" menu visible
- [ ] All menu items navigate correctly

### 3. **API Integration** ✓

- [ ] Data loads from API (not cached)
- [ ] Pull-to-refresh works
- [ ] Pesanan detail shows line items (detail_pesanan)

### 4. **CRUD Operations**

- [ ] Create new Pelanggan (Customer)
- [ ] Update Pesanan status
- [ ] Delete actions work

---

## 📋 Database Connection Info

```
Host: 127.0.0.1
Port: 3307
Database: hutch
Username: hutch
Password: secret
```

---

## 🎨 UI Improvements Needed

The following UI improvements would make mobile match website better:

1. Improve card designs in list views
2. Add status color indicators
3. Better typography and spacing
4. Add search/filter functionality
5. Improve responsive layout

---

## 🔍 Troubleshooting

### Issue: API Connection Error

- Check if website backend is running: `http://localhost:8082/dashboard`
- Verify AppConfig.apiBaseUrl is correct: `http://localhost:8082/api`
- For physical devices, use network IP instead of localhost

### Issue: Data Not Loading

- Check Flutter console for API errors
- Verify token is being set after login
- Check database has data (customers, orders, products)

### Issue: Fields Missing or Wrong

- All model field names have been updated to match database
- Check API response format matches model expectations

---

## 📱 Quick Commands

```bash
# Get Flutter device list
flutter devices

# Run in debug mode
flutter run -d <device_id>

# Build for release
flutter build apk    # Android
flutter build ios    # iOS

# Check for errors
flutter analyze

# Clean and rebuild
flutter clean
flutter pub get
flutter run
```

---

## 🚀 Next Steps

1. **Test the app** using one of the methods above
2. **Verify all data displays** correctly
3. **Test CRUD operations** (create, read, update, delete)
4. **Improve UI** to match website design
5. **Add missing features** if needed

---

## 📊 Summary of Changes

| Component       | Before         | After                         |
| --------------- | -------------- | ----------------------------- |
| Profile Menu    | ✓ Visible      | ✗ Removed                     |
| Database Models | ✗ Mismatched   | ✓ Synced                      |
| API Responses   | ✗ Wrong format | ✓ Correct format              |
| Data Loading    | ✗ Empty        | ✓ Shows 2 customers, 2 orders |
| Navigation      | ✗ 6-7 items    | ✓ 3-5 items                   |

---

Good luck! 🎉
