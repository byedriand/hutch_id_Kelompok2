import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/pesanan_provider.dart';
import '../../providers/pelanggan_provider.dart';
import '../../providers/produk_provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/custom_widgets.dart';
import '../../widgets/product_picker_dialog.dart';
import '../../config/app_config.dart';
import '../../models/produk.dart';

class PesananFormScreen extends StatefulWidget {
  final int? pesananId;

  const PesananFormScreen({super.key, this.pesananId});

  @override
  State<PesananFormScreen> createState() => _PesananFormScreenState();
}

class _PesananFormScreenState extends State<PesananFormScreen> {
  late TextEditingController _catatanController;
  late TextEditingController _spesifikasiController;
  late TextEditingController _jumlahController;

  String _selectedStatus = 'menunggu_konfirmasi';
  bool _isSubmitting = false;

  int? _selectedPelangganId;
  int? _selectedProdukId;
  DateTime _tanggalPesanan = DateTime.now();
  DateTime _tanggalPengiriman = DateTime.now().add(const Duration(days: 7));
  final List<Map<String, dynamic>> _orderItems = [];

  bool _stokNotifTerkirim = false;
  bool _isSendingStokNotif = false;

  final dateFormat = DateFormat('dd MMM yyyy', 'id_ID');
  final numberFormat = NumberFormat('#,##0', 'id_ID');

  // ── Design tokens (match website palette) ──────────────────────────────────
  static const Color _primary = Color(0xFF0052A3);
  static const Color _primaryLight = Color(0xFF0066CC);
  static const Color _primaryBg = Color(0xFFE6F2FF);
  static const Color _border = Color(0xFFDBE5F1);
  static const Color _labelColor = Color(0xFF0052A3);
  static const Color _helperColor = Color(0xFF64748B);
  static const Color _errorColor = Color(0xFFEF4444);
  static const Color _bgField = Color(0xFFF8FBFF);
  static const Color _bgPage = Color(0xFFEEF4FB);
  static const Color _green = Color(0xFF059669);
  static const Color _greenBg = Color(0xFFECFDF5);

  @override
  void initState() {
    super.initState();
    _catatanController = TextEditingController();
    _spesifikasiController = TextEditingController();
    _jumlahController = TextEditingController();

    if (widget.pesananId != null) {
      Future.microtask(() {
        if (mounted) {
          Provider.of<PesananProvider>(context, listen: false)
              .getPesananDetail(widget.pesananId!);
        }
      });
    } else {
      Future.microtask(() {
        if (mounted) {
          Provider.of<PelangganProvider>(context, listen: false).fetchPelanggan();
          Provider.of<ProdukProvider>(context, listen: false).fetchProduk();
        }
      });
    }
  }

  @override
  void dispose() {
    _catatanController.dispose();
    _spesifikasiController.dispose();
    _jumlahController.dispose();
    super.dispose();
  }

  // ── Helpers ─────────────────────────────────────────────────────────────────

  String _formatRupiah(num value) =>
      'Rp ${numberFormat.format(value)}';

  Future<void> _addOrderItem() async {
    if (_selectedProdukId == null || _jumlahController.text.isEmpty) {
      _snack('Pilih produk dan masukkan jumlah');
      return;
    }
    final jumlah = int.tryParse(_jumlahController.text) ?? 0;
    if (jumlah <= 0) { _snack('Jumlah harus lebih dari 0'); return; }

    setState(() {
      _orderItems.add({
        'produk_id': _selectedProdukId,
        'jumlah': jumlah,
        'spesifikasi': _spesifikasiController.text,
      });
      _selectedProdukId = null;
      _jumlahController.clear();
      _spesifikasiController.clear();
      _stokNotifTerkirim = false;
    });
  }

  void _removeOrderItem(int index) =>
      setState(() { _orderItems.removeAt(index); _stokNotifTerkirim = false; });

