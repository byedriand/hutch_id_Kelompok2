import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/produk_provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/custom_widgets.dart';

// Palet warna disamakan dengan tema biru website (lihat staff-edit.blade.php
// & home_screen.dart): #1e40af (biru tua) -> #3b82f6 (biru terang).
const _kPrimaryDark = Color(0xFF1e40af);
const _kPrimary = Color(0xFF3b82f6);
const _kBgSoft = Color(0xFFf0f7ff);

class ProdukDetailScreen extends StatefulWidget {
  final int produkId;

  const ProdukDetailScreen({super.key, required this.produkId});

  @override
  State<ProdukDetailScreen> createState() => _ProdukDetailScreenState();
}

class _ProdukDetailScreenState extends State<ProdukDetailScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (mounted) {
        Provider.of<ProdukProvider>(
          context,
          listen: false,
        ).getProdukDetail(widget.produkId);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final formatter = NumberFormat('#,##0', 'id_ID');
    final dateFormat = DateFormat('dd MMMM yyyy, HH:mm', 'id_ID');
    final userRole = Provider.of<AuthProvider>(context).user?.role ?? '';
    // Sesuai web ProdukController: edit/hapus info produk untuk
    // staf_penjualan, administrator, pemilik_umkm (BUKAN operator_gudang).
    final canEditDelete = userRole == 'staf_penjualan' ||
        userRole == 'administrator' ||
        userRole == 'pemilik_umkm';

