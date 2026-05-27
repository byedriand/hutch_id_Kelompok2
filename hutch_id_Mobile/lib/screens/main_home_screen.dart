import 'package:flutter/material.dart';
import '../utils/responsive.dart';
import '../widgets/sidebar.dart';
import 'pelanggan/daftar_pelanggan_screen.dart';
import '../models/user_model.dart';
import '../models/pelanggan_model.dart';
import '../services/api_service.dart';

class MainHomeScreen extends StatefulWidget {
  final User user;
  const MainHomeScreen({super.key, required this.user});

  @override
  State<MainHomeScreen> createState() => _MainHomeScreenState();
}

class _MainHomeScreenState extends State<MainHomeScreen> {
  int selectedMenuIndex = 0;
  bool _isLoading = true;

  int _totalPesanan = 0;
  int _totalPelanggan = 0;
  int _poPending = 0;
  int _poSelesai = 0;

  // Global shared state
  List<Pelanggan> pelangganList = [];
  List<Map<String, dynamic>> pesananList = [];
  List<Map<String, dynamic>> pdfFiles = [];

  @override
  void initState() {
    super.initState();
    _loadAllData();
  }

  Future<void> _loadAllData() async {
    setState(() => _isLoading = true);
    try {
      final dashboardData = await ApiService.getDashboard();
      final pelangganData = await ApiService.getPelanggan();
      final pesananData = await ApiService.getPesanan();
      final pdfData = await ApiService.getArsipPdf();

      setState(() {
        if (dashboardData != null) {
          _totalPesanan = dashboardData['totalPesanan'] ?? 0;
          _totalPelanggan = dashboardData['totalPelanggan'] ?? 0;
          _poPending = dashboardData['poPending'] ?? 0;
          _poSelesai = dashboardData['poSelesai'] ?? 0;
        }
        pelangganList = pelangganData;
        pesananList = pesananData;
        pdfFiles = pdfData;
      });
    } catch (e) {
      debugPrint('Error loading data from API: $e');
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final bool isMobile = Responsive.isMobile(context);

    // Generate screens dynamically to ensure state changes are passed down correctly
    final List<Widget> screens = [
      DashboardScreenContent(
        totalPesanan: _totalPesanan,
        totalPelanggan: _totalPelanggan,
        poPending: _poPending,
        poSelesai: _poSelesai,
        user: widget.user,
        pesananList: pesananList,
        onNavigate: (index) {
          setState(() {
            selectedMenuIndex = index;
          });
        },
      ),
      DaftarPesananScreenContent(
        pesananList: pesananList,
        onDelete: (id) async {
          final success = await ApiService.deletePesanan(id);
          if (success) {
            await _loadAllData();
          }
        },
        onStatusChanged: (id, newStatus) async {
          final success = await ApiService.updatePesananStatus(id, newStatus);
          if (success) {
            await _loadAllData();
          }
        },
      ),
      BuatPoScreenContent(
        pelangganList: pelangganList,
        onSave: (pelangganNama, deskripsi, jumlah, harga, status) async {
          final result = await ApiService.createPesanan(pelangganNama, deskripsi, jumlah, harga, status);
          if (result != null) {
            await _loadAllData();
            setState(() {
              selectedMenuIndex = 1; // Switch to Daftar Pesanan tab
            });
          }
        },
      ),
      DaftarPelangganScreenWidget(
        pelangganList: pelangganList,
        onAdd: (nama, telepon, alamat, email) async {
          final result = await ApiService.createPelanggan(nama, telepon, alamat, email);
          if (result != null) {
            await _loadAllData();
          }
        },
        onEdit: (id, nama, telepon, alamat, email) async {
          final result = await ApiService.updatePelanggan(id, nama, telepon, alamat, email);
          if (result != null) {
            await _loadAllData();
          }
        },
        onDelete: (id) async {
          final success = await ApiService.deletePelanggan(id);
          if (success) {
            await _loadAllData();
          }
        },
      ),
      ArsipPdfScreenContent(
        pdfFiles: pdfFiles,
        onDelete: (id) async {
          final success = await ApiService.deleteArsipPdf(id);
          if (success) {
            await _loadAllData();
          }
        },
      ),
    ];

    // ── Mobile Layout ────────────────────────────────────────────────────────
    if (isMobile) {
      return Scaffold(
        backgroundColor: const Color(0xFFF0F4FF),
        body: Stack(
          children: [
            AnimatedSwitcher(
              duration: const Duration(milliseconds: 350),
              switchInCurve: Curves.easeInOut,
              switchOutCurve: Curves.easeInOut,
              transitionBuilder: (Widget child, Animation<double> animation) {
                return FadeTransition(opacity: animation, child: child);
              },
              child: Container(
                key: ValueKey<int>(selectedMenuIndex),
                child: screens[selectedMenuIndex],
              ),
            ),
            if (_isLoading)
              const Positioned(
                top: 0, left: 0, right: 0,
                child: LinearProgressIndicator(
                  backgroundColor: Colors.transparent,
                  valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF2563eb)),
                  minHeight: 3,
                ),
              ),
          ],
        ),
        bottomNavigationBar: _buildMobileBottomNav(),
      );
    }

