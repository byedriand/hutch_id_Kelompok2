class AppConfig {
  // API Configuration
  static const String apiBaseUrl =
      'http://127.0.0.1:8000/api'; // Chrome / Localhost - Website Backend
  // Untuk Android emulator: 'http://10.0.2.2:8000/api'
  // Untuk physical device: 'http://192.168.1.X:8000/api'

  // App Information
  static const String appName = 'Hutch ID Mobile';
  static const String appVersion = '1.0.0';

  // Storage Keys
  static const String tokenKey = 'auth_token';
  static const String userKey = 'user_data';
  static const String isLoggedInKey = 'is_logged_in';
}
