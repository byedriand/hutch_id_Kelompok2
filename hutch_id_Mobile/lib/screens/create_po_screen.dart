import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../models/models.dart';
import '../widgets/widgets.dart';

// ============================================================
// CREATE PO SCREEN
// ============================================================
class CreatePoScreen extends StatefulWidget {
  final UserRole role;
  final VoidCallback onSaved;
  const CreatePoScreen({super.key, required this.role, required this.onSaved});

  @override
  State<CreatePoScreen> createState() => _CreatePoScreenState();
}

class _CreatePoScreenState extends State<CreatePoScreen> {
  final _noteCtrl =
      TextEditingController(text: 'Logo bordir depan, benang putih');
  DateTime _tglKirim = DateTime(2026, 4, 28);
  Customer? _selected = dummyCustomers.first;
  final List<_ItemRow> _items = [_ItemRow()];
  bool _saving = false;

  double get _total => _items.fold(0, (s, i) => s + i.subtotal);

  String _rp(double v) {
    final s = v.toInt().toString();
    final buf = StringBuffer('Rp ');
    for (int i = 0; i < s.length; i++) {
      if (i > 0 && (s.length - i) % 3 == 0) buf.write('.');
      buf.write(s[i]);
    }
    return buf.toString();
  }

  void _save() async {
    setState(() => _saving = true);
    await Future.delayed(const Duration(milliseconds: 900));
    if (!mounted) return;
    setState(() => _saving = false);
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(
          'PO-20260421-004 berhasil disimpan! Email notifikasi terkirim.',
          style: GoogleFonts.plusJakartaSans()),
      backgroundColor: AppColors.green,
      behavior: SnackBarBehavior.floating,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
    ));
    widget.onSaved();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: hutchAppBar(
        title: 'Buat PO Baru',
        subtitle: 'REQ-PO-001 s/d REQ-PO-007',
        showBack: false,
        actions: [
          TextButton(
            onPressed: _saving ? null : _save,
            child: _saving
                ? const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                        color: Colors.white, strokeWidth: 2))
                : Text('Simpan',
                    style: GoogleFonts.plusJakartaSans(
                        color: Colors.white, fontWeight: FontWeight.w700)),
          ),
        ],
      ),
      body: ListView(
        padding: const EdgeInsets.only(bottom: 30),
        children: [
          // Nomor PO
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
            child:
                Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text('Nomor PO (Auto-generate)',
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppColors.navy)),
              const SizedBox(height: 6),
              Container(
                padding:
                    const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                decoration: BoxDecoration(
                    color: const Color(0xFFF8FAFC),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: AppColors.border)),
                child: Row(children: [
                  const Icon(Icons.tag, size: 16, color: AppColors.accent),
                  const SizedBox(width: 8),
                  Text('PO-20260421-004',
                      style: GoogleFonts.firaCode(
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: AppColors.accent)),
                  const Spacer(),
                  const Icon(Icons.lock_outline,
                      size: 14, color: AppColors.gray),
                ]),
              ),
              const SizedBox(height: 4),
              Text('Format: PO-YYYYMMDD-XXX · tidak dapat diubah',
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 10, color: AppColors.gray)),
            ]),
          ),

          const SizedBox(height: 14),

          // Pelanggan
          SectionCard(
            title: 'Informasi Pelanggan',
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Nama Pelanggan *',
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: AppColors.navy)),
                    const SizedBox(height: 6),
                    // Dropdown pelanggan
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      decoration: BoxDecoration(
                          color: Colors.white,
                          border:
                              Border.all(color: AppColors.border, width: 1.5),
                          borderRadius: BorderRadius.circular(10)),
                      child: DropdownButtonHideUnderline(
                        child: DropdownButton<Customer>(
                          value: _selected,
                          isExpanded: true,
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 13,
                              color: AppColors.navy,
                              fontWeight: FontWeight.w500),
                          items: dummyCustomers
                              .map((c) => DropdownMenuItem(
                                  value: c, child: Text(c.nama)))
                              .toList(),
                          onChanged: (v) => setState(() => _selected = v),
                        ),
                      ),
                    ),
                    if (_selected != null) ...[
                      const SizedBox(height: 10),
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                            color: AppColors.bg,
                            borderRadius: BorderRadius.circular(8)),
                        child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text('📞 ${_selected!.telepon}',
                                  style: GoogleFonts.plusJakartaSans(
                                      fontSize: 11.5, color: AppColors.gray)),
                              Text('✉ ${_selected!.email}',
                                  style: GoogleFonts.plusJakartaSans(
                                      fontSize: 11.5, color: AppColors.gray)),
                              Text('📍 ${_selected!.alamat}',
                                  style: GoogleFonts.plusJakartaSans(
                                      fontSize: 11.5, color: AppColors.gray)),
                            ]),
                      ),
                    ],
                    const SizedBox(height: 12),
                    Text('Tanggal Pengiriman *',
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 12,
                            fontWeight: FontWeight.w600,
                            color: AppColors.navy)),
                    const SizedBox(height: 6),
                    GestureDetector(
                      onTap: () async {
                        final picked = await showDatePicker(
                          context: context,
                          initialDate: _tglKirim,
                          firstDate: DateTime.now(),
                          lastDate: DateTime(2027),
                        );
                        if (picked != null) setState(() => _tglKirim = picked);
                      },
                      child: Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 14, vertical: 12),
                        decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(10),
                            border: Border.all(
                                color: AppColors.border, width: 1.5)),
                        child: Row(children: [
                          const Icon(Icons.calendar_today_outlined,
                              size: 16, color: AppColors.accent),
                          const SizedBox(width: 8),
                          Text('${_tglKirim.day} Apr ${_tglKirim.year}',
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 13, color: AppColors.navy)),
                          const Spacer(),
                          const Icon(Icons.arrow_drop_down,
                              color: AppColors.gray),
                        ]),
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text('✓ Tidak boleh di masa lalu (REQ-PO-006)',
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 10, color: AppColors.green)),
                  ]),
            ),
          ),

          // Items
          SectionCard(
            title: '🛍 Item Pesanan',
            trailing: TextButton.icon(
              icon: const Icon(Icons.add, size: 16),
              label: Text('Tambah',
                  style:
                      GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700)),
              onPressed: () => setState(() => _items.add(_ItemRow())),
            ),
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: Column(children: [
                ..._items
                    .asMap()
                    .entries
                    .map((e) => _buildItemRow(e.key, e.value)),
                const Divider(height: 20),
                Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Text('Total Nilai PO',
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 12, color: AppColors.gray)),
                      Text(_rp(_total),
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 18,
                              fontWeight: FontWeight.w800,
                              color: AppColors.navy)),
                    ]),
              ]),
            ),
          ),

          // Catatan
          SectionCard(
            title: '📝 Catatan Khusus (Opsional)',
            child: Padding(
              padding: const EdgeInsets.all(14),
              child: TextField(
                controller: _noteCtrl,
                maxLines: 3,
                style: GoogleFonts.plusJakartaSans(fontSize: 13),
                decoration: const InputDecoration(
                    hintText:
                        'Instruksi produksi, desain, catatan untuk pelanggan...'),
              ),
            ),
          ),

          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: HutchButton(
              label: _saving ? 'Menyimpan...' : '💾 Simpan Pesanan',
              fullWidth: true,
              onPressed: _saving ? null : _save,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildItemRow(int idx, _ItemRow item) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
          color: AppColors.bg,
          borderRadius: BorderRadius.circular(10),
          border: Border.all(color: AppColors.border)),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
          Text('Item ${idx + 1}',
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 12,
                  fontWeight: FontWeight.w700,
                  color: AppColors.navy)),
          if (_items.length > 1)
            GestureDetector(
                onTap: () => setState(() => _items.removeAt(idx)),
                child: const Icon(Icons.close, size: 16, color: AppColors.red)),
        ]),
        const SizedBox(height: 8),
        Container(
          padding: const EdgeInsets.symmetric(horizontal: 12),
          decoration: BoxDecoration(
              color: Colors.white,
              border: Border.all(color: AppColors.border, width: 1.5),
              borderRadius: BorderRadius.circular(8)),
          child: DropdownButtonHideUnderline(
            child: DropdownButton<String>(
              value: item.produk,
              isExpanded: true,
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 13, color: AppColors.navy),
              items: [
                'Tas Kanvas Custom',
                'Tas Punggung',
                'Tas Selempang',
                'Dompet Kulit',
                'Tas Travel',
                'Tas Pinggang'
              ].map((p) => DropdownMenuItem(value: p, child: Text(p))).toList(),
              onChanged: (v) => setState(() => item.produk = v!),
            ),
          ),
        ),
        const SizedBox(height: 8),
        Row(children: [
          Expanded(
            child: TextField(
              keyboardType: TextInputType.number,
              style: GoogleFonts.plusJakartaSans(fontSize: 13),
              decoration: const InputDecoration(
                labelText: 'Jumlah (pcs)',
                prefixIcon: Icon(Icons.numbers, size: 16),
              ),
              controller: TextEditingController(text: item.qty.toString()),
              onChanged: (v) => setState(() => item.qty = int.tryParse(v) ?? 1),
            ),
          ),
          const SizedBox(width: 10),
          Expanded(
            child: Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: AppColors.border)),
              child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('Subtotal',
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 10, color: AppColors.gray)),
                    Text(_rp(item.subtotal),
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 13,
                            fontWeight: FontWeight.w700,
                            color: AppColors.accent)),
                  ]),
            ),
          ),
        ]),
      ]),
    );
  }
}

