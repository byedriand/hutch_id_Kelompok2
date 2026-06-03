class Pelanggan {
  final String id;
  final String nama;
  final String telepon;
  final String alamat;
  final String email;
  final int jumlahPO;

  Pelanggan({
    required this.id,
    required this.nama,
    required this.telepon,
    required this.alamat,
    required this.email,
    required this.jumlahPO,
  });

  factory Pelanggan.fromJson(Map<String, dynamic> json) {
    return Pelanggan(
      id: json['id']?.toString() ?? '',
      nama: json['nama'] ?? '',
      telepon: json['telepon'] ?? '',
      alamat: json['alamat'] ?? '',
      email: json['email'] ?? '',
      jumlahPO: json['jumlah_po'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama': nama,
      'telepon': telepon,
      'alamat': alamat,
      'email': email,
      'jumlah_po': jumlahPO,
    };
  }
}