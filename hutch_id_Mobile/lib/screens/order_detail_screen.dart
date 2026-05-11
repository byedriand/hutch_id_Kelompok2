import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../models/models.dart';
import '../widgets/widgets.dart';
import 'pdf_preview_screen.dart';

class OrderDetailScreen extends StatelessWidget {
  final PurchaseOrder po;
  const OrderDetailScreen({super.key, required this.po});

  String _rp(double v) {
    final s = v.toInt().toString();
    final buf = StringBuffer('Rp ');
    for (int i = 0; i < s.length; i++) {
      if (i > 0 && (s.length - i) % 3 == 0) buf.write('.');
      buf.write(s[i]);
    }
    return buf.toString();
  }

  String _date(DateTime d) {
    const m = [
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Agu',
      'Sep',
      'Okt',
      'Nov',
      'Des'
    ];
    return '${d.day} ${m[d.month - 1]} ${d.year}';
  }

  bool get _hasStockIssue => po.stokItems.any((s) => !s.cukup);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: hutchAppBar(
        title: po.nomorPo,
        subtitle: po.pelanggan.nama,
        context: context,
        actions: [
          IconButton(
            icon: const Icon(Icons.picture_as_pdf_outlined),
            onPressed: () => Navigator.push(context,
                MaterialPageRoute(builder: (_) => PdfPreviewScreen(po: po))),
          ),
          IconButton(
            icon: const Icon(Icons.share_outlined),
            onPressed: () => _showShare(context),
          ),
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.only(bottom: 100),
        children: [
          // Status banner
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                StatusBadge(po.status),
                Text(_date(po.tanggalPesanan),
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 12, color: AppColors.gray)),
              ],
            ),
          ),

          // Stock warning
          if (po.stokItems.isNotEmpty && _hasStockIssue)
            Container(
              margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFFEF2F2),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: const Color(0xFFFECACA)),
              ),
              child: Row(children: [
                const Icon(Icons.warning_amber_rounded,
                    color: AppColors.red, size: 18),
                const SizedBox(width: 8),
                Expanded(
                    child: Text(
                        'Bahan baku tidak mencukupi — verifikasi manual diperlukan',
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: AppColors.red))),
              ]),
            ),

          // Stock OK
          if (po.stokItems.isNotEmpty && !_hasStockIssue)
            Container(
              margin: const EdgeInsets.fromLTRB(16, 12, 16, 0),
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: const Color(0xFFF0FDF4),
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: const Color(0xFFBBF7D0)),
              ),
              child: Row(children: [
                const Icon(Icons.check_circle_outline,
                    color: AppColors.green, size: 18),
                const SizedBox(width: 8),
                Text('Seluruh bahan baku tersedia',
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 12,
                        fontWeight: FontWeight.w600,
                        color: AppColors.green)),
              ]),
            ),

          // Detail info
          SectionCard(
            title: '📦 Detail Pesanan',
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 14),
              child: Column(children: [
                InfoRow(label: 'Nomor PO', value: po.nomorPo, isCode: true),
                const Divider(),
                InfoRow(
                    label: 'Tanggal Pesanan', value: _date(po.tanggalPesanan)),
                const Divider(),
                InfoRow(label: 'Tanggal Kirim', value: _date(po.tanggalKirim)),
                const Divider(),
                InfoRow(label: 'Pelanggan', value: po.pelanggan.nama),
                const Divider(),
                InfoRow(label: 'Telepon', value: po.pelanggan.telepon),
                const Divider(),
                InfoRow(label: 'Email', value: po.pelanggan.email),
                if (po.catatanKhusus != null) ...[
                  const Divider(),
                  InfoRow(label: 'Catatan Khusus', value: po.catatanKhusus!),
                ],
                const SizedBox(height: 4),
              ]),
            ),
          ),

          // Items
          SectionCard(
            title: '🛍 Item Pesanan',
            child: Column(children: [
              // Header
              Container(
                color: const Color(0xFFF8FAFC),
                padding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                child: Row(children: [
                  Expanded(
                      flex: 3,
                      child: Text('Produk',
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 10.5,
                              fontWeight: FontWeight.w700,
                              color: AppColors.gray,
                              letterSpacing: .5))),
                  Expanded(
                      flex: 1,
                      child: Text('Qty',
                          textAlign: TextAlign.center,
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 10.5,
                              fontWeight: FontWeight.w700,
                              color: AppColors.gray))),
                  Expanded(
                      flex: 2,
                      child: Text('Subtotal',
                          textAlign: TextAlign.right,
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 10.5,
                              fontWeight: FontWeight.w700,
                              color: AppColors.gray))),
                ]),
              ),
              const Divider(),
              ...po.items.asMap().entries.map((e) => Column(children: [
                    Padding(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 14, vertical: 10),
                      child: Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(
                                flex: 3,
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(e.value.produk,
                                        style: GoogleFonts.plusJakartaSans(
                                            fontSize: 12.5,
                                            fontWeight: FontWeight.w700,
                                            color: AppColors.navy)),
                                    Text(e.value.spesifikasi,
                                        style: GoogleFonts.plusJakartaSans(
                                            fontSize: 11,
                                            color: AppColors.gray)),
                                  ],
                                )),
                            Expanded(
                                flex: 1,
                                child: Text('${e.value.jumlah} pcs',
                                    textAlign: TextAlign.center,
                                    style: GoogleFonts.plusJakartaSans(
                                        fontSize: 12, color: AppColors.navy))),
                            Expanded(
                                flex: 2,
                                child: Text(_rp(e.value.subtotal),
                                    textAlign: TextAlign.right,
                                    style: GoogleFonts.plusJakartaSans(
                                        fontSize: 12.5,
                                        fontWeight: FontWeight.w700,
                                        color: AppColors.navy))),
                          ]),
                    ),
                    if (e.key < po.items.length - 1) const Divider(),
                  ])),
              Container(
                padding: const EdgeInsets.all(14),
                decoration: const BoxDecoration(
                    border: Border(top: BorderSide(color: AppColors.border))),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text('Total Nilai PO',
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 12, color: AppColors.gray)),
                    Text(_rp(po.totalNilai),
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 18,
                            fontWeight: FontWeight.w800,
                            color: AppColors.navy)),
                  ],
                ),
              ),
            ]),
          ),

          // Stok
          if (po.stokItems.isNotEmpty)
            SectionCard(
              title: '🧵 Ketersediaan Bahan Baku',
              child: Column(children: [
                Container(
                  color: const Color(0xFFF8FAFC),
                  padding:
                      const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  child: Row(children: [
                    Expanded(
                        flex: 3,
                        child: Text('Bahan Baku',
                            style: GoogleFonts.plusJakartaSans(
                                fontSize: 10.5,
                                fontWeight: FontWeight.w700,
                                color: AppColors.gray))),
                    Expanded(
                        flex: 2,
                        child: Text('Butuh',
                            textAlign: TextAlign.center,
                            style: GoogleFonts.plusJakartaSans(
                                fontSize: 10.5,
                                fontWeight: FontWeight.w700,
                                color: AppColors.gray))),
                    Expanded(
                        flex: 2,
                        child: Text('Ada',
                            textAlign: TextAlign.center,
                            style: GoogleFonts.plusJakartaSans(
                                fontSize: 10.5,
                                fontWeight: FontWeight.w700,
                                color: AppColors.gray))),
                    const Expanded(
                        flex: 1, child: Text('', textAlign: TextAlign.center)),
                  ]),
                ),
                const Divider(),
                ...po.stokItems.asMap().entries.map((e) => Column(children: [
                      StockRow(e.value),
                      if (e.key < po.stokItems.length - 1) const Divider(),
                    ])),
                const SizedBox(height: 6),
              ]),
            ),

          // Histori
          if (po.histori.isNotEmpty)
            SectionCard(
              title: '🔄 Histori Status',
              child: Padding(
                padding: const EdgeInsets.fromLTRB(24, 12, 16, 14),
                child: Column(children: [
                  ...po.histori.asMap().entries.map((e) => _TimelineItem(
                        item: e.value,
                        isLast: e.key == po.histori.length - 1,
                        isCurrent: false,
                      )),
                  _TimelineItem(
                    item: StatusHistory(
                      status: po.status.label,
                      waktu: 'Status saat ini',
                      oleh: '',
                    ),
                    isLast: true,
                    isCurrent: true,
                  ),
                ]),
              ),
            ),
        ],
      ),

      // Action buttons
      bottomNavigationBar: _buildActions(context),
    );
  }

  Widget _buildActions(BuildContext context) {
    if (po.status == PoStatus.selesai || po.status == PoStatus.dibatalkan) {
      return const SizedBox.shrink();
    }

    return Container(
      padding: const EdgeInsets.fromLTRB(16, 12, 16, 24),
      decoration: const BoxDecoration(
        color: AppColors.white,
        border: Border(top: BorderSide(color: AppColors.border)),
      ),
      child: Row(children: [
        if (po.status == PoStatus.menungguKonfirmasi) ...[
          Expanded(
            child: HutchButton(
              label: 'Batalkan',
              bg: const Color(0xFFFEE2E2),
              fg: AppColors.red,
              icon: Icons.close,
              onPressed: () => _confirmAction(context, 'batalkan'),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: HutchButton(
              label: 'Konfirmasi PO',
              icon: Icons.check,
              onPressed: () => _confirmAction(context, 'konfirmasi'),
            ),
          ),
        ],
        if (po.status == PoStatus.dikonfirmasi)
          Expanded(
            child: HutchButton(
              label: 'Mulai Produksi',
              icon: Icons.play_arrow,
              fullWidth: true,
              bg: AppColors.purple,
              onPressed: () => _confirmAction(context, 'mulai produksi'),
            ),
          ),
        if (po.status == PoStatus.dalamProduksi)
          Expanded(
            child: HutchButton(
              label: 'Selesai Produksi',
              icon: Icons.done_all,
              fullWidth: true,
              bg: AppColors.green,
              onPressed: () => _confirmAction(context, 'selesaikan produksi'),
            ),
          ),
        if (po.status == PoStatus.siapKirim)
          Expanded(
            child: HutchButton(
              label: 'Tandai Selesai',
              icon: Icons.flag,
              fullWidth: true,
              onPressed: () => _confirmAction(context, 'tandai selesai'),
            ),
          ),
      ]),
    );
  }

  void _confirmAction(BuildContext context, String action) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => Padding(
        padding: const EdgeInsets.fromLTRB(20, 20, 20, 36),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(
              width: 36,
              height: 4,
              decoration: BoxDecoration(
                  color: AppColors.border,
                  borderRadius: BorderRadius.circular(2))),
          const SizedBox(height: 16),
          Text('Konfirmasi Tindakan',
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 16, fontWeight: FontWeight.w700)),
          const SizedBox(height: 8),
          Text('Apakah Anda yakin ingin $action PO ini?',
              textAlign: TextAlign.center,
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 13, color: AppColors.gray)),
          const SizedBox(height: 20),
          Row(children: [
            Expanded(
                child: HutchButton(
              label: 'Batal',
              outlined: true,
              onPressed: () => Navigator.pop(context),
            )),
            const SizedBox(width: 12),
            Expanded(
                child: HutchButton(
              label: 'Ya, Lanjutkan',
              onPressed: () {
                Navigator.pop(context);
                Navigator.pop(context);
                ScaffoldMessenger.of(context).showSnackBar(
                  SnackBar(
                    content: Text('Berhasil: PO telah di-$action',
                        style: GoogleFonts.plusJakartaSans()),
                    backgroundColor: AppColors.green,
                    behavior: SnackBarBehavior.floating,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10)),
                  ),
                );
              },
            )),
          ]),
        ]),
      ),
    );
  }

  void _showShare(BuildContext context) {
    showModalBottomSheet(
      context: context,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => Padding(
        padding: const EdgeInsets.fromLTRB(20, 20, 20, 36),
        child: Column(mainAxisSize: MainAxisSize.min, children: [
          Container(
              width: 36,
              height: 4,
              decoration: BoxDecoration(
                  color: AppColors.border,
                  borderRadius: BorderRadius.circular(2))),
          const SizedBox(height: 16),
          Text('Bagikan PO',
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 16, fontWeight: FontWeight.w700)),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
                color: AppColors.bg,
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: AppColors.border)),
            child: Row(children: [
              Expanded(
                  child: Text('https://hutch.id/po/abc123xyz',
                      style: GoogleFonts.firaCode(
                          fontSize: 11.5, color: AppColors.accent))),
              const SizedBox(width: 8),
              const Icon(Icons.copy, size: 16, color: AppColors.gray),
            ]),
          ),
          const SizedBox(height: 8),
          Text('Link valid selama 24 jam · REQ-PO-022',
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 11, color: AppColors.gray)),
        ]),
      ),
    );
  }
}

