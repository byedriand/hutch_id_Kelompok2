import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/pesanan_provider.dart';
import '../../providers/pelanggan_provider.dart';
import '../../providers/notifikasi_provider.dart';
import '../home/dashboard_screen.dart';
import '../pesanan/pesanan_list_screen.dart';
import '../pelanggan/pelanggan_list_screen.dart';
import '../produk/produk_list_screen.dart';
import '../notifikasi/notifikasi_screen.dart';
import '../arsip/arsip_screen.dart';
import '../gudang/gudang_stok_screen.dart';
import '../../widgets/chatbot_dialog.dart';
import '../chatbot/chatbot_screen.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;

  late List<Widget> _screens;
  late List<NavigationDestination> _navigationDestinations;

  @override
  void initState() {
    super.initState();
    _buildNavigation();

    // Start auto-refresh polling for real-time sync
    Future.microtask(() {
      try {
        if (mounted) {
          Provider.of<PesananProvider>(
            context,
            listen: false,
          ).startAutoRefresh();
          Provider.of<PelangganProvider>(
            context,
            listen: false,
          ).startAutoRefresh();
          Provider.of<NotifikasiProvider>(
            context,
            listen: false,
          ).fetchNotifikasi();
          Provider.of<NotifikasiProvider>(
            context,
            listen: false,
          ).startAutoRefresh();
        }
      } catch (e) {
        // Silently handle if providers not available
      }

      // Tampilkan welcome popup jika baru saja login
      final auth = Provider.of<AuthProvider>(context, listen: false);
      if (auth.justLoggedIn) {
        final roleName = auth.loggedInRoleName;
        auth.consumeJustLoggedIn();
        Future.delayed(const Duration(milliseconds: 300), () {
          if (!mounted) return;
          _showWelcomeDialog(roleName);
        });
      }
    });
  }

  @override
  void dispose() {
    // Stop polling when leaving screen
    try {
      Provider.of<PesananProvider>(context, listen: false).stopAutoRefresh();
      Provider.of<PelangganProvider>(context, listen: false).stopAutoRefresh();
      Provider.of<NotifikasiProvider>(context, listen: false).stopAutoRefresh();
    } catch (e) {
      // Silently handle
    }
    super.dispose();
  }

  String _roleLabel(String? role) {
    switch (role) {
      case 'staf_penjualan':
        return 'Staf Penjualan';
      case 'operator_gudang':
        return 'Operator Gudang';
      case 'administrator':
        return 'Administrator';
      case 'pemilik_umkm':
        return 'Pemilik UMKM';
      default:
        return 'Pengguna';
    }
  }

  void _showWelcomeDialog(String roleName) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (_) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 80,
                height: 80,
                decoration: BoxDecoration(
                  color: const Color(0xFF10b981).withValues(alpha: 0.12),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.check_circle_rounded,
                  color: Color(0xFF10b981),
                  size: 48,
                ),
              ),
              const SizedBox(height: 20),
              const Text(
                'Login Berhasil!',
                style: TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF1e3a5f),
                ),
              ),
              const SizedBox(height: 10),
              Text(
                'Selamat datang kembali,\n$roleName!',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey[600],
                  height: 1.5,
                ),
              ),
            ],
          ),
        ),
      ),
    );
    // Auto-dismiss setelah 1.5 detik — di sini Navigator.pop aman
    // karena dialog dibuka dari HomeScreen context sendiri.
    Future.delayed(const Duration(milliseconds: 1500), () {
      if (mounted) Navigator.of(context).pop();
    });
  }

  void _buildNavigation() {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final userRole = authProvider.user?.role ?? '';

    // Jika user null (sudah logout), jangan rebuild navigation
    // AuthGate akan handle redirect ke /landing
    if (userRole.isEmpty && !authProvider.isLoggedIn) return;

    // Screens yang ada
    final dashboardScreen = const DashboardScreen();
    final pesananScreen = const PesananListScreen();
    final pelangganScreen = const PelangganListScreen();
    final produkScreen = const ProdukListScreen();
    final arsipScreen = const ArsipScreen();

    // Helper untuk membuat NavigationDestination
    NavigationDestination buildNavDest(
      IconData active,
      IconData inactive,
      String label,
    ) {
      // Find index by label to determine if it's selected
      // Actually, we can just use the properties directly
      return NavigationDestination(
        icon: Icon(inactive),
        selectedIcon: Icon(active),
        label: label,
      );
    }

    // Build navigation berdasarkan role
    // Ikon disamakan dengan sidebar website (FontAwesome):
    // fa-tachometer-alt -> speed, fa-list -> list_alt, fa-users -> groups,
    // fa-boxes -> inventory_2, fa-cube -> view_in_ar, fa-archive -> archive
    if (userRole == 'operator_gudang') {
      // Operator Gudang tidak punya menu Pesanan — tugasnya hanya kelola stok.
      _screens = [dashboardScreen, const GudangStokScreen()];
      _navigationDestinations = [
        buildNavDest(Icons.speed_rounded, Icons.speed_outlined, 'Dashboard'),
        buildNavDest(
          Icons.inventory_2_rounded,
          Icons.inventory_2_outlined,
          'Manajemen Stok',
        ),
      ];
    } else if (userRole == 'staf_penjualan') {
      _screens = [
        dashboardScreen,
        pesananScreen,
        pelangganScreen,
        produkScreen,
      ];
      _navigationDestinations = [
        buildNavDest(Icons.speed_rounded, Icons.speed_outlined, 'Dashboard'),
        buildNavDest(
          Icons.list_alt_rounded,
          Icons.list_alt_outlined,
          'Pesanan',
        ),
        buildNavDest(Icons.groups_rounded, Icons.groups_outlined, 'Pelanggan'),
        buildNavDest(
          Icons.view_in_ar_rounded,
          Icons.view_in_ar_outlined,
          'Tambah Produk',
        ),
      ];
    } else {
      // administrator — hanya Dashboard dan Arsip; Buat PO & tambah Pelanggan hanya untuk staf_penjualan
      _screens = [dashboardScreen, pesananScreen, arsipScreen];
      _navigationDestinations = [
        buildNavDest(Icons.speed_rounded, Icons.speed_outlined, 'Dashboard'),
        buildNavDest(
          Icons.list_alt_rounded,
          Icons.list_alt_outlined,
          'Pesanan',
        ),
        buildNavDest(Icons.archive_rounded, Icons.archive_outlined, 'Arsip'),
      ];
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, authProvider, _) {
        // Rebuild navigation jika role berubah
        _buildNavigation();

        return PopScope(
          canPop: _selectedIndex == 0,
          onPopInvokedWithResult: (didPop, _) {
            if (didPop) return;
            if (_selectedIndex != 0) {
              setState(() {
                _selectedIndex = 0;
              });
            }
          },
          child: Scaffold(
            body: _screens[_selectedIndex],
            bottomNavigationBar: Container(
              decoration: BoxDecoration(
                color: Colors.white,
                border: Border(
                  top: BorderSide(color: const Color(0xFFe5e7eb), width: 1),
                ),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF1e40af).withValues(alpha: 0.08),
                    blurRadius: 20,
                    offset: const Offset(0, -8),
                  ),
                ],
              ),
              child: NavigationBar(
                selectedIndex: _selectedIndex,
                onDestinationSelected: (index) {
                  setState(() {
                    _selectedIndex = index;
                  });
                },
                backgroundColor: Colors.white,
                elevation: 0,
                indicatorColor: const Color(0xFF1e40af).withValues(alpha: 0.15),
                labelBehavior: NavigationDestinationLabelBehavior.alwaysShow,
                destinations: _navigationDestinations,
              ),
            ),
            appBar: AppBar(
              automaticallyImplyLeading: false,
              backgroundColor: Colors.white,
              elevation: 2,
              shadowColor: const Color(0xFF1e40af).withValues(alpha: 0.1),
              title: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF3b82f6), Color(0xFF1e40af)],
                      ),
                      borderRadius: BorderRadius.circular(10),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF1e40af).withValues(alpha: 0.2),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Image.asset(
                      'assets/images/hutch-logo.png',
                      width: 28,
                      height: 28,
                      fit: BoxFit.contain,
                    ),
                  ),
                  const SizedBox(width: 12),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'HUTCH PRESTIGE',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w900,
                          color: Color(0xFF0c2340),
                          letterSpacing: 0.5,
                        ),
                      ),
                      const Text(
                        'Modul Manajemen',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Color(0xFF64748b),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              actions: [
                Consumer<NotifikasiProvider>(
                  builder: (context, notifikasiProvider, _) {
                    final unread = notifikasiProvider.unreadCount;
                    return IconButton(
                      icon: Stack(
                        clipBehavior: Clip.none,
                        children: [
                          const Icon(
                            Icons.notifications_outlined,
                            color: Color(0xFF1e40af),
                          ),
                          if (unread > 0)
                            Positioned(
                              top: -2,
                              right: -2,
                              child: Container(
                                width: 10,
                                height: 10,
                                decoration: BoxDecoration(
                                  color: Colors.red[600],
                                  shape: BoxShape.circle,
                                  border: Border.all(
                                    color: Colors.white,
                                    width: 1.5,
                                  ),
                                ),
                              ),
                            ),
                        ],
                      ),
                      onPressed: () {
                        // Navigate to NotifikasiScreen or open as modal
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => const NotifikasiScreen(),
                          ),
                        ).then((_) {
                          // Refresh unread count setelah balik dari layar Notifikasi
                          if (context.mounted) {
                            Provider.of<NotifikasiProvider>(
                              context,
                              listen: false,
                            ).fetchNotifikasi(silent: true);
                          }
                        });
                      },
                    );
                  },
                ),
                Consumer<AuthProvider>(
                  builder: (context, authProvider, _) {
                    final userName = authProvider.user?.name ?? 'User';
                    return PopupMenuButton<String>(key: const Key('profile_avatar'),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(14),
                      ),
                      position: PopupMenuPosition.under,
                      itemBuilder: (context) => <PopupMenuEntry<String>>[
                        PopupMenuItem<String>(
                          enabled: false,
                          child: Container(
                            padding: const EdgeInsets.all(14),
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [Color(0xFFdbeafe), Color(0xFFbfdbfe)],
                              ),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: Row(
                              children: [
                                Stack(
                                  children: [
                                    Icon(
                                      Icons.person_rounded,
                                      color: const Color(0xFF1e40af),
                                      size: 28,
                                    ),
                                    // Online Indicator
                                    Positioned(
                                      bottom: 0,
                                      right: 0,
                                      child: Container(
                                        width: 12,
                                        height: 12,
                                        decoration: BoxDecoration(
                                          color: const Color(0xFF10b981),
                                          shape: BoxShape.circle,
                                          border: Border.all(
                                            color: Colors.white,
                                            width: 2,
                                          ),
                                          boxShadow: [
                                            BoxShadow(
                                              color: const Color(
                                                0xFF10b981,
                                              ).withValues(alpha: 0.4),
                                              blurRadius: 4,
                                            ),
                                          ],
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(width: 12),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      userName,
                                      style: const TextStyle(
                                        fontWeight: FontWeight.w800,
                                        color: Color(0xFF0c2340),
                                      ),
                                    ),
                                    Text(
                                      authProvider.user?.email ?? 'N/A',
                                      style: TextStyle(
                                        fontSize: 11,
                                        color: Colors.grey[600],
                                      ),
                                    ),
                                    const SizedBox(height: 4),
                                    Row(
                                      children: [
                                        Container(
                                          width: 6,
                                          height: 6,
                                          decoration: const BoxDecoration(
                                            color: Color(0xFF10b981),
                                            shape: BoxShape.circle,
                                          ),
                                        ),
                                        const SizedBox(width: 5),
                                        Text(
                                          'Masuk sebagai: ${_roleLabel(authProvider.user?.role)}',
                                          style: const TextStyle(
                                            fontSize: 11,
                                            fontWeight: FontWeight.w700,
                                            color: Color(0xFF1e40af),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ),
                        const PopupMenuDivider(),
                        PopupMenuItem<String>(
                          value: 'profile',
                          child: const Text('Profil Pengguna'),
                          onTap: () {
                            Future.delayed(
                              const Duration(milliseconds: 100),
                              () {
                                if (context.mounted) {
                                  Navigator.pushNamed(context, '/profile');
                                }
                              },
                            );
                          },
                        ),
                        if (authProvider.user?.role == 'administrator') ...[
                          const PopupMenuDivider(),
                          PopupMenuItem<String>(
                            value: 'user_management',
                            child: const Text('Manajemen Pengguna'),
                            onTap: () {
                              Future.delayed(
                                const Duration(milliseconds: 100),
                                () {
                                  if (context.mounted) {
                                    Navigator.pushNamed(
                                      context,
                                      '/user-management',
                                    );
                                  }
                                },
                              );
                            },
                          ),
                        ],
                        const PopupMenuDivider(),
                        PopupMenuItem<String>(
                          value: 'chatbot',
                          child: Row(
                            children: [
                              Icon(
                                Icons.smart_toy_rounded,
                                size: 18,
                                color: Colors.blue[700],
                              ),
                              const SizedBox(width: 10),
                              Text(
                                'ChatBot AI',
                                style: TextStyle(
                                  color: Colors.blue[700],
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ],
                          ),
                          onTap: () {
                            // Delay sedikit agar menu tertutup dulu sebelum
                            // layar chatbot dibuka (transisi lebih mulus).
                            Future.delayed(
                              const Duration(milliseconds: 100),
                              () {
                                if (context.mounted) {
                                  showHutchChatbot(context);
                                }
                              },
                            );
                          },
                        ),
                        const PopupMenuDivider(),
                        PopupMenuItem<String>(
                          value: 'logout',
                          child: const Text(
                            'Logout',
                            style: TextStyle(color: Colors.red),
                          ),
                          onTap: () {
                            _showLogoutDialog(context);
                          },
                        ),
                      ],
                      child: Padding(
                        padding: const EdgeInsets.all(8.0),
                        child: Stack(
                          clipBehavior: Clip.none,
                          children: [
                            CircleAvatar(
                              backgroundColor: Colors.blue[900],
                              radius: 18,
                              child: Icon(
                                Icons.person_rounded,
                                color: Colors.white,
                                size: 22,
                              ),
                            ),
                            Positioned(
                              bottom: 0,
                              right: 0,
                              child: Container(
                                width: 11,
                                height: 11,
                                decoration: BoxDecoration(
                                  color: const Color(0xFF10b981),
                                  shape: BoxShape.circle,
                                  border: Border.all(
                                    color: Colors.white,
                                    width: 2,
                                  ),
                                  boxShadow: [
                                    BoxShadow(
                                      color: const Color(
                                        0xFF10b981,
                                      ).withValues(alpha: 0.5),
                                      blurRadius: 4,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
                const SizedBox(width: 8),
              ],
            ),
          ),
        );
      },
    );
  }

  void _showLogoutDialog(BuildContext context) {
    // Tangkap context HomeScreen (bukan context dialog) sebelum showDialog
    final homeContext = context;

    showDialog(
      context: context,
      builder: (dialogContext) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: Container(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.red[50],
                  shape: BoxShape.circle,
                ),
                child: Icon(
                  Icons.logout_rounded,
                  color: Colors.red[700],
                  size: 32,
                ),
              ),
              const SizedBox(height: 16),
              const Text(
                'Keluar dari Aplikasi?',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 8),
              const Text(
                'Apakah Anda yakin ingin keluar dari aplikasi?',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 13, color: Colors.grey),
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.pop(dialogContext),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        side: BorderSide(color: Colors.grey[300]!),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: const Text('Batal'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: FilledButton(
                      onPressed: () async {
                        // Tutup dialog dulu
                        Navigator.pop(dialogContext);

                        // Ambil auth dari homeContext sebelum logout
                        final auth = Provider.of<AuthProvider>(
                          homeContext,
                          listen: false,
                        );

                        // Jalankan logout (hapus token & SharedPreferences)
                        await auth.logout();

                        // Navigasi ke landing menggunakan homeContext
                        // pushNamedAndRemoveUntil agar seluruh stack dibersihkan
                        if (homeContext.mounted) {
                          Navigator.pushNamedAndRemoveUntil(
                            homeContext,
                            '/login',
                            (route) => false,
                          );
                        }
                      },
                      style: FilledButton.styleFrom(
                        backgroundColor: Colors.red[700],
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: const Text('Keluar'),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _showSyncDialog(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final token = authProvider.token;
    final syncUrl = 'http://localhost:8082/auth/mobile-sync?token=$token';

    showDialog(
      context: context,
      builder: (context) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: SingleChildScrollView(
          child: Container(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.blue[50],
                    shape: BoxShape.circle,
                  ),
                  child: Icon(
                    Icons.sync_rounded,
                    color: Colors.blue[900],
                    size: 32,
                  ),
                ),
                const SizedBox(height: 16),
                const Text(
                  'Sinkronisasi dengan Web',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 8),
                const Text(
                  'Buka tautan ini di browser web untuk login otomatis dan sinkronisasi session',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 13, color: Colors.grey),
                ),
                const SizedBox(height: 20),
                // Display the sync URL
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.grey[100],
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: Colors.grey[300]!),
                  ),
                  child: SelectableText(
                    syncUrl,
                    textAlign: TextAlign.center,
                    style: const TextStyle(
                      fontSize: 11,
                      fontFamily: 'monospace',
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                // Copy button
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () {
                          // Copy to clipboard using Dart's method
                          // For now, just show a message
                          ScaffoldMessenger.of(context).showSnackBar(
                            const SnackBar(
                              content: Text(
                                'Salin URL di atas untuk dibuka di web',
                              ),
                              duration: Duration(seconds: 2),
                            ),
                          );
                        },
                        style: OutlinedButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          side: BorderSide(color: Colors.grey[300]!),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: const Text('Tutup'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: FilledButton(
                        onPressed: () {
                          // Copy URL to clipboard
                          // Using a basic implementation without external clipboard package
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text('URL disalin: $syncUrl'),
                              duration: const Duration(seconds: 3),
                            ),
                          );
                          Navigator.pop(context);
                        },
                        style: FilledButton.styleFrom(
                          backgroundColor: Colors.blue[700],
                          padding: const EdgeInsets.symmetric(vertical: 12),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(8),
                          ),
                        ),
                        child: const Text('Salin & Tutup'),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}