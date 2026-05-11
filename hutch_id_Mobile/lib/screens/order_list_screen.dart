import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../models/models.dart';
import '../widgets/widgets.dart';
import 'order_detail_screen.dart';

class OrderListScreen extends StatefulWidget {
  final UserRole role;
  const OrderListScreen({super.key, required this.role});

  @override
  State<OrderListScreen> createState() => _OrderListScreenState();
}

class _OrderListScreenState extends State<OrderListScreen> {
  String _search     = '';
  PoStatus? _filter;

  List<PurchaseOrder> get _filtered => dummyOrders.where((o) {
    final matchSearch = _search.isEmpty ||
        o.pelanggan.nama.toLowerCase().contains(_search.toLowerCase()) ||
        o.nomorPo.toLowerCase().contains(_search.toLowerCase());
    final matchFilter = _filter == null || o.status == _filter;
    return matchSearch && matchFilter;
  }).toList();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: hutchAppBar(
        title: 'Daftar Pesanan',
        subtitle: 'Semua Purchase Order · REQ-PO-023',
        showBack: false,
      ),
      body: Column(children: [
        // Search & Filter
        Container(
          color: AppColors.white,
          padding: const EdgeInsets.fromLTRB(14, 10, 14, 12),
          child: Column(children: [
            // Search
            TextField(
              onChanged: (v) => setState(() => _search = v),
              style: GoogleFonts.plusJakartaSans(fontSize: 13),
              decoration: const InputDecoration(
                hintText: 'Cari nama pelanggan atau nomor PO...',
                prefixIcon: Icon(Icons.search, color: AppColors.gray, size: 18),
                contentPadding: EdgeInsets.symmetric(vertical: 10, horizontal: 14),
              ),
            ),
            const SizedBox(height: 8),
            // Filter chips
            SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(children: [
                _chip('Semua', null),
                const SizedBox(width: 6),
                ...PoStatus.values.map((s) => Padding(
                  padding: const EdgeInsets.only(right: 6),
                  child: _chip(s.label, s),
                )),
              ]),
            ),
          ]),
        ),
        // List
        Expanded(
          child: _filtered.isEmpty
              ? Center(child: Text('Tidak ada pesanan ditemukan.',
                  style: GoogleFonts.plusJakartaSans(color: AppColors.gray)))
              : ListView.builder(
                  padding: const EdgeInsets.only(top: 8, bottom: 80),
                  itemCount: _filtered.length,
                  itemBuilder: (_, i) => PoCard(
                    po: _filtered[i],
                    onTap: () => Navigator.push(context, MaterialPageRoute(
                      builder: (_) => OrderDetailScreen(po: _filtered[i]))),
                  ),
                ),
        ),
      ]),
    );
  }

  Widget _chip(String label, PoStatus? status) {
    final selected = _filter == status;
    return GestureDetector(
      onTap: () => setState(() => _filter = status),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: selected ? AppColors.accent : AppColors.bg,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
            color: selected ? AppColors.accent : AppColors.border),
        ),
        child: Text(label,
          style: GoogleFonts.plusJakartaSans(
            fontSize: 11.5, fontWeight: FontWeight.w600,
            color: selected ? Colors.white : AppColors.gray)),
      ),
    );
  }
}
