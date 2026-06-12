import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../providers/pesanan_provider.dart';
import '../../widgets/custom_widgets.dart';

class PesananDetailScreen extends StatefulWidget {
  final int pesananId;

  const PesananDetailScreen({super.key, required this.pesananId});

  @override
  State<PesananDetailScreen> createState() => _PesananDetailScreenState();
}

class _PesananDetailScreenState extends State<PesananDetailScreen> {
  late String _selectedStatus;
  bool _isUpdatingStatus = false;

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
    try {
      final pdfUrl = Uri.parse('http://localhost:8082/pesanan/$pesananId/pdf');
      if (await canLaunchUrl(pdfUrl)) {
        await launchUrl(pdfUrl, mode: LaunchMode.externalApplication);
        if (mounted) {
          ScaffoldMessenger.of(
            context,
          ).showSnackBar(SnackBar(content: Text('Membuka PDF: $nomorPo')));
        }
      } else {
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Tidak dapat membuka URL')),
          );
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text('Error: $e')));
      }
    }
  }

  void _showStatusDialog() {
    final statuses = [
      'menunggu_konfirmasi',
      'dikonfirmasi',
      'dalam_produksi',
      'siap_kirim',
      'selesai',
      'dibatalkan',
    ];
    final statusLabels = [
      'Menunggu Konfirmasi',
      'Dikonfirmasi',
      'Dalam Produksi',
      'Siap Kirim',
      'Selesai',
      'Dibatalkan',
    ];

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Ubah Status Pesanan'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: List.generate(
            statuses.length,
            (index) => RadioListTile(
              title: Text(statusLabels[index]),
              value: statuses[index],
              // ignore: deprecated_member_use
              groupValue: _selectedStatus,
              // ignore: deprecated_member_use
              onChanged: (value) {
                setState(() {
                  _selectedStatus = value!;
                });
                Navigator.pop(context);
                _updateStatus();
              },
            ),
          ),
        ),
      ),
    );
  }

  Future<void> _updateStatus() async {
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
    );

    setState(() {
      _isUpdatingStatus = false;
    });

    if (!mounted) return;
    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Status pesanan berhasil diubah')),
      );
      // Refresh detail
      pesananProvider.getPesananDetail(widget.pesananId);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Gagal mengubah status pesanan')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
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
                // Header with Action Buttons
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          pesanan.nomorPo ?? 'N/A',
                          style: const TextStyle(
                            fontSize: 28,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF1e40af),
                          ),
                        ),
                        const SizedBox(height: 4),
                        StatusBadge(status: pesanan.status ?? 'unknown'),
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        OutlinedButton.icon(
                          onPressed: () => _downloadPDF(
                            pesanan.id!,
                            pesanan.nomorPo ?? 'Unknown',
                          ),
                          icon: const Icon(Icons.download, size: 16),
                          label: const Text('Unduh PDF'),
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(
                              color: Color(0xFF2563eb),
                              width: 1.5,
                            ),
                            foregroundColor: const Color(0xFF2563eb),
                          ),
                        ),
                        const SizedBox(height: 8),
                        OutlinedButton.icon(
                          onPressed: _showStatusDialog,
                          icon: const Icon(Icons.edit, size: 16),
                          label: const Text('Edit'),
                          style: OutlinedButton.styleFrom(
                            side: const BorderSide(
                              color: Color(0xFFf97316),
                              width: 1.5,
                            ),
                            foregroundColor: const Color(0xFFf97316),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
                const SizedBox(height: 32),

                // 3-Column Layout
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
                else
                  Column(
                    children: [
                      _buildInfoSection(
                        title: 'INFORMASI PO',
                        pesanan: pesanan,
                        dateFormat: dateFormat,
                      ),
                      const SizedBox(height: 20),
                      _buildCustomerSection(pesanan),
                      const SizedBox(height: 20),
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
                  const Text(
                    'DETAIL PRODUK',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF1e40af),
                    ),
                  ),
                  const SizedBox(height: 12),
                  // Table Header
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 12,
                    ),
                    decoration: BoxDecoration(
                      color: const Color(0xFF2563eb),
                      borderRadius: const BorderRadius.only(
                        topLeft: Radius.circular(8),
                        topRight: Radius.circular(8),
                      ),
                    ),
                    child: Row(
                      children: [
                        SizedBox(
                          width: 35,
                          child: Text(
                            '#',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                          ),
                        ),
                        Expanded(
                          flex: 3,
                          child: Text(
                            'PRODUK',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                          ),
                        ),
                        Expanded(
                          child: Text(
                            'QTY',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                            textAlign: TextAlign.center,
                          ),
                        ),
                        Expanded(
                          flex: 2,
                          child: Text(
                            'HARGA',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                            textAlign: TextAlign.right,
                          ),
                        ),
                        Expanded(
                          flex: 2,
                          child: Text(
                            'SUBTOTAL',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.bold,
                              fontSize: 12,
                            ),
                            textAlign: TextAlign.right,
                          ),
                        ),
                      ],
                    ),
                  ),
                  // Table Items
                  ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: pesanan.detailPesanan!.length,
                    itemBuilder: (context, index) {
                      final item = pesanan.detailPesanan![index];
                      final subtotal =
                          (item.jumlah ?? 0) * (item.hargaSatuan ?? 0);
                      return Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 12,
                        ),
                        decoration: BoxDecoration(
                          color: index.isEven ? Colors.grey[50] : Colors.white,
                          border: Border(
                            bottom: BorderSide(
                              color: Colors.grey[200]!,
                              width: 1,
                            ),
                          ),
                        ),
                        child: Row(
                          children: [
                            SizedBox(
                              width: 35,
                              child: Text(
                                '${index + 1}',
                                style: const TextStyle(
                                  fontWeight: FontWeight.w500,
                                  fontSize: 13,
                                ),
                              ),
                            ),
                            Expanded(
                              flex: 3,
                              child: Row(
                                children: [
                                  // Product Image Thumbnail
                                  Container(
                                    width: 45,
                                    height: 45,
                                    margin: const EdgeInsets.only(right: 10),
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(8),
                                      border: Border.all(
                                        color: const Color(0xFFe0e7ff),
                                        width: 1,
                                      ),
                                      color: const Color(0xFFF3F4F6),
                                    ),
                                    child: ClipRRect(
                                      borderRadius: BorderRadius.circular(7),
                                      child:
                                          item.produk?.foto != null &&
                                              item.produk!.foto!.isNotEmpty
                                          ? Image.network(
                                              item.produk!.foto!,
                                              fit: BoxFit.cover,
                                              errorBuilder:
                                                  (context, error, stackTrace) {
                                                    return _buildDetailPlaceholder(
                                                      item.produk!.nama,
                                                    );
                                                  },
                                            )
                                          : _buildDetailPlaceholder(
                                              item.produk!.nama,
                                            ),
                                    ),
                                  ),
                                  // Product Info
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          item.produk?.nama ?? 'Unknown',
                                          style: const TextStyle(
                                            fontWeight: FontWeight.w600,
                                            fontSize: 13,
                                          ),
                                          maxLines: 2,
                                          overflow: TextOverflow.ellipsis,
                                        ),
                                        if (item.spesifikasi != null &&
                                            item.spesifikasi!.isNotEmpty)
                                          Text(
                                            item.spesifikasi!,
                                            style: TextStyle(
                                              fontSize: 11,
                                              color: Colors.grey[600],
                                            ),
                                            maxLines: 1,
                                            overflow: TextOverflow.ellipsis,
                                          ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            Expanded(
                              child: Text(
                                '${item.jumlah ?? 0}',
                                textAlign: TextAlign.center,
                                style: const TextStyle(
                                  fontWeight: FontWeight.w500,
                                  fontSize: 13,
                                ),
                              ),
                            ),
                            Expanded(
                              flex: 2,
                              child: Text(
                                'Rp ${formatter.format(item.hargaSatuan ?? 0)}',
                                style: const TextStyle(fontSize: 11),
                                textAlign: TextAlign.right,
                              ),
                            ),
                            Expanded(
                              flex: 2,
                              child: Text(
                                'Rp ${formatter.format(subtotal)}',
                                style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                  fontSize: 12,
                                  color: Color(0xFF10b981),
                                ),
                                textAlign: TextAlign.right,
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

                // Action Buttons
                if (!_isUpdatingStatus)
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton.icon(
                          onPressed: () {
                            showDialog(
                              context: context,
                              builder: (context) => AlertDialog(
                                title: const Text('Hapus Pesanan'),
                                content: const Text(
                                  'Apakah Anda yakin ingin menghapus pesanan ini?',
                                ),
                                actions: [
                                  TextButton(
                                    onPressed: () => Navigator.pop(context),
                                    child: const Text('Batal'),
                                  ),
                                  TextButton(
                                    onPressed: () {
                                      Navigator.pop(context);
                                      _deletePesanan();
                                    },
                                    child: const Text(
                                      'Hapus',
                                      style: TextStyle(color: Colors.red),
                                    ),
                                  ),
                                ],
                              ),
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
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: FilledButton.icon(
                          onPressed: () => Navigator.pop(context),
                          icon: const Icon(Icons.arrow_back, size: 16),
                          label: const Text('Kembali'),
                          style: FilledButton.styleFrom(
                            backgroundColor: const Color(0xFF2563eb),
                          ),
                        ),
                      ),
                    ],
                  )
                else
                  const Center(child: CircularProgressIndicator()),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildInfoSection({
    required String title,
    required dynamic pesanan,
    required DateFormat dateFormat,
  }) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        border: Border.all(color: Colors.grey[300]!),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: const TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.bold,
              color: Color(0xFF1e40af),
            ),
          ),
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
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        border: Border.all(color: Colors.grey[300]!),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'PELANGGAN',
            style: TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.bold,
              color: Color(0xFF1e40af),
            ),
          ),
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
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFFF0F7FF),
        border: Border.all(color: Colors.grey[300]!),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'RINGKASAN',
            style: TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.bold,
              color: Color(0xFF1e40af),
            ),
          ),
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
          _buildDetailRow('Disimpan oleh', 'Admin'),
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
