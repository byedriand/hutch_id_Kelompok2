// ============================================================
// models.dart — Data models Modul PO hutch.id
// ============================================================

enum PoStatus {
  menungguKonfirmasi,
  dikonfirmasi,
  dalamProduksi,
  siapKirim,
  selesai,
  dibatalkan,
}

enum UserRole {
  stafPenjualan,
  pemilikUmkm,
  operatorGudang,
  administrator,
}

extension PoStatusExt on PoStatus {
  String get label {
    switch (this) {
      case PoStatus.menungguKonfirmasi: return 'Menunggu Konfirmasi';
      case PoStatus.dikonfirmasi:       return 'Dikonfirmasi';
      case PoStatus.dalamProduksi:      return 'Dalam Produksi';
      case PoStatus.siapKirim:          return 'Siap Kirim';
      case PoStatus.selesai:            return 'Selesai';
      case PoStatus.dibatalkan:         return 'Dibatalkan';
    }
  }

  String get emoji {
    switch (this) {
      case PoStatus.menungguKonfirmasi: return '⏳';
      case PoStatus.dikonfirmasi:       return '✅';
      case PoStatus.dalamProduksi:      return '🔄';
      case PoStatus.siapKirim:          return '📦';
      case PoStatus.selesai:            return '🏁';
      case PoStatus.dibatalkan:         return '✕';
    }
  }
}

extension UserRoleExt on UserRole {
  String get label {
    switch (this) {
      case UserRole.stafPenjualan:   return 'Staf Penjualan';
      case UserRole.pemilikUmkm:     return 'Pemilik UMKM';
      case UserRole.operatorGudang:  return 'Operator Gudang';
      case UserRole.administrator:   return 'Administrator';
    }
  }

  String get initials {
    switch (this) {
      case UserRole.stafPenjualan:   return 'SP';
      case UserRole.pemilikUmkm:     return 'PU';
      case UserRole.operatorGudang:  return 'OG';
      case UserRole.administrator:   return 'AD';
    }
  }
}

class Customer {
  final int    id;
  final String nama;
  final String alamat;
  final String telepon;
  final String email;

  const Customer({
    required this.id,
    required this.nama,
    required this.alamat,
    required this.telepon,
    required this.email,
  });
}

class PoItem {
  final String produk;
  final String spesifikasi;
  final int    jumlah;
  final double hargaSatuan;

  double get subtotal => jumlah * hargaSatuan;

  const PoItem({
    required this.produk,
    required this.spesifikasi,
    required this.jumlah,
    required this.hargaSatuan,
  });
}

class StockItem {
  final String bahan;
  final String satuan;
  final int    kebutuhan;
  final int    tersedia;

  bool get cukup => tersedia >= kebutuhan;
  int  get selisih => tersedia - kebutuhan;

  const StockItem({
    required this.bahan,
    required this.satuan,
    required this.kebutuhan,
    required this.tersedia,
  });
}

class StatusHistory {
  final String status;
  final String waktu;
  final String oleh;

  const StatusHistory({
    required this.status,
    required this.waktu,
    required this.oleh,
  });
}

class PurchaseOrder {
  final String        nomorPo;
  final DateTime      tanggalPesanan;
  final DateTime      tanggalKirim;
  final Customer      pelanggan;
  final List<PoItem>  items;
  final PoStatus      status;
  final String?       catatanKhusus;
  final List<StockItem>     stokItems;
  final List<StatusHistory> histori;

  double get totalNilai => items.fold(0, (s, i) => s + i.subtotal);

  const PurchaseOrder({
    required this.nomorPo,
    required this.tanggalPesanan,
    required this.tanggalKirim,
    required this.pelanggan,
    required this.items,
    required this.status,
    this.catatanKhusus,
    this.stokItems    = const [],
    this.histori      = const [],
  });
}

// ============================================================
// DUMMY DATA
// ============================================================

final dummyCustomers = [
  const Customer(id: 1, nama: 'Budi Bag Store',    telepon: '0812-3456-7890', email: 'budi@bagstore.id',     alamat: 'Jl. Sudirman No. 45, Jakarta Selatan'),
  const Customer(id: 2, nama: 'Toko Maju Jaya',    telepon: '0878-9012-3456', email: 'majujaya@gmail.com',   alamat: 'Jl. Gatot Subroto No. 12, Bandung'),
  const Customer(id: 3, nama: 'CV Sinar Baru',     telepon: '0856-7890-1234', email: 'sinarbaru@company.id', alamat: 'Jl. Asia Afrika No. 8, Surabaya'),
  const Customer(id: 4, nama: 'BagWorld Indonesia', telepon: '0821-4567-8901', email: 'bagworld@id.co',       alamat: 'Jl. Thamrin No. 99, Jakarta Pusat'),
  const Customer(id: 5, nama: 'Tiga Bintang Store', telepon: '0813-2222-3333', email: 'tigabintang@store.id', alamat: 'Jl. Malioboro No. 5, Yogyakarta'),
  const Customer(id: 6, nama: 'Indo Bag Co',        telepon: '0877-5555-6666', email: 'indo@bagco.id',        alamat: 'Jl. Pemuda No. 33, Semarang'),
];