    // ── Desktop Layout ───────────────────────────────────────────────────────
    return Scaffold(
      body: Row(
        children: [
          Sidebar(
            selectedIndex: selectedMenuIndex,
            user: widget.user,
            pesananBadgeCount: pesananList.where((p) => p['status'] == 'Pending' || p['status'] == 'Proses').length,
            onMenuSelected: (index) {
              setState(() {
                selectedMenuIndex = index;
              });
            },
          ),
          Expanded(
            child: Stack(
              children: [
                AnimatedSwitcher(
                  duration: const Duration(milliseconds: 800),
                  switchInCurve: Curves.easeInOutCubic,
                  switchOutCurve: Curves.easeInOutCubic,
                  transitionBuilder: (Widget child, Animation<double> animation) {
                    return FadeTransition(
                      opacity: animation,
                      child: SlideTransition(
                        position: Tween<Offset>(
                          begin: const Offset(0.04, 0.0),
                          end: Offset.zero,
                        ).animate(animation),
                        child: child,
                      ),
                    );
                  },
                  child: Container(
                    key: ValueKey<int>(selectedMenuIndex),
                    child: screens[selectedMenuIndex],
                  ),
                ),
                if (_isLoading)
                  const Positioned(
                    top: 0, left: 0, right: 0,
                    child: LinearProgressIndicator(
                      backgroundColor: Colors.transparent,
                      valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF2563eb)),
                      minHeight: 3,
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMobileBottomNav() {
    final int badge = pesananList
        .where((p) => p['status'] == 'Pending' || p['status'] == 'Proses')
        .length;
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.08),
            blurRadius: 16,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: SafeArea(
        child: SizedBox(
          height: 65,
          child: Row(
            children: [
              _buildNavItem(0, Icons.dashboard_rounded, 'Dashboard'),
              _buildNavItem(1, Icons.shopping_cart_rounded, 'Pesanan', badge: badge),
              _buildNavItem(2, Icons.add_circle_rounded, 'Buat PO'),
              _buildNavItem(3, Icons.people_rounded, 'Pelanggan'),
              _buildNavItem(4, Icons.picture_as_pdf_rounded, 'Arsip'),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(int index, IconData icon, String label, {int badge = 0}) {
    final bool isSelected = selectedMenuIndex == index;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => selectedMenuIndex = index),
        child: Container(
          color: Colors.transparent,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Stack(
                clipBehavior: Clip.none,
                children: [
                  AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: isSelected
                          ? const Color(0xFF2563eb).withValues(alpha: 0.12)
                          : Colors.transparent,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(
                      icon,
                      size: 22,
                      color: isSelected ? const Color(0xFF2563eb) : Colors.grey[400],
                    ),
                  ),
                  if (badge > 0)
                    Positioned(
                      top: -4, right: -4,
                      child: Container(
                        padding: const EdgeInsets.all(3),
                        decoration: const BoxDecoration(
                          color: Colors.red,
                          shape: BoxShape.circle,
                        ),
                        child: Text(
                          '$badge',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 9,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                ],
              ),
              const SizedBox(height: 2),
              Text(
                label,
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: isSelected ? FontWeight.w700 : FontWeight.w400,
                  color: isSelected ? const Color(0xFF2563eb) : Colors.grey[400],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// Dashboard Content - Responsive (Mobile + Desktop)
class DashboardScreenContent extends StatelessWidget {
  final int totalPesanan;
  final int totalPelanggan;
  final int poPending;
  final int poSelesai;
  final User user;
  final List<Map<String, dynamic>> pesananList;
  final ValueChanged<int> onNavigate;

  const DashboardScreenContent({
    super.key,
    required this.totalPesanan,
    required this.totalPelanggan,
    required this.poPending,
    required this.poSelesai,
    required this.user,
    required this.pesananList,
    required this.onNavigate,
  });

  @override
  Widget build(BuildContext context) {
    return Responsive.isMobile(context)
        ? _buildMobileLayout(context)
        : _buildDesktopLayout(context);
  }

  // ── Desktop Layout ──────────────────────────────────────────────────────────
  Widget _buildDesktopLayout(BuildContext context) {
    return Container(
      color: const Color(0xFFF8FAFC),
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Selamat Datang 👋',
                style: TextStyle(fontSize: 32, fontWeight: FontWeight.bold, color: Color(0xFF1e3a8a)),
              ),
              const SizedBox(height: 8),
              Text(
                'Kelola pesanan dan pelanggan dengan mudah',
                style: TextStyle(fontSize: 14, color: Colors.grey[600]),
              ),
            ],
          ),
          const SizedBox(height: 30),
          Expanded(
            child: GridView.count(
              crossAxisCount: 2,
              crossAxisSpacing: 20,
              mainAxisSpacing: 20,
              children: [
                _buildModernCard(context, 'Total Pesanan', '$totalPesanan', Icons.shopping_cart, const Color(0xFF3B82F6), const Color(0xFFDBEAFE), () => onNavigate(1)),
                _buildModernCard(context, 'Total Pelanggan', '$totalPelanggan', Icons.people, const Color(0xFF10B981), const Color(0xFFD1FAE5), () => onNavigate(3)),
                _buildModernCard(context, 'PO Pending', '$poPending', Icons.pending_actions, const Color(0xFFF59E0B), const Color(0xFFFEF3C7), () => onNavigate(1)),
                _buildModernCard(context, 'Selesai', '$poSelesai', Icons.check_circle, const Color(0xFF8B5CF6), const Color(0xFFEDE9FE), () => onNavigate(1)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // ── Mobile Layout ───────────────────────────────────────────────────────────
  Widget _buildMobileLayout(BuildContext context) {
    final String initials = user.nama.isNotEmpty
        ? user.nama.split(' ').take(2).map((e) => e.isNotEmpty ? e[0].toUpperCase() : '').join()
        : 'U';
    final recentPesanan = pesananList.take(3).toList();

    return Container(
      color: const Color(0xFFF0F4FF),
      child: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // ── Header gradient ──────────────────────────────────────────
              Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [Color(0xFF1e3a8a), Color(0xFF2563eb)],
                  ),
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(28),
                    bottomRight: Radius.circular(28),
                  ),
                ),
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 32),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Halo, ${user.nama.split(' ').first} 👋',
                              style: const TextStyle(color: Colors.white, fontSize: 20, fontWeight: FontWeight.bold),
                            ),
                            const SizedBox(height: 2),
                            Text(user.role, style: const TextStyle(color: Colors.white70, fontSize: 13)),
                          ],
                        ),
                        CircleAvatar(
                          radius: 26,
                          backgroundColor: Colors.white24,
                          child: Text(
                            initials,
                            style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                      decoration: BoxDecoration(color: Colors.white12, borderRadius: BorderRadius.circular(12)),
                      child: const Row(
                        children: [
                          Icon(Icons.storefront, color: Colors.white70, size: 16),
                          SizedBox(width: 8),
                          Text('HUTCHID — Bag Manufacturing', style: TextStyle(color: Colors.white, fontSize: 12)),
                        ],
                      ),
                    ),
                  ],
                ),
              ),

              // ── Stats Cards ──────────────────────────────────────────────
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Ringkasan', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF1e3a8a))),
                    const SizedBox(height: 12),
                    GridView.count(
                      crossAxisCount: 2,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      crossAxisSpacing: 12,
                      mainAxisSpacing: 12,
                      childAspectRatio: 1.6,
                      children: [
                        _buildMobileStatCard('Total Pesanan', '$totalPesanan', Icons.shopping_cart_rounded, const Color(0xFF3B82F6), const Color(0xFFDBEAFE), () => onNavigate(1)),
                        _buildMobileStatCard('Pelanggan', '$totalPelanggan', Icons.people_rounded, const Color(0xFF10B981), const Color(0xFFD1FAE5), () => onNavigate(3)),
                        _buildMobileStatCard('PO Pending', '$poPending', Icons.pending_actions_rounded, const Color(0xFFF59E0B), const Color(0xFFFEF3C7), () => onNavigate(1)),
                        _buildMobileStatCard('Selesai', '$poSelesai', Icons.check_circle_rounded, const Color(0xFF8B5CF6), const Color(0xFFEDE9FE), () => onNavigate(1)),
                      ],
                    ),
                  ],
                ),
              ),

              // ── Quick Actions ────────────────────────────────────────────
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('Aksi Cepat', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF1e3a8a))),
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Expanded(child: _buildQuickAction(Icons.add_circle_rounded, 'Buat PO', const Color(0xFF2563eb), () => onNavigate(2))),
                        const SizedBox(width: 10),
                        Expanded(child: _buildQuickAction(Icons.person_add_rounded, 'Tambah\nPelanggan', const Color(0xFF10B981), () => onNavigate(3))),
                        const SizedBox(width: 10),
                        Expanded(child: _buildQuickAction(Icons.list_alt_rounded, 'Daftar\nPesanan', const Color(0xFFF59E0B), () => onNavigate(1))),
                      ],
                    ),
                  ],
                ),
              ),

              // ── Recent Pesanan ───────────────────────────────────────────
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 24),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Pesanan Terbaru', style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF1e3a8a))),
                        GestureDetector(
                          onTap: () => onNavigate(1),
                          child: const Text('Lihat Semua →', style: TextStyle(fontSize: 12, color: Color(0xFF2563eb), fontWeight: FontWeight.w600)),
                        ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    recentPesanan.isEmpty
                        ? Container(
                            padding: const EdgeInsets.all(24),
                            decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(16)),
                            child: Center(child: Text('Belum ada pesanan', style: TextStyle(color: Colors.grey[400], fontSize: 13))),
                          )
                        : Column(children: recentPesanan.map(_buildRecentItem).toList()),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMobileStatCard(String title, String value, IconData icon, Color color, Color bgColor, VoidCallback onTap) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: color.withValues(alpha: 0.15)),
            boxShadow: [BoxShadow(color: color.withValues(alpha: 0.08), blurRadius: 12, offset: const Offset(0, 4))],
          ),
          padding: const EdgeInsets.all(14),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(color: bgColor, borderRadius: BorderRadius.circular(10)),
                child: Icon(icon, color: color, size: 20),
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(value, style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold, color: color)),
                  Text(title, style: TextStyle(fontSize: 11, color: Colors.grey[500], fontWeight: FontWeight.w500)),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildQuickAction(IconData icon, String label, Color color, VoidCallback onTap) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(color: color.withValues(alpha: 0.2)),
          boxShadow: [BoxShadow(color: color.withValues(alpha: 0.06), blurRadius: 10, offset: const Offset(0, 3))],
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(color: color.withValues(alpha: 0.1), shape: BoxShape.circle),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(height: 6),
            Text(label, style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: color), textAlign: TextAlign.center),
          ],
        ),
      ),
    );
  }

  Widget _buildRecentItem(Map<String, dynamic> pesanan) {
    Color statusColor = Colors.orange;
    if (pesanan['status'] == 'Proses') statusColor = Colors.blue;
    else if (pesanan['status'] == 'Selesai') statusColor = Colors.green;
    else if (pesanan['status'] == 'Draft') statusColor = Colors.grey;

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        boxShadow: [BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 10, offset: const Offset(0, 3))],
      ),
      child: Row(
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(color: statusColor.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(10)),
            child: Icon(Icons.receipt_long_rounded, color: statusColor, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(pesanan['pelanggan'] ?? 'Umum', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Color(0xFF1e3a8a))),
                const SizedBox(height: 2),
                Text(pesanan['no'] ?? '', style: TextStyle(fontSize: 11, color: Colors.grey[400])),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
            decoration: BoxDecoration(color: statusColor.withValues(alpha: 0.12), borderRadius: BorderRadius.circular(20)),
            child: Text(pesanan['status'] ?? 'Pending', style: TextStyle(fontSize: 11, fontWeight: FontWeight.w600, color: statusColor)),
          ),
        ],
      ),
    );
  }

  Widget _buildModernCard(BuildContext context, String title, String value, IconData icon, Color color, Color bgColor, VoidCallback onTap) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(16),
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [color.withValues(alpha: 0.1), bgColor],
            ),
            border: Border.all(color: color.withValues(alpha: 0.2), width: 1.5),
            boxShadow: [
              BoxShadow(
                color: color.withValues(alpha: 0.1),
                blurRadius: 20,
                offset: const Offset(0, 10),
              ),
            ],
          ),
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.2),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(icon, color: color, size: 28),
                ),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      value,
                      style: const TextStyle(
                        fontSize: 36,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF1e3a8a),
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      title,
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey[600],
                        fontWeight: FontWeight.w500,
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

