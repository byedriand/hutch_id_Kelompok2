class ArsipPdf {
  final int? id;
  final int? pesananId;
  final String? namaBerkas;
  final String? pathBerkas;
  final int? ukuran;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  ArsipPdf({
    this.id,
    this.pesananId,
    this.namaBerkas,
    this.pathBerkas,
    this.ukuran,
    this.createdAt,
    this.updatedAt,
  });

  factory ArsipPdf.fromJson(Map<String, dynamic> json) {
    return ArsipPdf(
      id: json['id'],
      pesananId: json['pesanan_id'],
      namaBerkas: json['nama_berkas'],
      pathBerkas: json['path_berkas'],
      ukuran: json['ukuran'],
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
      'pesanan_id': pesananId,
      'nama_berkas': namaBerkas,
      'path_berkas': pathBerkas,
      'ukuran': ukuran,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }
}