final dummyOrders = [
  PurchaseOrder(
    nomorPo: 'PO-20260421-003',
    tanggalPesanan: DateTime(2026, 4, 21),
    tanggalKirim:   DateTime(2026, 4, 28),
    pelanggan: dummyCustomers[0],
    status: PoStatus.menungguKonfirmasi,
    catatanKhusus: 'Logo bordir di bagian depan, warna benang putih',
    items: const [
      PoItem(produk: 'Tas Kanvas Custom', spesifikasi: 'Hitam, 40×30cm', jumlah: 50, hargaSatuan: 150000),
    ],
    stokItems: const [
      StockItem(bahan: 'Kain Canvas 300D', satuan: 'meter', kebutuhan: 100, tersedia: 150),
      StockItem(bahan: 'Resleting YKK 50cm', satuan: 'pcs', kebutuhan: 50, tersedia: 80),
      StockItem(bahan: 'Benang Nilon', satuan: 'meter', kebutuhan: 500, tersedia: 300),
      StockItem(bahan: 'Label Merek', satuan: 'pcs', kebutuhan: 50, tersedia: 200),
    ],
    histori: const [
      StatusHistory(status: 'PO Dibuat', waktu: '21 Apr 2026 · 14:32', oleh: 'Nayla'),
      StatusHistory(status: 'Email Notifikasi Terkirim', waktu: '21 Apr 2026 · 14:32', oleh: 'Sistem'),
    ],
  ),
  PurchaseOrder(
    nomorPo: 'PO-20260421-002',
    tanggalPesanan: DateTime(2026, 4, 21),
    tanggalKirim:   DateTime(2026, 4, 25),
    pelanggan: dummyCustomers[1],
    status: PoStatus.menungguKonfirmasi,
    items: const [
      PoItem(produk: 'Tas Punggung', spesifikasi: 'Navy, 45×35cm', jumlah: 30, hargaSatuan: 140000),
    ],
    stokItems: const [],
    histori: const [],
  ),
  PurchaseOrder(
    nomorPo: 'PO-20260418-001',
    tanggalPesanan: DateTime(2026, 4, 18),
    tanggalKirim:   DateTime(2026, 4, 23),
    pelanggan: dummyCustomers[3],
    status: PoStatus.dalamProduksi,
    items: const [
      PoItem(produk: 'Tas Travel', spesifikasi: 'Hitam, 55×40cm', jumlah: 20, hargaSatuan: 300000),
    ],
    stokItems: const [],
    histori: const [],
  ),
  PurchaseOrder(
    nomorPo: 'PO-20260415-004',
    tanggalPesanan: DateTime(2026, 4, 15),
    tanggalKirim:   DateTime(2026, 4, 30),
    pelanggan: dummyCustomers[2],
    status: PoStatus.dikonfirmasi,
    items: const [
      PoItem(produk: 'Dompet Kulit', spesifikasi: 'Coklat, 20×12cm', jumlah: 100, hargaSatuan: 50000),
    ],
    stokItems: const [],
    histori: const [],
  ),
  PurchaseOrder(
    nomorPo: 'PO-20260410-007',
    tanggalPesanan: DateTime(2026, 4, 10),
    tanggalKirim:   DateTime(2026, 4, 18),
    pelanggan: dummyCustomers[4],
    status: PoStatus.siapKirim,
    items: const [
      PoItem(produk: 'Tas Selempang', spesifikasi: 'Merah, 30×20cm', jumlah: 80, hargaSatuan: 120000),
    ],
    stokItems: const [],
    histori: const [],
  ),
  PurchaseOrder(
    nomorPo: 'PO-20260405-002',
    tanggalPesanan: DateTime(2026, 4, 5),
    tanggalKirim:   DateTime(2026, 4, 12),
    pelanggan: dummyCustomers[4],
    status: PoStatus.selesai,
    items: const [
      PoItem(produk: 'Tas Pinggang', spesifikasi: 'Hitam, 25×15cm', jumlah: 60, hargaSatuan: 60000),
    ],
    stokItems: const [],
    histori: const [],
  ),
];
