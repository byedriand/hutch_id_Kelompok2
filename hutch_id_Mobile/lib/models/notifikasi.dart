class Notifikasi {
  final int? id;
  final String? judul;
  final String? isi;
  final String? tipe;
  final int? pesananId;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Notifikasi({
    this.id,
    this.judul,
    this.isi,
    this.tipe,
    this.pesananId,
    this.createdAt,
    this.updatedAt,
  });

  factory Notifikasi.fromJson(Map<String, dynamic> json) {
    return Notifikasi(
      id: json['id'],
      judul: json['judul'],
      isi: json['isi'],
      tipe: json['tipe'],
      pesananId: json['pesanan_id'],
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
      'judul': judul,
      'isi': isi,
      'tipe': tipe,
      'pesanan_id': pesananId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }
}
