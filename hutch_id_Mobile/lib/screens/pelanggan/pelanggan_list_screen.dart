import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/pelanggan_provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/custom_widgets.dart';

class PelangganListScreen extends StatefulWidget {
  const PelangganListScreen({super.key});

  @override
  State<PelangganListScreen> createState() => _PelangganListScreenState();
}

class _PelangganListScreenState extends State<PelangganListScreen>
    with TickerProviderStateMixin {
  late AnimationController _animationController;

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    );
    Future.microtask(() {
      if (mounted) {
        Provider.of<PelangganProvider>(context, listen: false).fetchPelanggan();
      }
      _animationController.forward();
    });
  }

  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final userRole = Provider.of<AuthProvider>(context).user?.role ?? '';
    final canManage = userRole != 'operator_gudang';

    return Scaffold(
      appBar: AppBar(
        title: const Text('Pelanggan'),
        elevation: 0,
        backgroundColor: const Color(0xFF1e40af),
        foregroundColor: Colors.white,
      ),
      body: Consumer<PelangganProvider>(
        builder: (context, pelangganProvider, _) {
          if (pelangganProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat pelanggan...');
          }

          if (pelangganProvider.errorMessage != null) {
            return Center(
              child: EmptyStateWidget(
                message: pelangganProvider.errorMessage!,
                onRetry: () {
                  pelangganProvider.fetchPelanggan();
                },
              ),
            );
          }

          if (pelangganProvider.pelangganList.isEmpty) {
            return const Center(
              child: EmptyStateWidget(
                message: 'Belum ada pelanggan',
                icon: Icons.person_outline,
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () => pelangganProvider.fetchPelanggan(),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: pelangganProvider.pelangganList.length,
              itemBuilder: (context, index) {
                final pelanggan = pelangganProvider.pelangganList[index];
                return SlideTransition(
                  position:
                      Tween<Offset>(
                        begin: const Offset(1.0, 0),
                        end: Offset.zero,
                      ).animate(
                        CurvedAnimation(
                          parent: _animationController,
                          curve: Interval(
                            index * 0.08,
                            0.5 + (index * 0.08),
                            curve: Curves.easeOut,
                          ),
                        ),
                      ),
                  child: _buildPelangganCard(context, pelanggan, canManage),
                );
              },
            ),
          );
        },
      ),
      floatingActionButton: canManage
          ? FloatingActionButton(
              onPressed: () {
                Navigator.pushNamed(context, '/pelanggan-form');
              },
              backgroundColor: const Color(0xFF1e40af),
              child: const Icon(Icons.add, color: Colors.white),
            )
          : null,
    );
  }

  Widget _buildPelangganCard(BuildContext context, dynamic pelanggan, bool canManage) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFe0e7ff), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF3b82f6).withValues(alpha: 0.08),
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
            // Header row: Avatar dan nama
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFdbeafe), Color(0xFFbfdbfe)],
                    ),
                    borderRadius: BorderRadius.circular(14),
                  ),
                  child: const Icon(
                    Icons.person_rounded,
                    color: Color(0xFF1e40af),
                    size: 28,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        pelanggan.nama,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF0c2340),
                        ),
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 4),
                      if (pelanggan.email != null)
                        Text(
                          pelanggan.email!,
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                            color: Colors.grey[600],
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 14),
            // Divider
            Container(height: 1, color: const Color(0xFFe5e7eb)),
            const SizedBox(height: 14),
            // Contact info dengan icons
            Wrap(
              spacing: 10,
              runSpacing: 10,
              children: [
                if (pelanggan.telepon != null)
                  _buildContactInfo(
                    icon: Icons.phone_rounded,
                    label: pelanggan.telepon!,
                    color: const Color(0xFF22c55e),
                  ),
                if (pelanggan.email != null)
                  _buildContactInfo(
                    icon: Icons.email_rounded,
                    label: pelanggan.email!,
                    color: const Color(0xFF2563eb),
                  ),
              ],
            ),
            const SizedBox(height: 14),
            Container(height: 1, color: const Color(0xFFe5e7eb)),
            const SizedBox(height: 14),
            // Action buttons: Edit & Delete
            if (canManage)
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () {
                        // Navigate to edit pelanggan form
                        Navigator.pushNamed(
                          context,
                          '/pelanggan-form',
                          arguments: pelanggan.id,
                        );
                      },
                      icon: const Icon(Icons.edit_rounded, size: 16),
                      label: const Text('Edit'),
                      style: OutlinedButton.styleFrom(
                        side: const BorderSide(
                          color: Color(0xFF2563eb),
                          width: 1.5,
                        ),
                        foregroundColor: const Color(0xFF2563eb),
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
                        // Show delete confirmation dialog
                        showDialog(
                          context: context,
                          builder: (BuildContext context) {
                            return AlertDialog(
                              title: const Text('Hapus Pelanggan?'),
                              content: Text(
                                'Anda yakin ingin menghapus pelanggan ${pelanggan.nama}? Tindakan ini tidak dapat dibatalkan.',
                              ),
                              actions: [
                                TextButton(
                                  onPressed: () => Navigator.pop(context),
                                  child: const Text('Batal'),
                                ),
                                TextButton(
                                  onPressed: () {
                                    Navigator.pop(context);
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(
                                        content: Text(
                                          'Pelanggan berhasil dihapus',
                                        ),
                                      ),
                                    );
                                  },
                                  child: const Text(
                                    'Hapus',
                                    style: TextStyle(color: Colors.red),
                                  ),
                                ),
                              ],
                            );
                          },
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

  Widget _buildContactInfo({
    required IconData icon,
    required String label,
    required Color color,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: color.withValues(alpha: 0.25), width: 1),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 16, color: color),
          const SizedBox(width: 6),
          Text(
            label,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w700,
              color: color,
            ),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          ),
        ],
      ),
    );
  }
}