  void _snack(String msg, {Color? bg}) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg),
      backgroundColor: bg,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
    ));
  }

  double _totalNilai(List<Produk> produkList) {
    double total = 0;
    for (final item in _orderItems) {
      final p = produkList.where((p) => p.id == item['produk_id']).firstOrNull;
      total += (p?.hargaJual ?? 0) * ((item['jumlah'] as int?) ?? 0);
    }
    return total;
  }

  List<Map<String, dynamic>> _computeStockSummary(
    List<Produk> produkList, {bool includeDraft = false}) {
    final Map<int, Map<String, dynamic>> summary = {};
    for (final item in _orderItems) {
      final produkId = item['produk_id'] as int?;
      final jumlah = item['jumlah'] as int? ?? 0;
      if (produkId == null || jumlah <= 0) continue;
      final produk = produkList.where((p) => p.id == produkId).firstOrNull;
      if (produk == null) continue;
      summary.putIfAbsent(produkId, () => {'produk': produk, 'kebutuhan': 0});
      summary[produkId]!['kebutuhan'] =
          (summary[produkId]!['kebutuhan'] as int) + jumlah;
    }
    if (includeDraft) {
      final draftJumlah = int.tryParse(_jumlahController.text) ?? 0;
      if (_selectedProdukId != null && draftJumlah > 0) {
        final produk = produkList.where((p) => p.id == _selectedProdukId).firstOrNull;
        if (produk != null) {
          summary.putIfAbsent(_selectedProdukId!, () => {'produk': produk, 'kebutuhan': 0});
          summary[_selectedProdukId!]!['kebutuhan'] =
              (summary[_selectedProdukId!]!['kebutuhan'] as int) + draftJumlah;
        }
      }
    }
    return summary.values.map((entry) {
      final produk = entry['produk'] as Produk;
      final kebutuhan = entry['kebutuhan'] as int;
      final tersedia = produk.stok ?? 0;
      final selisih = tersedia - kebutuhan;
      return {
        'produk': produk, 'kebutuhan': kebutuhan,
        'tersedia': tersedia, 'selisih': selisih, 'cukup': selisih >= 0,
      };
    }).toList();
  }

  Future<void> _kirimNotifikasiStokKurang(
      List<Map<String, dynamic>> stockSummary) async {
    final kurang = stockSummary.where((r) => r['cukup'] != true).toList();
    if (kurang.isEmpty) return;
    setState(() => _isSendingStokNotif = true);
    final success = await Provider.of<ProdukProvider>(context, listen: false)
        .sendStokKurangNotifikasi(detailKurang: kurang.map((row) {
      final p = row['produk'] as Produk;
      return {
        'nama_produk': p.nama, 'produk_id': p.id,
        'stok_tersedia': row['tersedia'], 'kebutuhan': row['kebutuhan'],
        'kurang': (row['kebutuhan'] as int) - (row['tersedia'] as int),
      };
    }).toList());
    if (!mounted) return;
    setState(() { _isSendingStokNotif = false; if (success) _stokNotifTerkirim = true; });
    _snack(
      success ? 'Notifikasi stok kurang berhasil dikirim ke Operator Gudang'
               : 'Gagal mengirim notifikasi stok kurang',
      bg: success ? _green : _errorColor,
    );
  }

  Future<void> _selectDate(BuildContext context, bool isDeliveryDate) async {
    final picked = await showDatePicker(
      context: context,
      initialDate: isDeliveryDate ? _tanggalPengiriman : _tanggalPesanan,
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
      builder: (context, child) => Theme(
        data: Theme.of(context).copyWith(
          colorScheme: const ColorScheme.light(primary: _primary),
        ),
        child: child!,
      ),
    );
    if (picked != null) {
      setState(() {
        if (isDeliveryDate) _tanggalPengiriman = picked;
        else _tanggalPesanan = picked;
      });
    }
  }

  Future<void> _handleCreatePesanan() async {
    if (_selectedPelangganId == null) {
      _snack('Pilih pelanggan terlebih dahulu'); return;
    }
    if (_orderItems.isEmpty) {
      _snack('Tambahkan minimal satu produk'); return;
    }
    setState(() => _isSubmitting = true);

    final produkProvider = Provider.of<ProdukProvider>(context, listen: false);
    final pesananProvider = Provider.of<PesananProvider>(context, listen: false);

    final data = {
      'pelanggan_id': _selectedPelangganId,
      'tanggal_pesanan': _tanggalPesanan.toString().split(' ')[0],
      'tanggal_pengiriman': _tanggalPengiriman.toString().split(' ')[0],
      'items': _orderItems,
      'total_nilai': _totalNilai(produkProvider.produkList),
      'catatan': _catatanController.text.isEmpty ? null : _catatanController.text,
      'send_shortage_notification': _stokNotifTerkirim,
    };

    final success = await pesananProvider.createPesanan(data);
    setState(() => _isSubmitting = false);

    if (success && mounted) {
      _snack('Pesanan berhasil dibuat!', bg: _green);
      Future.delayed(const Duration(milliseconds: 500), () {
        if (mounted) Navigator.pop(context);
      });
    } else if (mounted) {
      _snack(pesananProvider.errorMessage ?? 'Gagal membuat pesanan', bg: _errorColor);
    }
  }

  // ── Status helpers (edit mode) ──────────────────────────────────────────────

  static const Map<String, String> _statusLabels = {
    'menunggu_konfirmasi': 'Menunggu Konfirmasi',
    'dikonfirmasi': 'Dikonfirmasi',
    'dalam_produksi': 'Dalam Produksi',
    'siap_kirim': 'Siap Kirim',
    'selesai': 'Selesai',
    'dibatalkan': 'Dibatalkan',
  };

  String _statusLabelText(String s) => _statusLabels[s] ?? s;

  List<String> _nextStatusOptions(String currentStatus, String userRole) {
    if (currentStatus == 'selesai' || currentStatus == 'dibatalkan') return [];
    List<String> next;
    switch (currentStatus) {
      case 'menunggu_konfirmasi': next = ['dikonfirmasi', 'dibatalkan']; break;
      case 'dikonfirmasi': next = ['dalam_produksi', 'dibatalkan']; break;
      case 'dalam_produksi': next = ['siap_kirim', 'dibatalkan']; break;
      case 'siap_kirim': next = ['selesai', 'dibatalkan']; break;
      default: next = [];
    }
    bool allowed(String ns) {
      if (userRole == 'administrator') return true;
      if (userRole == 'pemilik_umkm') {
        if (currentStatus == 'menunggu_konfirmasi' && ns == 'dikonfirmasi') return true;
        return ['dalam_produksi','siap_kirim','selesai','dibatalkan'].contains(ns);
      }
      if (userRole == 'operator_gudang' || userRole == 'staf_penjualan') {
        return ns == 'dibatalkan';
      }
      return false;
    }
    return next.where(allowed).toList();
  }

  Future<void> _handleUpdatePesanan() async {
    String? alasanPembatalan;
    if (_selectedStatus == 'dibatalkan') {
      final rc = TextEditingController();
      final confirmed = await showDialog<bool>(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Text('Batalkan Pesanan'),
          content: TextField(controller: rc, maxLines: 3,
            decoration: const InputDecoration(
              hintText: 'Alasan pembatalan (min. 5 karakter)',
              border: OutlineInputBorder())),
          actions: [
            TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Batal')),
            ElevatedButton(
              onPressed: () => Navigator.pop(ctx, true),
              style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
              child: const Text('Ya, Batalkan', style: TextStyle(color: Colors.white))),
          ],
        ),
      );
      if (confirmed != true) return;
      if (rc.text.trim().length < 5) {
        _snack('Alasan pembatalan minimal 5 karakter'); return;
      }
      alasanPembatalan = rc.text.trim();
    }
    setState(() => _isSubmitting = true);
    final pesananProvider = Provider.of<PesananProvider>(context, listen: false);
    final success = await pesananProvider.updatePesananStatus(
        widget.pesananId!, _selectedStatus, alasanPembatalan: alasanPembatalan);
    setState(() => _isSubmitting = false);
    if (!mounted) return;
    if (success) {
      final result = pesananProvider.lastStatusUpdateResult;
      final message = (result?['message'] as String?) ?? 'Status pesanan berhasil diperbarui.';
      final waMessage = result?['whatsapp_message'] as String?;
      await showDialog(
        context: context, barrierDismissible: false,
        builder: (ctx) => AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: Column(children: const [
            Icon(Icons.info_outline, color: _primaryLight, size: 48),
            SizedBox(height: 12),
            Text('Status Diperbarui', textAlign: TextAlign.center),
          ]),
          content: Column(mainAxisSize: MainAxisSize.min, children: [
            Text(message, textAlign: TextAlign.center),
            if (waMessage != null) ...[
              const SizedBox(height: 14),
              Text(waMessage, style: const TextStyle(fontSize: 13, color: Color(0xFF334155))),
            ],
          ]),
          actionsAlignment: MainAxisAlignment.center,
          actions: [
            ElevatedButton(
              onPressed: () => Navigator.pop(ctx),
              style: ElevatedButton.styleFrom(backgroundColor: _primary, foregroundColor: Colors.white),
              child: const Text('OK')),
          ],
        ),
      );
      if (mounted) Navigator.pop(context);
    } else {
      _snack(pesananProvider.errorMessage ?? 'Gagal mengupdate pesanan', bg: _errorColor);
    }
  }

  // ── Build ───────────────────────────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    final isEdit = widget.pesananId != null;
    return Scaffold(
      backgroundColor: _bgPage,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 2,
        shadowColor: _primary.withValues(alpha: 0.1),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: _primary),
          onPressed: () => Navigator.pop(context),
        ),
        title: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Text(
            isEdit ? 'Edit Pesanan' : 'Buat Pesanan Baru',
            style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w800,
                color: Color(0xFF0C2340)),
          ),
          Text(
            isEdit ? 'Perbarui status pesanan'
                   : 'Isi data pelanggan, pilih produk, dan kelola pesanan.',
            style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w500,
                color: _helperColor),
          ),
        ]),
        actions: isEdit ? null : [
          Padding(
            padding: const EdgeInsets.only(right: 12),
            child: ElevatedButton.icon(
              onPressed: _isSubmitting ? null : _handleCreatePesanan,
              icon: _isSubmitting
                  ? const SizedBox(width: 14, height: 14,
                      child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                  : const Icon(Icons.save_rounded, size: 16),
              label: Text(_isSubmitting ? 'Menyimpan...' : 'Simpan PO',
                style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w700)),
              style: ElevatedButton.styleFrom(
                backgroundColor: _primary, foregroundColor: Colors.white,
                elevation: 0, padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
            ),
          ),
        ],
      ),
      body: isEdit ? _buildUpdateForm() : _buildCreateForm(),
    );
  }

  // ── Section card wrapper ────────────────────────────────────────────────────

  Widget _sectionCard({required Widget child, EdgeInsets? padding}) {
    return Container(
      width: double.infinity,
      padding: padding ?? const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: _primary.withValues(alpha: 0.1), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: _primary.withValues(alpha: 0.06),
            blurRadius: 24, offset: const Offset(0, 8)),
        ],
      ),
      child: child,
    );
  }

  Widget _sectionHeader(IconData icon, String title, String subtitle) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(
            width: 40, height: 40,
            decoration: BoxDecoration(color: _primaryBg,
                borderRadius: BorderRadius.circular(10)),
            child: Icon(icon, color: _primaryLight, size: 20),
          ),
          const SizedBox(width: 12),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(title, style: const TextStyle(fontSize: 14,
                fontWeight: FontWeight.w800, color: _primary)),
            if (subtitle.isNotEmpty)
              Text(subtitle, style: const TextStyle(fontSize: 11,
                  color: _helperColor, fontWeight: FontWeight.w500)),
          ])),
        ]),
        const SizedBox(height: 12),
        Stack(children: [
          Container(height: 2, color: const Color(0x26006ACC)),
          Container(height: 2, width: 60,
            decoration: const BoxDecoration(
              gradient: LinearGradient(colors: [_primary, _primaryLight]))),
        ]),
      ]),
    );
  }

  Widget _labelText(String label, {bool required = false, bool optional = false}) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(children: [
        Text(label.toUpperCase(), style: const TextStyle(
            fontSize: 11, fontWeight: FontWeight.w800,
            color: _labelColor, letterSpacing: 0.8)),
        if (required) ...[
          const SizedBox(width: 4),
          const Text('*', style: TextStyle(color: _errorColor,
              fontWeight: FontWeight.w900, fontSize: 13)),
        ],
        if (optional) ...[
          const SizedBox(width: 6),
          Text('(Opsional)', style: TextStyle(fontSize: 10,
              fontWeight: FontWeight.w600,
              color: _helperColor.withValues(alpha: 0.8))),
        ],
      ]),
    );
  }

  InputDecoration _fieldDeco({String? hint}) => InputDecoration(
    hintText: hint,
    hintStyle: TextStyle(color: _border.withValues(alpha: 1.4),
        fontWeight: FontWeight.w500, fontSize: 14),
    filled: true, fillColor: _bgField,
    contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
    border: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: _border, width: 2)),
    enabledBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: _border, width: 2)),
    focusedBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: _primaryLight, width: 2)),
    errorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: _errorColor, width: 2)),
    focusedErrorBorder: OutlineInputBorder(borderRadius: BorderRadius.circular(12),
        borderSide: const BorderSide(color: _errorColor, width: 2)),
  );

  // ── Date picker tile ────────────────────────────────────────────────────────

  Widget _dateTile(String label, DateTime date, VoidCallback onTap,
      {bool required = false}) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      _labelText(label, required: required),
      InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          decoration: BoxDecoration(
            color: _bgField,
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: _border, width: 2),
          ),
          child: Row(children: [
            const Icon(Icons.calendar_today_rounded, color: _primary, size: 18),
            const SizedBox(width: 12),
            Text(dateFormat.format(date),
                style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600,
                    color: Color(0xFF1E293B))),
            const Spacer(),
            const Icon(Icons.edit_calendar_rounded, color: _helperColor, size: 16),
          ]),
        ),
      ),
    ]);
  }

  // ── CREATE FORM ─────────────────────────────────────────────────────────────

  Widget _buildCreateForm() {
    return Consumer3<PelangganProvider, ProdukProvider, PesananProvider>(
      builder: (context, pelangganProvider, produkProvider, pesananProvider, _) {
        final pelangganList = pelangganProvider.pelangganList;
        final produkList = produkProvider.produkList;
        final selectedPelanggan = _selectedPelangganId != null
            ? pelangganList.where((p) => p.id == _selectedPelangganId).firstOrNull
            : null;
        final selectedProduk = _selectedProdukId != null
            ? produkList.where((p) => p.id == _selectedProdukId).firstOrNull
            : null;
        final total = _totalNilai(produkList);
        final stockSummary = _computeStockSummary(produkList, includeDraft: true);
        final hasShortage = stockSummary.any((r) => r['cukup'] != true);
        final showStok = _orderItems.isNotEmpty ||
            (_selectedProdukId != null && (int.tryParse(_jumlahController.text) ?? 0) > 0);

        return SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(16, 20, 16, 40),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

            // ── 1. Informasi PO ─────────────────────────────────────────────
            _sectionCard(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _sectionHeader(Icons.receipt_long_rounded, 'Informasi PO',
                    'Isi data pelanggan dan tanggal pesanan'),

                // Pelanggan search/select
                _labelText('Pelanggan', required: true),
                Container(
                  decoration: BoxDecoration(
                    color: _bgField,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: _border, width: 2),
                  ),
                  child: DropdownButton<int>(
                    value: _selectedPelangganId,
                    hint: const Padding(
                      padding: EdgeInsets.symmetric(horizontal: 16),
                      child: Row(children: [
                        Icon(Icons.search_rounded, color: _helperColor, size: 18),
                        SizedBox(width: 8),
                        Text('Pilih pelanggan...', style: TextStyle(
                            color: _helperColor, fontWeight: FontWeight.w500, fontSize: 14)),
                      ]),
                    ),
                    isExpanded: true,
                    underline: Container(),
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                    items: pelangganList.map((p) => DropdownMenuItem<int>(
                        value: p.id, child: Text(p.nama))).toList(),
                    onChanged: (v) => setState(() => _selectedPelangganId = v),
                  ),
                ),
                const SizedBox(height: 6),
                Text('Pilih pelanggan yang sudah ada atau tambahkan dari menu Pelanggan.',
                    style: const TextStyle(fontSize: 12, color: _helperColor,
                        fontWeight: FontWeight.w500)),

                // Show pelanggan info card after selection (like website)
                if (selectedPelanggan != null) ...[
                  const SizedBox(height: 16),
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(
                      color: _primaryBg,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: _border, width: 1.5),
                    ),
                    child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                      Row(children: [
                        Container(
                          padding: const EdgeInsets.all(6),
                          decoration: BoxDecoration(
                            color: _primary, borderRadius: BorderRadius.circular(8)),
                          child: const Icon(Icons.person_rounded, color: Colors.white, size: 16),
                        ),
                        const SizedBox(width: 10),
                        Expanded(child: Text(selectedPelanggan.nama,
                            style: const TextStyle(fontSize: 14,
                                fontWeight: FontWeight.w700, color: _primary))),
                      ]),
                      if (selectedPelanggan.alamat?.isNotEmpty ?? false) ...[
                        const SizedBox(height: 10),
                        _pelangganInfoRow(Icons.location_on_rounded, selectedPelanggan.alamat!),
                      ],
                      if (selectedPelanggan.telepon?.isNotEmpty ?? false) ...[
                        const SizedBox(height: 6),
                        _pelangganInfoRow(Icons.phone_rounded, selectedPelanggan.telepon!),
                      ],
                      if (selectedPelanggan.email?.isNotEmpty ?? false) ...[
                        const SizedBox(height: 6),
                        _pelangganInfoRow(Icons.email_rounded, selectedPelanggan.email!),
                      ],
                    ]),
                  ),
                ],

                const SizedBox(height: 20),

                // Tanggal Pesanan
                _dateTile('Tanggal Pesanan', _tanggalPesanan,
                    () => _selectDate(context, false)),

                const SizedBox(height: 18),

                // Tanggal Pengiriman
                _dateTile('Tanggal Pengiriman', _tanggalPengiriman,
                    () => _selectDate(context, true), required: true),
              ],
            )),

            const SizedBox(height: 16),

            // ── 2. Item Pesanan ──────────────────────────────────────────────
            _sectionCard(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
                  Expanded(child: _sectionHeader(Icons.inventory_2_rounded,
                      'Item Pesanan', 'Tambah produk dan atur jumlah pesanan')),
                  // Tambah Item button (top right like website)
                  ElevatedButton.icon(
                    onPressed: _addOrderItem,
                    icon: const Icon(Icons.add_rounded, size: 16),
                    label: const Text('Tambah Item',
                        style: TextStyle(fontSize: 13, fontWeight: FontWeight.w700)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: _primaryLight, foregroundColor: Colors.white,
                      elevation: 0, padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 10),
                      shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10)),
                    ),
                  ),
                ]),

                // Items table header
                if (_orderItems.isNotEmpty) ...[
                  _itemTableHeader(),
                  const SizedBox(height: 8),
                  ..._orderItems.asMap().entries.map((entry) {
                    final index = entry.key;
                    final item = entry.value;
                    final produk = produkList
                        .where((p) => p.id == item['produk_id'])
                        .firstOrNull;
                    return _itemTableRow(index, item, produk, produkList);
                  }),
                  const SizedBox(height: 16),
                ],

                // Product picker
                _labelText('Nama Produk'),
                InkWell(
                  onTap: () async {
                    final picked = await showDialog<Produk>(
                      context: context,
                      builder: (ctx) => ProductPickerDialog(
                        products: produkList,
                        selectedProduct: selectedProduk,
                      ),
                    );
                    if (picked != null) setState(() => _selectedProdukId = picked.id);
                  },
                  borderRadius: BorderRadius.circular(12),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                    decoration: BoxDecoration(
                      color: _bgField, borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: _border, width: 2),
                    ),
                    child: Row(children: [
                      // Product thumbnail
                      Container(
                        width: 44, height: 44,
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(8),
                          color: const Color(0xFFF1F5F9),
                        ),
                        child: selectedProduk?.fotoUrl != null
                            ? ClipRRect(borderRadius: BorderRadius.circular(8),
                                child: Image.network(selectedProduk!.fotoUrl!,
                                    fit: BoxFit.cover,
                                    errorBuilder: (_, __, ___) =>
                                        const Icon(Icons.image_not_supported,
                                            size: 20, color: _border)))
                            : const Icon(Icons.image_not_supported_outlined,
                                size: 20, color: _border),
                      ),
                      const SizedBox(width: 12),
                      Expanded(child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            selectedProduk?.nama ?? 'Pilih produk...',
                            style: TextStyle(fontSize: 14,
                                fontWeight: FontWeight.w600,
                                color: selectedProduk != null
                                    ? const Color(0xFF1E293B) : _helperColor),
                          ),
                          if (selectedProduk != null) ...[
                            const SizedBox(height: 3),
                            Row(children: [
                              Text(_formatRupiah(selectedProduk.hargaJual ?? 0),
                                  style: const TextStyle(fontSize: 12,
                                      color: _green, fontWeight: FontWeight.w700)),
                              const SizedBox(width: 8),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                    horizontal: 6, vertical: 2),
                                decoration: BoxDecoration(
                                  color: (selectedProduk.stok ?? 0) > 0
                                      ? _greenBg : const Color(0xFFFEF2F2),
                                  borderRadius: BorderRadius.circular(4),
                                ),
                                child: Text('Stok: ${selectedProduk.stok ?? 0}',
                                    style: TextStyle(fontSize: 10,
                                        fontWeight: FontWeight.w700,
                                        color: (selectedProduk.stok ?? 0) > 0
                                            ? const Color(0xFF047857)
                                            : _errorColor)),
                              ),
                            ]),
                          ],
                        ],
                      )),
                      const Icon(Icons.chevron_right_rounded, color: _border),
                    ]),
                  ),
                ),

                const SizedBox(height: 16),

                // QTY field
                _labelText('QTY'),
                TextFormField(
                  controller: _jumlahController,
                  keyboardType: TextInputType.number,
                  onChanged: (_) => setState(() {}),
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600,
                      color: Color(0xFF1E293B)),
                  decoration: _fieldDeco(hint: '0'),
                ),
              ],
            )),

            const SizedBox(height: 16),

            // ── 3. Verifikasi Stok ───────────────────────────────────────────
            if (showStok) ...[
              _buildStokVerificationCard(produkList),
              const SizedBox(height: 16),
            ],

            // ── 4. Ringkasan Pesanan ─────────────────────────────────────────
            if (_orderItems.isNotEmpty) ...[
              _sectionCard(child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  _sectionHeader(Icons.summarize_rounded, 'Ringkasan Pesanan',
                      'Total item dan nilai pesanan'),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Total Item', style: TextStyle(
                          fontSize: 13, color: _helperColor, fontWeight: FontWeight.w600)),
                      Text('${_orderItems.length} item', style: const TextStyle(
                          fontSize: 14, fontWeight: FontWeight.w700,
                          color: Color(0xFF0C2340))),
                    ],
                  ),
                  const Divider(height: 20, color: _border),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text('Total PO', style: TextStyle(
                          fontSize: 14, fontWeight: FontWeight.w700, color: _primary)),
                      Text(_formatRupiah(total), style: const TextStyle(
                          fontSize: 20, fontWeight: FontWeight.w900, color: _primary)),
                    ],
                  ),
                ],
              )),
              const SizedBox(height: 16),
            ],

            // ── 5. Catatan Khusus ────────────────────────────────────────────
            _sectionCard(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _sectionHeader(Icons.sticky_note_2_rounded, 'Catatan Khusus (Opsional)',
                    'Catatan tambahan untuk pesanan ini'),
                _labelText('Catatan', optional: true),
                TextFormField(
                  controller: _catatanController,
                  maxLines: 3,
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500,
                      color: Color(0xFF1E293B)),
                  decoration: _fieldDeco(hint: 'Catatan tambahan untuk pesanan ini...'),
                ),
              ],
            )),

            const SizedBox(height: 24),

            // ── Bottom action buttons ────────────────────────────────────────
            Container(
              padding: const EdgeInsets.only(top: 20),
              decoration: BoxDecoration(
                border: Border(top: BorderSide(
                    color: _primary.withValues(alpha: 0.1), width: 2)),
              ),
              child: Row(children: [
                Expanded(child: OutlinedButton.icon(
                  onPressed: _isSubmitting ? null : () => Navigator.pop(context),
                  icon: const Icon(Icons.arrow_back_rounded, size: 18),
                  label: const Text('Batal'),
                  style: OutlinedButton.styleFrom(
                    foregroundColor: _helperColor,
                    side: BorderSide(color: _primary.withValues(alpha: 0.25), width: 2),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12)),
                    textStyle: const TextStyle(fontSize: 14,
                        fontWeight: FontWeight.w700),
                  ),
                )),
                const SizedBox(width: 12),
                Expanded(flex: 2, child: ElevatedButton.icon(
                  onPressed: _isSubmitting ? null : _handleCreatePesanan,
                  icon: _isSubmitting
                      ? const SizedBox(width: 16, height: 16,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                      : const Icon(Icons.check_rounded, size: 18),
                  label: Text(_isSubmitting ? 'Menyimpan...' : 'Buat Pesanan'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: _primary, foregroundColor: Colors.white,
                    elevation: 0,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12)),
                    textStyle: const TextStyle(fontSize: 14,
                        fontWeight: FontWeight.w700),
                    shadowColor: _primary.withValues(alpha: 0.35),
                  ),
                )),
              ]),
            ),
          ]),
        );
      },
    );
  }

  // ── Item table ──────────────────────────────────────────────────────────────

  // _itemTableHeader not used (replaced by card layout below)
  Widget _itemTableHeader() => const SizedBox.shrink();

  /// Each order item is shown as a responsive card (no fixed-width columns)
  /// so it never overflows on narrow mobile screens (≤ 420 px).
  Widget _itemTableRow(int index, Map<String, dynamic> item,
      Produk? produk, List<Produk> produkList) {
    final nama = produk?.nama ?? 'Produk tidak ditemukan';
    final qty = item['jumlah'] as int;
    final harga = produk?.hargaJual ?? 0;
    final subtotal = harga * qty;

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: _border, width: 1.5),
      ),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        // Nomor
        Container(
          width: 24, height: 24, alignment: Alignment.center,
          decoration: BoxDecoration(color: _primaryBg,
              borderRadius: BorderRadius.circular(6)),
          child: Text('${index + 1}', style: const TextStyle(
              fontSize: 11, fontWeight: FontWeight.w800, color: _primary)),
        ),
        const SizedBox(width: 10),
        // Thumbnail
        Container(
          width: 52, height: 52,
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(8),
              color: const Color(0xFFF1F5F9)),
          child: produk?.fotoUrl != null
              ? ClipRRect(borderRadius: BorderRadius.circular(8),
                  child: Image.network(produk!.fotoUrl!, fit: BoxFit.cover,
                      errorBuilder: (_, __, ___) => const Icon(
                          Icons.image_not_supported_outlined,
                          size: 18, color: _border)))
              : const Icon(Icons.image_not_supported_outlined,
                  size: 18, color: _border),
        ),
        const SizedBox(width: 10),
        // Info – takes remaining width, no overflow
        Expanded(child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(nama, style: const TextStyle(fontSize: 13,
                fontWeight: FontWeight.w700, color: Color(0xFF1E293B))),
            const SizedBox(height: 6),
            Wrap(spacing: 6, runSpacing: 4, children: [
              _infoBadge('QTY', '$qty', _primaryBg, _primary),
              _infoBadge('Harga', _formatRupiah(harga),
                  const Color(0xFFF1F5F9), _helperColor),
            ]),
            const SizedBox(height: 6),
            Row(children: [
              const Text('Subtotal:', style: TextStyle(fontSize: 11,
                  color: _helperColor, fontWeight: FontWeight.w600)),
              const SizedBox(width: 6),
              Flexible(child: Text(_formatRupiah(subtotal), style: const TextStyle(
                  fontSize: 14, fontWeight: FontWeight.w900, color: _primary),
                  overflow: TextOverflow.ellipsis)),
            ]),
          ],
        )),
        const SizedBox(width: 8),
        // Delete
        GestureDetector(
          onTap: () => _removeOrderItem(index),
          child: Container(
            width: 30, height: 30,
            decoration: BoxDecoration(color: const Color(0xFFFEF2F2),
                borderRadius: BorderRadius.circular(8)),
            child: const Icon(Icons.delete_rounded,
                color: Color(0xFFEF4444), size: 16),
          ),
        ),
      ]),
    );
  }

  Widget _infoBadge(String label, String value, Color bg, Color textColor) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(6)),
      child: Text.rich(TextSpan(children: [
        TextSpan(text: '$label: ', style: TextStyle(
            fontSize: 10, fontWeight: FontWeight.w600,
            color: textColor.withValues(alpha: 0.7))),
        TextSpan(text: value, style: TextStyle(
            fontSize: 11, fontWeight: FontWeight.w800, color: textColor)),
      ])),
    );
  }

  Widget _pelangganInfoRow(IconData icon, String text) {
    return Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Icon(icon, size: 14, color: _primary),
      const SizedBox(width: 8),
      Expanded(child: Text(text, style: const TextStyle(fontSize: 12,
          fontWeight: FontWeight.w500, color: Color(0xFF334155)))),
    ]);
  }

  Widget _stokStatCol(String label, String value, Color valueColor) {
    return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
      Text(label, style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w600,
          color: _helperColor)),
      const SizedBox(height: 2),
      Text(value, style: TextStyle(fontSize: 15, fontWeight: FontWeight.w900,
          color: valueColor)),
    ]);
  }

  // ── Verifikasi Stok card ────────────────────────────────────────────────────

  Widget _buildStokVerificationCard(List<Produk> produkList) {
    final summary = _computeStockSummary(produkList, includeDraft: true);
    final hasShortage = summary.any((r) => r['cukup'] != true);

    return _sectionCard(child: Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          Expanded(child: _sectionHeader(Icons.verified_rounded, 'Verifikasi Stok',
              'Stok tersedia vs kebutuhan pesanan')),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
            decoration: BoxDecoration(
              color: hasShortage ? _errorColor : _green,
              borderRadius: BorderRadius.circular(20)),
            child: Text(hasShortage ? 'Stok Tidak Cukup' : 'Semua Stok Cukup',
                style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w700,
                    color: Colors.white)),
          ),
        ]),

        const SizedBox(height: 4),

        ...summary.map((row) {
          final produk = row['produk'] as Produk;
          final tersedia = row['tersedia'] as int;
          final kebutuhan = row['kebutuhan'] as int;
          final selisih = row['selisih'] as int;
          final cukup = row['cukup'] as bool;
          return Container(
            margin: const EdgeInsets.only(bottom: 8),
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: cukup ? const Color(0xFFF8FAFC) : const Color(0xFFFEF2F2),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(color: cukup ? _border : const Color(0xFFFECACA)),
            ),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              // Nama produk + badge status
              Row(children: [
                Expanded(child: Text(produk.nama, style: const TextStyle(
                    fontSize: 13, fontWeight: FontWeight.w700,
                    color: Color(0xFF1E293B)))),
                const SizedBox(width: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  decoration: BoxDecoration(
                    color: cukup ? _greenBg : const Color(0xFFFEE2E2),
                    borderRadius: BorderRadius.circular(8)),
                  child: Text(cukup ? 'Cukup' : 'Kurang',
                      style: TextStyle(fontSize: 11, fontWeight: FontWeight.w800,
                          color: cukup ? const Color(0xFF047857) : _errorColor)),
                ),
              ]),
              const SizedBox(height: 10),
              // Stats row: Tersedia / Kebutuhan / Selisih
              Row(children: [
                Expanded(child: _stokStatCol('Tersedia', '$tersedia',
                    const Color(0xFF1E293B))),
                Expanded(child: _stokStatCol('Kebutuhan', '$kebutuhan',
                    const Color(0xFF1E293B))),
                Expanded(child: _stokStatCol('Selisih',
                    selisih > 0 ? '+$selisih' : '$selisih',
                    cukup ? const Color(0xFF047857) : _errorColor)),
              ]),
            ]),
          );
        }),

        if (hasShortage) ...[
          const SizedBox(height: 8),
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: const Color(0xFFFEF2F2),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: const Color(0xFFFECACA))),
            child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
              const Icon(Icons.error_outline_rounded, color: _errorColor, size: 18),
              const SizedBox(width: 8),
              Expanded(child: Text(
                'Beberapa item melebihi stok. PO masih bisa disimpan. '
                'Kirim notifikasi ke Operator Gudang.',
                style: TextStyle(fontSize: 12, color: Colors.red[800], height: 1.4))),
            ]),
          ),
          const SizedBox(height: 12),
          if (_stokNotifTerkirim)
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 12),
              decoration: BoxDecoration(color: _greenBg,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: const Color(0xFFA7F3D0))),
              child: const Row(mainAxisAlignment: MainAxisAlignment.center, children: [
                Icon(Icons.check_circle_rounded, size: 16, color: Color(0xFF047857)),
                SizedBox(width: 6),
                Text('Notifikasi sudah dikirim ke Operator Gudang',
                    style: TextStyle(fontSize: 12, fontWeight: FontWeight.w700,
                        color: Color(0xFF047857))),
              ]),
            )
          else
            SizedBox(width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _isSendingStokNotif
                    ? null : () => _kirimNotifikasiStokKurang(summary),
                icon: _isSendingStokNotif
                    ? const SizedBox(width: 14, height: 14,
                        child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                    : const Icon(Icons.notifications_active_rounded, size: 16),
                label: Text(_isSendingStokNotif
                    ? 'Mengirim...'
                    : 'Kirim Notifikasi ke Operator Gudang',
                    textAlign: TextAlign.center),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFF59E0B), foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 12),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                ),
              ),
            ),
        ],
      ],
    ));
  }

  // ── UPDATE FORM ─────────────────────────────────────────────────────────────

  Widget _buildUpdateForm() {
    return Consumer<PesananProvider>(
      builder: (context, pesananProvider, _) {
        if (pesananProvider.isLoading) {
          return const LoadingWidget(message: 'Memuat pesanan...');
        }
        final pesanan = pesananProvider.selectedPesanan;
        if (pesanan == null) {
          return const Center(child: Text('Pesanan tidak ditemukan'));
        }
        if (_catatanController.text.isEmpty && pesanan.catatan != null) {
          _catatanController.text = pesanan.catatan ?? '';
        }
        _selectedStatus = pesanan.status ?? 'menunggu_konfirmasi';
        final userRole =
            Provider.of<AuthProvider>(context, listen: false).user?.role ?? '';
        final currentStatus = pesanan.status ?? 'menunggu_konfirmasi';
        final nextOptions = _nextStatusOptions(currentStatus, userRole);
        if (nextOptions.isNotEmpty && !nextOptions.contains(_selectedStatus)) {
          _selectedStatus = nextOptions.first;
        }

        return SingleChildScrollView(
          padding: const EdgeInsets.fromLTRB(16, 20, 16, 40),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

            // PO info banner
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(color: _primaryBg,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: _border, width: 1.5)),
              child: Row(children: [
                const Icon(Icons.info_outline_rounded, color: _primary, size: 20),
                const SizedBox(width: 10),
                Expanded(child: Text('Nomor PO: ${pesanan.nomorPo}',
                    style: const TextStyle(fontSize: 13, color: _primary,
                        fontWeight: FontWeight.w700))),
              ]),
            ),
            const SizedBox(height: 16),

            _sectionCard(child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                _sectionHeader(Icons.update_rounded, 'Perbarui Status',
                    'Status hanya bisa maju satu tahap'),
                if (nextOptions.isEmpty)
                  Container(
                    padding: const EdgeInsets.all(14),
                    decoration: BoxDecoration(color: const Color(0xFFF1F5F9),
                        borderRadius: BorderRadius.circular(10)),
                    child: const Text('Status pesanan sudah final dan tidak bisa diubah.',
                        style: TextStyle(color: _helperColor)),
                  )
                else ...[
                  _labelText('Status Baru', required: true),
                  Container(
                    decoration: BoxDecoration(color: _bgField,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: _border, width: 2)),
                    child: DropdownButton<String>(
                      value: _selectedStatus,
                      isExpanded: true,
                      underline: Container(),
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                      items: nextOptions.map((s) => DropdownMenuItem(
                          value: s, child: Text(_statusLabelText(s)))).toList(),
                      onChanged: (v) =>
                          setState(() => _selectedStatus = v ?? _selectedStatus),
                    ),
                  ),
                ],
                const SizedBox(height: 20),
                _labelText('Catatan', optional: true),
                TextFormField(
                  controller: _catatanController, maxLines: 3,
                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500,
                      color: Color(0xFF1E293B)),
                  decoration: _fieldDeco(hint: 'Tambahkan catatan pesanan...'),
                ),
              ],
            )),

            const SizedBox(height: 24),

            if (nextOptions.isNotEmpty) ...[
              Container(
                padding: const EdgeInsets.only(top: 20),
                decoration: BoxDecoration(border: Border(
                    top: BorderSide(color: _primary.withValues(alpha: 0.1), width: 2))),
                child: Row(children: [
                  Expanded(child: OutlinedButton.icon(
                    onPressed: _isSubmitting ? null : () => Navigator.pop(context),
                    icon: const Icon(Icons.arrow_back_rounded, size: 18),
                    label: const Text('Batal'),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: _helperColor,
                      side: BorderSide(color: _primary.withValues(alpha: 0.25), width: 2),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      textStyle: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
                    ),
                  )),
                  const SizedBox(width: 12),
                  Expanded(flex: 2, child: ElevatedButton.icon(
                    onPressed: _isSubmitting ? null : _handleUpdatePesanan,
                    icon: _isSubmitting
                        ? const SizedBox(width: 16, height: 16,
                            child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                        : const Icon(Icons.check_rounded, size: 18),
                    label: Text(_isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: _primary, foregroundColor: Colors.white,
                      elevation: 0, padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                      textStyle: const TextStyle(fontSize: 14, fontWeight: FontWeight.w700),
                    ),
                  )),
                ]),
              ),
            ],
          ]),
        );
      },
    );
  }
}