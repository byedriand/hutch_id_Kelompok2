class Notifikasi {
  final int? id;
  final String? judul;
  final String? isi;
  final String? tipe;
  final int? pesananId;
  final Map<String, dynamic>? data;
  final List<String>? untukRoles;
  final DateTime? dibacaAt;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Notifikasi({
    this.id,
    this.judul,
    this.isi,
    this.tipe,
    this.pesananId,
    this.data,
    this.untukRoles,
    this.dibacaAt,
    this.createdAt,
    this.updatedAt,
  });

  /// Sudah dibaca atau belum (dibaca_at terisi = sudah dibaca).
  bool get sudahDibaca => dibacaAt != null;

  /// Detail produk yang kurang, hanya relevan untuk tipe == 'stok_kurang'.
  /// Format tiap item: { nama_produk, stok_tersedia, kebutuhan, kurang, produk_id? }
  List<Map<String, dynamic>> get detailKurang {
    final list = data?['detail_kurang'];
    if (list is List) {
      return list
          .whereType<Map>()
          .map((e) => Map<String, dynamic>.from(e))
          .toList();
    }
    return [];
  }

  /// Untuk notifikasi tipe 'stok_kurang' yang dibuat dari quick-update produk
  /// (bukan dari draft PO), backend menaruh produk_id langsung di `data`.
  int? get produkId {
    final value = data?['produk_id'];
    if (value == null) return null;
    if (value is int) return value;
    return int.tryParse(value.toString());
  }

  String? get namaProduk => data?['nama_produk']?.toString();

  factory Notifikasi.fromJson(Map<String, dynamic> json) {
    return Notifikasi(
      id: json['id'],
      judul: json['judul']?.toString(),
      // Backend (Laravel) menyimpan isi notifikasi di kolom 'pesan', bukan 'isi'.
      isi: (json['pesan'] ?? json['isi'])?.toString(),
      tipe: json['tipe']?.toString(),
      pesananId: json['pesanan_id'] is String
          ? int.tryParse(json['pesanan_id'])
          : json['pesanan_id'],
      data: json['data'] is Map
          ? Map<String, dynamic>.from(json['data'])
          : null,
      untukRoles: json['untuk_roles'] is List
          ? List<String>.from(
              (json['untuk_roles'] as List).map((e) => e.toString()),
            )
          : null,
      dibacaAt: json['dibaca_at'] != null
          ? DateTime.tryParse(json['dibaca_at'].toString())
          : null,
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
      'judul': judul,
      'pesan': isi,
      'tipe': tipe,
      'pesanan_id': pesananId,
      'data': data,
      'untuk_roles': untukRoles,
      'dibaca_at': dibacaAt?.toIso8601String(),
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }
}
