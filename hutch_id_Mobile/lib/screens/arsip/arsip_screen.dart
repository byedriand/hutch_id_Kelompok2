import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../models/pesanan.dart';
import '../../providers/pesanan_provider.dart';
import '../../widgets/custom_widgets.dart';

class ArsipScreen extends StatefulWidget {
  const ArsipScreen({super.key});

  @override
  State<ArsipScreen> createState() => _ArsipScreenState();
}

class _ArsipScreenState extends State<ArsipScreen>
    with TickerProviderStateMixin {
  late AnimationController _animationController;
  final TextEditingController _searchController = TextEditingController();
  String _selectedStatus = 'semua'; // semua, selesai, dibatalkan
  bool _isTableView = false; // Toggle between card and table view
  final dateFormat = DateFormat('dd MMM yyyy', 'id_ID');
  final numberFormat = NumberFormat('#,##0', 'id_ID');

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    );
    _animationController.forward();
    _searchController.addListener(_filterArsip);

    // Load pesanan data
    Future.microtask(() {
      if (mounted) {
        Provider.of<PesananProvider>(context, listen: false).fetchPesanan();
      }
    });
  }

  @override
  void dispose() {
    _animationController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  void _filterArsip() {
    setState(() {});
  }

  List<Pesanan> _getFilteredArsip(List<Pesanan> pesananList) {
    return pesananList.where((pesanan) {
      final matchesStatus =
          _selectedStatus == 'semua' || pesanan.status == _selectedStatus;

      final matchesSearch =
          _searchController.text.isEmpty ||
          (pesanan.nomorPo?.toLowerCase().contains(
                _searchController.text.toLowerCase(),
              ) ??
              false) ||
          (pesanan.pelanggan?.nama.toLowerCase().contains(
                _searchController.text.toLowerCase(),
              ) ??
              false);

      // Only show selesai and dibatalkan orders
      final isArchived =
          pesanan.status == 'selesai' || pesanan.status == 'dibatalkan';

      return matchesStatus && matchesSearch && isArchived;
    }).toList();
  }

  String _getStatusText(String? status) {
    switch (status) {
      case 'selesai':
        return 'Selesai';
      case 'dibatalkan':
        return 'Dibatalkan';
      default:
        return status ?? '-';
    }
  }

  Color _getStatusColor(String? status) {
    switch (status) {
      case 'selesai':
        return const Color(0xFF22c55e);
      case 'dibatalkan':
        return const Color(0xFFef4444);
      default:
        return Colors.grey;
    }
  }

  String _getMainProduct(Pesanan pesanan) {
    if (pesanan.detailPesanan?.isEmpty ?? true) {
      return '-';
    }
    final firstItem = pesanan.detailPesanan!.first;
    if (pesanan.detailPesanan!.length > 1) {
      return '${firstItem.produk?.nama} +${pesanan.detailPesanan!.length - 1} item lainnya';
    }
    return firstItem.produk?.nama ?? '-';
  }

  Future<void> _downloadPDF(Pesanan pesanan) async {
    final scaffoldMessenger = ScaffoldMessenger.of(context);
    try {
      final pdfUrl = Uri.parse(
        'http://localhost:8082/pesanan/${pesanan.id}/pdf',
      );
      if (await canLaunchUrl(pdfUrl)) {
        await launchUrl(pdfUrl, mode: LaunchMode.externalApplication);
        scaffoldMessenger.showSnackBar(
          SnackBar(content: Text('Membuka PDF: ${pesanan.nomorPo}')),
        );
      } else {
        scaffoldMessenger.showSnackBar(
          const SnackBar(content: Text('Tidak dapat membuka URL')),
        );
      }
    } catch (e) {
      scaffoldMessenger.showSnackBar(SnackBar(content: Text('Error: $e')));
    }
  }

  Future<void> _viewPDF(Pesanan pesanan) async {
    await _downloadPDF(pesanan);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Arsip'),
        elevation: 0,
        backgroundColor: const Color(0xFF2563eb),
        foregroundColor: Colors.white,
      ),
      body: Consumer<PesananProvider>(
        builder: (context, pesananProvider, _) {
          if (pesananProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat arsip...');
          }

          final arsipList = _getFilteredArsip(pesananProvider.pesananList);

          if (arsipList.isEmpty) {
            return Center(
              child: EmptyStateWidget(
                message: _searchController.text.isNotEmpty
                    ? 'Tidak ada hasil pencarian'
                    : 'Belum ada arsip',
                icon: Icons.archive,
                onRetry: () {
                  pesananProvider.fetchPesanan();
                },
              ),
            );
          }

          return Column(
            children: [
              // Search and Filter
              Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  children: [
                    // Search Bar
                    TextField(
                      controller: _searchController,
                      decoration: InputDecoration(
                        hintText: 'Cari Nomor PO atau Pelanggan',
                        prefixIcon: const Icon(Icons.search),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(10),
                          borderSide: const BorderSide(
                            color: Color(0xFFe5e7eb),
                          ),
                        ),
                        contentPadding: const EdgeInsets.symmetric(
                          horizontal: 14,
                          vertical: 12,
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    // Filter Chips and View Toggle
                    Row(
                      children: [
                        // Status Filter
                        Expanded(
                          child: SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            child: Row(
                              children: ['semua', 'selesai', 'dibatalkan'].map((
                                status,
                              ) {
                                final isSelected = _selectedStatus == status;
                                return Padding(
                                  padding: const EdgeInsets.only(right: 8),
                                  child: FilterChip(
                                    label: Text(
                                      status == 'semua'
                                          ? 'Semua'
                                          : status == 'selesai'
                                          ? 'Selesai'
                                          : 'Dibatalkan',
                                    ),
                                    selected: isSelected,
                                    onSelected: (value) {
                                      setState(() {
                                        _selectedStatus = status;
                                      });
                                    },
                                    backgroundColor: Colors.white,
                                    selectedColor: const Color(0xFF2563eb),
                                    labelStyle: TextStyle(
                                      color: isSelected
                                          ? Colors.white
                                          : Colors.grey[700],
                                    ),
                                  ),
                                );
                              }).toList(),
                            ),
                          ),
                        ),
                        // View Toggle
                        IconButton(
                          onPressed: () {
                            setState(() {
                              _isTableView = false;
                            });
                          },
                          icon: const Icon(Icons.view_agenda),
                          color: !_isTableView
                              ? const Color(0xFF2563eb)
                              : Colors.grey,
                          tooltip: 'Card View',
                        ),
                        IconButton(
                          onPressed: () {
                            setState(() {
                              _isTableView = true;
                            });
                          },
                          icon: const Icon(Icons.table_chart),
                          color: _isTableView
                              ? const Color(0xFF2563eb)
                              : Colors.grey,
                          tooltip: 'Table View',
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              // Content
              Expanded(
                child: _isTableView
                    ? _buildTableView(arsipList)
                    : _buildCardView(arsipList),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildCardView(List<Pesanan> arsipList) {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: arsipList.length,
      itemBuilder: (context, index) {
        final pesanan = arsipList[index];
        final animation =
            Tween<Offset>(
              begin: const Offset(0.3, 0),
              end: Offset.zero,
            ).animate(
              CurvedAnimation(
                parent: _animationController,
                curve: Interval(
                  (index / arsipList.length) * 0.5,
                  ((index + 1) / arsipList.length),
                  curve: Curves.easeOut,
                ),
              ),
            );

        return SlideTransition(
          position: animation,
          child: Container(
            margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(14),
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
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              pesanan.nomorPo ?? '-',
                              style: const TextStyle(
                                fontSize: 14,
                                fontWeight: FontWeight.w800,
                                color: Color(0xFF0c2340),
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              pesanan.pelanggan?.nama ?? 'Unknown',
                              style: TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                                color: Colors.grey[700],
                              ),
                            ),
                          ],
                        ),
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 6,
                        ),
                        decoration: BoxDecoration(
                          color: _getStatusColor(
                            pesanan.status,
                          ).withValues(alpha: 0.1),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(
                            color: _getStatusColor(
                              pesanan.status,
                            ).withValues(alpha: 0.3),
                          ),
                        ),
                        child: Text(
                          _getStatusText(pesanan.status),
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w700,
                            color: _getStatusColor(pesanan.status),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Container(height: 1, color: const Color(0xFFe5e7eb)),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            'Tanggal',
                            style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: Colors.grey[600],
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            pesanan.tanggalPesanan != null
                                ? dateFormat.format(pesanan.tanggalPesanan!)
                                : '-',
                            style: const TextStyle(
                              fontSize: 12,
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
                            'Rp ${numberFormat.format(pesanan.totalNilai ?? 0)}',
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
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Produk Utama',
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w600,
                                color: Colors.grey[600],
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              _getMainProduct(pesanan),
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                                color: Color(0xFF0c2340),
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Tooltip(
                        message: 'Download PDF',
                        child: IconButton(
                          onPressed: () => _downloadPDF(pesanan),
                          icon: const Icon(Icons.download),
                          color: const Color(0xFF2563eb),
                          iconSize: 20,
                          padding: EdgeInsets.zero,
                          constraints: const BoxConstraints(),
                        ),
                      ),
                      Tooltip(
                        message: 'View PDF',
                        child: IconButton(
                          onPressed: () => _viewPDF(pesanan),
                          icon: const Icon(Icons.visibility),
                          color: const Color(0xFF2563eb),
                          iconSize: 20,
                          padding: EdgeInsets.zero,
                          constraints: const BoxConstraints(),
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
  }

  Widget _buildTableView(List<Pesanan> arsipList) {
    return SingleChildScrollView(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: DataTable(
            columns: const [
              DataColumn(
                label: Expanded(
                  child: Text(
                    'NOMOR PO',
                    style: TextStyle(
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
              DataColumn(
                label: Expanded(
                  child: Text(
                    'TANGGAL',
                    style: TextStyle(
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
              DataColumn(
                label: Expanded(
                  child: Text(
                    'PELANGGAN',
                    style: TextStyle(
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
              DataColumn(
                label: Expanded(
                  child: Text(
                    'STATUS',
                    style: TextStyle(
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
              DataColumn(
                label: Expanded(
                  child: Text(
                    'AKSI',
                    style: TextStyle(
                      fontWeight: FontWeight.w700,
                      color: Colors.white,
                    ),
                  ),
                ),
              ),
            ],
            headingRowColor: WidgetStateProperty.all(const Color(0xFF2563eb)),
            rows: arsipList.asMap().entries.map((entry) {
              final index = entry.key;
              final pesanan = entry.value;
              final bgColor = index.isEven
                  ? Colors.white
                  : const Color(0xFFF9FAFB);

              return DataRow(
                color: WidgetStateProperty.all(bgColor),
                cells: [
                  DataCell(Text(pesanan.nomorPo ?? '-')),
                  DataCell(
                    Text(
                      pesanan.tanggalPesanan != null
                          ? dateFormat.format(pesanan.tanggalPesanan!)
                          : '-',
                    ),
                  ),
                  DataCell(
                    Text(
                      pesanan.pelanggan?.nama ?? '-',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  DataCell(
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: _getStatusColor(pesanan.status).withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(
                        _getStatusText(pesanan.status),
                        style: TextStyle(
                          fontSize: 11,
                          fontWeight: FontWeight.w700,
                          color: _getStatusColor(pesanan.status),
                        ),
                      ),
                    ),
                  ),
                  DataCell(
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Tooltip(
                          message: 'Download',
                          child: IconButton(
                            onPressed: () => _downloadPDF(pesanan),
                            icon: const Icon(Icons.download),
                            color: const Color(0xFF2563eb),
                            iconSize: 18,
                            padding: EdgeInsets.zero,
                            constraints: const BoxConstraints(),
                          ),
                        ),
                        const SizedBox(width: 4),
                        Tooltip(
                          message: 'View',
                          child: IconButton(
                            onPressed: () => _viewPDF(pesanan),
                            icon: const Icon(Icons.visibility),
                            color: const Color(0xFF2563eb),
                            iconSize: 18,
                            padding: EdgeInsets.zero,
                            constraints: const BoxConstraints(),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              );
            }).toList(),
          ),
        ),
      ),
    );
  }
}