// Daftar Pesanan - Design Wow
class DaftarPesananScreenContent extends StatefulWidget {
  final List<Map<String, dynamic>> pesananList;
  final Future<void> Function(String) onDelete;
  final Future<void> Function(String, String) onStatusChanged;

  const DaftarPesananScreenContent({
    super.key,
    required this.pesananList,
    required this.onDelete,
    required this.onStatusChanged,
  });

  @override
  State<DaftarPesananScreenContent> createState() => _DaftarPesananScreenContentState();
}

class _DaftarPesananScreenContentState extends State<DaftarPesananScreenContent> {
  @override
  Widget build(BuildContext context) {
    return Container(
      color: const Color(0xFFF8FAFC),
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Daftar Pesanan 📋',
            style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Color(0xFF1e3a8a)),
          ),
          const SizedBox(height: 4),
          Text(
            'Kelola semua pesanan Anda di sini',
            style: TextStyle(fontSize: 13, color: Colors.grey[600]),
          ),
          const SizedBox(height: 24),
          Expanded(
            child: widget.pesananList.isEmpty
                ? Center(
                    child: Text(
                      'Tidak ada pesanan',
                      style: TextStyle(color: Colors.grey[600], fontSize: 14),
                    ),
                  )
                : ListView.builder(
                    itemCount: widget.pesananList.length,
                    itemBuilder: (context, index) {
                      final pesanan = widget.pesananList[index];

                      // Map status to color and icon dynamically
                      Color statusColor = Colors.orange;
                      IconData statusIcon = Icons.hourglass_bottom;
                      if (pesanan['status'] == 'Proses') {
                        statusColor = Colors.blue;
                        statusIcon = Icons.autorenew;
                      } else if (pesanan['status'] == 'Selesai') {
                        statusColor = Colors.green;
                        statusIcon = Icons.check_circle;
                      } else if (pesanan['status'] == 'Draft') {
                        statusColor = Colors.grey;
                        statusIcon = Icons.drafts;
                      }

                      return Container(
                        margin: const EdgeInsets.only(bottom: 16),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          color: Colors.white,
                          border: Border.all(color: Colors.grey[200]!, width: 1),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.05),
                              blurRadius: 12,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            children: [
                              Row(
                                children: [
                                  Container(
                                    padding: const EdgeInsets.all(12),
                                    decoration: BoxDecoration(
                                      color: statusColor.withValues(alpha: 0.15),
                                      borderRadius: BorderRadius.circular(10),
                                    ),
                                    child: Icon(
                                      statusIcon,
                                      color: statusColor,
                                      size: 24,
                                    ),
                                  ),
                                  const SizedBox(width: 16),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          pesanan['pelanggan'] ?? 'Umum',
                                          style: const TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                            color: Color(0xFF1e3a8a),
                                          ),
                                        ),
                                        const SizedBox(height: 4),
                                        Text(
                                          '${pesanan['no'] ?? ''} • ${pesanan['tanggal'] ?? ''}',
                                          style: TextStyle(
                                            fontSize: 12,
                                            color: Colors.grey[500],
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                  Container(
                                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                    decoration: BoxDecoration(
                                      color: statusColor.withValues(alpha: 0.15),
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Text(
                                      pesanan['status'] ?? 'Pending',
                                      style: TextStyle(
                                        fontSize: 12,
                                        fontWeight: FontWeight.w600,
                                        color: statusColor,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                              const SizedBox(height: 12),
                              const Divider(height: 1),
                              const SizedBox(height: 12),
                              Row(
                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                children: [
                                  Text(
                                    pesanan['total'] ?? 'Rp 0',
                                    style: const TextStyle(
                                      fontSize: 14,
                                      fontWeight: FontWeight.bold,
                                      color: Color(0xFF1e3a8a),
                                    ),
                                  ),
                                  Row(
                                    children: [
                                      SizedBox(
                                        height: 32,
                                        child: OutlinedButton.icon(
                                          onPressed: () {
                                            _showDetailDialog(context, index, pesanan);
                                          },
                                          icon: const Icon(Icons.visibility, size: 16),
                                          label: const Text('Lihat'),
                                          style: OutlinedButton.styleFrom(
                                            foregroundColor: const Color(0xFF2563eb),
                                            side: const BorderSide(color: Color(0xFF2563eb)),
                                            padding: const EdgeInsets.symmetric(horizontal: 12),
                                          ),
                                        ),
                                      ),
                                      const SizedBox(width: 8),
                                      SizedBox(
                                        height: 32,
                                        child: ElevatedButton.icon(
                                          onPressed: () {
                                            showDialog(
                                              context: context,
                                              builder: (BuildContext context) {
                                                return AlertDialog(
                                                  title: const Text('Hapus Pesanan'),
                                                  content: Text('Hapus pesanan ${pesanan['no']}?'),
                                                  actions: [
                                                    TextButton(
                                                      onPressed: () => Navigator.pop(context),
                                                      child: const Text('Batal'),
                                                    ),
                                                    TextButton(
                                                      onPressed: () async {
                                                        await widget.onDelete(pesanan['id'].toString());
                                                        if (context.mounted) {
                                                          Navigator.pop(context);
                                                          ScaffoldMessenger.of(context).showSnackBar(
                                                            SnackBar(content: Text('${pesanan['no']} dihapus')),
                                                          );
                                                        }
                                                      },
                                                      child: const Text('Hapus', style: TextStyle(color: Colors.red)),
                                                    ),
                                                  ],
                                                );
                                              },
                                            );
                                          },
                                          icon: const Icon(Icons.delete, size: 16),
                                          label: const Text('Hapus'),
                                          style: ElevatedButton.styleFrom(
                                            backgroundColor: Colors.red,
                                            foregroundColor: Colors.white,
                                            padding: const EdgeInsets.symmetric(horizontal: 12),
                                          ),
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }

  void _showDetailDialog(BuildContext context, int index, Map<String, dynamic> pesanan) {
    String currentStatus = pesanan['status'] ?? 'Pending';
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setDialogState) {
            final double hargaUnit = (pesanan['harga'] ?? 100000).toDouble();
            final int qty = pesanan['jumlah'] ?? 10;
            final String deskripsi = pesanan['deskripsi'] ?? 'Produk Custom';

            return AlertDialog(
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              title: Row(
                children: [
                  const Icon(Icons.receipt_long, color: Color(0xFF1e3a8a)),
                  const SizedBox(width: 8),
                  Text(
                    'Detail Pesanan ${pesanan['no']}',
                    style: const TextStyle(color: Color(0xFF1e3a8a), fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              content: SingleChildScrollView(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildDetailRow('Pelanggan', pesanan['pelanggan'] ?? 'Umum'),
                    _buildDetailRow('Tanggal', pesanan['tanggal'] ?? ''),
                    _buildDetailRow('Deskripsi', deskripsi),
                    _buildDetailRow('Jumlah', '$qty Pcs'),
                    _buildDetailRow('Harga Satuan', 'Rp ${hargaUnit.toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}/Pcs'),
                    const Divider(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text('Total Pembayaran', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15)),
                        Text(
                          pesanan['total'] ?? 'Rp 0',
                          style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: Color(0xFF2563eb)),
                        ),
                      ],
                    ),
                    const SizedBox(height: 20),
                    const Text('Status Pesanan', style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
                    const SizedBox(height: 8),
                    DropdownButtonFormField<String>(
                      initialValue: currentStatus,
                      decoration: InputDecoration(
                        border: OutlineInputBorder(borderRadius: BorderRadius.circular(8)),
                        contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      ),
                      items: ['Draft', 'Pending', 'Proses', 'Selesai']
                          .map((status) => DropdownMenuItem(
                                value: status,
                                child: Text(status),
                              ))
                          .toList(),
                      onChanged: (value) {
                        if (value != null) {
                          setDialogState(() {
                            currentStatus = value;
                          });
                        }
                      },
                    ),
                  ],
                ),
              ),
              actions: [
                TextButton(
                  onPressed: () => Navigator.pop(context),
                  child: const Text('Batal'),
                ),
                ElevatedButton(
                  onPressed: () async {
                    await widget.onStatusChanged(pesanan['id'].toString(), currentStatus);
                    if (context.mounted) {
                      Navigator.pop(context);
                      ScaffoldMessenger.of(context).showSnackBar(
                        SnackBar(
                          content: Text('Status pesanan ${pesanan['no']} diubah menjadi $currentStatus'),
                          backgroundColor: Colors.green,
                        ),
                      );
                    }
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2563eb),
                    foregroundColor: Colors.white,
                  ),
                  child: const Text('Simpan Perubahan'),
                ),
              ],
            );
          },
        );
      },
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 100,
            child: Text(
              label,
              style: TextStyle(fontWeight: FontWeight.w600, color: Colors.grey[600], fontSize: 12),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: const TextStyle(fontWeight: FontWeight.w500, color: Color(0xFF1e3a8a), fontSize: 12),
            ),
          ),
        ],
      ),
    );
  }
}

// Buat PO - Design Wow
class BuatPoScreenContent extends StatefulWidget {
  final List<Pelanggan> pelangganList;
  final Function(String, String, int, int, String) onSave;

  const BuatPoScreenContent({
    super.key,
    required this.pelangganList,
    required this.onSave,
  });

  @override
  State<BuatPoScreenContent> createState() => _BuatPoScreenContentState();
}

class _BuatPoScreenContentState extends State<BuatPoScreenContent> {
  final _formKey = GlobalKey<FormState>();
  String? selectedPelanggan;
  late TextEditingController deskripsiCtrl;
  late TextEditingController jumlahCtrl;
  late TextEditingController hargaCtrl;
  String? selectedStatus;

  @override
  void initState() {
    super.initState();
    deskripsiCtrl = TextEditingController();
    jumlahCtrl = TextEditingController();
    hargaCtrl = TextEditingController();
  }

  @override
  void dispose() {
    deskripsiCtrl.dispose();
    jumlahCtrl.dispose();
    hargaCtrl.dispose();
    super.dispose();
  }

  void simpanPO() {
    if (_formKey.currentState!.validate()) {
      final int jumlah = int.parse(jumlahCtrl.text);
      final int harga = int.parse(hargaCtrl.text);
      final String totalFormatted = 'Rp ${(jumlah * harga).toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}';

      showDialog(
        context: context,
        builder: (BuildContext context) {
          return AlertDialog(
            title: const Text('✅ Konfirmasi Simpan PO'),
            content: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _buildInfoRow('Pelanggan', selectedPelanggan ?? ''),
                _buildInfoRow('Deskripsi', deskripsiCtrl.text),
                _buildInfoRow('Jumlah', '${jumlahCtrl.text} Pcs'),
                _buildInfoRow('Harga Satuan', 'Rp ${hargaCtrl.text}'),
                _buildInfoRow('Total', totalFormatted),
                _buildInfoRow('Status', selectedStatus ?? ''),
              ],
            ),
            actions: [
              TextButton(
                onPressed: () => Navigator.pop(context),
                child: const Text('Batal'),
              ),
              TextButton(
                onPressed: () {
                  Navigator.pop(context);
                  widget.onSave(
                    selectedPelanggan!,
                    deskripsiCtrl.text,
                    jumlah,
                    harga,
                    selectedStatus ?? 'Pending',
                  );
                  // Clear fields
                  selectedPelanggan = null;
                  deskripsiCtrl.clear();
                  jumlahCtrl.clear();
                  hargaCtrl.clear();
                  selectedStatus = null;
                  setState(() {});
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('✅ PO berhasil dibuat'),
                      backgroundColor: Colors.green,
                    ),
                  );
                },
                child: const Text('Simpan', style: TextStyle(color: Colors.green, fontWeight: FontWeight.bold)),
              ),
            ],
          );
        },
      );
    }
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 90,
            child: Text(label, style: const TextStyle(fontWeight: FontWeight.w600)),
          ),
          Expanded(
            child: Text(value, style: TextStyle(color: Colors.grey[700])),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      color: const Color(0xFFF8FAFC),
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Buat PO Baru ✨',
            style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Color(0xFF1e3a8a)),
          ),
          const SizedBox(height: 4),
          Text(
            'Buat Purchase Order baru untuk pelanggan',
            style: TextStyle(fontSize: 13, color: Colors.grey[600]),
          ),
          const SizedBox(height: 24),
          Expanded(
            child: SingleChildScrollView(
              child: Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(16),
                  color: Colors.white,
                  border: Border.all(color: Colors.grey[200]!, width: 1),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.05),
                      blurRadius: 12,
                      offset: const Offset(0, 4),
                    ),
                  ],
                ),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildSectionTitle('📦 Data Pelanggan'),
                      const SizedBox(height: 16),
                      _buildPelangganDropdown(),
                      const SizedBox(height: 20),
                      _buildSectionTitle('📝 Detail PO'),
                      const SizedBox(height: 16),
                      _buildTextField(
                        controller: deskripsiCtrl,
                        label: 'Deskripsi Produk',
                        icon: Icons.shopping_bag,
                        maxLines: 3,
                        validator: (value) => value?.isEmpty ?? true ? 'Deskripsi tidak boleh kosong' : null,
                      ),
                      const SizedBox(height: 16),
                      _buildTextField(
                        controller: jumlahCtrl,
                        label: 'Jumlah (Pcs)',
                        icon: Icons.numbers,
                        keyboardType: TextInputType.number,
                        validator: (value) {
                          if (value?.isEmpty ?? true) return 'Jumlah tidak boleh kosong';
                          if (int.tryParse(value!) == null) return 'Jumlah harus berupa angka';
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),
                      _buildTextField(
                        controller: hargaCtrl,
                        label: 'Harga Satuan (Rp)',
                        icon: Icons.monetization_on,
                        keyboardType: TextInputType.number,
                        validator: (value) {
                          if (value?.isEmpty ?? true) return 'Harga tidak boleh kosong';
                          if (int.tryParse(value!) == null) return 'Harga harus berupa angka';
                          return null;
                        },
                      ),
                      const SizedBox(height: 16),
                      _buildDropdown(),
                      const SizedBox(height: 24),
                      Row(
                        children: [
                          Expanded(
                            child: ElevatedButton.icon(
                              onPressed: simpanPO,
                              icon: const Icon(Icons.save),
                              label: const Text('Simpan PO'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: const Color(0xFF2563eb),
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(vertical: 14),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(10),
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: OutlinedButton.icon(
                              onPressed: () {
                                selectedPelanggan = null;
                                deskripsiCtrl.clear();
                                jumlahCtrl.clear();
                                hargaCtrl.clear();
                                selectedStatus = null;
                                setState(() {});
                              },
                              icon: const Icon(Icons.refresh),
                              label: const Text('Bersihkan'),
                              style: OutlinedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(vertical: 14),
                                side: const BorderSide(color: Color(0xFF2563eb)),
                                shape: RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(10),
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Text(
      title,
      style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF1e3a8a)),
    );
  }

  Widget _buildPelangganDropdown() {
    return DropdownButtonFormField<String>(
      value: selectedPelanggan,
      hint: const Text('Pilih Pelanggan'),
      decoration: InputDecoration(
        labelText: 'Nama Pelanggan',
        prefixIcon: const Icon(Icons.person, color: Color(0xFF2563eb)),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFF2563eb), width: 2),
        ),
        filled: true,
        fillColor: const Color(0xFFF8FAFC),
      ),
      items: widget.pelangganList
          .map((p) => DropdownMenuItem(
                value: p.nama,
                child: Text(p.nama),
              ))
          .toList(),
      onChanged: (value) {
        setState(() => selectedPelanggan = value);
      },
      validator: (value) => value == null ? 'Pelanggan harus dipilih' : null,
    );
  }

  Widget _buildTextField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    int maxLines = 1,
    TextInputType keyboardType = TextInputType.text,
    required String? Function(String?)? validator,
  }) {
    return TextFormField(
      controller: controller,
      maxLines: maxLines,
      keyboardType: keyboardType,
      validator: validator,
      decoration: InputDecoration(
        labelText: label,
        prefixIcon: Icon(icon, color: const Color(0xFF2563eb)),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFF2563eb), width: 2),
        ),
        filled: true,
        fillColor: const Color(0xFFF8FAFC),
      ),
    );
  }

  Widget _buildDropdown() {
    return DropdownButtonFormField<String>(
      value: selectedStatus,
      hint: const Text('Pilih Status'),
      decoration: InputDecoration(
        labelText: 'Status',
        prefixIcon: const Icon(Icons.list, color: Color(0xFF2563eb)),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFF2563eb), width: 2),
        ),
        filled: true,
        fillColor: const Color(0xFFF8FAFC),
      ),
      items: ['Draft', 'Pending', 'Proses', 'Selesai']
          .map((status) => DropdownMenuItem(
                value: status,
                child: Text(status),
              ))
          .toList(),
      onChanged: (value) {
        setState(() => selectedStatus = value);
      },
      validator: (value) => value == null ? 'Status harus dipilih' : null,
    );
  }
}

