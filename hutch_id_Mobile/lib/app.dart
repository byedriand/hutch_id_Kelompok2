import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'config/app_config.dart';
import 'services/api_service.dart';
import 'providers/auth_provider.dart';
import 'providers/dashboard_provider.dart';
import 'providers/pesanan_provider.dart';
import 'providers/pelanggan_provider.dart';
import 'providers/produk_provider.dart';
import 'providers/notifikasi_provider.dart';
import 'providers/arsip_provider.dart';
import 'screens/auth/login_screen.dart';
import 'screens/auth/welcome_screen.dart';
import 'screens/home/dashboard_screen.dart';
import 'screens/home/home_screen.dart';
import 'screens/pesanan/pesanan_list_screen.dart';
import 'screens/pesanan/pesanan_detail_screen.dart';
import 'screens/pesanan/pesanan_form_screen.dart';
import 'screens/pelanggan/pelanggan_list_screen.dart';
import 'screens/pelanggan/pelanggan_detail_screen.dart';
import 'screens/pelanggan/pelanggan_form_screen.dart';
import 'screens/produk/produk_list_screen.dart';
import 'screens/produk/produk_detail_screen.dart';
import 'screens/notifikasi/notifikasi_screen.dart';
import 'screens/arsip/arsip_screen.dart';

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
          floatingActionButtonTheme: FloatingActionButtonThemeData(
            backgroundColor: const Color(0xFF1e40af),
            foregroundColor: Colors.white,
          ),
        ),
        home: Consumer<AuthProvider>(
          builder: (context, authProvider, _) {
            // Check if user is logged in
            if (authProvider.isLoggedIn) {
              return const HomeScreen();
            } else {
              return const WelcomeScreen();
            }
          },
        ),
        routes: {
          '/welcome': (context) => const WelcomeScreen(),
          '/login': (context) => const LoginScreen(),
          '/home': (context) => const HomeScreen(),
          '/dashboard': (context) => const DashboardScreen(),
          '/pesanan': (context) {
            final status = ModalRoute.of(context)?.settings.arguments as String?;
            return PesananListScreen(initialStatus: status ?? '');
          },
          '/pesanan-detail': (context) {
            final pesananId = ModalRoute.of(context)?.settings.arguments as int;
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
            final produkId = ModalRoute.of(context)?.settings.arguments as int;
            return ProdukDetailScreen(produkId: produkId);
          },
          '/notifikasi': (context) => const NotifikasiScreen(),
          '/arsip': (context) => const ArsipScreen(),
        },
      ),
    );
  }
}
