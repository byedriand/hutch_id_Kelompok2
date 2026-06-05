# API Synchronization Summary - Mobile to Website

## Overview

The mobile app's API data structure has been synchronized with the website's form data structure for the pesanan (order) filtering functionality.

## Changes Made

### 1. API Service Updates (`lib/services/api_service.dart`)

#### Modified Method: `getPesanan()`

The `getPesanan()` method has been updated to accept the following optional filter parameters:

```dart
static Future<List<Map<String, dynamic>>> getPesanan({
  String? cari,              // Search keyword (PO, Customer, Product)
  String? status,            // Order status filter
  String? dari,              // Date from
  String? sampai,            // Date until
  int? minTotal,             // Minimum total amount
  int? maxTotal,             // Maximum total amount
  String? produk,            // Product name
  bool? multiItem,           // Filter multi-item orders only
})
```

**Query Parameters Sent:**

- `cari` - Search term for PO number, customer name, or product
- `status` - Order status (menunggu_konfirmasi, dikonfirmasi, dalam_produksi, siap_kirim, selesai, dibatalkan)
- `dari` - Start date in YYYY-MM-DD format
- `sampai` - End date in YYYY-MM-DD format
- `min_total` - Minimum order value in Rupiah
- `max_total` - Maximum order value in Rupiah
- `produk` - Product name to search
- `multi_item` - Include this parameter with value 'on' for multi-item filter

### 2. Main Home Screen Updates (`lib/screens/main_home_screen.dart`)

#### New Filter State Variables in `_MainHomeScreenState`:

```dart
String? _filterCari;
String? _filterStatus;
String? _filterDari;
String? _filterSampai;
int? _filterMinTotal;
int? _filterMaxTotal;
String? _filterProduk;
bool _filterMultiItem = false;
```

#### New Methods:

- `_applyFilters()` - Applies the selected filters by calling `_loadAllData()`
- `_resetFilters()` - Resets all filters to null/false and reloads data

#### Updated Method: `_loadAllData()`

Now passes all filter parameters to `ApiService.getPesanan()`:

```dart
final pesananData = await ApiService.getPesanan(
  cari: _filterCari,
  status: _filterStatus,
  dari: _filterDari,
  sampai: _filterSampai,
  minTotal: _filterMinTotal,
  maxTotal: _filterMaxTotal,
  produk: _filterProduk,
  multiItem: _filterMultiItem,
);
```

### 3. UI Component Updates

#### Modified: `DaftarPesananScreenContent` Widget

Added filter parameters and callbacks:

- Filter state properties to receive current filter values
- Callback functions to update filter values when user interacts with UI
- `onApplyFilters` and `onResetFilters` callbacks

#### New Filter Form Method: `_buildFilterForm()`

Displays a responsive filter form with:

- **Search Input** - Search by keyword (PO, Customer, Product)
- **Status Dropdown** - Filter by order status
- **Product Name Input** - Search by product name
- **Date Range Inputs** - Filter by date from and until
- **Amount Range Inputs** - Filter by minimum and maximum amount
- **Multi-Item Checkbox** - Filter for multi-item orders only
- **Apply Button** - Applies the selected filters
- **Reset Button** - Clears all filters and reloads data

## Filter Form Layout

The filter form is displayed in a responsive grid:

- **Mobile (1 column):** All fields stack vertically
- **Desktop (3 columns):** Fields organized in a 3-column grid

## API Endpoint

All filters are sent as query parameters to the same endpoint:

```
GET /pesanan?cari=...&status=...&dari=...&sampai=...&min_total=...&max_total=...&produk=...&multi_item=on
```

## Status Mapping

The mobile app now supports the same status values as the website:

- `menunggu_konfirmasi` - Waiting for Confirmation
- `dikonfirmasi` - Confirmed
- `dalam_produksi` - In Production
- `siap_kirim` - Ready to Ship
- `selesai` - Completed
- `dibatalkan` - Cancelled

## Backward Compatibility

The `getPesanan()` method remains backward compatible. Calling it without parameters will fetch all pesanan (no filters applied):

```dart
// Still works - fetches all pesanan
final allPesanan = await ApiService.getPesanan();

// With filters
final filteredPesanan = await ApiService.getPesanan(
  cari: 'PO-001',
  status: 'dalam_produksi',
);
```

## Testing Checklist

- [ ] Filter by search keyword works
- [ ] Filter by status works
- [ ] Filter by date range works
- [ ] Filter by amount range works
- [ ] Filter by product name works
- [ ] Multi-item filter works
- [ ] Multiple filters combined work together
- [ ] Reset button clears all filters
- [ ] Apply button sends filters to API
- [ ] API returns filtered results matching website

## Files Modified

1. `lib/services/api_service.dart` - API method signature updated
2. `lib/screens/main_home_screen.dart` - Filter state, methods, and UI components added
