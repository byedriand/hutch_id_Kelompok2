import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/pesanan_provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/custom_widgets.dart';
import '../../services/api_service.dart';
import '../../utils/pdf_downloader.dart';

class PesananDetailScreen extends StatefulWidget {
  final int pesananId;

  const PesananDetailScreen({super.key, required this.pesananId});

  @override
  State<PesananDetailScreen> createState() => _PesananDetailScreenState();
}

class _PesananDetailScreenState extends State<PesananDetailScreen> {
  late String _selectedStatus;
  bool _isUpdatingStatus = false;
  bool _isDownloadingPdf = false;

  // Helper method to calculate total from detail_pesanan
  double _calculateTotal() {
    final pesanan = Provider.of<PesananProvider>(
      context,
      listen: false,
    ).selectedPesanan;

    if (pesanan?.detailPesanan == null || pesanan!.detailPesanan!.isEmpty) {
      return 0;
    }

    return pesanan.detailPesanan!.fold(0, (total, item) {
      return total + ((item.jumlah ?? 0) * (item.hargaSatuan ?? 0));
    });
  }

  // Helper method to check if there are insufficient stocks
  bool _hasInsufficientStock() {
    final pesanan = Provider.of<PesananProvider>(
      context,
      listen: false,
    ).selectedPesanan;

    if (pesanan?.detailPesanan == null || pesanan!.detailPesanan!.isEmpty) {
      return false;
    }

    return pesanan.detailPesanan!.any((item) {
      final stok = item.produk?.stok ?? 0;
      final jumlah = item.jumlah ?? 0;
      return jumlah > stok;
    });
  }

