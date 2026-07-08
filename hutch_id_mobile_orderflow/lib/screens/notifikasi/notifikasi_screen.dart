import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../models/notifikasi.dart';
import '../../providers/notifikasi_provider.dart';
import '../../providers/produk_provider.dart';
import '../../providers/auth_provider.dart';
import '../../models/produk.dart';
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
        Provider.of<NotifikasiProvider>(
          context,
          listen: false,
        ).fetchNotifikasi();
        // Dibutuhkan supaya tombol "Tambah Stok" di kartu notifikasi
        // stok_kurang bisa mencocokkan nama produk -> id produk.
        Provider.of<ProdukProvider>(context, listen: false).fetchProduk();
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
                onPressed: () async {
                  final provider = Provider.of<NotifikasiProvider>(
                    context,
                    listen: false,
                  );
                  final success = await provider.markAllAsRead();
                  if (!mounted) return;
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(
                        success
                            ? 'Semua notifikasi ditandai sudah dibaca'
                            : 'Gagal menandai semua notifikasi',
                      ),
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
                            .where((n) => !n.sudahDibaca)
                            .length,
                      ),
                      const SizedBox(width: 10),
                      _buildFilterTab(
                        label: 'Sudah Dibaca',
                        value: 'sudah_dibaca',
                        count: notifikasiProvider.notifikasiList
                            .where((n) => n.sudahDibaca)
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

  List<Notifikasi> _getFilteredNotifications(
    List<Notifikasi> notifikasiList,
  ) {
    if (_selectedFilter == 'belum_dibaca') {
      return notifikasiList.where((n) => !n.sudahDibaca).toList();
    } else if (_selectedFilter == 'sudah_dibaca') {
      return notifikasiList.where((n) => n.sudahDibaca).toList();
    }
    return notifikasiList;
  }

  /// Dialog untuk Operator Gudang menambah stok langsung dari notifikasi
  /// "Stok Tidak Cukup". Mencocokkan produk dari detail_kurang/notif.data
  /// dengan daftar produk (by produk_id kalau ada, atau nama produk),
  /// lalu memanggil API update stok yang sama dengan layar Manajemen Stok
  /// Gudang. Backend otomatis menutup/menyelesaikan notifikasi ini begitu
  /// stok sudah mencukupi.
  Future<void> _showTambahStokDialog(
    BuildContext context,
    Notifikasi notifikasi,
  ) async {
    final produkProvider = Provider.of<ProdukProvider>(
      context,
      listen: false,
    );
    final notifikasiProvider = Provider.of<NotifikasiProvider>(
      context,
      listen: false,
    );

    // Kumpulkan baris produk yang kurang dari notifikasi ini.
    final rows = notifikasi.detailKurang.isNotEmpty
        ? notifikasi.detailKurang
        : (notifikasi.namaProduk != null
              ? [
                  {
                    'nama_produk': notifikasi.namaProduk,
                    'produk_id': notifikasi.produkId,
                  },
                ]
              : <Map<String, dynamic>>[]);

    if (rows.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Detail produk tidak ditemukan pada notifikasi ini'),
        ),
      );
      return;
    }

    // Cocokkan tiap baris dengan produk asli (untuk dapat id & stok saat ini).
    final matched = <Map<String, dynamic>>[];
    for (final row in rows) {
      Produk? produk;
      final produkId = row['produk_id'];
      if (produkId != null) {
        produk = produkProvider.produkList
            .where(
              (p) =>
                  p.id ==
                  (produkId is int ? produkId : int.tryParse(produkId.toString())),
            )
            .firstOrNull;
      }
      produk ??= produkProvider.produkList
          .where(
            (p) =>
                p.nama.toLowerCase() ==
                (row['nama_produk']?.toString().toLowerCase() ?? ''),
          )
          .firstOrNull;

      matched.add({
        'produk': produk,
        'nama_produk': row['nama_produk']?.toString() ?? produk?.nama ?? '-',
        'kurang': row['kurang'],
      });
    }

    final controllers = <TextEditingController>[
      for (final m in matched)
        TextEditingController(
          text: ((m['produk'] as Produk?)?.stok ?? 0).toString(),
        ),
    ];

    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Tambah Stok'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Masukkan stok baru untuk produk yang kurang berikut ini.',
                style: TextStyle(fontSize: 13, color: Colors.grey),
              ),
              const SizedBox(height: 14),
              for (var i = 0; i < matched.length; i++) ...[
                Text(
                  matched[i]['nama_produk'] as String,
                  style: const TextStyle(fontWeight: FontWeight.w700),
                ),
                if (matched[i]['produk'] == null)
                  const Padding(
                    padding: EdgeInsets.only(top: 2, bottom: 6),
                    child: Text(
                      'Produk tidak ditemukan di daftar produk saat ini',
                      style: TextStyle(fontSize: 11, color: Colors.red),
                    ),
                  )
                else
                  Padding(
                    padding: const EdgeInsets.only(top: 2, bottom: 6),
                    child: Text(
                      'Stok saat ini: ${(matched[i]['produk'] as Produk).stok ?? 0} unit'
                      '${matched[i]['kurang'] != null ? ' \u00b7 Kurang: ${matched[i]['kurang']} unit' : ''}',
                      style: const TextStyle(fontSize: 11, color: Colors.grey),
                    ),
                  ),
                TextField(
                  controller: controllers[i],
                  enabled: matched[i]['produk'] != null,
                  keyboardType: TextInputType.number,
                  decoration: InputDecoration(
                    labelText: 'Stok baru',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    isDense: true,
                  ),
                ),
                const SizedBox(height: 12),
              ],
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Batal'),
          ),
          FilledButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Simpan Stok'),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    var successCount = 0;
    var attempted = 0;
    for (var i = 0; i < matched.length; i++) {
      final produk = matched[i]['produk'] as Produk?;
      if (produk?.id == null) continue;
      final newStok = int.tryParse(controllers[i].text);
      if (newStok == null || newStok < 0) continue;

      attempted++;
      final ok = await produkProvider.updateProdukStok(
        produk!.id!,
        newStok,
        keterangan: 'Ditambahkan dari notifikasi stok kurang',
      );
      if (ok) successCount++;
    }

    if (!mounted) return;

    if (attempted == 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text(
            'Tidak ada produk yang cocok untuk diperbarui stoknya',
          ),
        ),
      );
      return;
    }

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          successCount == attempted
              ? 'Stok berhasil diperbarui ($successCount produk)'
              : 'Sebagian stok gagal diperbarui ($successCount/$attempted berhasil)',
        ),
        backgroundColor: successCount == attempted
            ? const Color(0xFF10b981)
            : const Color(0xFFf59e0b),
      ),
    );

    await produkProvider.fetchProduk();
    // Notifikasi stok_kurang yang sudah teratasi otomatis dihapus/diupdate
    // oleh backend, jadi cukup refresh ulang daftar notifikasi.
    await notifikasiProvider.fetchNotifikasi();
  }

  /// Cek apakah notifikasi tipe 'stok_kurang' ini masih relevan, yaitu
  /// stok produk terkait masih di bawah kebutuhan. Dipakai untuk mengunci
  /// tombol "Hapus" supaya operator gudang tidak bisa menghapus notifikasi
  /// stok kurang sebelum stok yang kurang benar-benar ditambahkan.
  bool _isStokMasihKurang(BuildContext context, Notifikasi notifikasi) {
    if (notifikasi.tipe != 'stok_kurang') return false;

    final produkProvider = Provider.of<ProdukProvider>(
      context,
      listen: false,
    );

    final rows = notifikasi.detailKurang.isNotEmpty
        ? notifikasi.detailKurang
        : (notifikasi.namaProduk != null
              ? [
                  {
                    'nama_produk': notifikasi.namaProduk,
                    'produk_id': notifikasi.produkId,
                  },
                ]
              : <Map<String, dynamic>>[]);

    if (rows.isEmpty) {
      // Tidak ada detail produk untuk dicek, anggap masih perlu ditindak
      // (aman: jangan biarkan terhapus begitu saja).
      return true;
    }

    for (final row in rows) {
      Produk? produk;
      final produkId = row['produk_id'];
      if (produkId != null) {
        produk = produkProvider.produkList
            .where(
              (p) =>
                  p.id ==
                  (produkId is int
                      ? produkId
                      : int.tryParse(produkId.toString())),
            )
            .firstOrNull;
      }
      produk ??= produkProvider.produkList
          .where(
            (p) =>
                p.nama.toLowerCase() ==
                (row['nama_produk']?.toString().toLowerCase() ?? ''),
          )
          .firstOrNull;

      if (produk == null) {
        // Produk tidak ditemukan lagi (mungkin sudah dihapus) — tidak bisa
        // dipastikan stoknya, anggap masih kurang supaya tetap aman.
        continue;
      }

      final stokSaatIni = produk.stok ?? 0;
      final kebutuhanRaw = row['kebutuhan'] ?? row['kurang'];
      final kebutuhan = kebutuhanRaw is int
          ? kebutuhanRaw
          : int.tryParse(kebutuhanRaw?.toString() ?? '');

      if (kebutuhan != null) {
        if (stokSaatIni < kebutuhan) return true;
      } else if (stokSaatIni <= 0) {
        return true;
      }
    }

    return false;
  }

  Widget _buildNotifikasiCard(BuildContext context, Notifikasi notifikasi) {
    final dateFormat = DateFormat('dd MMM yyyy HH:mm', 'id_ID');
    final isNew = !notifikasi.sudahDibaca;
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final role = authProvider.user?.role;
    final isGudangRole = role == 'operator_gudang' ||
        role == 'administrator' ||
        role == 'pemilik_umkm';
    final isStokKurang = notifikasi.tipe == 'stok_kurang';
    // listen: true supaya kartu ini rebuild otomatis saat stok produk
    // diperbarui (mis. setelah dialog "Tambah Stok" menyimpan perubahan).
    Provider.of<ProdukProvider>(context);
    final stokMasihKurang = _isStokMasihKurang(context, notifikasi);

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
                    color: _getColorByType(
                      notifikasi.tipe,
                    ).withValues(alpha: 0.15),
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
                Icon(
                  Icons.schedule_rounded,
                  size: 14,
                  color: Colors.grey[600],
                ),
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
            // Quick action: Operator Gudang bisa langsung menambah stok dari
            // notifikasi "Stok Tidak Cukup", sama seperti alur di web.
            if (isStokKurang && isGudangRole) ...[
              SizedBox(
                width: double.infinity,
                child: ElevatedButton.icon(
                  onPressed: () => _showTambahStokDialog(context, notifikasi),
                  icon: const Icon(Icons.inventory_2_rounded, size: 16),
                  label: const Text('Tambah Stok'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFf59e0b),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 10),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 8),
            ],
            if (stokMasihKurang) ...[
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 8,
                ),
                decoration: BoxDecoration(
                  color: Colors.red[50],
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: Colors.red[200]!),
                ),
                child: Row(
                  children: [
                    Icon(Icons.lock_outline_rounded,
                        size: 14, color: Colors.red[700]),
                    const SizedBox(width: 6),
                    Expanded(
                      child: Text(
                        'Notifikasi tidak bisa dihapus sebelum stok yang kurang ditambahkan',
                        style: TextStyle(
                          fontSize: 11,
                          color: Colors.red[700],
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 8),
            ],
            // Action buttons
            Row(
              children: [
                if (!notifikasi.sudahDibaca)
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: () async {
                        final provider = Provider.of<NotifikasiProvider>(
                          context,
                          listen: false,
                        );
                        final success = await provider.markAsRead(
                          notifikasi.id!,
                        );
                        if (!context.mounted) return;
                        ScaffoldMessenger.of(context).showSnackBar(
                          SnackBar(
                            content: Text(
                              success
                                  ? 'Notifikasi ditandai sudah dibaca'
                                  : 'Gagal menandai notifikasi',
                            ),
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
                if (!notifikasi.sudahDibaca) const SizedBox(width: 8),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: stokMasihKurang
                        ? null
                        : () async {
                            final provider = Provider.of<NotifikasiProvider>(
                              context,
                              listen: false,
                            );
                            final success = await provider.deleteNotifikasi(
                              notifikasi.id!,
                            );
                            if (!context.mounted) return;
                            ScaffoldMessenger.of(context).showSnackBar(
                              SnackBar(
                                content: Text(
                                  success
                                      ? 'Notifikasi dihapus'
                                      : 'Gagal menghapus notifikasi',
                                ),
                              ),
                            );
                          },
                    icon: const Icon(Icons.delete_rounded, size: 16),
                    label: const Text('Hapus'),
                    style: OutlinedButton.styleFrom(
                      disabledForegroundColor: Colors.grey[400],
                      side: BorderSide(
                        color: stokMasihKurang
                            ? Colors.grey[300]!
                            : const Color(0xFFef4444),
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
      case 'pesanan_dibuat':
        return Icons.shopping_bag_rounded;
      case 'pengiriman':
        return Icons.local_shipping_rounded;
      case 'pembayaran':
        return Icons.payment_rounded;
      case 'stok_kurang':
        return Icons.warning_amber_rounded;
      case 'stok_ditambah':
        return Icons.inventory_2_rounded;
      case 'produk_baru':
        return Icons.add_box_rounded;
      case 'produk_dihapus':
        return Icons.delete_forever_rounded;
      default:
        return Icons.notifications_rounded;
    }
  }

  Color _getColorByType(String? type) {
    switch (type) {
      case 'pesanan':
      case 'pesanan_dibuat':
        return const Color(0xFF2563eb);
      case 'pengiriman':
        return const Color(0xFF22c55e);
      case 'pembayaran':
        return const Color(0xFFf97316);
      case 'stok_kurang':
        return const Color(0xFFdc2626);
      case 'stok_ditambah':
        return const Color(0xFF16a34a);
      case 'produk_baru':
        return const Color(0xFF0ea5e9);
      case 'produk_dihapus':
        return const Color(0xFF6b7280);
      default:
        return const Color(0xFF3b82f6);
    }
  }
}
