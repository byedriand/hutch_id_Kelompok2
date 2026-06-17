import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';
import '../../providers/dashboard_provider.dart';
import '../../providers/produk_provider.dart';
import '../../providers/auth_provider.dart';
import '../../models/produk.dart';
import '../../widgets/custom_widgets.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen>
    with TickerProviderStateMixin {
  late AnimationController _cardAnimationController;

  @override
  void initState() {
    super.initState();

    // Initialize intl for date formatting
    initializeDateFormatting('id_ID', null);

    _cardAnimationController = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );

    Future.microtask(() {
      if (mounted) {
        Provider.of<DashboardProvider>(context, listen: false).fetchDashboard();
        Provider.of<ProdukProvider>(context, listen: false).fetchProduk();
      }
      _cardAnimationController.forward();
    });
  }

  @override
  void dispose() {
    _cardAnimationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final authProvider = Provider.of<AuthProvider>(context);
    final userRole = authProvider.user?.role ?? '';

    if (userRole == 'operator_gudang') {
      return Scaffold(
        body: _buildWarehouseDashboard(context),
      );
    }

    return Scaffold(
      body: Consumer<DashboardProvider>(
        builder: (context, dashboardProvider, _) {
          if (dashboardProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat dashboard...');
          }

          if (dashboardProvider.errorMessage != null) {
            return Center(
              child: EmptyStateWidget(
                message: dashboardProvider.errorMessage!,
                onRetry: () {
                  dashboardProvider.fetchDashboard();
                },
              ),
            );
          }

          final dashboard = dashboardProvider.dashboardData;
          if (dashboard == null) {
            return const EmptyStateWidget(
              message: 'Data dashboard tidak tersedia',
            );
          }

          final currencyFormatter = NumberFormat('#,##0', 'id_ID');

          return RefreshIndicator(
            onRefresh: () {
              _cardAnimationController.reset();
              _cardAnimationController.forward();
              return dashboardProvider.refresh();
            },
            child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              child: Column(
                children: [
                  // Header Section - Enhanced with website design
                  Container(
                    padding: const EdgeInsets.fromLTRB(20, 16, 20, 24),
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors: [
                          const Color(0xFF1e40af),
                          const Color(0xFF2563eb),
                        ],
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: const Color(0xFF1e40af).withValues(alpha: 0.15),
                          blurRadius: 20,
                          offset: const Offset(0, 8),
                        ),
                      ],
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Container(
                              padding: const EdgeInsets.all(10),
                              decoration: BoxDecoration(
                                color: Colors.white.withValues(alpha: 0.25),
                                borderRadius: BorderRadius.circular(14),
                              ),
                              child: const Icon(
                                Icons.dashboard_rounded,
                                color: Colors.white,
                                size: 28,
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  const Text(
                                    'Dashboard Pesanan',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 22,
                                      fontWeight: FontWeight.w900,
                                      letterSpacing: 0.5,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    'Ringkasan aktivitas dan status pesanan dengan insight cepat',
                                    style: TextStyle(
                                      color: Colors.white.withValues(alpha: 0.85),
                                      fontSize: 12,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 20),
                        // Date & Time Display - Enhanced
                        Container(
                          padding: const EdgeInsets.all(14),
                          decoration: BoxDecoration(
                            color: Colors.white.withValues(alpha: 0.12),
                            borderRadius: BorderRadius.circular(14),
                            border: Border.all(
                              color: Colors.white.withValues(alpha: 0.25),
                              width: 1.5,
                            ),
                          ),
                          child: Row(
                            children: [
                              Icon(
                                Icons.access_time_rounded,
                                color: Colors.white.withValues(alpha: 0.95),
                                size: 20,
                              ),
                              const SizedBox(width: 12),
                              Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    _getFormattedDate(),
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.w700,
                                      fontSize: 13,
                                    ),
                                  ),
                                  Text(
                                    _getFormattedTime(),
                                    style: TextStyle(
                                      color: Colors.white.withValues(alpha: 0.85),
                                      fontSize: 11,
                                      fontWeight: FontWeight.w500,
                                    ),
                                  ),
                                ],
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  // Content Section
                  Padding(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      children: [
                        // Stats Grid - Blue & White Theme Only
                        GridView.count(
                          crossAxisCount: 2,
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          mainAxisSpacing: 12,
                          crossAxisSpacing: 12,
                          childAspectRatio: 1,
                          children: [
                            _buildAnimatedStatCard(
                              index: 0,
                              title: 'Total PO Aktif',
                              value: dashboard.totalAktif.toString(),
                              subtitle: 'Bulan ini',
                              icon: Icons.hourglass_top_rounded,
                              gradient: [
                                const Color(0xFF3b82f6),
                                const Color(0xFF1e40af),
                              ],
                              onTap: () => Navigator.pushNamed(context, '/pesanan', arguments: ''),
                            ),
                            _buildAnimatedStatCard(
                              index: 1,
                              title: 'Menunggu Konfirmasi',
                              value: dashboard.totalMenunggu.toString(),
                              subtitle: 'Perlu tindakan',
                              icon: Icons.schedule_rounded,
                              gradient: [
                                const Color(0xFF2563eb),
                                const Color(0xFF1e3a8a),
                              ],
                              onTap: () => Navigator.pushNamed(context, '/pesanan', arguments: 'menunggu_konfirmasi'),
                            ),
                            _buildAnimatedStatCard(
                              index: 2,
                              title: 'Siap Kirim',
                              value: dashboard.totalSiapKirim.toString(),
                              subtitle: 'Ready to go',
                              icon: Icons.local_shipping_rounded,
                              gradient: [
                                const Color(0xFF0284c7),
                                const Color(0xFF0c4a6e),
                              ],
                              onTap: () => Navigator.pushNamed(context, '/pesanan', arguments: 'siap_kirim'),
                            ),
                            _buildAnimatedStatCard(
                              index: 3,
                              title: 'Selesai Bulan Ini',
                              value: dashboard.totalSelesaiBulanIni.toString(),
                              subtitle:
                                  'Rp ${currencyFormatter.format(dashboard.nilaiSelesaiBulanIni)}',
                              icon: Icons.check_circle_rounded,
                              gradient: [
                                const Color(0xFF1e40af),
                                const Color(0xFF0f2942),
                              ],
                              onTap: () => Navigator.pushNamed(context, '/pesanan', arguments: 'selesai'),
                            ),
                          ],
                        ),
                        const SizedBox(height: 24),
                        // Revenue Card - Blue Theme
                        ScaleTransition(
                          scale: Tween<double>(begin: 0.9, end: 1.0).animate(
                            CurvedAnimation(
                              parent: _cardAnimationController,
                              curve: const Interval(0.3, 0.7),
                            ),
                          ),
                          child: Container(
                            padding: const EdgeInsets.all(20),
                            decoration: BoxDecoration(
                              gradient: LinearGradient(
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                                colors: [
                                  const Color(0xFF3b82f6),
                                  const Color(0xFF1e40af),
                                ],
                              ),
                              borderRadius: BorderRadius.circular(18),
                              boxShadow: [
                                BoxShadow(
                                  color: const Color(
                                    0xFF3b82f6,
                                  ).withValues(alpha: 0.3),
                                  blurRadius: 25,
                                  offset: const Offset(0, 12),
                                ),
                              ],
                            ),
                            child: Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(14),
                                  decoration: BoxDecoration(
                                    color: Colors.white.withValues(alpha: 0.25),
                                    borderRadius: BorderRadius.circular(14),
                                  ),
                                  child: const Icon(
                                    Icons.trending_up_rounded,
                                    color: Colors.white,
                                    size: 32,
                                  ),
                                ),
                                const SizedBox(width: 16),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        'Nilai Selesai Bulan Ini',
                                        style: TextStyle(
                                          color: Colors.white.withValues(alpha: 0.95),
                                          fontSize: 12,
                                          fontWeight: FontWeight.w600,
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Text(
                                        'Rp ${currencyFormatter.format(dashboard.nilaiSelesaiBulanIni)}',
                                        style: const TextStyle(
                                          color: Colors.white,
                                          fontSize: 26,
                                          fontWeight: FontWeight.w900,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(height: 24),
                        // Quick Stats Section - Enhanced
                        SlideTransition(
                          position:
                              Tween<Offset>(
                                begin: const Offset(0, 0.3),
                                end: Offset.zero,
                              ).animate(
                                CurvedAnimation(
                                  parent: _cardAnimationController,
                                  curve: const Interval(0.4, 0.8),
                                ),
                              ),
                          child: Container(
                            padding: const EdgeInsets.all(20),
                            decoration: BoxDecoration(
                              color: const Color(0xFFf0f9ff),
                              borderRadius: BorderRadius.circular(18),
                              border: Border.all(
                                color: const Color(0xFF93c5fd),
                                width: 2,
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: const Color(
                                    0xFF3b82f6,
                                  ).withValues(alpha: 0.08),
                                  blurRadius: 15,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Container(
                                      padding: const EdgeInsets.all(10),
                                      decoration: BoxDecoration(
                                        color: const Color(0xFFdbeafe),
                                        borderRadius: BorderRadius.circular(10),
                                      ),
                                      child: const Icon(
                                        Icons.info_rounded,
                                        color: Color(0xFF1e40af),
                                        size: 22,
                                      ),
                                    ),
                                    const SizedBox(width: 12),
                                    const Text(
                                      'Ringkasan Status Pesanan',
                                      style: TextStyle(
                                        fontSize: 16,
                                        fontWeight: FontWeight.w800,
                                        color: Color(0xFF0c2340),
                                        letterSpacing: 0.3,
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 20),
                                _buildStatItem(
                                  icon: Icons.pending_actions_rounded,
                                  label: 'Total Aktif + Menunggu',
                                  value:
                                      '${dashboard.totalAktif + dashboard.totalMenunggu}',
                                  color: const Color(0xFF2563eb),
                                  onTap: () => Navigator.pushNamed(context, '/pesanan', arguments: ''),
                                ),
                                const SizedBox(height: 12),
                                _buildStatItem(
                                  icon: Icons.verified_rounded,
                                  label: 'Siap Dikirim',
                                  value: dashboard.totalSiapKirim.toString(),
                                  color: const Color(0xFF0284c7),
                                  onTap: () => Navigator.pushNamed(context, '/pesanan', arguments: 'siap_kirim'),
                                ),
                                const SizedBox(height: 12),
                                _buildStatItem(
                                  icon: Icons.done_all_rounded,
                                  label: 'Selesai Bulan Ini',
                                  value: dashboard.totalSelesaiBulanIni
                                      .toString(),
                                  color: const Color(0xFF1e40af),
                                  onTap: () => Navigator.pushNamed(context, '/pesanan', arguments: 'selesai'),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildAnimatedStatCard({
    required int index,
    required String title,
    required String value,
    required String subtitle,
    required IconData icon,
    required List<Color> gradient,
    VoidCallback? onTap,
  }) {
    return ScaleTransition(
      scale: Tween<double>(begin: 0.8, end: 1.0).animate(
        CurvedAnimation(
          parent: _cardAnimationController,
          curve: Interval(
            0.1 + (index * 0.1),
            0.4 + (index * 0.1),
            curve: Curves.elasticOut,
          ),
        ),
      ),
      child: GestureDetector(
        onTap: onTap,
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: gradient,
            ),
            borderRadius: BorderRadius.circular(18),
            boxShadow: [
              BoxShadow(
                color: gradient.first.withValues(alpha: 0.35),
                blurRadius: 20,
                offset: const Offset(0, 10),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.3),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: Colors.white, size: 24),
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    value,
                    style: const TextStyle(
                      fontSize: 32,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.w700,
                      color: Colors.white.withValues(alpha: 0.95),
                    ),
                  ),
                  Text(
                    subtitle,
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w500,
                      color: Colors.white.withValues(alpha: 0.75),
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

  Widget _buildStatItem({
    required IconData icon,
    required String label,
    required String value,
    required Color color,
    VoidCallback? onTap,
  }) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.all(12),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: Colors.grey[200]!, width: 1),
        ),
        child: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(8),
              ),
              child: Icon(icon, color: color, size: 20),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                label,
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w600,
                  color: Colors.grey[700],
                ),
              ),
            ),
            Text(
              value,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w800,
                color: color,
              ),
            ),
          ],
        ),
      ),
    );
  }

  String _getFormattedDate() {
    try {
      final now = DateTime.now();
      final formatter = DateFormat('EEEE, d MMMM yyyy', 'id_ID');
      return formatter.format(now);
    } catch (e) {
      // Fallback jika locale belum diinisialisasi
      final now = DateTime.now();
      final months = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
      ];
      final days = [
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu',
      ];
      return '${days[now.weekday % 7]}, ${now.day} ${months[now.month - 1]} ${now.year}';
    }
  }

  String _getFormattedTime() {
    try {
      final now = DateTime.now();
      final formatter = DateFormat('HH:mm:ss', 'id_ID');
      return formatter.format(now);
    } catch (e) {
      // Fallback formatting
      final now = DateTime.now();
      final hour = now.hour.toString().padLeft(2, '0');
      final minute = now.minute.toString().padLeft(2, '0');
      final second = now.second.toString().padLeft(2, '0');
      return '$hour:$minute:$second';
    }
  }

  Widget _buildWarehouseDashboard(BuildContext context) {
    return Consumer<ProdukProvider>(
      builder: (context, produkProvider, _) {
        if (produkProvider.isLoading) {
          return const LoadingWidget(message: 'Memuat data stok...');
        }

        if (produkProvider.errorMessage != null) {
          return Center(
            child: EmptyStateWidget(
              message: produkProvider.errorMessage!,
              onRetry: () {
                produkProvider.fetchProduk();
              },
            ),
          );
        }

        final produkList = produkProvider.produkList;
        
        // Calculate statistics
        int totalStok = 0;
        int produkTerdaftar = produkList.length;
        int stokRendah = 0;
        
        for (var p in produkList) {
          final s = p.stok ?? 0;
          totalStok += s;
          if (s <= 10) {
            stokRendah++;
          }
        }

        return RefreshIndicator(
          onRefresh: () => produkProvider.fetchProduk(),
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header Card containing title and stat cards in a row
                _buildWarehouseHeaderCard(context, totalStok, produkTerdaftar, stokRendah),
                
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Grid of Products
                      _buildWarehouseProductGrid(produkList),
                    ],
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildWarehouseHeaderCard(BuildContext context, int totalStok, int produkTerdaftar, int stokRendah) {
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.all(20),
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Color(0xFF0A192F), // Dark slate
            Color(0xFF1E3A8A), // Dark blue
          ],
        ),
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0A192F).withValues(alpha: 0.2),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.15),
                  shape: BoxShape.circle,
                ),
                child: const Icon(
                  Icons.warehouse_rounded,
                  color: Colors.white,
                  size: 26,
                ),
              ),
              const SizedBox(width: 14),
              const Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'Manajemen Stok Barang',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 22,
                        fontWeight: FontWeight.w900,
                        letterSpacing: 0.3,
                      ),
                    ),
                    SizedBox(height: 4),
                    Text(
                      'Kelola stok produk dan pantau ketersediaan barang dengan mudah',
                      style: TextStyle(
                        color: Color(0xFF94A3B8),
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 28),
          
          // Horizontal Stat Cards Row (scrollable on narrow screens)
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            physics: const BouncingScrollPhysics(),
            child: Row(
              children: [
                _buildWarehouseStatCard(
                  title: 'TOTAL STOK',
                  value: totalStok.toString(),
                  subtitle: 'Unit tersedia di gudang',
                  icon: Icons.inventory_2_rounded,
                  iconColor: const Color(0xFF2563EB),
                  iconBgColor: const Color(0xFFEFF6FF),
                ),
                const SizedBox(width: 16),
                _buildWarehouseStatCard(
                  title: 'PRODUK TERDAFTAR',
                  value: produkTerdaftar.toString(),
                  subtitle: 'Jenis produk aktif',
                  icon: Icons.grid_view_rounded,
                  iconColor: const Color(0xFF16A34A),
                  iconBgColor: const Color(0xFFF0FDF4),
                ),
                const SizedBox(width: 16),
                _buildWarehouseStatCard(
                  title: 'STOK RENDAH',
                  value: stokRendah.toString(),
                  subtitle: 'Produk memerlukan pengisian',
                  icon: Icons.warning_amber_rounded,
                  iconColor: const Color(0xFFDC2626),
                  iconBgColor: const Color(0xFFFEF2F2),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildWarehouseStatCard({
    required String title,
    required String value,
    required String subtitle,
    required IconData icon,
    required Color iconColor,
    required Color iconBgColor,
  }) {
    return Container(
      width: 240,
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, 5),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: iconBgColor,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Icon(icon, color: iconColor, size: 22),
              ),
              Text(
                title,
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w900,
                  color: Colors.grey[500],
                  letterSpacing: 0.5,
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Text(
            value,
            style: const TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.w900,
              color: Color(0xFF0F172A),
            ),
          ),
          const SizedBox(height: 6),
          Text(
            subtitle,
            style: TextStyle(
              fontSize: 11,
              color: Colors.grey[500],
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildWarehouseProductGrid(List<Produk> produkList) {
    if (produkList.isEmpty) {
      return Container(
        width: double.infinity,
        padding: const EdgeInsets.all(32),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: Colors.grey[200]!),
        ),
        child: const Center(
          child: Column(
            children: [
              Icon(Icons.inventory_2_outlined, size: 48, color: Colors.grey),
              SizedBox(height: 12),
              Text(
                'Tidak ada produk terdaftar',
                style: TextStyle(color: Colors.grey, fontWeight: FontWeight.bold),
              ),
            ],
          ),
        ),
      );
    }

    final formatter = NumberFormat('#,##0', 'id_ID');

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: 2,
        mainAxisSpacing: 16,
        crossAxisSpacing: 16,
        childAspectRatio: 0.8,
      ),
      itemCount: produkList.length,
      itemBuilder: (context, index) {
        final produk = produkList[index];
        final stock = produk.stok ?? 0;
        final hasStock = stock > 0;
        
        return Container(
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.04),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
            border: Border.all(
              color: Colors.grey[200]!,
              width: 1,
            ),
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Product Image container
              Expanded(
                child: Container(
                  width: double.infinity,
                  decoration: BoxDecoration(
                    color: const Color(0xFFF8FAFC),
                    borderRadius: const BorderRadius.only(
                      topLeft: Radius.circular(16),
                      topRight: Radius.circular(16),
                    ),
                  ),
                  child: ClipRRect(
                    borderRadius: const BorderRadius.only(
                      topLeft: Radius.circular(16),
                      topRight: Radius.circular(16),
                    ),
                    child: produk.foto != null
                        ? Image.network(
                            produk.foto!,
                            fit: BoxFit.cover,
                            errorBuilder: (context, error, stackTrace) {
                              return Icon(
                                Icons.image_not_supported_rounded,
                                color: Colors.grey[300],
                                size: 36,
                              );
                            },
                          )
                        : Icon(
                            Icons.shopping_bag_rounded,
                            color: Colors.blue[300],
                            size: 40,
                          ),
                  ),
                ),
              ),
              
              // Product details below image
              Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Badge circle ID and Status chip row
                    Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        // Circle Badge ID
                        Container(
                          width: 22,
                          height: 22,
                          decoration: const BoxDecoration(
                            color: Color(0xFF2563EB),
                            shape: BoxShape.circle,
                          ),
                          child: Center(
                            child: Text(
                              '${produk.id ?? (index + 1)}',
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 10,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ),
                        // Status chip
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: hasStock ? const Color(0xFFDCFCE7) : const Color(0xFFFEF2F2),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Container(
                                width: 5,
                                height: 5,
                                decoration: BoxDecoration(
                                  color: hasStock ? const Color(0xFF16A34A) : const Color(0xFFDC2626),
                                  shape: BoxShape.circle,
                                ),
                              ),
                              const SizedBox(width: 4),
                              Text(
                                hasStock ? 'TERSEDIA' : 'KOSONG',
                                style: TextStyle(
                                  color: hasStock ? const Color(0xFF15803D) : const Color(0xFFB91C1C),
                                  fontSize: 8,
                                  fontWeight: FontWeight.w900,
                                  letterSpacing: 0.3,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 10),
                    // Product Name
                    Text(
                      produk.nama,
                      style: const TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 13,
                        color: Color(0xFF0F172A),
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 8),
                    // Price Row "HARGA JUAL Rp X.XXX.XXX"
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.baseline,
                      textBaseline: TextBaseline.alphabetic,
                      children: [
                        Text(
                          'HARGA JUAL  ',
                          style: TextStyle(
                            fontSize: 8,
                            fontWeight: FontWeight.bold,
                            color: Colors.grey[500],
                          ),
                        ),
                        Expanded(
                          child: Text(
                            'Rp ${formatter.format(produk.hargaJual ?? 0)}',
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF2563EB),
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}
