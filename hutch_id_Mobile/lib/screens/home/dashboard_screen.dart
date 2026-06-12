import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:intl/date_symbol_data_local.dart';
import '../../providers/dashboard_provider.dart';
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
                                ),
                                const SizedBox(height: 12),
                                _buildStatItem(
                                  icon: Icons.verified_rounded,
                                  label: 'Siap Dikirim',
                                  value: dashboard.totalSiapKirim.toString(),
                                  color: const Color(0xFF0284c7),
                                ),
                                const SizedBox(height: 12),
                                _buildStatItem(
                                  icon: Icons.done_all_rounded,
                                  label: 'Selesai Bulan Ini',
                                  value: dashboard.totalSelesaiBulanIni
                                      .toString(),
                                  color: const Color(0xFF1e40af),
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
    );
  }

  Widget _buildStatItem({
    required IconData icon,
    required String label,
    required String value,
    required Color color,
  }) {
    return Container(
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
}
