class User {
  final int? id;
  final String? name;
  final String email;
  final String? phone;
  final String? role;
  final String? avatar;
  final DateTime? createdAt;

  User({
    this.id,
    this.name,
    required this.email,
    this.phone,
    this.role,
    this.avatar,
    this.createdAt,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'] ?? '',
      phone: json['phone'],
      role: json['role'],
      avatar: json['avatar'],
      createdAt: json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'phone': phone,
      'role': role,
      'avatar': avatar,
      'created_at': createdAt?.toIso8601String(),
    };
  }
}
