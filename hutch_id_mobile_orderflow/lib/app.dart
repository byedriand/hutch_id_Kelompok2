import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'config/app_config.dart';
import 'services/api_service.dart';
import 'providers/auth_provider.dart';
import 'providers/dashboard_provider.dart';
import 'providers/pesanan_provider.dart';
import 'providers/pelanggan_provider.dart';
import 'providers/produk_provider.dart';
import 'providers/user_provider.dart';
import 'providers/notifikasi_provider.dart';
import 'providers/arsip_provider.dart';
import 'models/produk.dart';
import 'screens/auth/login_screen.dart';
import 'screens/landing/splash_screen.dart';
import 'screens/landing/landing_screen.dart';
import 'screens/home/dashboard_screen.dart';
import 'screens/home/home_screen.dart';
import 'screens/home/profile_screen.dart';
import 'screens/home/user_management_screen.dart';
import 'screens/pesanan/pesanan_list_screen.dart';
import 'screens/pesanan/pesanan_detail_screen.dart';
import 'screens/pesanan/pesanan_form_screen.dart';
import 'screens/pelanggan/pelanggan_list_screen.dart';
import 'screens/pelanggan/pelanggan_detail_screen.dart';
import 'screens/pelanggan/pelanggan_form_screen.dart';
import 'screens/produk/produk_list_screen.dart';
import 'screens/produk/produk_detail_screen.dart';
import 'screens/produk/produk_staf_tambah_screen.dart';
import 'screens/gudang/gudang_stok_screen.dart';
import 'screens/notifikasi/notifikasi_screen.dart';
import 'screens/arsip/arsip_screen.dart';

/// AuthGate mendengarkan [AuthProvider] secara permanen via listener.
/// Dipasang sebagai `home` MaterialApp — bertugas menjadi "gerbang awal"
/// dan router otomatis:
///   - Saat login berhasil  → /home  (stack dibersihkan)
///   - Saat logout          → /landing (stack dibersihkan)
///
/// Karena memakai addListener (bukan Consumer/Provider.of rebuild),
/// listener ini tetap aktif bahkan setelah AuthGate tidak ada di stack.
class AuthGate extends StatefulWidget {
  const AuthGate({super.key});

  @override
  State<AuthGate> createState() => _AuthGateState();
}

class _AuthGateState extends State<AuthGate> {
  AuthProvider? _authProvider;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    final newAuth = Provider.of<AuthProvider>(context, listen: false);
    if (_authProvider != newAuth) {
      _authProvider?.removeListener(_onAuthChanged);
      _authProvider = newAuth;
      _authProvider!.addListener(_onAuthChanged);
    }
  }

  void _onAuthChanged() {
    if (!mounted) return;
    final auth = _authProvider!;
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!mounted) return;
      final navigator = Navigator.of(context, rootNavigator: true);
      if (auth.isLoggedIn) {
        navigator.pushNamedAndRemoveUntil('/home', (route) => false);
      } else {

      }
    });
  }

  @override
  void dispose() {
    _authProvider?.removeListener(_onAuthChanged);
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    // Tampilkan splash selama AuthGate belum menentukan arah
    return const SplashScreen();
  }
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize API Service
  final apiService = ApiService();
  await apiService.init();

  runApp(const HutchMobileApp());
}

class HutchMobileApp extends StatelessWidget {
  const HutchMobileApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => DashboardProvider()),
        ChangeNotifierProvider(create: (_) => PesananProvider()),
        ChangeNotifierProvider(create: (_) => PelangganProvider()),
        ChangeNotifierProvider(create: (_) => ProdukProvider()),
        ChangeNotifierProvider(create: (_) => UserProvider()),
        ChangeNotifierProvider(create: (_) => NotifikasiProvider()),
        ChangeNotifierProvider(create: (_) => ArsipProvider()),
      ],
      child: MaterialApp(
        title: AppConfig.appName,
        debugShowCheckedModeBanner: false,
        theme: ThemeData(
          primaryColor: const Color(0xFF1e40af),
          primarySwatch: Colors.blue,
          useMaterial3: true,
          appBarTheme: const AppBarTheme(
            backgroundColor: Color(0xFF1e40af),
            foregroundColor: Colors.white,
            elevation: 0,
          ),
          floatingActionButtonTheme: const FloatingActionButtonThemeData(
            backgroundColor: Color(0xFF1e40af),
            foregroundColor: Colors.white,
          ),
        ),
        home: const AuthGate(),
        routes: {
          '/landing': (context) => const LandingScreen(),
          '/login': (context) => const LoginScreen(),
          '/home': (context) => const HomeScreen(),
          '/dashboard': (context) => const DashboardScreen(),
          '/profile': (context) => const ProfileScreen(),
          '/user-management': (context) => const UserManagementScreen(),
          '/pesanan': (context) => const PesananListScreen(),
          '/pesanan-detail': (context) {
            final pesananId =
                ModalRoute.of(context)?.settings.arguments as int;
            return PesananDetailScreen(pesananId: pesananId);
          },
          '/pesanan-form': (context) {
            final pesananId =
                ModalRoute.of(context)?.settings.arguments as int?;
            return PesananFormScreen(pesananId: pesananId);
          },
          '/pelanggan': (context) => const PelangganListScreen(),
          '/pelanggan-detail': (context) {
            final pelangganId =
                ModalRoute.of(context)?.settings.arguments as int;
            return PelangganDetailScreen(pelangganId: pelangganId);
          },
          '/pelanggan-form': (context) {
            final pelangganId =
                ModalRoute.of(context)?.settings.arguments as int?;
            return PelangganFormScreen(pelangganId: pelangganId);
          },
          '/produk': (context) => const ProdukListScreen(),
          '/produk-detail': (context) {
            final produkId =
                ModalRoute.of(context)?.settings.arguments as int;
            return ProdukDetailScreen(produkId: produkId);
          },
          '/produk-staf-tambah': (context) {
            final produkToEdit =
                ModalRoute.of(context)!.settings.arguments as Produk?;
            return ProdukStafTambahScreen(produkToEdit: produkToEdit);
          },
          '/gudang-stok': (context) => const GudangStokScreen(),
          '/notifikasi': (context) => const NotifikasiScreen(),
          '/arsip': (context) => const ArsipScreen(),
        },
      ),
    );
  }
}