class _ItemRow {
  String produk = 'Tas Kanvas Custom';
  int qty = 10;
  double harga = 150000;

  double get subtotal => qty * harga;
}

// ============================================================
// CUSTOMER SCREEN
// ============================================================
class CustomerScreen extends StatelessWidget {
  final UserRole role;
  const CustomerScreen({super.key, required this.role});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: hutchAppBar(
        title: 'Manajemen Pelanggan',
        subtitle: 'CRUD master pelanggan · REQ-PO-026',
        showBack: false,
        actions: [
          IconButton(
            icon: const Icon(Icons.person_add_outlined),
            onPressed: () => _showAddSheet(context),
          ),
        ],
      ),
      body: ListView.builder(
        padding: const EdgeInsets.only(top: 8, bottom: 30),
        itemCount: dummyCustomers.length,
        itemBuilder: (_, i) => _CustomerCard(
          customer: dummyCustomers[i],
          onEdit: () => _showEditSheet(context, dummyCustomers[i]),
          onDelete: () => _confirmDelete(context, dummyCustomers[i]),
        ),
      ),
    );
  }

  void _showAddSheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => const _CustomerForm(customer: null),
    );
  }

  void _showEditSheet(BuildContext context, Customer c) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(20))),
      builder: (_) => _CustomerForm(customer: c),
    );
  }

  void _confirmDelete(BuildContext context, Customer c) {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        title: Text('Hapus Pelanggan?',
            style: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700)),
        content: Text('Pelanggan "${c.nama}" akan dihapus permanen.',
            style: GoogleFonts.plusJakartaSans(fontSize: 13)),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Batal')),
          ElevatedButton(
              onPressed: () => Navigator.pop(context),
              style: ElevatedButton.styleFrom(backgroundColor: AppColors.red),
              child: const Text('Hapus')),
        ],
      ),
    );
  }
}

