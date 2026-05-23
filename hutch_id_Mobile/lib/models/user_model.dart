class User {
  final String id;
  final String nama;
  final String role;
  final String deskripsi;
  final String email;
  final String password;

  User({
    required this.id,
    required this.nama,
    required this.role,
    required this.deskripsi,
    required this.email,
    this.password = '',
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id']?.toString() ?? '',
      nama: json['nama'] ?? '',
      role: json['role'] ?? '',
      deskripsi: json['deskripsi'] ?? '',
      email: json['email'] ?? '',
      password: json['password'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'nama': nama,
      'role': role,
      'deskripsi': deskripsi,
      'email': email,
    };
  }
}