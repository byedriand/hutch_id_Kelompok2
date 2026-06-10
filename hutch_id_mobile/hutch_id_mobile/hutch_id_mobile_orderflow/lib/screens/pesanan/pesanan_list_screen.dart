import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/pesanan_provider.dart';
import '../../widgets/custom_widgets.dart';

class PesananListScreen extends StatefulWidget {
  const PesananListScreen({super.key});

  @override
  State<PesananListScreen> createState() => _PesananListScreenState();
}

class _PesananListScreenState extends State<PesananListScreen> {
  String _selectedStatus = '';

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      Provider.of<PesananProvider>(context, listen: false).fetchPesanan();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Pesanan'),
        elevation: 0,
        backgroundColor: const Color(0xFF1e40af),
        foregroundColor: Colors.white,
      ),
      body: Consumer<PesananProvider>(
        builder: (context, pesananProvider, _) {
          if (pesananProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat pesanan...');
          }

          if (pesananProvider.errorMessage != null) {
            return Center(
              child: EmptyStateWidget(
                message: pesananProvider.errorMessage!,
                onRetry: () {
                  pesananProvider.fetchPesanan();
                },
              ),
            );
          }

          final pesananList = _selectedStatus.isEmpty
              ? pesananProvider.pesananList
              : pesananProvider.pesananList
                    .where((p) => p.status == _selectedStatus)
                    .toList();

          if (pesananList.isEmpty) {
            return Center(
              child: EmptyStateWidget(
                message: _selectedStatus.isEmpty
                    ? 'Belum ada pesanan'
                    : 'Tidak ada pesanan dengan status ini',
                icon: Icons.shopping_bag_outlined,
              ),
            );
          }

          return Column(
            children: [
              // Filter Buttons - Enhanced
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 14,
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
                      _buildFilterChip(
                        label: 'Semua',
                        selected: _selectedStatus.isEmpty,
                        onTap: () {
                          setState(() {
                            _selectedStatus = '';
                          });
                        },
                      ),
                      const SizedBox(width: 10),
                      _buildFilterChip(
                        label: 'Menunggu',
                        selected: _selectedStatus == 'menunggu_konfirmasi',
                        onTap: () {
                          setState(() {
                            _selectedStatus = 'menunggu_konfirmasi';
                          });
                        },
                      ),
                      const SizedBox(width: 10),
                      _buildFilterChip(
                        label: 'Dikonfirmasi',
                        selected: _selectedStatus == 'dikonfirmasi',
                        onTap: () {
                          setState(() {
                            _selectedStatus = 'dikonfirmasi';
                          });
                        },
                      ),
                      const SizedBox(width: 10),
                      _buildFilterChip(
                        label: 'Produksi',
                        selected: _selectedStatus == 'dalam_produksi',
                        onTap: () {
                          setState(() {
                            _selectedStatus = 'dalam_produksi';
                          });
                        },
                      ),
                      const SizedBox(width: 10),
                      _buildFilterChip(
                        label: 'Siap Kirim',
                        selected: _selectedStatus == 'siap_kirim',
                        onTap: () {
                          setState(() {
                            _selectedStatus = 'siap_kirim';
                          });
                        },
                      ),
                      const SizedBox(width: 10),
                      _buildFilterChip(
                        label: 'Selesai',
                        selected: _selectedStatus == 'selesai',
                        onTap: () {
                          setState(() {
                            _selectedStatus = 'selesai';
                          });
                        },
                      ),
                      const SizedBox(width: 10),
                      _buildFilterChip(
                        label: 'Dibatalkan',
                        selected: _selectedStatus == 'dibatalkan',
                        onTap: () {
                          setState(() {
                            _selectedStatus = 'dibatalkan';
                          });
                        },
                      ),
                    ],
                  ),
                ),
              ),
              // Pesanan List
              Expanded(
                child: RefreshIndicator(
                  onRefresh: () =>
                      pesananProvider.fetchPesanan(status: _selectedStatus),
                  child: ListView.builder(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 16,
                    ),
                    itemCount: pesananList.length,
                    itemBuilder: (context, index) {
                      final pesanan = pesananList[index];
                      return _buildPesananCard(context, pesanan);
                    },
                  ),
                ),
              ),
            ],
          );
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () {
          Navigator.pushNamed(context, '/pesanan-form');
        },
        backgroundColor: const Color(0xFF1e40af),
        child: const Icon(Icons.add, color: Colors.white),
      ),
    );
  }

  Widget _buildFilterChip({
    required String label,
    required bool selected,
    required VoidCallback onTap,
  }) {
    return FilterChip(
      label: Text(label),
      selected: selected,
      onSelected: (_) => onTap(),
      backgroundColor: const Color(0xFFF0F5FF),
      selectedColor: const Color(0xFF1e40af),
      side: BorderSide(
        color: selected ? const Color(0xFF1e40af) : const Color(0xFFdbeafe),
        width: 1.5,
      ),
      labelStyle: TextStyle(
        color: selected ? Colors.white : const Color(0xFF1e40af),
        fontWeight: selected ? FontWeight.w700 : FontWeight.w600,
        fontSize: 12,
      ),
    );
  }

  Widget _buildPesananCard(context, pesanan) {
    final formatter = NumberFormat('#,##0', 'id_ID');
    final dateFormat = DateFormat('dd MMM yyyy');

    // Status color mapping
    Color statusColor = Colors.grey;
    Color statusBgColor = Colors.grey[100]!;
    IconData statusIcon = Icons.info;

    switch (pesanan.status) {
      case 'menunggu_konfirmasi':
        statusColor = Colors.orange[700]!;
        statusBgColor = Colors.orange[50]!;
        statusIcon = Icons.hourglass_empty_rounded;
        break;
      case 'dikonfirmasi':
        statusColor = Colors.blue[700]!;
        statusBgColor = Colors.blue[50]!;
        statusIcon = Icons.check_circle_rounded;
        break;
      case 'dalam_produksi':
        statusColor = Colors.indigo[700]!;
        statusBgColor = Colors.indigo[50]!;
        statusIcon = Icons.build_rounded;
        break;
      case 'siap_kirim':
        statusColor = Colors.green[700]!;
        statusBgColor = Colors.green[50]!;
        statusIcon = Icons.local_shipping_rounded;
        break;
      case 'selesai':
        statusColor = Colors.teal[700]!;
        statusBgColor = Colors.teal[50]!;
        statusIcon = Icons.done_all_rounded;
        break;
      case 'dibatalkan':
        statusColor = Colors.red[700]!;
        statusBgColor = Colors.red[50]!;
        statusIcon = Icons.cancel_rounded;
        break;
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: const Color(0xFFe0e7ff), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF3b82f6).withOpacity(0.08),
            blurRadius: 12,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: InkWell(
        onTap: () {
          // Navigate to pesanan detail page
          Navigator.pushNamed(
            context,
            '/pesanan-detail',
            arguments: pesanan.id,
          );
        },
        borderRadius: BorderRadius.circular(16),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header row: PO Number dan Status Badge
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'PO ${pesanan.nomorPo}',
                          style: const TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF0c2340),
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 4),
                        Text(
                          pesanan.pelanggan?.nama ?? 'Unknown',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.w500,
                            color: Colors.grey[700],
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(width: 12),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 8,
                    ),
                    decoration: BoxDecoration(
                      color: statusBgColor,
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: statusColor.withOpacity(0.3),
                        width: 1,
                      ),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(statusIcon, size: 16, color: statusColor),
                        const SizedBox(width: 6),
                        Text(
                          _getStatusText(pesanan.status),
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w700,
                            color: statusColor,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 14),
              // Product Images Preview
              if (pesanan.detailPesanan != null &&
                  pesanan.detailPesanan!.isNotEmpty) ...[
                SizedBox(
                  height: 60,
                  child: ListView.builder(
                    scrollDirection: Axis.horizontal,
                    itemCount: pesanan.detailPesanan!.length,
                    itemBuilder: (context, index) {
                      final item = pesanan.detailPesanan![index];
                      final hasImage =
                          item.produk?.foto != null &&
                          item.produk!.foto!.isNotEmpty;

                      return Container(
                        margin: EdgeInsets.only(
                          right: index < pesanan.detailPesanan!.length - 1
                              ? 10
                              : 0,
                        ),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(10),
                          border: Border.all(
                            color: const Color(0xFFe0e7ff),
                            width: 1,
                          ),
                          color: const Color(0xFFF3F4F6),
                        ),
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(9),
                          child: hasImage
                              ? Image.network(
                                  item.produk!.foto!,
                                  width: 60,
                                  height: 60,
                                  fit: BoxFit.cover,
                                  errorBuilder: (context, error, stackTrace) {
                                    return _buildPlaceholderImage(
                                      item.produk!.nama,
                                    );
                                  },
                                )
                              : _buildPlaceholderImage(item.produk!.nama),
                        ),
                      );
                    },
                  ),
                ),
                const SizedBox(height: 14),
              ],
              // Divider
              Container(height: 1, color: const Color(0xFFe5e7eb)),
              const SizedBox(height: 14),
              // Info row: Date dan Total
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Tanggal PO',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Colors.grey[600],
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        dateFormat.format(
                          pesanan.tanggalPesanan ?? DateTime.now(),
                        ),
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: Color(0xFF0c2340),
                        ),
                      ),
                    ],
                  ),
                  Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        'Total Nilai',
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w600,
                          color: Colors.grey[600],
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Rp ${formatter.format(pesanan.totalNilai ?? 0)}',
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF22c55e),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              if (pesanan.catatan != null && pesanan.catatan!.isNotEmpty) ...[
                const SizedBox(height: 14),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF0F9FF),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(
                      color: const Color(0xFF93c5fd),
                      width: 1,
                    ),
                  ),
                  child: Row(
                    children: [
                      Icon(
                        Icons.info_outline_rounded,
                        size: 18,
                        color: const Color(0xFF1e40af),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          pesanan.catatan ?? '',
                          style: const TextStyle(
                            fontSize: 12,
                            color: Color(0xFF0c2340),
                            fontWeight: FontWeight.w500,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
              const SizedBox(height: 14),
              // Detail Button
              Container(
                width: double.infinity,
                decoration: BoxDecoration(
                  color: const Color(0xFF1e40af),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Material(
                  color: Colors.transparent,
                  child: InkWell(
                    onTap: () {
                      Navigator.pushNamed(
                        context,
                        '/pesanan-detail',
                        arguments: pesanan.id,
                      );
                    },
                    borderRadius: BorderRadius.circular(10),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(vertical: 10),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          const Icon(
                            Icons.info_outline_rounded,
                            size: 18,
                            color: Colors.white,
                          ),
                          const SizedBox(width: 8),
                          const Text(
                            'Detail Pesanan',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w700,
                              fontSize: 13,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildPlaceholderImage(String productName) {
    return Container(
      width: 60,
      height: 60,
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFFdbeafe), Color(0xFFbfdbfe)],
        ),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const Icon(
            Icons.image_not_supported_rounded,
            size: 24,
            color: Color(0xFF0284c7),
          ),
          const SizedBox(height: 2),
          Text(
            productName.substring(0, 1),
            style: const TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.bold,
              color: Color(0xFF0284c7),
            ),
          ),
        ],
      ),
    );
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'menunggu_konfirmasi':
        return 'Menunggu';
      case 'dikonfirmasi':
        return 'Dikonfirmasi';
      case 'dalam_produksi':
        return 'Produksi';
      case 'siap_kirim':
        return 'Siap Kirim';
      case 'selesai':
        return 'Selesai';
      case 'dibatalkan':
        return 'Dibatalkan';
      default:
        return status;
    }
  }
}