// Daftar Pelanggan Content
class DaftarPelangganScreenContent extends StatefulWidget {
  const DaftarPelangganScreenContent({super.key});

  @override
  State<DaftarPelangganScreenContent> createState() => _DaftarPelangganScreenContentState();
}

class _DaftarPelangganScreenContentState extends State<DaftarPelangganScreenContent> {
  @override
  Widget build(BuildContext context) {
    return const SizedBox(); // Unused placeholder wrapper
  }
}

// Arsip PDF - Design Wow
class ArsipPdfScreenContent extends StatefulWidget {
  final List<Map<String, dynamic>> pdfFiles;
  final Future<void> Function(String) onDelete;

  const ArsipPdfScreenContent({
    super.key,
    required this.pdfFiles,
    required this.onDelete,
  });

  @override
  State<ArsipPdfScreenContent> createState() => _ArsipPdfScreenContentState();
}

class _ArsipPdfScreenContentState extends State<ArsipPdfScreenContent> {
  @override
  Widget build(BuildContext context) {
    return Container(
      color: const Color(0xFFF8FAFC),
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Arsip PDF 📑',
            style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold, color: Color(0xFF1e3a8a)),
          ),
          const SizedBox(height: 4),
          Text(
            'Kelola arsip dokumen PDF Anda dengan mudah',
            style: TextStyle(fontSize: 13, color: Colors.grey[600]),
          ),
          const SizedBox(height: 24),
          Expanded(
            child: widget.pdfFiles.isEmpty
                ? Center(
                    child: Text(
                      'Tidak ada arsip PDF',
                      style: TextStyle(color: Colors.grey[600], fontSize: 14),
                    ),
                  )
                : ListView.builder(
                    itemCount: widget.pdfFiles.length,
                    itemBuilder: (context, index) {
                      final file = widget.pdfFiles[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(12),
                          color: Colors.white,
                          border: Border.all(color: Colors.grey[200]!, width: 1),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.05),
                              blurRadius: 8,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: ListTile(
                          contentPadding: const EdgeInsets.all(16),
                          leading: Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.red.withValues(alpha: 0.15),
                              borderRadius: BorderRadius.circular(10),
                            ),
                            child: const Icon(Icons.picture_as_pdf, color: Colors.red, size: 28),
                          ),
                          title: Text(
                            file['nama'],
                            style: const TextStyle(
                              fontWeight: FontWeight.w600,
                              fontSize: 13,
                              color: Color(0xFF1e3a8a),
                            ),
                          ),
                          subtitle: Text(
                            '${file['ukuran']} • ${file['tanggal']}',
                            style: TextStyle(fontSize: 11, color: Colors.grey[500]),
                          ),
                          trailing: PopupMenuButton(
                            itemBuilder: (BuildContext context) => [
                              PopupMenuItem(
                                child: const Row(
                                  children: [
                                    Icon(Icons.download, size: 18, color: Color(0xFF2563eb)),
                                    SizedBox(width: 8),
                                    Text('Download'),
                                  ],
                                ),
                                onTap: () {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    SnackBar(
                                      content: Text('📥 Download ${file['nama']}'),
                                      backgroundColor: Colors.green,
                                    ),
                                  );
                                },
                              ),
                              PopupMenuItem(
                                child: const Row(
                                  children: [
                                    Icon(Icons.share, size: 18, color: Colors.orange),
                                    SizedBox(width: 8),
                                    Text('Bagikan'),
                                  ],
                                ),
                                onTap: () {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    SnackBar(
                                      content: Text('🔗 Bagikan ${file['nama']}'),
                                      backgroundColor: Colors.orange,
                                    ),
                                  );
                                },
                              ),
                              PopupMenuItem(
                                child: const Row(
                                  children: [
                                    Icon(Icons.delete, size: 18, color: Colors.red),
                                    SizedBox(width: 8),
                                    Text('Hapus'),
                                  ],
                                ),
                                onTap: () {
                                  showDialog(
                                    context: context,
                                    builder: (BuildContext context) {
                                      return AlertDialog(
                                        title: const Text('🗑️ Hapus File'),
                                        content: Text('Hapus ${file['nama']}?'),
                                        actions: [
                                          TextButton(
                                            onPressed: () => Navigator.pop(context),
                                            child: const Text('Batal'),
                                          ),
                                          TextButton(
                                            onPressed: () async {
                                              Navigator.pop(context);
                                              await widget.onDelete(file['id'].toString());
                                              if (context.mounted) {
                                                ScaffoldMessenger.of(context).showSnackBar(
                                                  SnackBar(
                                                    content: Text('✅ ${file['nama']} dihapus'),
                                                    backgroundColor: Colors.red,
                                                  ),
                                                );
                                              }
                                            },
                                            child: const Text('Hapus', style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold)),
                                          ),
                                        ],
                                      );
                                    },
                                  );
                                },
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}