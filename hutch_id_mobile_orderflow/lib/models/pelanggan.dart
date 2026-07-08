class Pelanggan {
  final int? id;
  final String nama;
  final String? email;
  final String? telepon;
  final String? nomorWhatsapp;
  final String? alamat;
  final String? catatan;
  final int pesananCount;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Pelanggan({
    this.id,
    required this.nama,
    this.email,
    this.telepon,
    this.nomorWhatsapp,
    this.alamat,
    this.catatan,
    this.pesananCount = 0,
    this.createdAt,
    this.updatedAt,
  });

  factory Pelanggan.fromJson(Map<String, dynamic> json) {
    return Pelanggan(
      id: json['id'] is String ? int.tryParse(json['id']) : json['id'],
      nama: json['nama']?.toString() ?? '',
      email: json['email']?.toString(),
      telepon: json['telepon']?.toString(),
      nomorWhatsapp: json['nomor_whatsapp']?.toString(),
      alamat: json['alamat']?.toString(),
      catatan: json['catatan']?.toString(),
      pesananCount: json['pesanan_count'] is String
          ? int.tryParse(json['pesanan_count']) ?? 0
          : (json['pesanan_count'] as num?)?.toInt() ?? 0,
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
      'email': email,
      'telepon': telepon,
      'nomor_whatsapp': nomorWhatsapp,
      'alamat': alamat,
      'catatan': catatan,
      'pesanan_count': pesananCount,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }
}