    return Scaffold(
      backgroundColor: const Color(0xFFf8fafc),
      body: Consumer<ProdukProvider>(
        builder: (context, produkProvider, _) {
          if (produkProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat detail produk...');
          }

          final produk = produkProvider.selectedProduk;
          if (produk == null) {
            return const Center(
              child: EmptyStateWidget(message: 'Produk tidak ditemukan'),
            );
          }

          return CustomScrollView(
            slivers: [
              // ---------- Header gradient + foto, ala header biru website ----------
              SliverAppBar(
                pinned: true,
                expandedHeight: 280,
                backgroundColor: _kPrimaryDark,
                iconTheme: const IconThemeData(color: Colors.white),
                title: const Text(
                  'Detail Produk',
                  style: TextStyle(color: Colors.white),
                ),
                actions: [
                  if (canEditDelete) ...[
                    IconButton(
                      icon: const Icon(Icons.edit_outlined, color: Colors.white),
                      tooltip: 'Edit Produk',
                      onPressed: () async {
                        await Navigator.pushNamed(
                          context,
                          '/produk-staf-tambah',
                          arguments: produk,
                        );
                        if (context.mounted) {
                          produkProvider.getProdukDetail(widget.produkId);
                        }
                      },
                    ),
                    IconButton(
                      icon: const Icon(Icons.delete_outline, color: Colors.white),
                      tooltip: 'Hapus Produk',
                      onPressed: () => _confirmDelete(context, produkProvider, produk),
                    ),
                  ],
                ],
                flexibleSpace: FlexibleSpaceBar(
                  background: Stack(
                    fit: StackFit.expand,
                    children: [
                      produk.fotoUrl != null
                          ? Image.network(
                              produk.fotoUrl!,
                              fit: BoxFit.cover,
                              errorBuilder: (context, error, stackTrace) =>
                                  _fotoPlaceholder(),
                            )
                          : _fotoPlaceholder(),
                      // Gradasi gelap agar judul & tombol tetap terbaca,
                      // senada dengan gradient biru website.
                      Container(
                        decoration: const BoxDecoration(
                          gradient: LinearGradient(
                            begin: Alignment.topCenter,
                            end: Alignment.bottomCenter,
                            colors: [
                              Colors.transparent,
                              Colors.black54,
                            ],
                            stops: [0.4, 1.0],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),

              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 16, 16, 24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Nama produk + ID
                      Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Expanded(
                            child: Text(
                              produk.nama,
                              style: const TextStyle(
                                fontSize: 22,
                                fontWeight: FontWeight.bold,
                                color: Color(0xFF0c2340),
                              ),
                            ),
                          ),
                          Container(
                            margin: const EdgeInsets.only(left: 8, top: 2),
                            padding: const EdgeInsets.symmetric(
                              horizontal: 10,
                              vertical: 5,
                            ),
                            decoration: BoxDecoration(
                              color: _kBgSoft,
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: _kPrimary.withValues(alpha: 0.25)),
                            ),
                            child: Text(
                              '#${produk.id ?? '-'}',
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w700,
                                color: _kPrimaryDark,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),

                      // Harga & Stok berdampingan, ala card statistik website
                      Row(
                        children: [
                          Expanded(
                            child: _StatCard(
                              icon: Icons.payments_outlined,
                              iconColor: const Color(0xFF16a34a),
                              iconBg: const Color(0xFFdcfce7),
                              label: 'Harga Jual',
                              value: produk.hargaJual != null
                                  ? 'Rp ${formatter.format(produk.hargaJual)}'
                                  : 'N/A',
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: _StatCard(
                              icon: Icons.inventory_2_outlined,
                              iconColor: const Color(0xFFd97706),
                              iconBg: const Color(0xFFfef3c7),
                              label: 'Stok Tersedia',
                              value: produk.stok != null
                                  ? '${produk.stok} unit'
                                  : 'N/A',
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),

                      // Keterangan — selalu ditampilkan (sesuai form web),
                      // dengan placeholder kalau memang belum diisi.
                      _SectionCard(
                        icon: Icons.sticky_note_2_outlined,
                        iconColor: const Color(0xFF0891b2),
                        title: 'Keterangan Produk',
                        child: Text(
                          (produk.keterangan != null &&
                                  produk.keterangan!.trim().isNotEmpty)
                              ? produk.keterangan!.trim()
                              : 'Belum ada keterangan untuk produk ini.',
                          style: TextStyle(
                            fontSize: 14,
                            height: 1.5,
                            color: (produk.keterangan != null &&
                                    produk.keterangan!.trim().isNotEmpty)
                                ? const Color(0xFF334155)
                                : Colors.grey[500],
                            fontStyle: (produk.keterangan != null &&
                                    produk.keterangan!.trim().isNotEmpty)
                                ? FontStyle.normal
                                : FontStyle.italic,
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Informasi Produk — menyamakan field dengan sidebar
                      // "Informasi Produk" pada staff-edit.blade.php
                      _SectionCard(
                        icon: Icons.info_outline,
                        iconColor: _kPrimary,
                        title: 'Informasi Produk',
                        child: Column(
                          children: [
                            _InfoRow(
                              icon: Icons.tag,
                              label: 'ID Produk',
                              value: produk.id?.toString() ?? '-',
                            ),
                            const Divider(height: 20),
                            _InfoRow(
                              icon: Icons.sell_outlined,
                              label: 'Harga Jual',
                              value: produk.hargaJual != null
                                  ? 'Rp ${formatter.format(produk.hargaJual)}'
                                  : '-',
                            ),
                            const Divider(height: 20),
                            _InfoRow(
                              icon: Icons.warehouse_outlined,
                              label: 'Stok',
                              value: produk.stok != null
                                  ? '${produk.stok} unit'
                                  : '-',
                              valueColor: (produk.stok ?? 0) > 0
                                  ? const Color(0xFF16a34a)
                                  : const Color(0xFFdc2626),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Timeline (Dibuat & Terakhir Diubah)
                      if (produk.createdAt != null || produk.updatedAt != null)
                        _SectionCard(
                          icon: Icons.history,
                          iconColor: const Color(0xFF7c3aed),
                          title: 'Timeline',
                          child: Column(
                            children: [
                              if (produk.createdAt != null)
                                _InfoRow(
                                  icon: Icons.add_circle_outline,
                                  label: 'Dibuat',
                                  value: dateFormat.format(produk.createdAt!),
                                ),
                              if (produk.createdAt != null &&
                                  produk.updatedAt != null)
                                const Divider(height: 20),
                              if (produk.updatedAt != null)
                                _InfoRow(
                                  icon: Icons.edit_calendar_outlined,
                                  label: 'Terakhir Diubah',
                                  value: dateFormat.format(produk.updatedAt!),
                                ),
                            ],
                          ),
                        ),

                      if (canEditDelete) ...[
                        const SizedBox(height: 24),
                        Row(
                          children: [
                            Expanded(
                              child: OutlinedButton.icon(
                                style: OutlinedButton.styleFrom(
                                  foregroundColor: Colors.red,
                                  side: const BorderSide(color: Colors.red),
                                  padding: const EdgeInsets.symmetric(vertical: 14),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                ),
                                onPressed: () =>
                                    _confirmDelete(context, produkProvider, produk),
                                icon: const Icon(Icons.delete_outline),
                                label: const Text('Hapus'),
                              ),
                            ),
                            const SizedBox(width: 12),
                            Expanded(
                              flex: 2,
                              child: ElevatedButton.icon(
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: _kPrimary,
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(vertical: 14),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                ),
                                onPressed: () async {
                                  await Navigator.pushNamed(
                                    context,
                                    '/produk-staf-tambah',
                                    arguments: produk,
                                  );
                                  if (context.mounted) {
                                    produkProvider
                                        .getProdukDetail(widget.produkId);
                                  }
                                },
                                icon: const Icon(Icons.edit_outlined),
                                label: const Text('Edit Produk'),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ],
                  ),
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _fotoPlaceholder() {
    return Container(
      color: _kBgSoft,
      child: Icon(Icons.inventory_2, color: _kPrimary.withValues(alpha: 0.4), size: 72),
    );
  }

  void _confirmDelete(
    BuildContext context,
    ProdukProvider produkProvider,
    dynamic produk,
  ) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        title: const Text('Hapus Produk'),
        content: Text(
          'Yakin ingin menghapus produk "${produk.nama}"? Tindakan ini tidak dapat dibatalkan.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Hapus', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );

    if (confirm == true && context.mounted) {
      final success = await produkProvider.deleteProduk(produk.id!);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              success
                  ? 'Produk berhasil dihapus'
                  : (produkProvider.errorMessage ?? 'Gagal menghapus produk'),
            ),
          ),
        );
        if (success) Navigator.pop(context);
      }
    }
  }
}

/// Kartu kecil untuk Harga / Stok, tampil berdampingan di bagian atas.
class _StatCard extends StatelessWidget {
  final IconData icon;
  final Color iconColor;
  final Color iconBg;
  final String label;
  final String value;

  const _StatCard({
    required this.icon,
    required this.iconColor,
    required this.iconBg,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFe5e7eb)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            padding: const EdgeInsets.all(8),
            decoration: BoxDecoration(
              color: iconBg,
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(icon, color: iconColor, size: 18),
          ),
          const SizedBox(height: 10),
          Text(
            label,
            style: TextStyle(fontSize: 11, color: Colors.grey[600]),
          ),
          const SizedBox(height: 4),
          Text(
            value,
            style: const TextStyle(
              fontSize: 15,
              fontWeight: FontWeight.bold,
              color: Color(0xFF0c2340),
            ),
          ),
        ],
      ),
    );
  }
}

/// Card dengan header biru kecil + ikon, meniru gaya "card-header
/// staff-form-header" di staff-edit.blade.php.
class _SectionCard extends StatelessWidget {
  final IconData icon;
  final Color iconColor;
  final String title;
  final Widget child;

  const _SectionCard({
    required this.icon,
    required this.iconColor,
    required this.title,
    required this.child,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFe5e7eb)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.04),
            blurRadius: 10,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 14),
            child: Row(
              children: [
                Icon(icon, size: 18, color: iconColor),
                const SizedBox(width: 8),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 15,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF0c2340),
                  ),
                ),
              ],
            ),
          ),
          const Divider(height: 1),
          Padding(
            padding: const EdgeInsets.all(16),
            child: child,
          ),
        ],
      ),
    );
  }
}

/// Baris label-nilai sederhana, dipakai di kartu Informasi Produk & Timeline.
class _InfoRow extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;
  final Color? valueColor;

  const _InfoRow({
    required this.icon,
    required this.label,
    required this.value,
    this.valueColor,
  });

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Icon(icon, size: 16, color: Colors.grey[500]),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            label,
            style: TextStyle(fontSize: 13, color: Colors.grey[600]),
          ),
        ),
        Text(
          value,
          textAlign: TextAlign.right,
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w700,
            color: valueColor ?? const Color(0xFF0c2340),
          ),
        ),
      ],
    );
  }
}