import '../config/app_config.dart';

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

  /// URL absolut foto produk, siap dipakai di Image.network().
  String? get fotoUrl => AppConfig.resolveMediaUrl(foto);

  /// CATATAN: field 'kategori' TIDAK ADA di backend (app/Models/Produk.php
  /// web hanya punya: nama, foto, harga_jual, stok, keterangan). Getter ini
  /// hanya disediakan agar kode UI yang sudah terlanjur memanggil
  /// `produk.kategori` tidak crash (NoSuchMethodError) — selalu null.
  /// Kalau kategori memang dibutuhkan, tambahkan dulu kolomnya di migration
  /// + $fillable di web, baru parse di sini.
  String? get kategori => null;

  /// Alias agar kode yang memanggil `produk.harga` (bukan `hargaJual`)
  /// tidak crash. Nilainya sama dengan hargaJual.
  double? get harga => hargaJual;

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
      // Backend bisa mengirim salah satu dari nama key ini tergantung versi
      // (foto = path relatif lama, foto_url/fotoUrl = url lengkap baru).
      // Diutamakan yang sudah berupa URL absolut kalau ada.
      foto: (json['foto_url'] ?? json['fotoUrl'] ?? json['foto'])
          ?.toString(),
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