class _CustomerCard extends StatelessWidget {
  final Customer customer;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  const _CustomerCard(
      {required this.customer, required this.onEdit, required this.onDelete});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 5),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
        // Avatar
        Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
              color: AppColors.light, borderRadius: BorderRadius.circular(10)),
          child: Center(
              child: Text(customer.nama[0],
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 18,
                      fontWeight: FontWeight.w800,
                      color: AppColors.accent))),
        ),
        const SizedBox(width: 12),
        Expanded(
          child:
              Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Text(customer.nama,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 13.5,
                    fontWeight: FontWeight.w700,
                    color: AppColors.navy)),
            const SizedBox(height: 4),
            Text('📞 ${customer.telepon}',
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 11.5, color: AppColors.gray)),
            Text('✉ ${customer.email}',
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 11.5, color: AppColors.gray)),
            Text('📍 ${customer.alamat}',
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 11.5, color: AppColors.gray),
                maxLines: 2,
                overflow: TextOverflow.ellipsis),
          ]),
        ),
        Column(children: [
          IconButton(
            icon: const Icon(Icons.edit_outlined,
                size: 18, color: AppColors.accent),
            onPressed: onEdit,
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(minWidth: 32, minHeight: 32),
          ),
          IconButton(
            icon: const Icon(Icons.delete_outline,
                size: 18, color: AppColors.red),
            onPressed: onDelete,
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(minWidth: 32, minHeight: 32),
          ),
        ]),
      ]),
    );
  }
}

