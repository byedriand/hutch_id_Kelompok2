import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/notifikasi_provider.dart';
import '../../widgets/custom_widgets.dart';

class NotifikasiScreen extends StatefulWidget {
  const NotifikasiScreen({super.key});

  @override
  State<NotifikasiScreen> createState() => _NotifikasiScreenState();
}

class _NotifikasiScreenState extends State<NotifikasiScreen>
    with TickerProviderStateMixin {
  late AnimationController _fadeAnimationController;
  String _selectedFilter = 'semua'; // semua, belum_dibaca, sudah_dibaca

  @override
  void initState() {
    super.initState();
    _fadeAnimationController = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    );
    Future.microtask(() {
      if (mounted) {
        Provider.of<NotifikasiProvider>(context, listen: false).fetchNotifikasi();
      }
      _fadeAnimationController.forward();
    });
  }

  @override
  void dispose() {
    _fadeAnimationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Notifikasi'),
        elevation: 0,
        backgroundColor: const Color(0xFF1e40af),
        foregroundColor: Colors.white,
        actions: [
          Padding(
            padding: const EdgeInsets.all(8),
            child: Center(
              child: ElevatedButton.icon(
                onPressed: () {
                  // Mark all as read functionality
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Semua notifikasi ditandai sudah dibaca'),
                    ),
                  );
                },
                icon: const Icon(Icons.done_all_rounded, size: 18),
                label: const Text(
                  'Tandai Semua',
                  style: TextStyle(fontSize: 12, fontWeight: FontWeight.w600),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.white,
                  foregroundColor: const Color(0xFF1e40af),
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 8,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
      body: Consumer<NotifikasiProvider>(
        builder: (context, notifikasiProvider, _) {
          if (notifikasiProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat notifikasi...');
          }

          if (notifikasiProvider.errorMessage != null) {
            return Center(
              child: EmptyStateWidget(
                message: notifikasiProvider.errorMessage!,
                onRetry: () {
                  notifikasiProvider.fetchNotifikasi();
                },
              ),
            );
          }

          // Filter notifications based on selected tab
          final filteredList = _getFilteredNotifications(
            notifikasiProvider.notifikasiList,
          );

          if (filteredList.isEmpty) {
            return Center(
              child: EmptyStateWidget(
                message: _selectedFilter == 'belum_dibaca'
                    ? 'Tidak ada notifikasi yang belum dibaca'
                    : _selectedFilter == 'sudah_dibaca'
                    ? 'Tidak ada notifikasi yang sudah dibaca'
                    : 'Tidak ada notifikasi',
                icon: Icons.notifications_none,
              ),
            );
          }

          return Column(
            children: [
              // Filter Tabs
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 12,
                ),
                decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border(
                    bottom: BorderSide(
                      color: const Color(0xFFe5e7eb),
                      width: 1,
                    ),
                  ),
                ),
                child: SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      _buildFilterTab(
                        label: 'Semua',
                        value: 'semua',
                        count: notifikasiProvider.notifikasiList.length,
                      ),
                      const SizedBox(width: 10),
                      _buildFilterTab(
                        label: 'Belum Dibaca',
                        value: 'belum_dibaca',
                        count: notifikasiProvider.notifikasiList
                            .where(
                              (n) =>
                                  n.createdAt != null &&
                                  DateTime.now()
                                          .difference(n.createdAt!)
                                          .inDays <
                                      1,
                            )
                            .length,
                      ),
                      const SizedBox(width: 10),
                      _buildFilterTab(
                        label: 'Sudah Dibaca',
                        value: 'sudah_dibaca',
                        count: notifikasiProvider.notifikasiList
                            .where(
                              (n) =>
                                  n.createdAt == null ||
                                  DateTime.now()
                                          .difference(n.createdAt!)
                                          .inDays >=
                                      1,
                            )
                            .length,
                      ),
                    ],
                  ),
                ),
              ),
              // Notifications List
              Expanded(
                child: RefreshIndicator(
                  onRefresh: () => notifikasiProvider.fetchNotifikasi(),
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: filteredList.length,
                    itemBuilder: (context, index) {
                      final notifikasi = filteredList[index];
                      return SlideTransition(
                        position:
                            Tween<Offset>(
                              begin: const Offset(1.0, 0),
                              end: Offset.zero,
                            ).animate(
                              CurvedAnimation(
                                parent: _fadeAnimationController,
                                curve: Interval(
                                  index * 0.1,
                                  0.5 + (index * 0.1),
                                  curve: Curves.easeOut,
                                ),
                              ),
                            ),
                        child: _buildNotifikasiCard(context, notifikasi),
                      );
                    },
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildFilterTab({
    required String label,
    required String value,
    required int count,
  }) {
    final isSelected = _selectedFilter == value;
    return FilterChip(
      label: Text('$label $count'),
      selected: isSelected,
      onSelected: (bool selected) {
        setState(() {
          _selectedFilter = value;
        });
      },
      backgroundColor: Colors.white,
      selectedColor: const Color(0xFF1e40af),
      labelStyle: TextStyle(
        color: isSelected ? Colors.white : Colors.grey[700],
        fontWeight: FontWeight.w600,
        fontSize: 12,
      ),
      side: BorderSide(
        color: isSelected ? const Color(0xFF1e40af) : Colors.grey[300]!,
        width: 1.5,
      ),
    );
  }

  List<dynamic> _getFilteredNotifications(List<dynamic> notifikasiList) {
    if (_selectedFilter == 'belum_dibaca') {
      return notifikasiList
          .where(
            (n) =>
                n.createdAt != null &&
                DateTime.now().difference(n.createdAt!).inDays < 1,
          )
          .toList();
    } else if (_selectedFilter == 'sudah_dibaca') {
      return notifikasiList
          .where(
            (n) =>
                n.createdAt == null ||
                DateTime.now().difference(n.createdAt!).inDays >= 1,
          )
          .toList();
    }
    return notifikasiList;
  }

  Widget _buildNotifikasiCard(BuildContext context, dynamic notifikasi) {
    final dateFormat = DateFormat('dd MMM yyyy HH:mm', 'id_ID');
    final isNew =
        notifikasi.createdAt != null &&
        DateTime.now().difference(notifikasi.createdAt!).inDays < 1;

    // Calculate time difference for display
    String getTimeAgo(DateTime? dateTime) {
      if (dateTime == null) return '-';
      final now = DateTime.now();
      final difference = now.difference(dateTime);

      if (difference.inMinutes < 60) {
        return '${difference.inMinutes} menit yang lalu';
      } else if (difference.inHours < 24) {
        return '${difference.inHours} jam yang lalu';
      } else if (difference.inDays < 7) {
        return '${difference.inDays} hari yang lalu';
      } else {
        return dateFormat.format(dateTime);
      }
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(
          color: isNew ? const Color(0xFF93c5fd) : Colors.grey[200]!,
          width: isNew ? 2 : 1,
        ),
        boxShadow: [
          BoxShadow(
            color: isNew
                ? const Color(0xFF3b82f6).withValues(alpha: 0.1)
                : Colors.black.withValues(alpha: 0.04),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header with icon and title
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: _getColorByType(notifikasi.tipe).withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    _getIconByType(notifikasi.tipe),
                    color: _getColorByType(notifikasi.tipe),
                    size: 24,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Expanded(
                            child: Text(
                              notifikasi.judul ?? 'Notifikasi',
                              style: const TextStyle(
                                fontSize: 15,
                                fontWeight: FontWeight.w800,
                                color: Color(0xFF0c2340),
                              ),
                            ),
                          ),
                          if (isNew)
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 10,
                                vertical: 4,
                              ),
                              decoration: BoxDecoration(
                                color: const Color(0xFF2563eb),
                                borderRadius: BorderRadius.circular(6),
                              ),
                              child: const Text(
                                'BARU',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontSize: 10,
                                  fontWeight: FontWeight.w700,
                                ),
                              ),
                            ),
                        ],
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            // Full description
            Text(
              notifikasi.isi ?? '',
              style: TextStyle(
                fontSize: 13,
                color: Colors.grey[700],
                fontWeight: FontWeight.w500,
                height: 1.5,
              ),
            ),
            const SizedBox(height: 12),
            Container(height: 1, color: Colors.grey[200]),
            const SizedBox(height: 12),
            // Time info
            Row(
              children: [
                Icon(Icons.schedule_rounded, size: 14, color: Colors.grey[600]),
                const SizedBox(width: 8),
                Text(
                  getTimeAgo(notifikasi.createdAt),
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey[600],
                    fontWeight: FontWeight.w500,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 14),
            // Action buttons
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Notifikasi ditandai sudah dibaca'),
                        ),
                      );
                    },
                    icon: const Icon(Icons.done_rounded, size: 16),
                    label: const Text('Tandai Sudah Dibaca'),
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(
                        color: Color(0xFF3b82f6),
                        width: 1.5,
                      ),
                      foregroundColor: const Color(0xFF3b82f6),
                      padding: const EdgeInsets.symmetric(vertical: 8),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(content: Text('Notifikasi dihapus')),
                      );
                    },
                    icon: const Icon(Icons.delete_rounded, size: 16),
                    label: const Text('Hapus'),
                    style: OutlinedButton.styleFrom(
                      side: const BorderSide(
                        color: Color(0xFFef4444),
                        width: 1.5,
                      ),
                      foregroundColor: const Color(0xFFef4444),
                      padding: const EdgeInsets.symmetric(vertical: 8),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  IconData _getIconByType(String? type) {
    switch (type) {
      case 'pesanan':
        return Icons.shopping_bag_rounded;
      case 'pengiriman':
        return Icons.local_shipping_rounded;
      case 'pembayaran':
        return Icons.payment_rounded;
      default:
        return Icons.notifications_rounded;
    }
  }

  Color _getColorByType(String? type) {
    switch (type) {
      case 'pesanan':
        return const Color(0xFF2563eb);
      case 'pengiriman':
        return const Color(0xFF22c55e);
      case 'pembayaran':
        return const Color(0xFFf97316);
      default:
        return const Color(0xFF3b82f6);
    }
  }
}
