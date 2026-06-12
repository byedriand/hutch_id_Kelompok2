import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/arsip_pdf.dart';
import '../../services/api_service.dart';
import '../../widgets/custom_widgets.dart';

class ArsipScreen extends StatefulWidget {
  const ArsipScreen({super.key});

  @override
  State<ArsipScreen> createState() => _ArsipScreenState();
}

class _ArsipScreenState extends State<ArsipScreen>
    with TickerProviderStateMixin {
  late Future<List<ArsipPdf>> _arsipFuture;
  final ApiService _apiService = ApiService();
  late AnimationController _animationController;
  final TextEditingController _searchController = TextEditingController();
  String _selectedStatus = 'semua'; // semua, selesai, dibatalkan
  final List<ArsipPdf> _filteredArsipList = [];
  bool _isTableView = false; // Toggle between card and table view

  @override
  void initState() {
    super.initState();
    _animationController = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    );
    _arsipFuture = _apiService.getArsipPdf();
    _animationController.forward();
    _searchController.addListener(_filterArsip);
  }

  @override
  void dispose() {
    _animationController.dispose();
    _searchController.dispose();
    super.dispose();
  }

  void _filterArsip() {
    setState(() {
      // Filter logic will be applied in build
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Arsip PDF'),
        elevation: 0,
        backgroundColor: const Color(0xFF1e40af),
        foregroundColor: Colors.white,
      ),
      body: FutureBuilder<List<ArsipPdf>>(
        future: _arsipFuture,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const LoadingWidget(message: 'Memuat arsip...');
          }

          if (snapshot.hasError) {
            return Center(
              child: EmptyStateWidget(
                message: 'Gagal memuat arsip: ${snapshot.error}',
                onRetry: () {
                  setState(() {
                    _arsipFuture = _apiService.getArsipPdf();
                    _animationController.reset();
                    _animationController.forward();
                  });
                },
              ),
            );
          }

          final arsipList = snapshot.data ?? [];

          // Apply search and filter
          var filtered = arsipList.where((arsip) {
            final matchesSearch =
                _searchController.text.isEmpty ||
                (arsip.namaBerkas?.toLowerCase().contains(
                      _searchController.text.toLowerCase(),
                    ) ??
                    false);
            return matchesSearch;
          }).toList();

          if (filtered.isEmpty) {
            return Center(
              child: EmptyStateWidget(
                message: _searchController.text.isNotEmpty
                    ? 'Tidak ada hasil pencarian'
                    : 'Belum ada arsip PDF',
                icon: Icons.description,
                onRetry: () {
                  _searchController.clear();
                  setState(() {});
                },
              ),
            );
          }

          return Column(
            children: [
              // Search & Filter Section
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.white,
                  border: Border(
                    bottom: BorderSide(
                      color: const Color(0xFFe5e7eb),
                      width: 1,
                    ),
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Search Bar
                    Container(
                      decoration: BoxDecoration(
                        color: const Color(0xFFF3F4F6),
                        borderRadius: BorderRadius.circular(10),
                        border: Border.all(
                          color: const Color(0xFFe5e7eb),
                          width: 1,
                        ),
                      ),
                      child: TextField(
                        controller: _searchController,
                        decoration: InputDecoration(
                          hintText: 'Cari nama file atau pesanan...',
                          prefixIcon: const Icon(Icons.search, size: 20),
                          border: InputBorder.none,
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 12,
                          ),
                          hintStyle: TextStyle(
                            color: Colors.grey[600],
                            fontSize: 14,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 12),
                    // Filter Chips and View Toggle
                    Row(
                      children: [
                        Expanded(
                          child: SingleChildScrollView(
                            scrollDirection: Axis.horizontal,
                            child: Row(
                              children: [
                                _buildFilterChip(
                                  label: 'Semua',
                                  value: 'semua',
                                  count: arsipList.length,
                                ),
                                const SizedBox(width: 8),
                                _buildFilterChip(
                                  label: 'File Terbaru',
                                  value: 'terbaru',
                                  count: arsipList.length,
                                ),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        // View Toggle Buttons
                        Container(
                          decoration: BoxDecoration(
                            border: Border.all(
                              color: const Color(0xFFe5e7eb),
                              width: 1.5,
                            ),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Row(
                            children: [
                              Tooltip(
                                message: 'Tampilan Kartu',
                                child: InkWell(
                                  onTap: () {
                                    setState(() {
                                      _isTableView = false;
                                    });
                                  },
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 8,
                                      vertical: 6,
                                    ),
                                    decoration: BoxDecoration(
                                      color: !_isTableView
                                          ? const Color(0xFF1e40af)
                                          : Colors.white,
                                      borderRadius: const BorderRadius.only(
                                        topLeft: Radius.circular(6),
                                        bottomLeft: Radius.circular(6),
                                      ),
                                    ),
                                    child: Icon(
                                      Icons.view_agenda,
                                      size: 18,
                                      color: !_isTableView
                                          ? Colors.white
                                          : const Color(0xFF6b7280),
                                    ),
                                  ),
                                ),
                              ),
                              Tooltip(
                                message: 'Tampilan Tabel',
                                child: InkWell(
                                  onTap: () {
                                    setState(() {
                                      _isTableView = true;
                                    });
                                  },
                                  child: Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 8,
                                      vertical: 6,
                                    ),
                                    decoration: BoxDecoration(
                                      color: _isTableView
                                          ? const Color(0xFF1e40af)
                                          : Colors.white,
                                      borderRadius: const BorderRadius.only(
                                        topRight: Radius.circular(6),
                                        bottomRight: Radius.circular(6),
                                      ),
                                    ),
                                    child: Icon(
                                      Icons.table_chart,
                                      size: 18,
                                      color: _isTableView
                                          ? Colors.white
                                          : const Color(0xFF6b7280),
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
              // Arsip List or Table
              Expanded(
                child: RefreshIndicator(
                  onRefresh: () {
                    setState(() {
                      _arsipFuture = _apiService.getArsipPdf();
                      _animationController.reset();
                      _animationController.forward();
                    });
                    return _arsipFuture;
                  },
                  child: _isTableView
                      ? _buildTableView(filtered)
                      : _buildCardView(filtered),
                ),
              ),
            ],
          );
        },
      ),
    );
  }

  Widget _buildFilterChip({
    required String label,
    required String value,
    required int count,
  }) {
    final isSelected = _selectedStatus == value;
    return FilterChip(
      label: Text('$label ($count)'),
      selected: isSelected,
      onSelected: (bool selected) {
        setState(() {
          _selectedStatus = value;
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

  Widget _buildArsipCard(BuildContext context, ArsipPdf arsip) {
    final dateFormat = DateFormat('dd MMM yyyy HH:mm', 'id_ID');
    final currencyFormatter = NumberFormat('#,##0', 'id_ID');

    String sizeFormat(int bytes) {
      if (bytes < 1024) return '$bytes B';
      if (bytes < 1024 * 1024) return '${(bytes / 1024).toStringAsFixed(2)} KB';
      return '${(bytes / (1024 * 1024)).toStringAsFixed(2)} MB';
    }

    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0xFFe0e7ff), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF3b82f6).withOpacity(0.08),
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
            // Header with PDF icon and info
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [Color(0xFFfee2e2), Color(0xFFfecaca)],
                    ),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: const Icon(
                    Icons.picture_as_pdf_rounded,
                    color: Color(0xFFdc2626),
                    size: 28,
                  ),
                ),
                const SizedBox(width: 14),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'PO ${arsip.pesananId}',
                        style: const TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w800,
                          color: Color(0xFF0c2340),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Container(height: 1, color: const Color(0xFFe5e7eb)),
            const SizedBox(height: 12),
            // Info rows
            Row(
              children: [
                Expanded(
                  child: _buildInfoColumn(
                    label: 'Tanggal',
                    value: arsip.createdAt != null
                        ? dateFormat.format(arsip.createdAt!)
                        : '-',
                  ),
                ),
                Expanded(
                  child: _buildInfoColumn(
                    label: 'Ukuran',
                    value: sizeFormat(arsip.ukuran ?? 0),
                    valueStyle: const TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF0c2340),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 14),
            // Action Buttons
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Fitur download akan segera tersedia'),
                        ),
                      );
                    },
                    icon: const Icon(Icons.download, size: 16),
                    label: const Text('Download'),
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
                        const SnackBar(
                          content: Text('Fitur preview akan segera tersedia'),
                        ),
                      );
                    },
                    icon: const Icon(Icons.visibility, size: 16),
                    label: const Text('Lihat'),
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
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoColumn({
    required String label,
    required String value,
    TextStyle? valueStyle,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: 11,
            fontWeight: FontWeight.w600,
            color: Colors.grey[600],
          ),
        ),
        const SizedBox(height: 4),
        Text(
          value,
          style:
              valueStyle ??
              const TextStyle(
                fontSize: 12,
                fontWeight: FontWeight.w600,
                color: Color(0xFF0c2340),
              ),
        ),
      ],
    );
  }

  // Card View
  Widget _buildCardView(List<ArsipPdf> filtered) {
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: filtered.length,
      itemBuilder: (context, index) {
        final arsip = filtered[index];
        return SlideTransition(
          position: Tween<Offset>(begin: const Offset(1.0, 0), end: Offset.zero)
              .animate(
                CurvedAnimation(
                  parent: _animationController,
                  curve: Interval(
                    index * 0.08,
                    0.5 + (index * 0.08),
                    curve: Curves.easeOut,
                  ),
                ),
              ),
          child: _buildArsipCard(context, arsip),
        );
      },
    );
  }

  // Table View
  Widget _buildTableView(List<ArsipPdf> filtered) {
    final dateFormat = DateFormat('dd MMM yyyy', 'id_ID');

    String sizeFormat(int bytes) {
      if (bytes < 1024) return '$bytes B';
      if (bytes < 1024 * 1024) return '${(bytes / 1024).toStringAsFixed(2)} KB';
      return '${(bytes / (1024 * 1024)).toStringAsFixed(2)} MB';
    }

    return SingleChildScrollView(
      child: Column(
        children: [
          // Table Header
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
            decoration: BoxDecoration(
              color: const Color(0xFF2563eb),
              borderRadius: const BorderRadius.only(
                topLeft: Radius.circular(8),
                topRight: Radius.circular(8),
              ),
            ),
            child: Row(
              children: [
                Expanded(
                  flex: 2,
                  child: Text(
                    'NOMOR PO',
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
                    ),
                  ),
                ),
                Expanded(
                  flex: 2,
                  child: Text(
                    'TANGGAL',
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
                    ),
                  ),
                ),
                Expanded(
                  child: Text(
                    'UKURAN',
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
                    'AKSI',
                    style: TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                      fontSize: 12,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ),
              ],
            ),
          ),
          // Table Rows
          ListView.builder(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            itemCount: filtered.length,
            itemBuilder: (context, index) {
              final arsip = filtered[index];
              return Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 12,
                ),
                decoration: BoxDecoration(
                  color: index.isEven ? Colors.grey[50] : Colors.white,
                  border: Border(
                    bottom: BorderSide(color: Colors.grey[200]!, width: 1),
                  ),
                ),
                child: Row(
                  children: [
                    // PO Number
                    Expanded(
                      flex: 2,
                      child: Text(
                        'PO ${arsip.pesananId}',
                        style: const TextStyle(
                          fontWeight: FontWeight.w600,
                          fontSize: 12,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    // Date
                    Expanded(
                      flex: 2,
                      child: Text(
                        arsip.createdAt != null
                            ? dateFormat.format(arsip.createdAt!)
                            : '-',
                        style: const TextStyle(fontSize: 12),
                      ),
                    ),
                    // Size
                    Expanded(
                      child: Text(
                        sizeFormat(arsip.ukuran ?? 0),
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w500,
                        ),
                        textAlign: TextAlign.center,
                      ),
                    ),
                    // Actions
                    Expanded(
                      flex: 2,
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          SizedBox(
                            width: 32,
                            height: 32,
                            child: Tooltip(
                              message: 'Download',
                              child: IconButton(
                                icon: const Icon(
                                  Icons.download,
                                  size: 16,
                                  color: Color(0xFF3b82f6),
                                ),
                                onPressed: () {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                        'Download akan segera tersedia',
                                      ),
                                    ),
                                  );
                                },
                                padding: EdgeInsets.zero,
                              ),
                            ),
                          ),
                          const SizedBox(width: 4),
                          SizedBox(
                            width: 32,
                            height: 32,
                            child: Tooltip(
                              message: 'Lihat',
                              child: IconButton(
                                icon: const Icon(
                                  Icons.visibility,
                                  size: 16,
                                  color: Color(0xFF3b82f6),
                                ),
                                onPressed: () {
                                  ScaffoldMessenger.of(context).showSnackBar(
                                    const SnackBar(
                                      content: Text(
                                        'Preview akan segera tersedia',
                                      ),
                                    ),
                                  );
                                },
                                padding: EdgeInsets.zero,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              );
            },
          ),
        ],
      ),
    );
  }

  void _showDeleteDialog(int arsipId) {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Hapus Arsip'),
        content: const Text('Apakah Anda yakin ingin menghapus arsip ini?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
          TextButton(
            onPressed: () {
              Navigator.pop(context);
              _deleteArsip(arsipId);
            },
            child: const Text('Hapus'),
          ),
        ],
      ),
    );
  }

  Future<void> _deleteArsip(int arsipId) async {
    final success = await _apiService.deleteArsipPdf(arsipId);
    if (success) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Arsip berhasil dihapus')));
      setState(() {
        _arsipFuture = _apiService.getArsipPdf();
      });
    } else {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Gagal menghapus arsip')));
    }
  }
}
