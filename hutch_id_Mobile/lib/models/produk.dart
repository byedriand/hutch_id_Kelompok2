class Produk {
  final int? id;
  final String nama;
  final String? foto;
  final double? hargaJual;
  final int? stok;
  final String? keterangan;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Produk({
    this.id,
    required this.nama,
    this.foto,
    this.hargaJual,
    this.stok,
    this.keterangan,
    this.createdAt,
    this.updatedAt,
  });

  factory Produk.fromJson(Map<String, dynamic> json) {
    // Parse hargaJual
    double? hargaJualValue;
    if (json['harga_jual'] != null) {
      if (json['harga_jual'] is String) {
        hargaJualValue = double.tryParse(json['harga_jual']);
      } else if (json['harga_jual'] is int) {
        hargaJualValue = (json['harga_jual'] as int).toDouble();
      } else if (json['harga_jual'] is double) {
        hargaJualValue = json['harga_jual'];
      }
    }

    // Parse stok
    int? stokValue;
    if (json['stok'] != null) {
      if (json['stok'] is String) {
        stokValue = int.tryParse(json['stok']);
      } else {
        stokValue = json['stok'];
      }
    }

    return Produk(
      id: json['id'] is String ? int.tryParse(json['id']) : json['id'],
      nama: json['nama']?.toString() ?? '',
      foto: json['foto']?.toString(),
      hargaJual: hargaJualValue,
      stok: stokValue,
      keterangan: json['keterangan']?.toString(),
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'].toString())
          : null,
      updatedAt: json['updated_at'] != null
          ? DateTime.tryParse(json['updated_at'].toString())
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama': nama,
      'foto': foto,
      'harga_jual': hargaJual,
      'stok': stok,
      'keterangan': keterangan,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }
}
