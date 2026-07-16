import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/produk_provider.dart';
import '../../widgets/custom_widgets.dart';

class GudangStokScreen extends StatefulWidget {
  const GudangStokScreen({super.key});

  @override
  State<GudangStokScreen> createState() => _GudangStokScreenState();
}

class _GudangStokScreenState extends State<GudangStokScreen> {
  final TextEditingController _searchController = TextEditingController();
  bool _isUpdating = false;

  static const _kPrimary = Color(0xFF2563EB);
  static const _kPrimaryDark = Color(0xFF1E3A8A);
  static const _kGreen = Color(0xFF10B981);
  static const _kRed = Color(0xFFEF4444);
  static const _kAmber = Color(0xFFF59E0B);

  // Sama dengan logika web: produk dengan stok <= 10 dianggap "stok rendah".
  static const int _lowStockThreshold = 10;

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (mounted) {
        Provider.of<ProdukProvider>(context, listen: false).fetchProduk();
      }
    });
  }

  @override
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  List<dynamic> _getFilteredProducts(List<dynamic> products) {
    if (_searchController.text.isEmpty) return products;
    final q = _searchController.text.toLowerCase();
    return products.where((p) {
      return (p.nama?.toLowerCase() ?? '').contains(q);
    }).toList();
  }

  Future<void> _refresh() async {
    await Provider.of<ProdukProvider>(context, listen: false).fetchProduk();
  }

  Future<void> _applyStockChange({
    required dynamic produk,
    required bool isTambah,
    required int jumlah,
    String? catatan,
  }) async {
    final currentStok = (produk.stok ?? 0) as int;
    final newStok = isTambah ? currentStok + jumlah : currentStok - jumlah;

    if (newStok < 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Stok tidak boleh menjadi negatif')),
      );
      return;
    }

    setState(() => _isUpdating = true);

    try {
      final produkProvider = Provider.of<ProdukProvider>(
        context,
        listen: false,
      );

      final success = await produkProvider.updateProdukStok(
        produk.id,
        newStok,
        keterangan: (catatan != null && catatan.trim().isNotEmpty)
            ? catatan.trim()
            : null,
      );

      if (!mounted) return;

      if (success) {
        setState(() => _isUpdating = false);
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Stok produk berhasil diperbarui'),
            duration: Duration(seconds: 2),
            backgroundColor: _kGreen,
          ),
        );
        await produkProvider.fetchProduk();
      } else {
        setState(() => _isUpdating = false);
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              produkProvider.errorMessage ?? 'Gagal memperbarui stok',
            ),
          ),
        );
      }
    } catch (e) {
      if (!mounted) return;
      setState(() => _isUpdating = false);
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Error: $e')));
    }
  }

  void _showUbahStokSheet(dynamic produk) {
    final currentStok = (produk.stok ?? 0) as int;
    bool isTambah = true;
    final jumlahController = TextEditingController();
    final catatanController = TextEditingController();
    final formatter = NumberFormat('#,##0', 'id_ID');

    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (sheetContext) {
        return StatefulBuilder(
          builder: (sheetContext, setSheetState) {
            return Padding(
              padding: EdgeInsets.only(
                bottom: MediaQuery.of(sheetContext).viewInsets.bottom,
              ),
              child: Container(
                decoration: const BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.vertical(
                    top: Radius.circular(20),
                  ),
                ),
                padding: const EdgeInsets.fromLTRB(20, 14, 20, 24),
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Center(
                        child: Container(
                          width: 40,
                          height: 4,
                          margin: const EdgeInsets.only(bottom: 16),
                          decoration: BoxDecoration(
                            color: Colors.grey[300],
                            borderRadius: BorderRadius.circular(4),
                          ),
                        ),
                      ),
                      Row(
                        children: [
                          const Icon(Icons.edit_note, color: _kPrimary),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              'Ubah Stok: ${produk.nama ?? ''}',
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Stok saat ini: ${formatter.format(currentStok)} unit',
                        style: TextStyle(color: Colors.grey[600], fontSize: 13),
                      ),
                      const SizedBox(height: 20),
                      const Text(
                        'PILIH TIPE PERUBAHAN',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          letterSpacing: 0.5,
                          color: Colors.black54,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Row(
                        children: [
                          Expanded(
                            child: _StokTypeOption(
                              label: 'Tambahkan Stok',
                              icon: Icons.add_circle_outline,
                              color: _kGreen,
                              selected: isTambah,
                              onTap: () =>
                                  setSheetState(() => isTambah = true),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: _StokTypeOption(
                              label: 'Kurangi Stok',
                              icon: Icons.remove_circle_outline,
                              color: _kRed,
                              selected: !isTambah,
                              onTap: () =>
                                  setSheetState(() => isTambah = false),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),
                      Text(
                        isTambah ? 'JUMLAH DITAMBAHKAN' : 'JUMLAH DIKURANGI',
                        style: const TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          letterSpacing: 0.5,
                          color: Colors.black54,
                        ),
                      ),
                      const SizedBox(height: 8),
                      TextField(
                        key: const Key('stok_baru_field'),
                        controller: jumlahController,
                        keyboardType: TextInputType.number,
                        decoration: InputDecoration(
                          hintText: 'Masukkan jumlah',
                          prefixIcon: Icon(
                            isTambah
                                ? Icons.arrow_upward
                                : Icons.arrow_downward,
                            color: isTambah ? _kGreen : _kRed,
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),
                      const Text(
                        'CATATAN (OPSIONAL)',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          letterSpacing: 0.5,
                          color: Colors.black54,
                        ),
                      ),
                      const SizedBox(height: 8),
                      TextField(
                        controller: catatanController,
                        maxLines: 2,
                        decoration: InputDecoration(
                          hintText:
                              'Misal: hasil restock, penyesuaian inventory, dll',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),
                      Row(
                        children: [
                          Expanded(
                            child: OutlinedButton.icon(
                              onPressed: () => Navigator.pop(sheetContext),
                              icon: const Icon(Icons.close),
                              label: const Text('Batal'),
                              style: OutlinedButton.styleFrom(
                                padding: const EdgeInsets.symmetric(
                                  vertical: 14,
                                ),
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            flex: 2,
                            child: ElevatedButton.icon(
                              onPressed: () async {
                                final jumlah = int.tryParse(
                                  jumlahController.text.trim(),
                                );
                                if (jumlah == null || jumlah <= 0) {
                                  ScaffoldMessenger.of(
                                    sheetContext,
                                  ).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                        'Masukkan jumlah yang valid',
                                      ),
                                    ),
                                  );
                                  return;
                                }
                                Navigator.pop(sheetContext);
                                await _applyStockChange(
                                  produk: produk,
                                  isTambah: isTambah,
                                  jumlah: jumlah,
                                  catatan: catatanController.text,
                                );
                              },
                              icon: const Icon(Icons.save),
                              label: const Text('Simpan Perubahan'),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: _kPrimary,
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(
                                  vertical: 14,
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
            );
          },
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final numberFormat = NumberFormat('#,##0', 'id_ID');

    return Scaffold(
      backgroundColor: const Color(0xFFF3F4F6),
      body: Consumer<ProdukProvider>(
        builder: (context, produkProvider, _) {
          if (produkProvider.isLoading && produkProvider.produkList.isEmpty) {
            return const LoadingWidget(message: 'Memuat data produk...');
          }

          final allProducts = produkProvider.produkList;
          final totalStok = allProducts.fold<int>(
            0,
            (sum, p) => sum + ((p.stok ?? 0) as int),
          );
          final produkTerdaftar = allProducts.length;
          final stokRendah = allProducts
              .where((p) => ((p.stok ?? 0) as int) <= _lowStockThreshold)
              .length;

          final products = _getFilteredProducts(allProducts);

          return RefreshIndicator(
            onRefresh: _refresh,
            child: CustomScrollView(
              slivers: [
                SliverToBoxAdapter(
                  child: _buildHeader(totalStok, produkTerdaftar, stokRendah),
                ),
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(16, 16, 16, 8),
                    child: TextField(
                      controller: _searchController,
                      onChanged: (_) => setState(() {}),
                      decoration: InputDecoration(
                        hintText: 'Cari nama produk...',
                        prefixIcon: const Icon(Icons.search),
                        filled: true,
                        fillColor: Colors.white,
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(12),
                          borderSide: BorderSide(color: Colors.grey[300]!),
                        ),
                        contentPadding: const EdgeInsets.symmetric(
                          vertical: 0,
                        ),
                      ),
                    ),
                  ),
                ),
                if (products.isEmpty)
                  const SliverFillRemaining(
                    child: Center(
                      child: EmptyStateWidget(
                        message: 'Tidak ada produk ditemukan',
                        icon: Icons.inventory_2_outlined,
                      ),
                    ),
                  )
                else
                  SliverPadding(
                    padding: const EdgeInsets.fromLTRB(16, 8, 16, 24),
                    sliver: SliverList(
                      delegate: SliverChildBuilderDelegate((context, index) {
                        final produk = products[index];
                        return _buildProductCard(produk, numberFormat);
                      }, childCount: products.length),
                    ),
                  ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildHeader(int totalStok, int produkTerdaftar, int stokRendah) {
    final numberFormat = NumberFormat('#,##0', 'id_ID');
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.fromLTRB(20, 20, 20, 28),
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [_kPrimaryDark, _kPrimary],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.only(
          bottomLeft: Radius.circular(24),
          bottomRight: Radius.circular(24),
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: const [
              Icon(Icons.warehouse, color: Colors.white, size: 26),
              SizedBox(width: 10),
              Text(
                'Manajemen Stok Barang',
                style: TextStyle(
                  color: Colors.white,
                  fontSize: 19,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ],
          ),
          const SizedBox(height: 6),
          Text(
            'Kelola stok produk dan pantau ketersediaan barang dengan mudah',
            style: TextStyle(color: Colors.white.withOpacity(0.85), fontSize: 12.5),
          ),
          const SizedBox(height: 20),
          Row(
            children: [
              Expanded(
                child: _StatCard(
                  icon: Icons.inventory_2_rounded,
                  iconColor: _kPrimary,
                  label: 'Total Stok',
                  value: numberFormat.format(totalStok),
                  subtext: 'Unit tersedia',
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _StatCard(
                  icon: Icons.category_rounded,
                  iconColor: _kPrimary,
                  label: 'Produk',
                  value: '$produkTerdaftar',
                  subtext: 'Jenis aktif',
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _StatCard(
                  icon: Icons.warning_rounded,
                  iconColor: _kRed,
                  label: 'Stok Rendah',
                  value: '$stokRendah',
                  subtext: 'Perlu diisi',
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildProductCard(dynamic produk, NumberFormat numberFormat) {
    final stok = (produk.stok ?? 0) as int;
    final isKosong = stok <= 0;
    final statusColor = isKosong ? _kRed : _kGreen;
    final statusLabel = isKosong ? 'KOSONG' : 'TERSEDIA';

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.05),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(12),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            ClipRRect(
              borderRadius: BorderRadius.circular(10),
              child: Container(
                width: 64,
                height: 64,
                color: const Color(0xFFF1F5F9),
                child: produk.fotoUrl != null
                    ? Image.network(
                        produk.fotoUrl!,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) =>
                            const Icon(
                              Icons.image_not_supported,
                              color: Color(0xFFd1d5db),
                            ),
                      )
                    : const Icon(
                        Icons.image_not_supported,
                        color: Color(0xFFd1d5db),
                      ),
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Expanded(
                        child: Text(
                          produk.nama ?? 'Unknown',
                          style: const TextStyle(
                            fontWeight: FontWeight.w700,
                            fontSize: 13.5,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 3,
                        ),
                        decoration: BoxDecoration(
                          color: statusColor.withOpacity(0.12),
                          borderRadius: BorderRadius.circular(20),
                        ),
                        child: Text(
                          statusLabel,
                          style: TextStyle(
                            color: statusColor,
                            fontSize: 9.5,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                    ],
                  ),
                  if (produk.hargaJual != null) ...[
                    const SizedBox(height: 4),
                    Text(
                      'Rp ${numberFormat.format(produk.hargaJual ?? 0)}',
                      style: const TextStyle(
                        fontSize: 12,
                        color: _kGreen,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ],
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 10,
                          vertical: 5,
                        ),
                        decoration: BoxDecoration(
                          color: _kPrimary.withOpacity(0.08),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Icon(
                              Icons.inventory,
                              size: 13,
                              color: _kPrimary,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              '$stok unit',
                              style: const TextStyle(
                                fontWeight: FontWeight.w700,
                                color: _kPrimary,
                                fontSize: 12,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const Spacer(),
                      SizedBox(
                        height: 34,
                        child: ElevatedButton.icon(
                          onPressed: _isUpdating
                              ? null
                              : () => _showUbahStokSheet(produk),
                          icon: const Icon(Icons.edit, size: 14),
                          label: const Text(
                            'Ubah Stok',
                            style: TextStyle(fontSize: 12.5),
                          ),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: _kPrimary,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(
                              horizontal: 12,
                            ),
                            disabledBackgroundColor: Colors.grey[300],
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
      ),
    );
  }
}

class _StatCard extends StatelessWidget {
  final IconData icon;
  final Color iconColor;
  final String label;
  final String value;
  final String subtext;

  const _StatCard({
    required this.icon,
    required this.iconColor,
    required this.label,
    required this.value,
    required this.subtext,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 14),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(0.96),
        borderRadius: BorderRadius.circular(14),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, color: iconColor, size: 18),
          const SizedBox(height: 8),
          Text(
            value,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w800,
              color: Color(0xFF1E293B),
            ),
          ),
          const SizedBox(height: 2),
          Text(
            label,
            style: const TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w700,
              color: Color(0xFF64748B),
            ),
          ),
          Text(
            subtext,
            style: const TextStyle(fontSize: 9, color: Color(0xFF94A3B8)),
          ),
        ],
      ),
    );
  }
}

class _StokTypeOption extends StatelessWidget {
  final String label;
  final IconData icon;
  final Color color;
  final bool selected;
  final VoidCallback onTap;

  const _StokTypeOption({
    required this.label,
    required this.icon,
    required this.color,
    required this.selected,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(10),
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 8),
        decoration: BoxDecoration(
          color: selected ? color.withOpacity(0.08) : Colors.white,
          border: Border.all(
            color: selected ? color : Colors.grey[300]!,
            width: selected ? 1.6 : 1,
          ),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, size: 18, color: selected ? color : Colors.grey[600]),
            const SizedBox(width: 6),
            Flexible(
              child: Text(
                label,
                style: TextStyle(
                  fontSize: 12.5,
                  fontWeight: FontWeight.w600,
                  color: selected ? color : Colors.grey[700],
                ),
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