  // Helper method to get insufficient stock items
  List<Map<String, dynamic>> _getInsufficientStockItems() {
    final pesanan = Provider.of<PesananProvider>(
      context,
      listen: false,
    ).selectedPesanan;

    List<Map<String, dynamic>> insufficientItems = [];

    if (pesanan?.detailPesanan != null) {
      for (var item in pesanan!.detailPesanan!) {
        final stok = item.produk?.stok ?? 0;
        final jumlah = item.jumlah ?? 0;
        if (jumlah > stok) {
          insufficientItems.add({
            'produk': item.produk?.nama ?? 'Unknown',
            'dipesan': jumlah,
            'tersedia': stok,
            'kurang': jumlah - stok,
          });
        }
      }
    }

    return insufficientItems;
  }

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (mounted) {
        Provider.of<PesananProvider>(
          context,
          listen: false,
        ).getPesananDetail(widget.pesananId);
      }
    });
  }

  void _downloadPDF(int pesananId, String nomorPo) async {
    if (_isDownloadingPdf) return;
    setState(() => _isDownloadingPdf = true);

    try {
      // Pakai endpoint mobile (base64) yang sudah otentikasi via token,
      // BUKAN route web (yang butuh session cookie & tidak akan jalan dari app).
      final result = await ApiService().downloadPesananPdf(pesananId);

      if (result == null) {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text(
                'Gagal mengambil PDF. Pastikan kamu punya akses & koneksi ke server.',
              ),
            ),
          );
        }
        return;
      }

      final bytes = base64Decode(result['base64'] as String);
      final filename = (result['filename'] as String?) ?? '$nomorPo.pdf';

      await savePdfBytes(bytes, filename);

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('PDF $nomorPo siap diunduh')),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text('Error: $e')));
      }
    } finally {
      if (mounted) setState(() => _isDownloadingPdf = false);
    }
  }

  static const Map<String, String> _statusLabels = {
    'menunggu_konfirmasi': 'Menunggu Konfirmasi',
    'dikonfirmasi': 'Dikonfirmasi',
    'dalam_produksi': 'Dalam Produksi',
    'siap_kirim': 'Siap Kirim',
    'selesai': 'Selesai',
    'dibatalkan': 'Dibatalkan',
  };

  /// Meniru persis `getAllowedStatusOptions()` + `canChangeStatusTo()` di
  /// PesananController (web): status berikutnya HANYA tahap selanjutnya
  /// (+ opsi batalkan), bukan daftar semua status sekaligus. Ini supaya
  /// alur di mobile "bertahap" persis seperti website, bukan bebas pilih.
  List<String> _availableStatusTargets(String userRole, String currentStatus) {
    if (currentStatus == 'selesai' || currentStatus == 'dibatalkan') {
      return [];
    }

    // Tahapan berikutnya berdasarkan status saat ini, sama seperti
    // getAllowedStatusOptions() di web.
    List<String> nextOptions;
    switch (currentStatus) {
      case 'menunggu_konfirmasi':
        nextOptions = ['dikonfirmasi', 'dibatalkan'];
        break;
      case 'dikonfirmasi':
        nextOptions = ['dalam_produksi', 'dibatalkan'];
        break;
      case 'dalam_produksi':
        nextOptions = ['siap_kirim', 'dibatalkan'];
        break;
      case 'siap_kirim':
        nextOptions = ['selesai', 'dibatalkan'];
        break;
      default:
        nextOptions = [];
    }

    // Lalu saring berdasarkan izin role, sama seperti canChangeStatusTo().
    return nextOptions
        .where((status) => _canChangeStatusTo(userRole, status, currentStatus))
        .toList();
  }

  bool _canChangeStatusTo(String userRole, String newStatus, String currentStatus) {
    if (userRole == 'administrator') return true;

    if (userRole == 'pemilik_umkm') {
      if (currentStatus == 'menunggu_konfirmasi' && newStatus == 'dikonfirmasi') {
        return true;
      }
      return ['dalam_produksi', 'siap_kirim', 'selesai', 'dibatalkan']
          .contains(newStatus);
    }

    if (userRole == 'operator_gudang' || userRole == 'staf_penjualan') {
      // Staf & gudang hanya bisa membatalkan pesanan, sama seperti website.
      return newStatus == 'dibatalkan';
    }

    return false;
  }

  void _showStatusDialog(String userRole, String currentStatus) {
    final statuses = _availableStatusTargets(userRole, currentStatus);
    if (statuses.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Tidak ada perubahan status yang diizinkan untuk pesanan ini.'),
        ),
      );
      return;
    }

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Ubah Status Pesanan'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: statuses
              .map(
                (status) => RadioListTile<String>(
                  title: Text(_statusLabels[status] ?? status),
                  value: status,
                  groupValue: _selectedStatus,
                  onChanged: (value) {
                    setState(() {
                      _selectedStatus = value!;
                    });
                    Navigator.pop(context);
                    if (status == 'dibatalkan') {
                      _confirmCancelThenUpdate();
                    } else {
                      _updateStatus();
                    }
                  },
                ),
              )
              .toList(),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
        ],
      ),
    );
  }

  /// Sama seperti website: pembatalan butuh alasan (alasan_pembatalan) yang
  /// dikirim ke backend, supaya validasi tidak gagal di sisi server.
  Future<void> _confirmCancelThenUpdate() async {
    final reasonController = TextEditingController();
    final confirmed = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Batalkan Pesanan'),
        content: TextField(
          controller: reasonController,
          maxLines: 3,
          decoration: const InputDecoration(
            hintText: 'Alasan pembatalan (wajib diisi, min. 5 karakter)',
            border: OutlineInputBorder(),
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            child: const Text('Ya, Batalkan', style: TextStyle(color: Colors.white)),
          ),
        ],
      ),
    );

    if (confirmed != true) return;

    if (reasonController.text.trim().length < 5) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Alasan pembatalan minimal 5 karakter')),
        );
      }
      return;
    }

    await _updateStatus(alasanPembatalan: reasonController.text.trim());
  }

  Future<void> _updateStatus({String? alasanPembatalan}) async {
    setState(() {
      _isUpdatingStatus = true;
    });

    final pesananProvider = Provider.of<PesananProvider>(
      context,
      listen: false,
    );
    final success = await pesananProvider.updatePesananStatus(
      widget.pesananId,
      _selectedStatus,
      alasanPembatalan: alasanPembatalan,
    );

    setState(() {
      _isUpdatingStatus = false;
    });

    if (!mounted) return;

    if (success) {
      // Refresh detail dulu supaya status/histori terbaru sudah kebaca saat
      // dialog ditutup.
      pesananProvider.getPesananDetail(widget.pesananId);

      // Backend (PesananController::updateStatus) otomatis mengirim
      // notifikasi WhatsApp ke pelanggan saat status diubah ke
      // 'siap_kirim'/'selesai' — sama seperti website. Info ini
      // (whatsapp_message) ditampilkan di dialog, meniru popup
      // "Status Diperbarui" di web.
      final result = pesananProvider.lastStatusUpdateResult;
      final message =
          (result?['message'] as String?) ?? 'Status pesanan berhasil diperbarui.';
      final waMessage = result?['whatsapp_message'] as String?;

      await showDialog(
        context: context,
        barrierDismissible: false,
        builder: (context) => AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          title: Column(
            children: const [
              Icon(Icons.info_outline, color: Color(0xFF2563eb), size: 48),
              SizedBox(height: 12),
              Text('Status Diperbarui', textAlign: TextAlign.center),
            ],
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(message, textAlign: TextAlign.center),
              if (waMessage != null) ...[
                const SizedBox(height: 14),
                Text(
                  waMessage,
                  style: const TextStyle(fontSize: 13, color: Color(0xFF334155)),
                ),
              ],
            ],
          ),
          actionsAlignment: MainAxisAlignment.center,
          actions: [
            ElevatedButton(
              onPressed: () => Navigator.pop(context),
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF2563eb),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 12),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
              child: const Text('OK'),
            ),
          ],
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(pesananProvider.errorMessage ?? 'Gagal mengubah status pesanan')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final userRole = Provider.of<AuthProvider>(context).user?.role ?? '';
    final canUpdateStatus = true;

    final formatter = NumberFormat('#,##0', 'id_ID');
    final dateFormat = DateFormat('dd MMM yyyy', 'id_ID');
    final size = MediaQuery.of(context).size;
    final isLargeScreen = size.width > 1200;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Detail Pesanan'),
        elevation: 0,
        backgroundColor: const Color(0xFF1e40af),
      ),
      body: Consumer<PesananProvider>(
        builder: (context, pesananProvider, _) {
          if (pesananProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat detail pesanan...');
          }

          final pesanan = pesananProvider.selectedPesanan;
          if (pesanan == null) {
            return const Center(
              child: EmptyStateWidget(message: 'Pesanan tidak ditemukan'),
            );
          }

          _selectedStatus = pesanan.status ?? 'aktif';
          final total = _calculateTotal();

          return SingleChildScrollView(
            padding: const EdgeInsets.all(20),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header with Action Buttons — di layar sempit, tombol
                // ditumpuk di bawah nomor PO (bukan dipaksa sejajar) supaya
                // tidak overflow (garis kuning-hitam) saat mode responsif.
                LayoutBuilder(
                  builder: (context, constraints) {
                    final headerInfo = Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Text(
                          pesanan.nomorPo ?? 'N/A',
                          style: const TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF1e40af),
                          ),
                        ),
                        const SizedBox(height: 6),
                        StatusBadge(status: pesanan.status ?? 'unknown'),
                      ],
                    );

                    final headerActions = Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        OutlinedButton.icon(
                          onPressed: _isDownloadingPdf
                              ? null
                              : () => _downloadPDF(
                                  pesanan.id!,
                                  pesanan.nomorPo ?? 'Unknown',
                                ),
                          icon: _isDownloadingPdf
                              ? const SizedBox(
                                  width: 14,
                                  height: 14,
                                  child: CircularProgressIndicator(strokeWidth: 2),
                                )
                              : const Icon(Icons.download, size: 16),
                          label: Text(_isDownloadingPdf ? 'Memuat...' : 'Unduh PDF'),
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(
                              color: Color(0xFF2563eb),
                              width: 1.5,
                            ),
                            foregroundColor: const Color(0xFF2563eb),
                          ),
                        ),
                        if (canUpdateStatus)
                          OutlinedButton.icon(
                            onPressed: () => _showStatusDialog(userRole, pesanan.status ?? ''),
                            icon: const Icon(Icons.edit, size: 16),
                            label: const Text('Update Status'),
                            style: OutlinedButton.styleFrom(
                              side: const BorderSide(
                                color: Color(0xFFf97316),
                                width: 1.5,
                              ),
                              foregroundColor: const Color(0xFFf97316),
                            ),
                          ),
                      ],
                    );

                    if (constraints.maxWidth < 480) {
                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          headerInfo,
                          const SizedBox(height: 16),
                          headerActions,
                        ],
                      );
                    }

                    return Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(child: headerInfo),
                        const SizedBox(width: 12),
                        headerActions,
                      ],
                    );
                  },
                ),
                const SizedBox(height: 32),

                // Layout Informasi PO / Pelanggan / Ringkasan — mengikuti
                // grid kartu di website: 3 kolom sejajar di layar lebar,
                // 2 kolom (Info PO + Pelanggan) di layar sedang, dan
                // ditumpuk penuh hanya di layar sempit (HP).
                if (isLargeScreen)
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Left Column: INFORMASI PO
                      Expanded(
                        child: _buildInfoSection(
                          title: 'INFORMASI PO',
                          pesanan: pesanan,
                          dateFormat: dateFormat,
                        ),
                      ),
                      const SizedBox(width: 20),
                      // Middle Column: PELANGGAN
                      Expanded(child: _buildCustomerSection(pesanan)),
                      const SizedBox(width: 20),
                      // Right Column: RINGKASAN
                      Expanded(
                        child: _buildSummarySection(
                          pesanan: pesanan,
                          total: total,
                          formatter: formatter,
                          dateFormat: dateFormat,
                        ),
                      ),
                    ],
                  )
                else if (size.width >= 640)
                  Column(
                    children: [
                      IntrinsicHeight(
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            Expanded(
                              child: _buildInfoSection(
                                title: 'INFORMASI PO',
                                pesanan: pesanan,
                                dateFormat: dateFormat,
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(child: _buildCustomerSection(pesanan)),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                      _buildSummarySection(
                        pesanan: pesanan,
                        total: total,
                        formatter: formatter,
                        dateFormat: dateFormat,
                      ),
                      const SizedBox(height: 20),
                    ],
                  )
                else
                  Column(
                    children: [
                      _buildInfoSection(
                        title: 'INFORMASI PO',
                        pesanan: pesanan,
                        dateFormat: dateFormat,
                      ),
                      const SizedBox(height: 16),
                      _buildCustomerSection(pesanan),
                      const SizedBox(height: 16),
                      _buildSummarySection(
                        pesanan: pesanan,
                        total: total,
                        formatter: formatter,
                        dateFormat: dateFormat,
                      ),
                      const SizedBox(height: 20),
                    ],
                  ),

                const SizedBox(height: 32),

                // Full Width: Detail Pesanan (Line Items)
                if (pesanan.detailPesanan != null &&
                    pesanan.detailPesanan!.isNotEmpty) ...[
                  // Stock Warning Alert
                  if (_hasInsufficientStock()) ...[
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: const Color(0xFFFEF3C7),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              const Icon(
                                Icons.warning_outlined,
                                color: Color(0xFFD97706),
                                size: 20,
                              ),
                              const SizedBox(width: 12),
                              const Expanded(
                                child: Text(
                                  'Perhatian: Stok beberapa produk pada PO ini tidak mencukupi',
                                  style: TextStyle(
                                    fontWeight: FontWeight.w600,
                                    fontSize: 13,
                                    color: Color(0xFFD97706),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          ..._getInsufficientStockItems().map((item) {
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 8),
                              child: Row(
                                children: [
                                  const SizedBox(width: 32),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          item['produk'],
                                          style: const TextStyle(
                                            fontWeight: FontWeight.w600,
                                            fontSize: 12,
                                            color: Color(0xFF1F2937),
                                          ),
                                        ),
                                        Text(
                                          'Dipesan ${item['dipesan']} unit — Stok tersedia ${item['tersedia']} (kurang ${item['kurang']} unit)',
                                          style: const TextStyle(
                                            fontSize: 11,
                                            color: Color(0xFF6B7280),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            );
                          }).toList(),
                          if (userRole == 'operator_gudang') ...[
                            const SizedBox(height: 12),
                            SizedBox(
                              width: double.infinity,
                              child: ElevatedButton.icon(
                                onPressed: () {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                        'Fitur "Tambah Stok" akan segera tersedia',
                                      ),
                                    ),
                                  );
                                },
                                icon: const Icon(
                                  Icons.add_circle_outline,
                                  size: 16,
                                ),
                                label: const Text('Tambah Stok'),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: const Color(0xFF10b981),
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(
                                    vertical: 10,
                                  ),
                                ),
                              ),
                            ),
                          ],
                        ],
                      ),
                    ),
                    const SizedBox(height: 20),
                  ],
                  const Text(
                    'DETAIL PRODUK',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF1e40af),
                    ),
                  ),
                  const SizedBox(height: 12),
                  // Card layout — setiap item produk tampil sebagai kartu
                  // responsif dengan baris Harga & Subtotal, tidak membutuhkan
                  // scroll horizontal sehingga semua info terbaca jelas di mobile.
                  // Header row (biru)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                    decoration: const BoxDecoration(
                      color: Color(0xFF2563eb),
                      borderRadius: BorderRadius.only(
                        topLeft: Radius.circular(10),
                        topRight: Radius.circular(10),
                      ),
                    ),
                    child: const Row(
                      children: [
                        SizedBox(
                          width: 28,
                          child: Text('#',
                              style: TextStyle(color: Colors.white,
                                  fontWeight: FontWeight.bold, fontSize: 12)),
                        ),
                        Expanded(
                          child: Text('PRODUK',
                              style: TextStyle(color: Colors.white,
                                  fontWeight: FontWeight.bold, fontSize: 12)),
                        ),
                      ],
                    ),
                  ),
                  // Item cards
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: pesanan.detailPesanan!.length,
                    itemBuilder: (context, index) {
                      final item = pesanan.detailPesanan![index];
                      final harga = item.hargaSatuan ?? 0;
                      final qty = item.jumlah ?? 0;
                      final subtotal = qty * harga;
                      return Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 12),
                        decoration: BoxDecoration(
                          color: index.isEven
                              ? Colors.grey[50]
                              : Colors.white,
                          border: Border(
                            bottom: BorderSide(
                                color: Colors.grey[200]!, width: 1),
                          ),
                        ),
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Nomor
                            SizedBox(
                              width: 28,
                              child: Padding(
                                padding: const EdgeInsets.only(top: 14),
                                child: Text(
                                  '${index + 1}',
                                  style: const TextStyle(
                                      fontWeight: FontWeight.w600,
                                      fontSize: 13,
                                      color: Color(0xFF1e40af)),
                                ),
                              ),
                            ),
                            // Thumbnail
                            Container(
                              width: 52,
                              height: 52,
                              margin: const EdgeInsets.only(right: 12),
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(
                                    color: const Color(0xFFe0e7ff), width: 1),
                                color: const Color(0xFFF3F4F6),
                              ),
                              child: ClipRRect(
                                borderRadius: BorderRadius.circular(7),
                                child: item.produk?.fotoUrl != null
                                    ? Image.network(
                                        item.produk!.fotoUrl!,
                                        fit: BoxFit.cover,
                                        errorBuilder: (_, __, ___) =>
                                            _buildDetailPlaceholder(
                                                item.produk!.nama),
                                      )
                                    : _buildDetailPlaceholder(
                                        item.produk?.nama ?? '?'),
                              ),
                            ),
                            // Info produk + baris harga
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    item.produk?.nama ?? 'Unknown',
                                    style: const TextStyle(
                                        fontWeight: FontWeight.w600,
                                        fontSize: 13,
                                        color: Color(0xFF1f2937)),
                                    maxLines: 2,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                  if (item.spesifikasi != null &&
                                      item.spesifikasi!.isNotEmpty) ...[
                                    const SizedBox(height: 2),
                                    Text(
                                      item.spesifikasi!,
                                      style: TextStyle(
                                          fontSize: 11,
                                          color: Colors.grey[600]),
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                    ),
                                  ],
                                  const SizedBox(height: 8),
                                  // Baris: QTY × Harga = Subtotal
                                  Wrap(
                                    spacing: 6,
                                    runSpacing: 4,
                                    children: [
                                      // QTY badge
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 8, vertical: 3),
                                        decoration: BoxDecoration(
                                          color: const Color(0xFFdbeafe),
                                          borderRadius:
                                              BorderRadius.circular(6),
                                        ),
                                        child: Text(
                                          'QTY: $qty',
                                          style: const TextStyle(
                                              fontSize: 11,
                                              fontWeight: FontWeight.w700,
                                              color: Color(0xFF1d4ed8)),
                                        ),
                                      ),
                                      // Harga satuan badge
                                      Container(
                                        padding: const EdgeInsets.symmetric(
                                            horizontal: 8, vertical: 3),
                                        decoration: BoxDecoration(
                                          color: const Color(0xFFF1F5F9),
                                          borderRadius:
                                              BorderRadius.circular(6),
                                        ),
                                        child: Text(
                                          'Harga: Rp ${formatter.format(harga)}',
                                          style: const TextStyle(
                                              fontSize: 11,
                                              fontWeight: FontWeight.w600,
                                              color: Color(0xFF475569)),
                                        ),
                                      ),
                                    ],
                                  ),
                                  const SizedBox(height: 6),
                                  // Subtotal row
                                  Row(
                                    children: [
                                      const Text(
                                        'Subtotal: ',
                                        style: TextStyle(
                                            fontSize: 11,
                                            color: Color(0xFF6B7280),
                                            fontWeight: FontWeight.w500),
                                      ),
                                      Text(
                                        'Rp ${formatter.format(subtotal)}',
                                        style: const TextStyle(
                                          fontSize: 13,
                                          fontWeight: FontWeight.w800,
                                          color: Color(0xFF10b981),
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
                  ),
                  const SizedBox(height: 12),
                  // Total Section
                  Container(
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: const Color(0xFF1e40af),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'TOTAL PESANAN',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 14,
                            color: Colors.white,
                          ),
                        ),
                        Text(
                          'Rp ${formatter.format(total)}',
                          style: const TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 18,
                            color: Colors.white,
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 32),
                ],

                // Histori Status (mirip section "Histori Status" di web)
                _buildHistoriStatusSection(pesanan),
                const SizedBox(height: 24),

                // Action Buttons
                if (_isUpdatingStatus)
                  Container(
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    alignment: Alignment.center,
                    child: const Row(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        SizedBox(
                          width: 18,
                          height: 18,
                          child: CircularProgressIndicator(strokeWidth: 2),
                        ),
                        SizedBox(width: 12),
                        Text(
                          'Memproses status & mengirim notifikasi WhatsApp...',
                          style: TextStyle(fontSize: 13, color: Color(0xFF64748b)),
                        ),
                      ],
                    ),
                  )
                else
                  SizedBox(
                    width: double.infinity,
                    child: FilledButton.icon(
                      onPressed: () => Navigator.pop(context),
                      icon: const Icon(Icons.arrow_back, size: 16),
                      label: const Text('Kembali'),
                      style: FilledButton.styleFrom(
                        backgroundColor: const Color(0xFF2563eb),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                    ),
                  ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Row(
      children: [
        Container(
          width: 3,
          height: 14,
          decoration: BoxDecoration(
            color: const Color(0xFF2563eb),
            borderRadius: BorderRadius.circular(2),
          ),
        ),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 13,
            fontWeight: FontWeight.bold,
            color: Color(0xFF1e40af),
            letterSpacing: 0.3,
          ),
        ),
      ],
    );
  }

  BoxDecoration _cardDecoration({Color? background}) {
    return BoxDecoration(
      color: background ?? Colors.white,
      border: Border.all(color: const Color(0xFFe5e7eb)),
      borderRadius: BorderRadius.circular(14),
      boxShadow: [
        BoxShadow(
          color: Colors.black.withValues(alpha: 0.03),
          blurRadius: 8,
          offset: const Offset(0, 2),
        ),
      ],
    );
  }

  Widget _buildInfoSection({
    required String title,
    required dynamic pesanan,
    required DateFormat dateFormat,
  }) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: _cardDecoration(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildSectionTitle(title),
          const SizedBox(height: 16),
          _buildDetailRow('Nomor PO', pesanan.nomorPo ?? '-'),
          const SizedBox(height: 12),
          _buildDetailRow(
            'Tanggal Pesanan',
            pesanan.tanggalPesanan != null
                ? dateFormat.format(pesanan.tanggalPesanan!)
                : '-',
          ),
          const SizedBox(height: 12),
          if (pesanan.tanggalPengiriman != null)
            _buildDetailRow(
              'Tanggal Pengiriman',
              dateFormat.format(pesanan.tanggalPengiriman!),
            ),
          if (pesanan.tanggalPengiriman != null) const SizedBox(height: 12),
          _buildDetailRow('Status', pesanan.status ?? '-'),
        ],
      ),
    );
  }

  Widget _buildCustomerSection(dynamic pesanan) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: _cardDecoration(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildSectionTitle('PELANGGAN'),
          const SizedBox(height: 16),
          if (pesanan.pelanggan != null) ...[
            _buildDetailRow('Nama', pesanan.pelanggan!.nama ?? '-'),
            const SizedBox(height: 12),
            _buildDetailRow('Telepon', pesanan.pelanggan!.telepon ?? '-'),
            const SizedBox(height: 12),
            _buildDetailRow('Email', pesanan.pelanggan!.email ?? '-'),
            const SizedBox(height: 12),
            _buildDetailRow('Alamat', pesanan.pelanggan!.alamat ?? '-'),
          ] else
            const Text(
              'Data pelanggan tidak tersedia',
              style: TextStyle(fontSize: 12, color: Colors.grey),
            ),
        ],
      ),
    );
  }

  Widget _buildSummarySection({
    required dynamic pesanan,
    required double total,
    required NumberFormat formatter,
    required DateFormat dateFormat,
  }) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: _cardDecoration(background: const Color(0xFFF0F7FF)),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildSectionTitle('RINGKASAN'),
          const SizedBox(height: 16),
          _buildSummaryRow(
            'Total Item',
            '${pesanan.detailPesanan?.length ?? 0}',
          ),
          const SizedBox(height: 12),
          _buildSummaryRow(
            'Total Nilai',
            'Rp ${formatter.format(total)}',
            isBold: true,
            color: const Color(0xFF1e40af),
          ),
          const SizedBox(height: 12),
          _buildDetailRow('Disimpan oleh', pesanan.creator?.name ?? 'Sistem'),
          const SizedBox(height: 12),
          _buildDetailRow(
            'PO dibuat',
            pesanan.createdAt != null
                ? dateFormat.format(pesanan.createdAt!)
                : '-',
          ),
        ],
      ),
    );
  }

  Widget _buildHistoriStatusSection(dynamic pesanan) {
    final dateTimeFormat = DateFormat('dd MMM yyyy HH:mm', 'id_ID');
    final List historiList = pesanan.historiStatus ?? const [];

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(18),
      decoration: _cardDecoration(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _buildSectionTitle('HISTORI STATUS'),
          const SizedBox(height: 16),
          if (historiList.isEmpty)
            Text(
              'Belum ada histori status.',
              style: TextStyle(fontSize: 13, color: Colors.grey[600]),
            )
          else
            for (int i = 0; i < historiList.length; i++) ...[
              if (i > 0) const Divider(height: 24),
              _buildHistoriStatusItem(historiList[i], dateTimeFormat),
            ],
        ],
      ),
    );
  }

  Widget _buildHistoriStatusItem(dynamic history, DateFormat dateTimeFormat) {
    String? createdAtText;
    try {
      if (history.createdAt != null) {
        createdAtText = '${dateTimeFormat.format(history.createdAt!)} WIB';
      }
    } catch (_) {
      createdAtText = null;
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Expanded(
              child: Text(
                history.getStatusLabel(),
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 14,
                  color: Color(0xFF1f2937),
                ),
              ),
            ),
            if (createdAtText != null)
              Text(
                createdAtText,
                style: TextStyle(fontSize: 11, color: Colors.grey[600]),
              ),
          ],
        ),
        if (history.keterangan != null &&
            history.keterangan.toString().isNotEmpty) ...[
          const SizedBox(height: 6),
          Text(
            history.keterangan,
            style: TextStyle(fontSize: 13, color: Colors.grey[700]),
          ),
        ],
        const SizedBox(height: 6),
        Text(
          'oleh ${history.user?.name ?? 'Sistem'}',
          style: TextStyle(fontSize: 12, color: Colors.grey[600]),
        ),
      ],
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: 11,
            color: Colors.grey[600],
            fontWeight: FontWeight.w500,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w500),
        ),
      ],
    );
  }

  Widget _buildSummaryRow(
    String label,
    String value, {
    bool isBold = false,
    Color color = Colors.black,
  }) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey)),
        Text(
          value,
          style: TextStyle(
            fontSize: 13,
            fontWeight: isBold ? FontWeight.bold : FontWeight.w500,
            color: color,
          ),
        ),
      ],
    );
  }

  Future<void> _deletePesanan() async {
    setState(() {
      _isUpdatingStatus = true;
    });

    final pesananProvider = Provider.of<PesananProvider>(
      context,
      listen: false,
    );
    final success = await pesananProvider.deletePesanan(widget.pesananId);

    if (!mounted) return;
    if (success) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Pesanan berhasil dihapus')));
      Navigator.pop(context);
    } else {
      setState(() {
        _isUpdatingStatus = false;
      });
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Gagal menghapus pesanan')));
    }
  }

  Widget _buildDetailPlaceholder(String productName) {
    return Container(
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFFdbeafe), Color(0xFFbfdbfe)],
        ),
      ),
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(
              Icons.image_not_supported_rounded,
              size: 20,
              color: Color(0xFF0284c7),
            ),
            Text(
              productName.substring(0, 1),
              style: const TextStyle(
                fontSize: 9,
                fontWeight: FontWeight.bold,
                color: Color(0xFF0284c7),
              ),
            ),
          ],
        ),
      ),
    );
  }
}