class Pelanggan {
  final int? id;
  final String nama;
  final String? email;
  final String? telepon;
  final String? alamat;
  final String? catatan;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Pelanggan({
    this.id,
    required this.nama,
    this.email,
    this.telepon,
    this.alamat,
    this.catatan,
    this.createdAt,
    this.updatedAt,
  });

  factory Pelanggan.fromJson(Map<String, dynamic> json) {
    return Pelanggan(
      id: json['id'] is String ? int.tryParse(json['id']) : json['id'],
      nama: json['nama']?.toString() ?? '',
      email: json['email']?.toString(),
      telepon: json['telepon']?.toString(),
      alamat: json['alamat']?.toString(),
      catatan: json['catatan']?.toString(),
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
      'alamat': alamat,
      'catatan': catatan,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }
}