class _TimelineItem extends StatelessWidget {
  final StatusHistory item;
  final bool isLast;
  final bool isCurrent;

  const _TimelineItem(
      {required this.item, required this.isLast, required this.isCurrent});

  @override
  Widget build(BuildContext context) {
    return IntrinsicHeight(
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Column(children: [
          Container(
            width: 12,
            height: 12,
            decoration: BoxDecoration(
              color: isCurrent ? AppColors.accent : AppColors.green,
              shape: BoxShape.circle,
              border: Border.all(color: Colors.white, width: 2),
              boxShadow: [
                BoxShadow(
                    color: (isCurrent ? AppColors.accent : AppColors.green)
                        .withAlpha(102),
                    blurRadius: 4)
              ],
            ),
          ),
          if (!isLast)
            Expanded(
              child: Container(width: 2, color: AppColors.border),
            ),
        ]),
        const SizedBox(width: 14),
        Expanded(
          child: Padding(
            padding: const EdgeInsets.only(bottom: 16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item.status,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 12.5,
                        fontWeight: FontWeight.w700,
                        color: isCurrent ? AppColors.yellow : AppColors.navy)),
                if (item.waktu.isNotEmpty)
                  Text(
                      '${item.waktu}${item.oleh.isNotEmpty ? ' · ${item.oleh}' : ''}',
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 11, color: AppColors.gray)),
              ],
            ),
          ),
        ),
      ]),
    );
  }
}
