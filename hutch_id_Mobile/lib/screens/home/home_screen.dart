import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';
import '../../providers/pesanan_provider.dart';
import '../../providers/pelanggan_provider.dart';
import '../../providers/notifikasi_provider.dart';
import '../home/dashboard_screen.dart';
import '../pesanan/pesanan_list_screen.dart';
import '../pesanan/pesanan_form_screen.dart';
import '../pelanggan/pelanggan_list_screen.dart';
import '../produk/produk_list_screen.dart';
import '../notifikasi/notifikasi_screen.dart';
import '../arsip/arsip_screen.dart';
import '../../config/app_config.dart';
import '../../widgets/app_sidebar.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  State<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  int _selectedIndex = 0;

  late List<Widget> _screens;
  late List<SidebarMenuItem> _menuItems;

  @override
  void initState() {
    super.initState();
    _buildNavigation();

    Future.microtask(() {
      try {
        if (mounted) {
          Provider.of<PesananProvider>(context, listen: false)
              .startAutoRefresh();
          Provider.of<PelangganProvider>(context, listen: false)
              .startAutoRefresh();
          Provider.of<NotifikasiProvider>(context, listen: false)
              .fetchNotifikasi();
        }
      } catch (e) {
        // Silently handle
      }
    });
  }

  @override
  void dispose() {
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

    final dashboardScreen = const DashboardScreen();
    final notifikasiScreen = const NotifikasiScreen();
    final pesananScreen = const PesananListScreen();
    final pelangganScreen = const PelangganListScreen();
    final produkScreen = const ProdukListScreen();
    final arsipScreen = const ArsipScreen();

    if (userRole == 'operator_gudang') {
      _screens = [
        dashboardScreen,
        notifikasiScreen,
        pesananScreen,
        produkScreen,
      ];
      _menuItems = const [
        SidebarMenuItem(
          index: 0,
          icon: Icons.dashboard_outlined,
          iconSelected: Icons.dashboard_rounded,
          label: 'Dashboard',
          section: 'menu',
        ),
        SidebarMenuItem(
          index: 1,
          icon: Icons.notifications_outlined,
          iconSelected: Icons.notifications_rounded,
          label: 'Notifikasi',
          section: 'menu',
          badgeKey: 'notifikasi',
        ),
        SidebarMenuItem(
          index: 2,
          icon: Icons.list_alt_outlined,
          iconSelected: Icons.list_alt_rounded,
          label: 'Daftar Pesanan',
          section: 'menu',
          badgeKey: 'pesanan',
        ),
        SidebarMenuItem(
          index: 3,
          icon: Icons.inventory_2_outlined,
          iconSelected: Icons.inventory_2_rounded,
          label: 'Manajemen Stok',
          section: 'menu',
        ),
      ];
    } else if (userRole == 'staf_penjualan') {
      _screens = [
        dashboardScreen,
        notifikasiScreen,
        pesananScreen,
        PesananFormScreen(),
        pelangganScreen,
      ];
      _menuItems = const [
        SidebarMenuItem(
          index: 0,
          icon: Icons.dashboard_outlined,
          iconSelected: Icons.dashboard_rounded,
          label: 'Dashboard',
          section: 'menu',
        ),
        SidebarMenuItem(
          index: 1,
          icon: Icons.notifications_outlined,
          iconSelected: Icons.notifications_rounded,
          label: 'Notifikasi',
          section: 'menu',
          badgeKey: 'notifikasi',
        ),
        SidebarMenuItem(
          index: 2,
          icon: Icons.list_alt_outlined,
          iconSelected: Icons.list_alt_rounded,
          label: 'Daftar Pesanan',
          section: 'menu',
          badgeKey: 'pesanan',
        ),
        SidebarMenuItem(
          index: 3,
          icon: Icons.add_circle_outline_rounded,
          iconSelected: Icons.add_circle_rounded,
          label: 'Buat PO',
          section: 'menu',
        ),
        SidebarMenuItem(
          index: 4,
          icon: Icons.people_outline_rounded,
          iconSelected: Icons.people_rounded,
          label: 'Pelanggan',
          section: 'menu',
        ),
      ];
    } else {
      // administrator
      _screens = [
        dashboardScreen,
        notifikasiScreen,
        pesananScreen,
        PesananFormScreen(),
        pelangganScreen,
        arsipScreen,
      ];
      _menuItems = const [
        SidebarMenuItem(
          index: 0,
          icon: Icons.dashboard_outlined,
          iconSelected: Icons.dashboard_rounded,
          label: 'Dashboard',
          section: 'menu',
        ),
        SidebarMenuItem(
          index: 1,
          icon: Icons.notifications_outlined,
          iconSelected: Icons.notifications_rounded,
          label: 'Notifikasi',
          section: 'menu',
          badgeKey: 'notifikasi',
        ),
        SidebarMenuItem(
          index: 2,
          icon: Icons.list_alt_outlined,
          iconSelected: Icons.list_alt_rounded,
          label: 'Daftar Pesanan',
          section: 'menu',
          badgeKey: 'pesanan',
        ),
        SidebarMenuItem(
          index: 3,
          icon: Icons.add_circle_outline_rounded,
          iconSelected: Icons.add_circle_rounded,
          label: 'Buat PO',
          section: 'menu',
        ),
        SidebarMenuItem(
          index: 4,
          icon: Icons.people_outline_rounded,
          iconSelected: Icons.people_rounded,
          label: 'Pelanggan',
          section: 'menu',
        ),
        SidebarMenuItem(
          index: 5,
          icon: Icons.archive_outlined,
          iconSelected: Icons.archive_rounded,
          label: 'Arsip PDF',
          section: 'admin',
        ),
      ];
    }
  }

  @override
  Widget build(BuildContext context) {
    return Consumer<AuthProvider>(
      builder: (context, authProvider, _) {
        _buildNavigation();

        return PopScope(
          canPop: _selectedIndex == 0,
          onPopInvokedWithResult: (didPop, _) {
            if (didPop) return;
            if (_selectedIndex != 0) {
              setState(() => _selectedIndex = 0);
            }
          },
          child: SidebarScaffold(
            selectedIndex: _selectedIndex,
            onItemSelected: (index) {
              setState(() => _selectedIndex = index);
            },
            menuItems: _menuItems,
            onChatBot: () => _showChatBotDialog(context),
            appBarActions: [
              // Notif badge action di mobile AppBar
              Consumer<NotifikasiProvider>(
                builder: (context, notifProvider, _) {
                  final count = notifProvider.notifikasiList.length;
                  return Stack(
                    children: [
                      IconButton(
                        icon: const Icon(Icons.notifications_outlined,
                            color: Colors.white),
                        onPressed: () {
                          setState(() => _selectedIndex = 1);
                        },
                      ),
                      if (count > 0)
                        Positioned(
                          top: 8,
                          right: 8,
                          child: Container(
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              color: Colors.red[600],
                              shape: BoxShape.circle,
                            ),
                            child: Text(
                              count > 9 ? '9+' : '$count',
                              style: const TextStyle(
                                fontSize: 8,
                                fontWeight: FontWeight.w700,
                                color: Colors.white,
                              ),
                            ),
                          ),
                        ),
                    ],
                  );
                },
              ),
              _buildSyncButton(context),
            ],
            body: IndexedStack(
              index: _selectedIndex,
              children: _screens,
            ),
          ),
        );
      },
    );
  }

  Widget _buildSyncButton(BuildContext context) {
    return IconButton(
      icon: const Icon(Icons.sync_rounded, color: Colors.white),
      tooltip: 'Sinkronisasi Web',
      onPressed: () => _showSyncDialog(context),
    );
  }

  void _showChatBotDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (ctx) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF0ea5e9), Color(0xFF0284c7)],
                  ),
                  shape: BoxShape.circle,
                ),
                child: const Icon(Icons.smart_toy_rounded,
                    color: Colors.white, size: 32),
              ),
              const SizedBox(height: 16),
              const Text(
                'ChatBot AI',
                style:
                    TextStyle(fontSize: 18, fontWeight: FontWeight.w700),
              ),
              const SizedBox(height: 8),
              const Text(
                'Fitur ChatBot AI akan segera tersedia.\nTunggu pembaruan berikutnya!',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 13, color: Colors.grey),
              ),
              const SizedBox(height: 24),
              SizedBox(
                width: double.infinity,
                child: FilledButton(
                  onPressed: () => Navigator.pop(ctx),
                  style: FilledButton.styleFrom(
                    backgroundColor: const Color(0xFF0ea5e9),
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  child: const Text('Tutup'),
                ),
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
    final baseUrl = AppConfig.apiBaseUrl.replaceAll('/api', '');
    final syncUrl = '$baseUrl/auth/mobile-sync?token=$token';

    showDialog(
      context: context,
      builder: (ctx) => Dialog(
        shape:
            RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.blue[50],
                    shape: BoxShape.circle,
                  ),
                  child: Icon(Icons.sync_rounded,
                      color: Colors.blue[900], size: 32),
                ),
                const SizedBox(height: 16),
                const Text(
                  'Sinkronisasi dengan Web',
                  style:
                      TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
                  textAlign: TextAlign.center,
                ),
                const SizedBox(height: 8),
                const Text(
                  'Buka tautan ini di browser web untuk login otomatis',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 13, color: Colors.grey),
                ),
                const SizedBox(height: 20),
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
                        fontSize: 11, fontFamily: 'monospace'),
                  ),
                ),
                const SizedBox(height: 20),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        onPressed: () => Navigator.pop(ctx),
                        style: OutlinedButton.styleFrom(
                          padding:
                              const EdgeInsets.symmetric(vertical: 12),
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
                          ScaffoldMessenger.of(context).showSnackBar(
                            SnackBar(
                              content: Text('URL: $syncUrl'),
                              duration: const Duration(seconds: 3),
                            ),
                          );
                          Navigator.pop(ctx);
                        },
                        style: FilledButton.styleFrom(
                          backgroundColor: Colors.blue[700],
                          padding:
                              const EdgeInsets.symmetric(vertical: 12),
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
