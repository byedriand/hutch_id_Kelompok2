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
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

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
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final userRole = Provider.of<AuthProvider>(context).user?.role ?? '';
    final canManage = userRole == 'staf_penjualan';

    return Scaffold(
      backgroundColor: const Color(0xFFf8fafc),
      body: Column(
        children: [
          // Header gradient + search bar kaca (glass) mengambang di atasnya,
          // menyamakan estetika "glass" seperti pada website.
          Stack(
            clipBehavior: Clip.none,
            children: [
              Container(
                padding: const EdgeInsets.fromLTRB(20, 16, 20, 38),
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [Color(0xFF1e40af), Color(0xFF2563eb)],
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF1e40af).withValues(alpha: 0.15),
                      blurRadius: 20,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        color: Colors.white.withValues(alpha: 0.25),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: const Icon(
                        Icons.groups_rounded,
                        color: Colors.white,
                        size: 26,
                      ),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Pelanggan',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 21,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 0.3,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            'Kelola data pelanggan untuk pembuatan PO',
                            style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.85),
                              fontSize: 11.5,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Consumer<PelangganProvider>(
                      builder: (context, provider, _) => GlassContainer(
                        borderRadius: 14,
                        blurSigma: 10,
                        tintOpacity: 0.16,
                        borderOpacity: 0.3,
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 8,
                        ),
                        child: Text(
                          '${provider.pelangganList.length}',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 16,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              // Search bar kaca, mengambang menutupi tepi bawah header gradient
              Positioned(
                left: 16,
                right: 16,
                bottom: -22,
                child: GlassContainer(
                  borderRadius: 16,
                  blurSigma: 12,
                  tintColor: Colors.white,
                  tintOpacity: 0.75,
                  borderColor: Colors.white,
                  borderOpacity: 0.6,
                  padding: const EdgeInsets.symmetric(horizontal: 6),
                  child: TextField(key: const Key('search_pelanggan'), controller: _searchController,
                    decoration: InputDecoration(
                      hintText: 'Cari nama, email, atau telepon...',
                      hintStyle: TextStyle(
                        color: Colors.grey[600],
                        fontSize: 13.5,
                      ),
                      prefixIcon: Icon(
                        Icons.search_rounded,
                        color: const Color(0xFF1e40af).withValues(alpha: 0.8),
                      ),
                      suffixIcon: _searchQuery.isNotEmpty
                          ? IconButton(
                              icon: const Icon(Icons.clear_rounded),
                              onPressed: () {
                                _searchController.clear();
                                setState(() => _searchQuery = '');
                              },
                            )
                          : null,
                      filled: false,
                      border: InputBorder.none,
                      contentPadding: const EdgeInsets.symmetric(vertical: 14),
                    ),
                    onChanged: (value) {
                      setState(() => _searchQuery = value.trim().toLowerCase());
                    },
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 36),
          Expanded(
            child: Consumer<PelangganProvider>(
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

                final filteredList = _searchQuery.isEmpty
                    ? pelangganProvider.pelangganList
                    : pelangganProvider.pelangganList.where((p) {
                        final q = _searchQuery;
                        return p.nama.toLowerCase().contains(q) ||
                            (p.email?.toLowerCase().contains(q) ?? false) ||
                            (p.telepon?.toLowerCase().contains(q) ?? false) ||
                            (p.nomorWhatsapp?.toLowerCase().contains(q) ??
                                false);
                      }).toList();

                if (pelangganProvider.pelangganList.isEmpty) {
                  return const Center(
                    child: EmptyStateWidget(
                      message: 'Belum ada pelanggan',
                      icon: Icons.person_outline,
                    ),
                  );
                }

                if (filteredList.isEmpty) {
                  return const Center(
                    child: EmptyStateWidget(
                      message: 'Pelanggan tidak ditemukan',
                      icon: Icons.search_off,
                    ),
                  );
                }

                return RefreshIndicator(
                  onRefresh: () => pelangganProvider.fetchPelanggan(),
                  child: ListView.builder(
                    padding: const EdgeInsets.fromLTRB(16, 4, 16, 16),
                    itemCount: filteredList.length,
                    itemBuilder: (context, index) {
                      final pelanggan = filteredList[index];
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
          ),
        ],
      ),
      floatingActionButton: canManage
          ? FloatingActionButton(key: const Key('FloatingActionButton'),
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
    final hasAlamat =
        pelanggan.alamat != null && pelanggan.alamat.toString().trim().isNotEmpty;
    final hasEmail =
        pelanggan.email != null && pelanggan.email.toString().trim().isNotEmpty;
    final int poCount = pelanggan.pesananCount ?? 0;

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      clipBehavior: Clip.antiAlias,
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFF1e40af).withValues(alpha: 0.08), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF1e40af).withValues(alpha: 0.08),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Aksen gradient tipis di puncak kartu, menyamakan dengan
          // garis aksen pada kartu pelanggan di website.
          Container(
            height: 4,
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                colors: [Color(0xFF1e40af), Color(0xFF2563eb)],
              ),
            ),
          ),
          // Header dengan tint gradient lembut: nama, badge PO, telepon
          Container(
            width: double.infinity,
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 14),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  const Color(0xFF1e40af).withValues(alpha: 0.05),
                  const Color(0xFF2563eb).withValues(alpha: 0.02),
                ],
              ),
              border: Border(
                bottom: BorderSide(
                  color: const Color(0xFF2563eb).withValues(alpha: 0.10),
                  width: 1.5,
                ),
              ),
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      child: Text(
                        pelanggan.nama,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF0c2340),
                        ),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    const SizedBox(width: 8),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          colors: [Color(0xFF2563eb), Color(0xFF1e40af)],
                        ),
                        borderRadius: BorderRadius.circular(10),
                        boxShadow: [
                          BoxShadow(
                            color: const Color(0xFF2563eb).withValues(alpha: 0.3),
                            blurRadius: 8,
                            offset: const Offset(0, 3),
                          ),
                        ],
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Container(
                            width: 6,
                            height: 6,
                            decoration: const BoxDecoration(
                              color: Colors.white,
                              shape: BoxShape.circle,
                            ),
                          ),
                          const SizedBox(width: 5),
                          Text(
                            '$poCount PO',
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 11,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                if (pelanggan.telepon != null) ...[
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Icon(
                        Icons.phone_rounded,
                        size: 13,
                        color: const Color(0xFF2563eb).withValues(alpha: 0.8),
                      ),
                      const SizedBox(width: 6),
                      Text(
                        pelanggan.telepon!,
                        style: TextStyle(
                          fontSize: 12.5,
                          fontWeight: FontWeight.w600,
                          color: Colors.grey[700],
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
          // Body: alamat & email
          if (hasAlamat || hasEmail)
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 14, 16, 14),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  if (hasAlamat)
                    _buildInfoItem(
                      icon: Icons.location_on_rounded,
                      label: 'ALAMAT',
                      value: pelanggan.alamat!,
                    ),
                  if (hasAlamat && hasEmail) const SizedBox(height: 12),
                  if (hasEmail)
                    _buildInfoItem(
                      icon: Icons.email_rounded,
                      label: 'EMAIL',
                      value: pelanggan.email!,
                    ),
                ],
              ),
            ),
          // Footer: tombol Edit & Hapus, gaya tinted seperti pada website
          if (canManage)
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFf8fafc),
                border: Border(
                  top: BorderSide(
                    color: const Color(0xFF2563eb).withValues(alpha: 0.08),
                    width: 1.5,
                  ),
                ),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () {
                        Navigator.pushNamed(
                          context,
                          '/pelanggan-form',
                          arguments: pelanggan.id,
                        );
                      },
                      icon: const Icon(Icons.edit_rounded, size: 15),
                      label: const Text('Edit'),
                      style: OutlinedButton.styleFrom(
                        backgroundColor: const Color(0xFF2563eb).withValues(alpha: 0.10),
                        side: BorderSide(
                          color: const Color(0xFF2563eb).withValues(alpha: 0.35),
                          width: 1.5,
                        ),
                        foregroundColor: const Color(0xFF1e40af),
                        padding: const EdgeInsets.symmetric(vertical: 9),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () => _showDeleteDialog(context, pelanggan),
                      icon: const Icon(Icons.delete_rounded, size: 15),
                      label: const Text('Hapus'),
                      style: OutlinedButton.styleFrom(
                        backgroundColor: const Color(0xFFef4444).withValues(alpha: 0.08),
                        side: BorderSide(
                          color: const Color(0xFFef4444).withValues(alpha: 0.35),
                          width: 1.5,
                        ),
                        foregroundColor: const Color(0xFFdc2626),
                        padding: const EdgeInsets.symmetric(vertical: 9),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }

  Widget _buildInfoItem({
    required IconData icon,
    required String label,
    required String value,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Icon(icon, size: 13, color: const Color(0xFF2563eb)),
            const SizedBox(width: 6),
            Text(
              label,
              style: const TextStyle(
                fontSize: 10.5,
                fontWeight: FontWeight.w800,
                color: Color(0xFF2563eb),
                letterSpacing: 0.6,
              ),
            ),
          ],
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.w600,
            color: Color(0xFF1e293b),
          ),
        ),
      ],
    );
  }

  void _showDeleteDialog(BuildContext context, dynamic pelanggan) {
    showDialog(
      context: context,
      builder: (dialogContext) {
        return AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Text('Hapus Pelanggan?'),
          content: Text(
            'Anda yakin ingin menghapus pelanggan ${pelanggan.nama}? Tindakan ini tidak dapat dibatalkan.',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(dialogContext),
              child: const Text('Batal'),
            ),
            TextButton(
              onPressed: () async {
                Navigator.pop(dialogContext);
                final provider = Provider.of<PelangganProvider>(
                  context,
                  listen: false,
                );
                final success = await provider.deletePelanggan(pelanggan.id);
                if (!context.mounted) return;
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text(
                      success
                          ? 'Pelanggan berhasil dihapus'
                          : 'Gagal menghapus pelanggan',
                    ),
                    backgroundColor: success ? Colors.green[600] : Colors.red[600],
                  ),
                );
              },
              child: const Text('Hapus', style: TextStyle(color: Colors.red)),
            ),
          ],
        );
      },
    );
  }
}