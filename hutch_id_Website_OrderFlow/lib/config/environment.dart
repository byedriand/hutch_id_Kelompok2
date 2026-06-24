/// Environment Configuration for Hutch Indonesia App
///
/// Gunakan file ini untuk manage URLs dan konfigurasi environment-specific
enum AppEnvironment { development, production }

class EnvironmentConfig {
  static const AppEnvironment _environment = AppEnvironment.development;

  /// Base URL untuk Backend API dan Website
  static String get baseUrl {
    switch (_environment) {
      case AppEnvironment.development:
        // Local Docker - pastikan laptop/docker running
        return 'http://10.119.239.161:8082/';
      case AppEnvironment.production:
        // Railway Cloud - bisa diakses kapan saja dari mana saja
        return 'https://hutch-web-production.up.railway.app/';
    }
  }

  /// Environment name (untuk logging)
  static String get environmentName {
    switch (_environment) {
      case AppEnvironment.development:
        return 'DEVELOPMENT (Local Docker)';
      case AppEnvironment.production:
        return 'PRODUCTION (Railway Cloud)';
    }
  }

  /// Debug mode
  static bool get isDebug => _environment == AppEnvironment.development;

  /// Timeout configuration
  static Duration get requestTimeout => Duration(seconds: isDebug ? 30 : 15);
}