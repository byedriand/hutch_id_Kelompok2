/// AppConfig - Environment Configuration
/// Switch between development and production by changing [_env].
class AppConfig {
  // ── Change this to Env.production before deploying ──────────────────────
  static const Env _env = Env.development;
  // ────────────────────────────────────────────────────────────────────────

  static String get baseUrl {
    switch (_env) {
      case Env.production:
        return 'https://api.hutchprestige.com/api';
      case Env.staging:
        return 'https://staging.hutchprestige.com/api';
      case Env.development:
        return 'http://127.0.0.1:8000/api';
    }
  }

  static bool get isProduction => _env == Env.production;
  static bool get isDebug => _env == Env.development;

  static Duration get connectTimeout =>
      Duration(seconds: isProduction ? 10 : 30);
  static Duration get receiveTimeout =>
      Duration(seconds: isProduction ? 15 : 60);
}

enum Env { development, staging, production }
