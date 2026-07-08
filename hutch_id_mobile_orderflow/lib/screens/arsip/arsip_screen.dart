import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../../models/pesanan.dart';
import '../../providers/pesanan_provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/custom_widgets.dart';
import '../../services/api_service.dart';
import '../../utils/pdf_downloader.dart';
import '../pesanan/pesanan_list_screen.dart';
import '../pesanan/pesanan_form_screen.dart';

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
  DateTime? _dariDate;
  DateTime? _sampaiDate;
  bool _isTableView = false; // Card view by default di mobile — lebih responsif
  final dateFormat = DateFormat('dd MMM yyyy', 'id_ID');
  final dateFieldFormat = DateFormat('dd/MM/yyyy', 'id_ID');
  final numberFormat = NumberFormat('#,##0', 'id_ID');

  // Menyimpan id pesanan yang PDF-nya sedang diunduh/dibuka, supaya tombol
  // bisa menampilkan indikator loading per baris.
  final Set<int> _loadingPdfIds = {};

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

  void _resetFilter() {
    setState(() {
      _searchController.clear();
      _selectedStatus = 'semua';
      _dariDate = null;
      _sampaiDate = null;
    });
  }

  Future<void> _pickDate({required bool isDari}) async {
    final initial = (isDari ? _dariDate : _sampaiDate) ?? DateTime.now();
    final picked = await showDatePicker(
      context: context,
      initialDate: initial,
      firstDate: DateTime(2020),
      lastDate: DateTime(2100),
      locale: const Locale('id', 'ID'),
    );
    if (picked != null) {
      setState(() {
        if (isDari) {
          _dariDate = picked;
        } else {
          _sampaiDate = picked;
        }
      });
    }
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

      // Filter tanggal berdasarkan tanggal pengiriman, sama seperti website
      // (ArsipController@index memakai kolom tanggal_pengiriman).
      final tglKirim = pesanan.tanggalPengiriman;
      final matchesDari =
          _dariDate == null ||
          tglKirim == null ||
          !tglKirim.isBefore(_dariDate!);
      final matchesSampai =
          _sampaiDate == null ||
          tglKirim == null ||
          !tglKirim.isAfter(_sampaiDate!.add(const Duration(days: 1)));

      // Only show selesai and dibatalkan orders
      final isArchived =
          pesanan.status == 'selesai' || pesanan.status == 'dibatalkan';

      return matchesStatus &&
          matchesSearch &&
          matchesDari &&
          matchesSampai &&
          isArchived;
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

  IconData _getStatusIcon(String? status) {
    switch (status) {
      case 'selesai':
        return Icons.check_circle;
      case 'dibatalkan':
        return Icons.cancel;
      default:
        return Icons.circle;
    }
  }

  String _getMainProduct(Pesanan pesanan) {
    if (pesanan.detailPesanan?.isEmpty ?? true) {
      return '-';
    }
    final firstItem = pesanan.detailPesanan!.first;
    final jumlah = firstItem.jumlah;
    final namaProduk = firstItem.produk?.nama ?? '-';
    final base = jumlah != null ? '$namaProduk ($jumlah pcs)' : namaProduk;
    if (pesanan.detailPesanan!.length > 1) {
      return '$base +${pesanan.detailPesanan!.length - 1} item lainnya';
    }
    return base;
  }

  // ---------------------------------------------------------------------
  // PDF actions
  //
  // PENTING: sebelumnya tombol ini membuka
  // `${AppConfig.mediaBaseUrl}/pesanan/{id}/pdf` lewat url_launcher, yaitu
  // ROUTE WEB (bukan API) yang butuh sesi login browser. Karena aplikasi
  // mobile ini login pakai token (Sanctum), bukan cookie sesi, browser yang
  // dibuka url_launcher tidak punya sesi apa pun sehingga selalu diarahkan
  // ke halaman login/dashboard website. Perbaikannya: ambil PDF lewat
  // endpoint API terautentikasi (ApiService.downloadPesananPdf, memakai
  // Bearer token), lalu simpan & buka filenya secara lokal di perangkat
  // (savePdfBytes), persis seperti yang sudah dipakai & terbukti berfungsi
  // di pesanan_detail_screen.dart.
  // ---------------------------------------------------------------------
  Future<void> _openPdf(Pesanan pesanan) async {
    final id = pesanan.id;
    if (id == null) return;
    if (_loadingPdfIds.contains(id)) return;

    final scaffoldMessenger = ScaffoldMessenger.of(context);
    setState(() => _loadingPdfIds.add(id));

    try {
      final result = await ApiService().downloadPesananPdf(id);

      if (result == null) {
        scaffoldMessenger.showSnackBar(
          const SnackBar(
            content: Text(
              'Gagal mengambil PDF. Pastikan kamu punya akses & koneksi ke server.',
            ),
          ),
        );
        return;
      }

      final bytes = base64Decode(result['base64'] as String);
      final filename =
          (result['filename'] as String?) ?? '${pesanan.nomorPo ?? 'pesanan'}.pdf';

      await savePdfBytes(bytes, filename);

      scaffoldMessenger.showSnackBar(
        SnackBar(content: Text('PDF ${pesanan.nomorPo ?? ''} siap dibuka')),
      );
    } catch (e) {
      scaffoldMessenger.showSnackBar(SnackBar(content: Text('Error: $e')));
    } finally {
      if (mounted) setState(() => _loadingPdfIds.remove(id));
    }
  }

  Widget _infoChip(IconData icon, String text, Color bg, Color fg) {
    return Flexible(
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 5),
        decoration: BoxDecoration(
          color: bg,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 12, color: fg),
            const SizedBox(width: 4),
            Flexible(
              child: Text(
                text,
                style: TextStyle(
                    fontSize: 11, fontWeight: FontWeight.w600, color: fg),
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      ),
    );
  }

  bool _isLoadingPdf(Pesanan pesanan) =>
      pesanan.id != null && _loadingPdfIds.contains(pesanan.id);

  Future<void> _downloadPDF(Pesanan pesanan) => _openPdf(pesanan);

  Future<void> _viewPDF(Pesanan pesanan) => _openPdf(pesanan);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF5F7FB),
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

          final fullArsipList = pesananProvider.pesananList
              .where(
                (p) => p.status == 'selesai' || p.status == 'dibatalkan',
              )
              .toList();
          final arsipList = _getFilteredArsip(pesananProvider.pesananList);

          return RefreshIndicator(
            onRefresh: () => pesananProvider.fetchPesanan(),
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                _buildPageHeader(),
                _buildFilterCard(),
                if (arsipList.isEmpty)
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 48),
                    child: EmptyStateWidget(
                      message: _searchController.text.isNotEmpty ||
                              _selectedStatus != 'semua' ||
                              _dariDate != null ||
                              _sampaiDate != null
                          ? 'Tidak ada hasil pencarian'
                          : 'Belum ada arsip',
                      icon: Icons.archive,
                      onRetry: () {
                        pesananProvider.fetchPesanan();
                      },
                    ),
                  )
                else ...[
                  _isTableView
                      ? _buildTableView(arsipList)
                      : _buildCardView(arsipList),
                  _buildSummaryFooter(arsipList.length, fullArsipList.length),
                ],
                const SizedBox(height: 16),
              ],
            ),
          );
        },
      ),
    );
  }

  // ---------------------------------------------------------------------
  // Page header: judul "Arsip PDF" + subjudul + tombol "Buat PO Baru" dan
  // "Daftar Pesanan", meniru page-header di website (resources/views/
  // arsip/index.blade.php).
  // ---------------------------------------------------------------------
  Widget _buildPageHeader() {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 16, 16, 12),
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFFF8FAFF), Color(0xFFE1F3FE)],
        ),
        border: Border.all(color: const Color(0x3394A3B8)),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF2563EB).withValues(alpha: 0.08),
            blurRadius: 24,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          ShaderMask(
            shaderCallback: (bounds) => const LinearGradient(
              colors: [Color(0xFF1e40af), Color(0xFF2563eb)],
            ).createShader(bounds),
            child: const Text(
              'Arsip PDF',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.w800,
                letterSpacing: -0.3,
                color: Colors.white,
              ),
            ),
          ),
          const SizedBox(height: 4),
          const Text(
            'Daftar Purchase Order yang telah selesai atau dibagikan',
            style: TextStyle(
              color: Color(0xFF64748b),
              fontWeight: FontWeight.w500,
              fontSize: 13,
            ),
          ),
          const SizedBox(height: 14),
          Builder(builder: (context) {
            final userRole =
                Provider.of<AuthProvider>(context, listen: false).user?.role ?? '';
            final canCreatePO = userRole != 'administrator';
            return Wrap(
              spacing: 8,
              runSpacing: 8,
              children: [
                if (canCreatePO)
                  ElevatedButton.icon(
                    onPressed: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => const PesananFormScreen(),
                        ),
                      );
                    },
                    icon: const Icon(Icons.add, size: 18),
                    label: const Text('Buat PO Baru'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF2563eb),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(
                        horizontal: 16,
                        vertical: 12,
                      ),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                OutlinedButton.icon(
                onPressed: () {
                  Navigator.push(
                    context,
                    MaterialPageRoute(
                      builder: (_) => const PesananListScreen(),
                    ),
                  );
                },
                icon: const Icon(Icons.list_alt, size: 18),
                label: const Text('Daftar Pesanan'),
                style: OutlinedButton.styleFrom(
                  foregroundColor: const Color(0xFF475569),
                  side: const BorderSide(color: Color(0xFF94A3B8)),
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 12,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
            ],
          );
          }),
        ],
      ),
    );
  }

  // ---------------------------------------------------------------------
  // Filter card: Cari Nama Pelanggan, Status, Dari, Sampai, Filter, Reset —
  // sama seperti filter-card di website.
  // ---------------------------------------------------------------------
  Widget _buildFilterCard() {
    final fieldDecoration = InputDecoration(
      filled: true,
      fillColor: const Color(0xFFF8FBFF),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFFE2E8F0), width: 1.5),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFFE2E8F0), width: 1.5),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: Color(0xFF2563eb), width: 1.5),
      ),
    );

    Widget label(String text) => Padding(
          padding: const EdgeInsets.only(bottom: 6),
          child: Text(
            text,
            style: const TextStyle(
              fontWeight: FontWeight.w700,
              color: Color(0xFF1e293b),
              fontSize: 13,
            ),
          ),
        );

    return Container(
      margin: const EdgeInsets.fromLTRB(16, 0, 16, 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF0F407C).withValues(alpha: 0.06),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          label('Cari Nama Pelanggan'),
          TextField(
            controller: _searchController,
            decoration: fieldDecoration.copyWith(
              hintText: 'Cari nama pelanggan...',
              prefixIcon: const Icon(
                Icons.search,
                color: Color(0xFF2563eb),
                size: 20,
              ),
            ),
          ),
          const SizedBox(height: 14),
          label('Status'),
          DropdownButtonFormField<String>(
            initialValue: _selectedStatus,
            decoration: fieldDecoration,
            items: const [
              DropdownMenuItem(value: 'semua', child: Text('Semua Status')),
              DropdownMenuItem(value: 'selesai', child: Text('Selesai')),
              DropdownMenuItem(
                value: 'dibatalkan',
                child: Text('Dibatalkan'),
              ),
            ],
            onChanged: (value) {
              setState(() => _selectedStatus = value ?? 'semua');
            },
          ),
          const SizedBox(height: 14),
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    label('Dari'),
                    InkWell(
                      borderRadius: BorderRadius.circular(12),
                      onTap: () => _pickDate(isDari: true),
                      child: InputDecorator(
                        decoration: fieldDecoration.copyWith(
                          suffixIcon: const Icon(
                            Icons.calendar_today,
                            size: 18,
                            color: Color(0xFF64748b),
                          ),
                        ),
                        child: Text(
                          _dariDate != null
                              ? dateFieldFormat.format(_dariDate!)
                              : 'dd/mm/yyyy',
                          style: TextStyle(
                            color: _dariDate != null
                                ? const Color(0xFF0c2340)
                                : Colors.grey[500],
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    label('Sampai'),
                    InkWell(
                      borderRadius: BorderRadius.circular(12),
                      onTap: () => _pickDate(isDari: false),
                      child: InputDecorator(
                        decoration: fieldDecoration.copyWith(
                          suffixIcon: const Icon(
                            Icons.calendar_today,
                            size: 18,
                            color: Color(0xFF64748b),
                          ),
                        ),
                        child: Text(
                          _sampaiDate != null
                              ? dateFieldFormat.format(_sampaiDate!)
                              : 'dd/mm/yyyy',
                          style: TextStyle(
                            color: _sampaiDate != null
                                ? const Color(0xFF0c2340)
                                : Colors.grey[500],
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: ElevatedButton.icon(
                  onPressed: () => setState(() {}),
                  icon: const Icon(Icons.filter_alt, size: 18),
                  label: const Text('Filter'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2563eb),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: OutlinedButton.icon(
                  onPressed: _resetFilter,
                  icon: const Icon(Icons.refresh, size: 18),
                  label: const Text('Reset'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: const Color(0xFF475569),
                    side: const BorderSide(color: Color(0xFF94A3B8)),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                ),
              ),

            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryFooter(int shown, int total) {
    return Container(
      margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
      decoration: BoxDecoration(
        color: const Color(0xFFEFF6FF),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFBFDBFE)),
      ),
      child: Row(
        children: [
          const Icon(Icons.info_outline_rounded,
              size: 15, color: Color(0xFF2563eb)),
          const SizedBox(width: 8),
          Text(
            'Menampilkan ',
            style: TextStyle(color: Colors.grey[600], fontSize: 13),
          ),
          Text(
            '${shown == 0 ? 0 : 1}–$shown dari $total pesanan',
            style: const TextStyle(
              fontWeight: FontWeight.w700,
              color: Color(0xFF0c2340),
              fontSize: 13,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPdfActionButton({
    required Pesanan pesanan,
    required IconData icon,
    required String tooltip,
    required VoidCallback onPressed,
    double iconSize = 20,
  }) {
    final isLoading = pesanan.id != null && _loadingPdfIds.contains(pesanan.id);
    return Tooltip(
      message: tooltip,
      child: SizedBox(
        width: iconSize + 12,
        height: iconSize + 12,
        child: IconButton(
          onPressed: isLoading ? null : onPressed,
          icon: isLoading
              ? SizedBox(
                  width: iconSize - 4,
                  height: iconSize - 4,
                  child: const CircularProgressIndicator(strokeWidth: 2),
                )
              : Icon(icon),
          color: const Color(0xFF2563eb),
          iconSize: iconSize,
          padding: EdgeInsets.zero,
          constraints: const BoxConstraints(),
        ),
      ),
    );
  }

  Widget _buildCardView(List<Pesanan> arsipList) {
    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      padding: const EdgeInsets.symmetric(horizontal: 16),
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
                            if (pesanan.pelanggan?.telepon != null)
                              Text(
                                pesanan.pelanggan!.telepon!,
                                style: TextStyle(
                                  fontSize: 11,
                                  color: Colors.grey[500],
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
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              _getStatusIcon(pesanan.status),
                              size: 12,
                              color: _getStatusColor(pesanan.status),
                            ),
                            const SizedBox(width: 4),
                            Text(
                              _getStatusText(pesanan.status),
                              style: TextStyle(
                                fontSize: 11,
                                fontWeight: FontWeight.w700,
                                color: _getStatusColor(pesanan.status),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  // Info baris: tanggal PO + tanggal kirim
                  Row(
                    children: [
                      _infoChip(
                        Icons.calendar_today_rounded,
                        pesanan.tanggalPesanan != null
                            ? dateFormat.format(pesanan.tanggalPesanan!)
                            : '-',
                        const Color(0xFFdbeafe),
                        const Color(0xFF1d4ed8),
                      ),
                      const SizedBox(width: 8),
                      _infoChip(
                        Icons.local_shipping_rounded,
                        pesanan.tanggalPengiriman != null
                            ? dateFormat.format(pesanan.tanggalPengiriman!)
                            : '-',
                        const Color(0xFFf3e8ff),
                        const Color(0xFF7c3aed),
                      ),
                    ],
                  ),
                  const SizedBox(height: 10),
                  // Total nilai — baris tersendiri, full lebar
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(
                        horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(
                      color: const Color(0xFFecfdf5),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: const Color(0xFFa7f3d0)),
                    ),
                    child: Row(
                      mainAxisAlignment: MainAxisAlignment.spaceBetween,
                      children: [
                        const Text(
                          'Total Nilai',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: Color(0xFF065f46),
                          ),
                        ),
                        Text(
                          'Rp ${numberFormat.format(pesanan.totalNilai ?? 0)}',
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.w800,
                            color: Color(0xFF059669),
                          ),
                        ),
                      ],
                    ),
                  ),
                  const SizedBox(height: 10),
                  // Produk utama
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Icon(Icons.inventory_2_rounded,
                          size: 14, color: Color(0xFF64748b)),
                      const SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          _getMainProduct(pesanan),
                          style: const TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                            color: Color(0xFF475569),
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  Container(height: 1, color: const Color(0xFFe5e7eb)),
                  const SizedBox(height: 12),
                  // Tombol aksi PDF — full-width agar mudah ditekan di mobile
                  Row(
                    children: [
                      Expanded(
                        child: _isLoadingPdf(pesanan)
                            ? const Center(
                                child: SizedBox(
                                  height: 20,
                                  width: 20,
                                  child: CircularProgressIndicator(
                                    strokeWidth: 2,
                                    color: Color(0xFF2563eb),
                                  ),
                                ),
                              )
                            : OutlinedButton.icon(
                                onPressed: () => _downloadPDF(pesanan),
                                icon: const Icon(Icons.download_rounded, size: 16),
                                label: const Text('Unduh PDF'),
                                style: OutlinedButton.styleFrom(
                                  foregroundColor: const Color(0xFF2563eb),
                                  side: const BorderSide(
                                      color: Color(0xFF2563eb), width: 1.5),
                                  padding: const EdgeInsets.symmetric(vertical: 10),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                  textStyle: const TextStyle(
                                      fontSize: 13, fontWeight: FontWeight.w600),
                                ),
                              ),
                      ),
                      const SizedBox(width: 10),
                      Expanded(
                        child: ElevatedButton.icon(
                          onPressed: _isLoadingPdf(pesanan)
                              ? null
                              : () => _viewPDF(pesanan),
                          icon: const Icon(Icons.picture_as_pdf_rounded, size: 16),
                          label: const Text('Lihat PDF'),
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF2563eb),
                            foregroundColor: Colors.white,
                            elevation: 0,
                            padding: const EdgeInsets.symmetric(vertical: 10),
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(10),
                            ),
                            textStyle: const TextStyle(
                                fontSize: 13, fontWeight: FontWeight.w600),
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
  }

  Widget _buildTableView(List<Pesanan> arsipList) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(color: const Color(0xFFE2E8F0)),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF0F407C).withValues(alpha: 0.06),
              blurRadius: 20,
              offset: const Offset(0, 8),
            ),
          ],
        ),
        clipBehavior: Clip.antiAlias,
        child: SingleChildScrollView(
          scrollDirection: Axis.horizontal,
          child: DataTable(
            headingRowColor: WidgetStateProperty.all(const Color(0xFF2563eb)),
            columnSpacing: 20,
            columns: const [
              DataColumn(
                label: Text(
                  'NOMOR PO',
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
              DataColumn(
                label: Text(
                  'TANGGAL',
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
              DataColumn(
                label: Text(
                  'PELANGGAN',
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
              DataColumn(
                label: Text(
                  'PRODUK UTAMA',
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
              DataColumn(
                label: Text(
                  'TOTAL NILAI',
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
              DataColumn(
                label: Text(
                  'TGL KIRIM',
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
              DataColumn(
                label: Text(
                  'STATUS',
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
              DataColumn(
                label: Text(
                  'AKSI',
                  style: TextStyle(
                    fontWeight: FontWeight.w700,
                    color: Colors.white,
                    fontSize: 12,
                  ),
                ),
              ),
            ],
            rows: arsipList.asMap().entries.map((entry) {
              final index = entry.key;
              final pesanan = entry.value;
              final bgColor = index.isEven
                  ? Colors.white
                  : const Color(0xFFF9FAFB);

              return DataRow(
                color: WidgetStateProperty.all(bgColor),
                cells: [
                  DataCell(
                    Text(
                      pesanan.nomorPo ?? '-',
                      style: const TextStyle(fontWeight: FontWeight.w700),
                    ),
                  ),
                  DataCell(
                    Text(
                      pesanan.tanggalPesanan != null
                          ? dateFormat.format(pesanan.tanggalPesanan!)
                          : '-',
                    ),
                  ),
                  DataCell(
                    ConstrainedBox(
                      constraints: const BoxConstraints(maxWidth: 140),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            pesanan.pelanggan?.nama ?? '-',
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                            style: const TextStyle(fontWeight: FontWeight.w600),
                          ),
                          if (pesanan.pelanggan?.telepon != null)
                            Text(
                              pesanan.pelanggan!.telepon!,
                              style: TextStyle(
                                fontSize: 11,
                                color: Colors.grey[500],
                              ),
                            ),
                        ],
                      ),
                    ),
                  ),
                  DataCell(
                    ConstrainedBox(
                      constraints: const BoxConstraints(maxWidth: 180),
                      child: Text(
                        _getMainProduct(pesanan),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(fontSize: 12),
                      ),
                    ),
                  ),
                  DataCell(
                    Text(
                      'Rp ${numberFormat.format(pesanan.totalNilai ?? 0)}',
                    ),
                  ),
                  DataCell(
                    Text(
                      pesanan.tanggalPengiriman != null
                          ? dateFormat.format(pesanan.tanggalPengiriman!)
                          : '-',
                    ),
                  ),
                  DataCell(
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: _getStatusColor(
                          pesanan.status,
                        ).withValues(alpha: 0.1),
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
                        _buildPdfActionButton(
                          pesanan: pesanan,
                          icon: Icons.download,
                          tooltip: 'Unduh PDF',
                          onPressed: () => _downloadPDF(pesanan),
                          iconSize: 18,
                        ),
                        const SizedBox(width: 8),
                        _buildPdfActionButton(
                          pesanan: pesanan,
                          icon: Icons.visibility,
                          tooltip: 'Lihat PDF',
                          onPressed: () => _viewPDF(pesanan),
                          iconSize: 18,
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