class AppConfig {
  // API Configuration
  // PENTING: arahkan ke backend yang sama dengan website (Docker: nginx + php-fpm
  // di port 8082). JANGAN pakai `php artisan serve` (single-threaded) untuk
  // testing mobile — server itu gampang ERR_EMPTY_RESPONSE saat grid produk
  // memuat banyak gambar sekaligus secara paralel.
 static const String apiBaseUrl = 'https://hutch-prestige.my.id/api';
     // Chrome / Localhost - Website Backend (Docker)
  // Untuk Android emulator: 'http://10.0.2.2:8082/api'
  // Untuk physical device: 'http://192.168.1.X:8082/api'

  // Base URL tanpa suffix '/api', dipakai untuk mengakses file publik
  // (foto produk, dsb) yang disimpan Laravel di folder public/ (mis. "images/abc.jpg"),
  // BUKAN di folder storage/. Jangan tambahkan '/storage' di sini.
  static String get mediaBaseUrl => apiBaseUrl.endsWith('/api')
      ? apiBaseUrl.substring(0, apiBaseUrl.length - '/api'.length)
      : apiBaseUrl;

  /// Mengubah path foto relatif dari API (mis. "images/xxx.jpg") menjadi
  /// URL absolut yang bisa dipakai Image.network(). Mengembalikan null
  /// jika path kosong, dan tidak mengubah path yang sudah berupa URL penuh.
  static String? resolveMediaUrl(String? path) {
    if (path == null || path.trim().isEmpty) return null;
    if (path.startsWith('http://') || path.startsWith('https://')) {
      return path;
    }
    final cleanPath = path.startsWith('/') ? path.substring(1) : path;
    return '$mediaBaseUrl/$cleanPath';
  }

  // App Information
  static const String appName = 'Hutch ID Mobile';
  static const String appVersion = '1.0.2';

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String isLoggedInKey = 'is_logged_in';
}
