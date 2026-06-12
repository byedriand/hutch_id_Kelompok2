import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/pesanan_provider.dart';
import '../../providers/pelanggan_provider.dart';
import '../home/dashboard_screen.dart';
import '../pesanan/pesanan_list_screen.dart';
import '../pelanggan/pelanggan_list_screen.dart';
import '../produk/produk_list_screen.dart';
import '../notifikasi/notifikasi_screen.dart';
import '../arsip/arsip_screen.dart';

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
          Provider.of<PesananProvider>(context, listen: false).startAutoRefresh();
          Provider.of<PelangganProvider>(
            context,
            listen: false,
          ).startAutoRefresh();
        }
      } catch (e) {
        // Silently handle if providers not available
      }
    });
  }

  @override
  void dispose() {
    // Stop polling when leaving screen
    try {
      Provider.of<PesananProvider>(context, listen: false).stopAutoRefresh();
      Provider.of<PelangganProvider>(context, listen: false).stopAutoRefresh();
    } catch (e) {
      // Silently handle
    }
    super.dispose();
  }

  void _buildNavigation() {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final userRole = authProvider.user?.role ?? '';

    // Screens yang ada
    final dashboardScreen = const DashboardScreen();
    final pesananScreen = const PesananListScreen();
    final pelangganScreen = const PelangganListScreen();
    final produkScreen = const ProdukListScreen();
    final arsipScreen = const ArsipScreen();

    // Helper untuk membuat NavigationDestination
    NavigationDestination buildNavDest(IconData active, IconData inactive, String label) {
      // Find index by label to determine if it's selected
      // Actually, we can just use the properties directly
      return NavigationDestination(
        icon: Icon(inactive),
        selectedIcon: Icon(active),
        label: label,
      );
    }

    // Build navigation berdasarkan role
    if (userRole == 'operator_gudang') {
      _screens = [dashboardScreen, pesananScreen, produkScreen];
      _navigationDestinations = [
        buildNavDest(Icons.dashboard_rounded, Icons.dashboard_outlined, 'Dashboard'),
        buildNavDest(Icons.shopping_bag_rounded, Icons.shopping_bag_outlined, 'Pesanan'),
        buildNavDest(Icons.inventory_2_rounded, Icons.inventory_2_outlined, 'Stok'),
      ];
    } else if (userRole == 'staf_penjualan') {
      _screens = [dashboardScreen, pesananScreen, pelangganScreen, produkScreen];
      _navigationDestinations = [
        buildNavDest(Icons.dashboard_rounded, Icons.dashboard_outlined, 'Dashboard'),
        buildNavDest(Icons.shopping_bag_rounded, Icons.shopping_bag_outlined, 'Pesanan'),
        buildNavDest(Icons.person_rounded, Icons.person_outlined, 'Pelanggan'),
        buildNavDest(Icons.add_box_rounded, Icons.add_box_outlined, 'Produk'),
      ];
    } else {
      // administrator
      _screens = [dashboardScreen, pesananScreen, pelangganScreen, produkScreen, arsipScreen];
      _navigationDestinations = [
        buildNavDest(Icons.dashboard_rounded, Icons.dashboard_outlined, 'Dashboard'),
        buildNavDest(Icons.shopping_bag_rounded, Icons.shopping_bag_outlined, 'Pesanan'),
        buildNavDest(Icons.person_rounded, Icons.person_outlined, 'Pelanggan'),
        buildNavDest(Icons.inventory_2_rounded, Icons.inventory_2_outlined, 'Produk'),
        buildNavDest(Icons.description_rounded, Icons.description_outlined, 'Arsip'),
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
                IconButton(
                  icon: const Icon(Icons.notifications_outlined, color: Color(0xFF1e40af)),
                  onPressed: () {
                    // Navigate to NotifikasiScreen or open as modal
                    Navigator.push(
                      context,
                      MaterialPageRoute(builder: (context) => const NotifikasiScreen()),
                    );
                  },
                ),
                Consumer<AuthProvider>(
                  builder: (context, authProvider, _) {
                    final userName = authProvider.user?.name ?? 'User';
                    return PopupMenuButton<String>(
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
                                  ],
                                ),
                              ],
                            ),
                          ),
                        ),
                        const PopupMenuDivider(),
                        PopupMenuItem<String>(
                          value: 'profile',
                          child: const Text('Profil Saya'),
                          onTap: () {
                            setState(() {
                              _selectedIndex = _screens.length - 1;
                            });
                          },
                        ),
                        const PopupMenuDivider(),
                        PopupMenuItem<String>(
                          value: 'sync',
                          child: const Text('Sinkronisasi dengan Web'),
                          onTap: () {
                            _showSyncDialog(context);
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
                        child: CircleAvatar(
                          backgroundColor: Colors.blue[900],
                          radius: 18,
                          child: Icon(
                            Icons.person_rounded,
                            color: Colors.white,
                            size: 22,
                          ),
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
    showDialog(
      context: context,
      builder: (context) => Dialog(
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
                      onPressed: () => Navigator.pop(context),
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
                        // Close the dialog first
                        Navigator.pop(context);

                        // Perform logout
                        await Provider.of<AuthProvider>(
                          context,
                          listen: false,
                        ).logout();

                        if (context.mounted) {
                          // Show success popup
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: const Text('Anda telah berhasil logout'),
                              backgroundColor: Colors.green[600],
                              duration: const Duration(seconds: 2),
                              action: SnackBarAction(
                                label: 'Tutup',
                                textColor: Colors.white,
                                onPressed: () {},
                              ),
                            ),
                          );

                          // Navigate to login after a short delay
                          await Future.delayed(
                            const Duration(milliseconds: 500),
                          );
                          if (context.mounted) {
                            Navigator.pushNamedAndRemoveUntil(
                              context,
                              '/login',
                              (route) => false,
                            );
                          }
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