class _CustomerForm extends StatelessWidget {
  final Customer? customer;
  const _CustomerForm({this.customer});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(
          left: 20,
          right: 20,
          top: 20,
          bottom: MediaQuery.of(context).viewInsets.bottom + 30),
      child: Column(mainAxisSize: MainAxisSize.min, children: [
        Container(
            width: 36,
            height: 4,
            decoration: BoxDecoration(
                color: AppColors.border,
                borderRadius: BorderRadius.circular(2))),
        const SizedBox(height: 16),
        Text(customer == null ? 'Tambah Pelanggan' : 'Edit Pelanggan',
            style: GoogleFonts.plusJakartaSans(
                fontSize: 16, fontWeight: FontWeight.w700)),
        const SizedBox(height: 16),
        TextField(
          controller: TextEditingController(text: customer?.nama ?? ''),
          style: GoogleFonts.plusJakartaSans(fontSize: 13),
          decoration: const InputDecoration(labelText: 'Nama Lengkap *'),
        ),
        const SizedBox(height: 10),
        TextField(
          controller: TextEditingController(text: customer?.telepon ?? ''),
          keyboardType: TextInputType.phone,
          style: GoogleFonts.plusJakartaSans(fontSize: 13),
          decoration: const InputDecoration(labelText: 'Nomor Telepon'),
        ),
        const SizedBox(height: 10),
        TextField(
          controller: TextEditingController(text: customer?.email ?? ''),
          keyboardType: TextInputType.emailAddress,
          style: GoogleFonts.plusJakartaSans(fontSize: 13),
          decoration: const InputDecoration(labelText: 'Email'),
        ),
        const SizedBox(height: 10),
        TextField(
          controller: TextEditingController(text: customer?.alamat ?? ''),
          style: GoogleFonts.plusJakartaSans(fontSize: 13),
          decoration: const InputDecoration(labelText: 'Alamat'),
        ),
        const SizedBox(height: 18),
        SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: () => Navigator.pop(context),
              child: Text('Simpan',
                  style:
                      GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700)),
            )),
      ]),
    );
  }
}

