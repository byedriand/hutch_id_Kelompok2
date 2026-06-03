import 'dart:io';
import 'package:flutter/material.dart';
import 'package:pdf/pdf.dart';
import 'package:pdf/widgets.dart' as pw;
import 'package:printing/printing.dart';
import 'package:share_plus/share_plus.dart';
import 'package:path_provider/path_provider.dart';
import '../../models/pelanggan_model.dart';
import '../../utils/responsive.dart';

class LihatCetakPoScreen extends StatefulWidget {
  final Map<String, dynamic> pesanan;
  final List<Pelanggan> pelangganList;

  const LihatCetakPoScreen({
    super.key,
    required this.pesanan,
    required this.pelangganList,
  });

  @override
  State<LihatCetakPoScreen> createState() => _LihatCetakPoScreenState();
}

class _LihatCetakPoScreenState extends State<LihatCetakPoScreen> with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late Animation<double> _fadeAnimation;
  late Animation<Offset> _slideAnimation;
  late Pelanggan _pelanggan;
  bool _isGeneratingPdf = false;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 600),
    );
    _fadeAnimation = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _slideAnimation = Tween<Offset>(begin: const Offset(0.0, 0.1), end: Offset.zero)
        .animate(CurvedAnimation(parent: _animController, curve: Curves.easeOutCubic));

    _animController.forward();
    _resolvePelanggan();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  void _resolvePelanggan() {
    final pelangganNama = widget.pesanan['pelanggan'] ?? '';
    try {
      _pelanggan = widget.pelangganList.firstWhere(
        (p) => p.nama.toLowerCase() == pelangganNama.toString().toLowerCase(),
        orElse: () => Pelanggan(
          id: '',
          nama: pelangganNama.toString().isNotEmpty ? pelangganNama.toString() : 'Umum',
          telepon: '-',
          alamat: 'Tidak ada alamat terdaftar',
          email: '-',
          jumlahPO: 0,
        ),
      );
    } catch (_) {
      _pelanggan = Pelanggan(
        id: '',
        nama: 'Umum',
        telepon: '-',
        alamat: 'Tidak ada alamat terdaftar',
        email: '-',
        jumlahPO: 0,
      );
    }
  }

  String _formatCurrency(double amount) {
    return 'Rp ${amount.toStringAsFixed(0).replaceAllMapped(RegExp(r"(\d{1,3})(?=(\d{3})+(?!\d))"), (Match m) => "${m[1]}.")}';
  }

  Future<pw.Document> _buildPdfDocument() async {
    final pdf = pw.Document();

    final double hargaUnit = (widget.pesanan['harga'] ?? 0).toDouble();
    final int qty = widget.pesanan['jumlah'] ?? 0;
    final double total = hargaUnit * qty;
    final String deskripsi = widget.pesanan['deskripsi'] ?? 'Produk Custom';
    final String noPo = widget.pesanan['no'] ?? 'PO-NEW';
    final String tanggal = widget.pesanan['tanggal'] ?? '';
    final String status = widget.pesanan['status'] ?? 'Pending';

    pdf.addPage(
      pw.Page(
        pageFormat: PdfPageFormat.a4,
        margin: const pw.EdgeInsets.all(32),
        build: (pw.Context context) {
          return pw.Column(
            crossAxisAlignment: pw.CrossAxisAlignment.start,
            children: [
              // Header
              pw.Row(
                mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
                children: [
                  pw.Column(
                    crossAxisAlignment: pw.CrossAxisAlignment.start,
                    children: [
                      pw.Text(
                        'HUTCH.ID',
                        style: pw.TextStyle(
                          fontSize: 22,
                          fontWeight: pw.FontWeight.bold,
                          color: PdfColor.fromHex('#1e3a8a'),
                        ),
                      ),
                      pw.SizedBox(height: 2),
                      pw.Text(
                        'Bag Manufacturing & In-House Brand',
                        style: pw.TextStyle(
                          fontSize: 9,
                          fontWeight: pw.FontWeight.bold,
                          color: PdfColor.fromHex('#475569'),
                        ),
                      ),
                      pw.SizedBox(height: 4),
                      pw.Text('Jl. Terusan Halimun No. 37, Bandung', style: const pw.TextStyle(fontSize: 8)),
                      pw.Text('Telp: +62 812-3456-7890 | Email: info@hutch.id', style: const pw.TextStyle(fontSize: 8)),
                    ],
                  ),
                  pw.Column(
                    crossAxisAlignment: pw.CrossAxisAlignment.end,
                    children: [
                      pw.Text(
                        'PURCHASE ORDER',
                        style: pw.TextStyle(
                          fontSize: 18,
                          fontWeight: pw.FontWeight.bold,
                          color: PdfColor.fromHex('#1e3a8a'),
                        ),
                      ),
                      pw.SizedBox(height: 4),
                      pw.Text('No. PO: $noPo', style: pw.TextStyle(fontSize: 9, fontWeight: pw.FontWeight.bold)),
                      pw.Text('Tanggal: $tanggal', style: const pw.TextStyle(fontSize: 8)),
                      pw.Text('Status: $status', style: pw.TextStyle(fontSize: 8, fontWeight: pw.FontWeight.bold)),
                    ],
                  ),
                ],
              ),
              pw.SizedBox(height: 10),
              pw.Divider(thickness: 1, color: PdfColor.fromHex('#cbd5e1')),
              pw.SizedBox(height: 15),

              // Customer / Shipping Info
              pw.Row(
                crossAxisAlignment: pw.CrossAxisAlignment.start,
                children: [
                  pw.Expanded(
                    child: pw.Column(
                      crossAxisAlignment: pw.CrossAxisAlignment.start,
                      children: [
                        pw.Text(
                          'BILL TO / PELANGGAN:',
                          style: pw.TextStyle(
                            fontSize: 9,
                            fontWeight: pw.FontWeight.bold,
                            color: PdfColor.fromHex('#475569'),
                          ),
                        ),
                        pw.SizedBox(height: 4),
                        pw.Text(
                          _pelanggan.nama,
                          style: pw.TextStyle(
                            fontSize: 11,
                            fontWeight: pw.FontWeight.bold,
                            color: PdfColor.fromHex('#0f172a'),
                          ),
                        ),
                        pw.SizedBox(height: 2),
                        pw.Text('Alamat: ${_pelanggan.alamat}', style: const pw.TextStyle(fontSize: 8)),
                        pw.Text('Telepon: ${_pelanggan.telepon}', style: const pw.TextStyle(fontSize: 8)),
                        pw.Text('Email: ${_pelanggan.email}', style: const pw.TextStyle(fontSize: 8)),
                      ],
                    ),
                  ),
                ],
              ),
              pw.SizedBox(height: 25),

              // Table header
              pw.Table(
                border: const pw.TableBorder(
                  bottom: pw.BorderSide(color: PdfColors.grey, width: 0.5),
                  horizontalInside: pw.BorderSide(color: PdfColors.grey, width: 0.5),
                ),
                columnWidths: {
                  0: const pw.FixedColumnWidth(30),
                  1: const pw.FlexColumnWidth(3),
                  2: const pw.FixedColumnWidth(50),
                  3: const pw.FixedColumnWidth(100),
                  4: const pw.FixedColumnWidth(100),
                },
                children: [
                  pw.TableRow(
                    decoration: pw.BoxDecoration(
                      color: PdfColor.fromHex('#f1f5f9'),
                    ),
                    children: [
                      _buildPdfTableCell('No.', isHeader: true),
                      _buildPdfTableCell('Deskripsi Produk', isHeader: true),
                      _buildPdfTableCell('Qty', isHeader: true, align: pw.TextAlign.center),
                      _buildPdfTableCell('Harga Satuan', isHeader: true, align: pw.TextAlign.right),
                      _buildPdfTableCell('Subtotal', isHeader: true, align: pw.TextAlign.right),
                    ],
                  ),
                  pw.TableRow(
                    children: [
                      _buildPdfTableCell('1'),
                      _buildPdfTableCell(deskripsi),
                      _buildPdfTableCell('$qty Pcs', align: pw.TextAlign.center),
                      _buildPdfTableCell(_formatCurrency(hargaUnit), align: pw.TextAlign.right),
                      _buildPdfTableCell(_formatCurrency(total), align: pw.TextAlign.right),
                    ],
                  ),
                ],
              ),
              pw.SizedBox(height: 20),

              // Summary
              pw.Row(
                mainAxisAlignment: pw.MainAxisAlignment.end,
                children: [
                  pw.Column(
                    crossAxisAlignment: pw.CrossAxisAlignment.end,
                    children: [
                      pw.Row(
                        children: [
                          pw.Text(
                            'Grand Total:  ',
                            style: pw.TextStyle(fontSize: 12, fontWeight: pw.FontWeight.bold, color: PdfColor.fromHex('#475569')),
                          ),
                          pw.Text(
                            _formatCurrency(total),
                            style: pw.TextStyle(fontSize: 14, fontWeight: pw.FontWeight.bold, color: PdfColor.fromHex('#1e3a8a')),
                          ),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
              pw.SizedBox(height: 40),

              // Signatures
              pw.Row(
                mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
                children: [
                  pw.Column(
                    crossAxisAlignment: pw.CrossAxisAlignment.center,
                    children: [
                      pw.Text('Dibuat Oleh,', style: const pw.TextStyle(fontSize: 9)),
                      pw.SizedBox(height: 50),
                      pw.Container(width: 120, height: 1, color: PdfColors.black),
                      pw.SizedBox(height: 3),
                      pw.Text('Staf Penjualan', style: pw.TextStyle(fontSize: 8, fontWeight: pw.FontWeight.bold)),
                    ],
                  ),
                  pw.Column(
                    crossAxisAlignment: pw.CrossAxisAlignment.center,
                    children: [
                      pw.Text('Disetujui Oleh,', style: const pw.TextStyle(fontSize: 9)),
                      pw.SizedBox(height: 50),
                      pw.Container(width: 120, height: 1, color: PdfColors.black),
                      pw.SizedBox(height: 3),
                      pw.Text('Pemilik UMKM', style: pw.TextStyle(fontSize: 8, fontWeight: pw.FontWeight.bold)),
                    ],
                  ),
                ],
              ),
              pw.Spacer(),

              // Footer
              pw.Divider(thickness: 0.5, color: PdfColors.grey),
              pw.Row(
                mainAxisAlignment: pw.MainAxisAlignment.spaceBetween,
                children: [
                  pw.Text('HUTCH.ID — Purchase Order Dokumen Resmi', style: const pw.TextStyle(fontSize: 7, color: PdfColors.grey)),
                  pw.Text('Halaman 1 dari 1', style: const pw.TextStyle(fontSize: 7, color: PdfColors.grey)),
                ],
              ),
            ],
          );
        },
      ),
    );

    return pdf;
  }

  pw.Widget _buildPdfTableCell(String text, {bool isHeader = false, pw.TextAlign align = pw.TextAlign.left}) {
    return pw.Padding(
      padding: const pw.EdgeInsets.symmetric(horizontal: 6, vertical: 8),
      child: pw.Text(
        text,
        textAlign: align,
        style: pw.TextStyle(
          fontSize: isHeader ? 8 : 8,
          fontWeight: isHeader ? pw.FontWeight.bold : pw.FontWeight.normal,
        ),
      ),
    );
  }

  Future<void> _cetakPo() async {
    setState(() => _isGeneratingPdf = true);
    try {
      final doc = await _buildPdfDocument();
      await Printing.layoutPdf(
        onLayout: (PdfPageFormat format) async => doc.save(),
        name: 'PO_${widget.pesanan['no']}_${_pelanggan.nama}.pdf',
      );
    } catch (e) {
      debugPrint('Error printing: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal mencetak dokumen: $e'), backgroundColor: Colors.red),
        );
      }
    } finally {
      setState(() => _isGeneratingPdf = false);
    }
  }

  Future<void> _bagikanPo() async {
    setState(() => _isGeneratingPdf = true);
    try {
      final doc = await _buildPdfDocument();
      final pdfBytes = await doc.save();
      final tempDir = await getTemporaryDirectory();
      final file = File('${tempDir.path}/PO_${widget.pesanan['no']}_${_pelanggan.nama.replaceAll(' ', '_')}.pdf');
      await file.writeAsBytes(pdfBytes);

      await Share.shareXFiles(
        [XFile(file.path)],
        text: 'Purchase Order HUTCH.ID - ${widget.pesanan['no']} untuk ${_pelanggan.nama}',
      );
    } catch (e) {
      debugPrint('Error sharing: $e');
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Gagal membagikan dokumen: $e'), backgroundColor: Colors.red),
        );
      }
    } finally {
      setState(() => _isGeneratingPdf = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final bool isMobile = Responsive.isMobile(context);
    final double hargaUnit = (widget.pesanan['harga'] ?? 0).toDouble();
    final int qty = widget.pesanan['jumlah'] ?? 0;
    final double total = hargaUnit * qty;
    final String deskripsi = widget.pesanan['deskripsi'] ?? 'Produk Custom';
    final String noPo = widget.pesanan['no'] ?? 'PO-NEW';
    final String tanggal = widget.pesanan['tanggal'] ?? '';
    final String status = widget.pesanan['status'] ?? 'Pending';

    // Status colors
    Color statusColor = Colors.orange;
    if (status == 'Proses') statusColor = Colors.blue;
    if (status == 'Selesai') statusColor = Colors.green;
    if (status == 'Dalam Produksi') statusColor = const Color(0xFF8B5CF6);
    if (status == 'Draft') statusColor = Colors.grey;

    return Scaffold(
      backgroundColor: const Color(0xFF0F172A), // Dark premium slate background
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_ios_new_rounded, color: Colors.white),
          onPressed: () => Navigator.pop(context),
        ),
        title: Text(
          'Purchase Order $noPo',
          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 16),
        ),
        centerTitle: true,
      ),
      body: SafeArea(
        child: FadeTransition(
          opacity: _fadeAnimation,
          child: SlideTransition(
            position: _slideAnimation,
            child: Padding(
              padding: EdgeInsets.symmetric(horizontal: isMobile ? 12.0 : 40.0, vertical: 12.0),
              child: Column(
                children: [
                  Expanded(
                    child: SingleChildScrollView(
                      physics: const BouncingScrollPhysics(),
                      child: Center(
                        child: Container(
                          constraints: const BoxConstraints(maxWidth: 800),
                          margin: const EdgeInsets.only(bottom: 24),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(24),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withValues(alpha: 0.4),
                                blurRadius: 30,
                                offset: const Offset(0, 10),
                              ),
                            ],
                          ),
                          child: ClipRRect(
                            borderRadius: BorderRadius.circular(24),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                // Top Accent Bar
                                Container(
                                  height: 8,
                                  color: const Color(0xFF2563EB),
                                ),
                                Padding(
                                  padding: const EdgeInsets.all(24.0),
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      // Company Header & Logo
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Row(
                                                children: [
                                                  Container(
                                                    padding: const EdgeInsets.all(8),
                                                    decoration: BoxDecoration(
                                                      color: const Color(0xFF2563EB).withValues(alpha: 0.1),
                                                      borderRadius: BorderRadius.circular(10),
                                                    ),
                                                    child: const Icon(Icons.shopping_bag_rounded, color: Color(0xFF2563EB), size: 24),
                                                  ),
                                                  const SizedBox(width: 10),
                                                  const Text(
                                                    'HUTCH.ID',
                                                    style: TextStyle(
                                                      fontSize: 22,
                                                      fontWeight: FontWeight.w900,
                                                      color: Color(0xFF1E3A8A),
                                                      letterSpacing: 1.5,
                                                    ),
                                                  ),
                                                ],
                                              ),
                                              const SizedBox(height: 8),
                                              Text(
                                                'Bag Manufacturing & In-House Brand',
                                                style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.grey[600]),
                                              ),
                                              const SizedBox(height: 4),
                                              Text('Jl. Terusan Halimun No. 37, Bandung', style: TextStyle(fontSize: 9, color: Colors.grey[500])),
                                              Text('Telp: +62 812-3456-7890 | Email: info@hutch.id', style: TextStyle(fontSize: 9, color: Colors.grey[500])),
                                            ],
                                          ),
                                          if (!isMobile)
                                            Column(
                                              crossAxisAlignment: CrossAxisAlignment.end,
                                              children: [
                                                const Text(
                                                  'PURCHASE ORDER',
                                                  style: TextStyle(
                                                    fontSize: 16,
                                                    fontWeight: FontWeight.bold,
                                                    color: Color(0xFF2563EB),
                                                    letterSpacing: 1,
                                                  ),
                                                ),
                                                const SizedBox(height: 6),
                                                Text('No: $noPo', style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w700)),
                                                Text('Tanggal: $tanggal', style: TextStyle(fontSize: 9, color: Colors.grey[600])),
                                                const SizedBox(height: 4),
                                                Container(
                                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                                  decoration: BoxDecoration(
                                                    color: statusColor.withValues(alpha: 0.12),
                                                    borderRadius: BorderRadius.circular(8),
                                                    border: Border.all(color: statusColor.withValues(alpha: 0.2)),
                                                  ),
                                                  child: Text(
                                                    status,
                                                    style: TextStyle(fontSize: 9, fontWeight: FontWeight.w700, color: statusColor),
                                                  ),
                                                ),
                                              ],
                                            ),
                                        ],
                                      ),
                                      if (isMobile) ...[
                                        const SizedBox(height: 16),
                                        const Divider(),
                                        const SizedBox(height: 8),
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Text('PO No: $noPo', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                                                Text('Tanggal: $tanggal', style: TextStyle(fontSize: 10, color: Colors.grey[600])),
                                              ],
                                            ),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                                              decoration: BoxDecoration(
                                                color: statusColor.withValues(alpha: 0.12),
                                                borderRadius: BorderRadius.circular(8),
                                                border: Border.all(color: statusColor.withValues(alpha: 0.2)),
                                              ),
                                              child: Text(
                                                status,
                                                style: TextStyle(fontSize: 9, fontWeight: FontWeight.w700, color: statusColor),
                                              ),
                                            ),
                                          ],
                                        ),
                                      ],
                                      const SizedBox(height: 16),
                                      const Divider(),
                                      const SizedBox(height: 16),

                                      // Billing Details
                                      Row(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                const Text(
                                                  'BILL TO / DIBAYAR OLEH:',
                                                  style: TextStyle(
                                                    fontSize: 10,
                                                    fontWeight: FontWeight.bold,
                                                    color: Colors.blueAccent,
                                                    letterSpacing: 0.5,
                                                  ),
                                                ),
                                                const SizedBox(height: 8),
                                                Text(
                                                  _pelanggan.nama,
                                                  style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                                                ),
                                                const SizedBox(height: 4),
                                                Text('Alamat: ${_pelanggan.alamat}', style: TextStyle(fontSize: 11, color: Colors.grey[700])),
                                                Text('Telepon: ${_pelanggan.telepon}', style: TextStyle(fontSize: 11, color: Colors.grey[700])),
                                                Text('Email: ${_pelanggan.email}', style: TextStyle(fontSize: 11, color: Colors.grey[700])),
                                              ],
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 32),

                                      // Table
                                      const Text(
                                        'DETAIL PESANAN',
                                        style: TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF1E3A8A), letterSpacing: 0.5),
                                      ),
                                      const SizedBox(height: 8),
                                      Container(
                                        decoration: BoxDecoration(
                                          border: Border.all(color: Colors.grey[200]!),
                                          borderRadius: BorderRadius.circular(12),
                                        ),
                                        child: Table(
                                          columnWidths: const {
                                            0: FlexColumnWidth(3),
                                            1: FixedColumnWidth(60),
                                            2: FixedColumnWidth(110),
                                            3: FixedColumnWidth(110),
                                          },
                                          children: [
                                            // Table Header
                                            TableRow(
                                              decoration: BoxDecoration(
                                                color: Colors.grey[50],
                                                borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
                                              ),
                                              children: const [
                                                TableCell(
                                                  child: Padding(
                                                    padding: EdgeInsets.all(12.0),
                                                    child: Text('Deskripsi Produk', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                                                  ),
                                                ),
                                                TableCell(
                                                  child: Padding(
                                                    padding: EdgeInsets.all(12.0),
                                                    child: Text('Qty', textAlign: TextAlign.center, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                                                  ),
                                                ),
                                                TableCell(
                                                  child: Padding(
                                                    padding: EdgeInsets.all(12.0),
                                                    child: Text('Harga Satuan', textAlign: TextAlign.right, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                                                  ),
                                                ),
                                                TableCell(
                                                  child: Padding(
                                                    padding: EdgeInsets.all(12.0),
                                                    child: Text('Subtotal', textAlign: TextAlign.right, style: TextStyle(fontWeight: FontWeight.bold, fontSize: 11)),
                                                  ),
                                                ),
                                              ],
                                            ),
                                            // Table Row
                                            TableRow(
                                              children: [
                                                TableCell(
                                                  child: Padding(
                                                    padding: const EdgeInsets.all(12.0),
                                                    child: Text(deskripsi, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w500)),
                                                  ),
                                                ),
                                                TableCell(
                                                  child: Padding(
                                                    padding: const EdgeInsets.all(12.0),
                                                    child: Text('$qty Pcs', textAlign: TextAlign.center, style: const TextStyle(fontSize: 11)),
                                                  ),
                                                ),
                                                TableCell(
                                                  child: Padding(
                                                    padding: const EdgeInsets.all(12.0),
                                                    child: Text(_formatCurrency(hargaUnit), textAlign: TextAlign.right, style: const TextStyle(fontSize: 11)),
                                                  ),
                                                ),
                                                TableCell(
                                                  child: Padding(
                                                    padding: const EdgeInsets.all(12.0),
                                                    child: Text(_formatCurrency(total), textAlign: TextAlign.right, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w600)),
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ],
                                        ),
                                      ),
                                      const SizedBox(height: 20),

                                      // Total section
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.end,
                                        children: [
                                          Container(
                                            padding: const EdgeInsets.all(16),
                                            decoration: BoxDecoration(
                                              color: const Color(0xFFEFF6FF),
                                              borderRadius: BorderRadius.circular(12),
                                              border: Border.all(color: const Color(0xFFDBEAFE)),
                                            ),
                                            child: Row(
                                              children: [
                                                const Text(
                                                  'Total Nilai PO: ',
                                                  style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12, color: Color(0xFF1E3A8A)),
                                                ),
                                                const SizedBox(width: 8),
                                                Text(
                                                  _formatCurrency(total),
                                                  style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16, color: Color(0xFF2563EB)),
                                                ),
                                              ],
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 32),

                                      // Signature
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Column(
                                            children: [
                                              const Text('Dibuat oleh,', style: TextStyle(fontSize: 10, color: Colors.grey)),
                                              const SizedBox(height: 48),
                                              Container(width: 100, height: 1, color: Colors.grey[300]),
                                              const SizedBox(height: 4),
                                              const Text('Staf Penjualan', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey)),
                                            ],
                                          ),
                                          Column(
                                            children: [
                                              const Text('Disetujui oleh,', style: TextStyle(fontSize: 10, color: Colors.grey)),
                                              const SizedBox(height: 48),
                                              Container(width: 100, height: 1, color: Colors.grey[300]),
                                              const SizedBox(height: 4),
                                              const Text('Pemilik UMKM', style: TextStyle(fontSize: 9, fontWeight: FontWeight.bold, color: Colors.grey)),
                                            ],
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 40),

                                      // Simulated Barcode for premium aesthetics
                                      Center(
                                        child: Column(
                                          children: [
                                            const BarcodeSimulated(),
                                            const SizedBox(height: 6),
                                            Text(
                                              '*$noPo*',
                                              style: TextStyle(fontFamily: 'monospace', fontSize: 10, letterSpacing: 2, color: Colors.grey[600]),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ),
                  ),

                  // Bottom Action Buttons
                  Container(
                    constraints: const BoxConstraints(maxWidth: 800),
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    child: Row(
                      children: [
                        Expanded(
                          child: ElevatedButton.icon(
                            onPressed: _isGeneratingPdf ? null : _cetakPo,
                            icon: _isGeneratingPdf
                                ? const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                                : const Icon(Icons.print_rounded),
                            label: const Text('Cetak PO PDF', style: TextStyle(fontWeight: FontWeight.bold)),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF2563EB),
                              foregroundColor: Colors.white,
                              disabledBackgroundColor: Colors.grey[800],
                              padding: const EdgeInsets.symmetric(vertical: 16),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                              elevation: 4,
                            ),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: ElevatedButton.icon(
                            onPressed: _isGeneratingPdf ? null : _bagikanPo,
                            icon: const Icon(Icons.share_rounded),
                            label: const Text('Bagikan PO', style: TextStyle(fontWeight: FontWeight.bold)),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF1E293B),
                              foregroundColor: Colors.white,
                              disabledBackgroundColor: Colors.grey[800],
                              padding: const EdgeInsets.symmetric(vertical: 16),
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                              elevation: 4,
                              side: BorderSide(color: Colors.white.withValues(alpha: 0.1)),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class BarcodeSimulated extends StatelessWidget {
  const BarcodeSimulated({super.key});

  @override
  Widget build(BuildContext context) {
    final List<double> barWidths = [1, 2, 1, 3, 2, 1, 4, 1, 2, 3, 1, 2, 1, 4, 2, 1, 3, 1, 2, 4, 1, 2, 1];
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: barWidths.map((w) {
        return Container(
          width: w * 1.5,
          height: 35,
          color: Colors.black.withValues(alpha: 0.75),
          margin: const EdgeInsets.only(right: 2),
        );
      }).toList(),
    );
  }
}
