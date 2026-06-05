class Produk {
  final int id;
  final String nama;
  final String deskripsi;
  final int hargaJual;
  final int stok;
  final String? fotoUrl;
  final bool aktif;

  Produk({
    required this.id,
    required this.nama,
    required this.deskripsi,
    required this.hargaJual,
    required this.stok,
    this.fotoUrl,
    this.aktif = true,
  });

  factory Produk.fromJson(Map<String, dynamic> json) {
    return Produk(
      id: json['id'] ?? 0,
      nama: json['nama'] ?? '',
      deskripsi: json['deskripsi'] ?? '',
      hargaJual: json['harga_jual'] ?? 0,
      stok: json['stok'] ?? 0,
      fotoUrl: json['foto_url'],
      aktif: json['aktif'] ?? true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama': nama,
      'deskripsi': deskripsi,
      'harga_jual': hargaJual,
      'stok': stok,
      'foto_url': fotoUrl,
      'aktif': aktif,
    };
  }
}
