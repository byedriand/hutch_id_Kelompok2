class Pesanan {
  final int id;
  final String nomorPo;
  final int pelangganId;
  final String pelangganNama;
  final String status;
  final DateTime tanggalPemesanan;
  final DateTime tanggalPengiriman;
  final int totalNilai;
  final String catatan;
  final List<ItemPesanan> items;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Pesanan({
    required this.id,
    required this.nomorPo,
    required this.pelangganId,
    required this.pelangganNama,
    required this.status,
    required this.tanggalPemesanan,
    required this.tanggalPengiriman,
    required this.totalNilai,
    required this.catatan,
    required this.items,
    this.createdAt,
    this.updatedAt,
  });

  factory Pesanan.fromJson(Map<String, dynamic> json) {
    return Pesanan(
      id: json['id'] ?? 0,
      nomorPo: json['nomor_po'] ?? '',
      pelangganId: json['pelanggan_id'] ?? 0,
      pelangganNama: json['pelanggan_nama'] ?? json['pelanggan']['nama'] ?? '',
      status: json['status'] ?? '',
      tanggalPemesanan: DateTime.parse(
        json['created_at'] ?? DateTime.now().toString(),
      ),
      tanggalPengiriman: DateTime.parse(
        json['tanggal_pengiriman'] ?? DateTime.now().toString(),
      ),
      totalNilai: json['total_nilai'] ?? 0,
      catatan: json['catatan'] ?? '',
      items:
          (json['items'] as List?)
              ?.map((item) => ItemPesanan.fromJson(item))
              .toList() ??
          [],
      createdAt: json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : null,
      updatedAt: json['updated_at'] != null
          ? DateTime.parse(json['updated_at'])
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nomor_po': nomorPo,
      'pelanggan_id': pelangganId,
      'pelanggan_nama': pelangganNama,
      'status': status,
      'tanggal_pengiriman': tanggalPengiriman.toIso8601String(),
      'total_nilai': totalNilai,
      'catatan': catatan,
      'items': items.map((item) => item.toJson()).toList(),
    };
  }
}

class ItemPesanan {
  final int id;
  final int pesananId;
  final int produkId;
  final String produkNama;
  final int jumlah;
  final int hargaSatuan;
  final int subtotal;

  ItemPesanan({
    required this.id,
    required this.pesananId,
    required this.produkId,
    required this.produkNama,
    required this.jumlah,
    required this.hargaSatuan,
    required this.subtotal,
  });

  factory ItemPesanan.fromJson(Map<String, dynamic> json) {
    return ItemPesanan(
      id: json['id'] ?? 0,
      pesananId: json['pesanan_id'] ?? 0,
      produkId: json['produk_id'] ?? 0,
      produkNama: json['produk_nama'] ?? json['produk']['nama'] ?? '',
      jumlah: json['jumlah'] ?? 0,
      hargaSatuan: json['harga_satuan'] ?? 0,
      subtotal: json['subtotal'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'produk_id': produkId,
      'jumlah': jumlah,
      'harga_satuan': hargaSatuan,
      'subtotal': subtotal,
    };
  }
}