// ============================================================
// PDF PREVIEW SCREEN
// ============================================================
class PdfPreviewScreen extends StatelessWidget {
  final PurchaseOrder po;
  const PdfPreviewScreen({super.key, required this.po});

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

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF334155),
      appBar: hutchAppBar(
        title: 'Preview PDF',
        subtitle: '${po.nomorPo}.pdf',
        context: context,
        actions: [
          IconButton(
            icon: const Icon(Icons.share_outlined),
            onPressed: () {},
          ),
          IconButton(
            icon: const Icon(Icons.download_outlined),
            onPressed: () {
              ScaffoldMessenger.of(context).showSnackBar(SnackBar(
                content: Text('Mengunduh ${po.nomorPo}.pdf...',
                    style: GoogleFonts.plusJakartaSans()),
                backgroundColor: AppColors.green,
                behavior: SnackBarBehavior.floating,
              ));
            },
          ),
        ],
      ),
      body: Center(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Container(
            constraints: const BoxConstraints(maxWidth: 500),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(8),
              boxShadow: const [
                BoxShadow(
                    color: Colors.black38, blurRadius: 20, offset: Offset(0, 4))
              ],
            ),
            padding: const EdgeInsets.all(24),
            child:
                Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              // Header
              Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('hutch.id',
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 24,
                                  fontWeight: FontWeight.w800,
                                  color: AppColors.navy,
                                  letterSpacing: -1)),
                          Text('Bag Manufacturing & In-House Brand',
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10, color: AppColors.gray)),
                          Text('Jl. Industri No. 7, Jakarta',
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10, color: AppColors.gray)),
                        ]),
                    Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text('PURCHASE ORDER',
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 16,
                                  fontWeight: FontWeight.w800,
                                  color: AppColors.navy)),
                          Text(po.nomorPo,
                              style: GoogleFonts.firaCode(
                                  fontSize: 12,
                                  fontWeight: FontWeight.w600,
                                  color: AppColors.accent)),
                          StatusBadge(po.status),
                        ]),
                  ]),

              // Divider
              Container(
                  height: 2.5,
                  color: AppColors.navy,
                  margin: const EdgeInsets.symmetric(vertical: 12)),

              // Info 2 columns
              Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
                Expanded(
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                      _pdfSectionTitle('Informasi PO'),
                      _pdfLine('Tgl Pesanan', _date(po.tanggalPesanan)),
                      _pdfLine('Tgl Kirim', _date(po.tanggalKirim)),
                      _pdfLine('Dibuat oleh', 'Staf Penjualan'),
                    ])),
                const SizedBox(width: 16),
                Expanded(
                    child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                      _pdfSectionTitle('Data Pelanggan'),
                      Text(po.pelanggan.nama,
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 11, fontWeight: FontWeight.w700)),
                      Text(po.pelanggan.alamat,
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 10.5, color: AppColors.gray)),
                      Text(po.pelanggan.telepon,
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 10.5, color: AppColors.gray)),
                    ])),
              ]),

              const SizedBox(height: 14),
              _pdfSectionTitle('Detail Produk'),
              Container(
                decoration: BoxDecoration(
                    border: Border.all(color: AppColors.border),
                    borderRadius: BorderRadius.circular(6)),
                child: Column(children: [
                  // Header row
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
                    decoration: const BoxDecoration(
                        color: AppColors.navy,
                        borderRadius: BorderRadius.only(
                            topLeft: Radius.circular(5),
                            topRight: Radius.circular(5))),
                    child: Row(children: [
                      Expanded(
                          flex: 3,
                          child: Text('Produk',
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.white))),
                      Expanded(
                          flex: 1,
                          child: Text('Qty',
                              textAlign: TextAlign.center,
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.white))),
                      Expanded(
                          flex: 2,
                          child: Text('Harga',
                              textAlign: TextAlign.center,
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.white))),
                      Expanded(
                          flex: 2,
                          child: Text('Subtotal',
                              textAlign: TextAlign.right,
                              style: GoogleFonts.plusJakartaSans(
                                  fontSize: 10,
                                  fontWeight: FontWeight.w700,
                                  color: Colors.white))),
                    ]),
                  ),
                  ...po.items.map((item) => Container(
                        padding: const EdgeInsets.symmetric(
                            horizontal: 10, vertical: 7),
                        decoration: const BoxDecoration(
                            border: Border(
                                bottom: BorderSide(color: AppColors.border))),
                        child: Row(children: [
                          Expanded(
                              flex: 3,
                              child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(item.produk,
                                        style: GoogleFonts.plusJakartaSans(
                                            fontSize: 11,
                                            fontWeight: FontWeight.w600)),
                                    Text(item.spesifikasi,
                                        style: GoogleFonts.plusJakartaSans(
                                            fontSize: 10,
                                            color: AppColors.gray)),
                                  ])),
                          Expanded(
                              flex: 1,
                              child: Text('${item.jumlah}',
                                  textAlign: TextAlign.center,
                                  style: GoogleFonts.plusJakartaSans(
                                      fontSize: 11))),
                          Expanded(
                              flex: 2,
                              child: Text(_rp(item.hargaSatuan),
                                  textAlign: TextAlign.center,
                                  style: GoogleFonts.plusJakartaSans(
                                      fontSize: 10.5))),
                          Expanded(
                              flex: 2,
                              child: Text(_rp(item.subtotal),
                                  textAlign: TextAlign.right,
                                  style: GoogleFonts.plusJakartaSans(
                                      fontSize: 11,
                                      fontWeight: FontWeight.w700))),
                        ]),
                      )),
                ]),
              ),
              // Total
              Align(
                alignment: Alignment.centerRight,
                child: Container(
                  margin: const EdgeInsets.only(top: 10),
                  padding:
                      const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
                  decoration: BoxDecoration(
                      color: AppColors.navy,
                      borderRadius: BorderRadius.circular(7)),
                  child: Text('Total: ${_rp(po.totalNilai)}',
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: Colors.white)),
                ),
              ),

              if (po.catatanKhusus != null) ...[
                const SizedBox(height: 12),
                _pdfSectionTitle('Catatan Khusus'),
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                      color: AppColors.bg,
                      borderRadius: BorderRadius.circular(6),
                      border: Border.all(color: AppColors.border)),
                  child: Text(po.catatanKhusus!,
                      style: GoogleFonts.plusJakartaSans(fontSize: 11.5)),
                ),
              ],

              // Signatures
              const SizedBox(height: 20),
              Row(children: [
                Expanded(child: _signBox('Dibuat oleh\nStaf Penjualan')),
                const SizedBox(width: 16),
                Expanded(child: _signBox('Disetujui oleh\nPemilik UMKM')),
              ]),

              // Footer
              const SizedBox(height: 16),
              Center(
                  child: Text('Halaman 1 dari 1 · hutch.id · Dokumen PO Resmi',
                      style: GoogleFonts.plusJakartaSans(
                          fontSize: 9, color: AppColors.gray))),
            ]),
          ),
        ),
      ),
    );
  }

  Widget _pdfSectionTitle(String t) => Padding(
        padding: const EdgeInsets.only(bottom: 5),
        child: Text(t.toUpperCase(),
            style: GoogleFonts.plusJakartaSans(
                fontSize: 9,
                fontWeight: FontWeight.w700,
                color: AppColors.gray,
                letterSpacing: .8)),
      );

  Widget _pdfLine(String l, String v) => Padding(
        padding: const EdgeInsets.only(bottom: 3),
        child: Row(children: [
          Text('$l: ',
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 10.5, fontWeight: FontWeight.w600)),
          Text(v,
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 10.5, color: AppColors.gray)),
        ]),
      );

  Widget _signBox(String label) => Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
            border: Border.all(color: AppColors.border),
            borderRadius: BorderRadius.circular(6)),
        child: Column(children: [
          const SizedBox(height: 40),
          Container(height: 1, color: AppColors.border),
          const SizedBox(height: 6),
          Text(label,
              textAlign: TextAlign.center,
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 10, color: AppColors.gray)),
        ]),
      );
}
