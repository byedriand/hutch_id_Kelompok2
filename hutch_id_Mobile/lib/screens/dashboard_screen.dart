import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../models/models.dart';
import '../widgets/widgets.dart';
import 'order_detail_screen.dart';

class DashboardScreen extends StatelessWidget {
  final UserRole role;
  final void Function(int) onNavigate;

  const DashboardScreen({
    super.key, required this.role, required this.onNavigate});

  @override
  Widget build(BuildContext context) {
    final waiting = dummyOrders.where(
      (o) => o.status == PoStatus.menungguKonfirmasi).toList();
    final inProd  = dummyOrders.where(
      (o) => o.status == PoStatus.dalamProduksi).toList();

    return Scaffold(
      backgroundColor: AppColors.bg,
      appBar: hutchAppBar(
        title: 'Dashboard Pesanan',
        subtitle: 'hutch.id Modul PO',
        showBack: false,
        actions: [
          Padding(
            padding: const EdgeInsets.only(right: 12),
            child: Center(
              child: Container(
                width: 34, height: 34,
                decoration: const BoxDecoration(
                  color: AppColors.accent, shape: BoxShape.circle),
                child: Center(
                  child: Text(role.initials,
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12, fontWeight: FontWeight.w700,
                      color: Colors.white)),
                ),
              ),
            ),
          ),
        ],
      ),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => onNavigate(2),
        backgroundColor: AppColors.accent,
        icon: const Icon(Icons.add, color: Colors.white),
        label: Text('Buat PO',
          style: GoogleFonts.plusJakartaSans(
            fontWeight: FontWeight.w700, color: Colors.white)),
      ),
      body: RefreshIndicator(
        onRefresh: () async => await Future.delayed(const Duration(seconds: 1)),
        child: ListView(
          children: [
            // Greeting
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 4),
              child: Text('Selamat datang, ${role.label} 👋',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 13, color: AppColors.gray)),
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: Text('Ringkasan Pesanan',
                style: GoogleFonts.plusJakartaSans(
                  fontSize: 18, fontWeight: FontWeight.w800, color: AppColors.navy)),
            ),
            const SizedBox(height: 12),

            // Stat grid
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16),
              child: GridView.count(
                crossAxisCount: 2,
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                crossAxisSpacing: 10,
                mainAxisSpacing: 10,
                childAspectRatio: 1.6,
                children: const [
                  StatCard(label: 'Total PO Aktif',     value: '24',  desc: 'Bulan April 2026',  dotColor: AppColors.accent),
                  StatCard(label: 'Menunggu Konfirmasi', value: '3',   desc: 'Perlu tindakan',    dotColor: AppColors.yellow, valueColor: AppColors.yellow),
                  StatCard(label: 'Siap Kirim',          value: '7',   desc: 'Siap dikirim',      dotColor: AppColors.green,  valueColor: AppColors.green),
                  StatCard(label: 'Selesai Bulan Ini',   value: '14',  desc: 'Rp 48.500.000',     dotColor: Color(0xFF94A3B8)),
                ],
              ),
            ),

            // Alert banner if waiting > 0
            if (waiting.isNotEmpty) ...[
              Container(
                margin: const EdgeInsets.fromLTRB(16, 16, 16, 0),
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: const Color(0xFFFEF3C7),
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: const Color(0xFFFDE68A)),
                ),
                child: Row(children: [
                  const Icon(Icons.warning_amber_rounded,
                    color: AppColors.yellow, size: 20),
                  const SizedBox(width: 8),
                  Expanded(child: Text(
                    '${waiting.length} pesanan menunggu konfirmasi Anda',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 12, fontWeight: FontWeight.w600,
                      color: AppColors.waitFg))),
                  TextButton(
                    onPressed: () => onNavigate(1),
                    style: TextButton.styleFrom(
                      padding: EdgeInsets.zero,
                      minimumSize: const Size(50, 30),
                    ),
                    child: Text('Lihat →',
                      style: GoogleFonts.plusJakartaSans(
                        fontSize: 12, fontWeight: FontWeight.w700,
                        color: AppColors.yellow))),
                ]),
              ),
            ],

            // Menunggu Konfirmasi
            SectionHeader('⚠ Menunggu Konfirmasi (${waiting.length})',
              trailing: TextButton(
                onPressed: () => onNavigate(1),
                child: Text('Lihat Semua',
                  style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.accent))),
            ),
            ...waiting.map((po) => PoCard(
              po: po,
              onTap: () => Navigator.push(context,
                MaterialPageRoute(builder: (_) => OrderDetailScreen(po: po))),
            )),

            // Dalam Produksi
            if (inProd.isNotEmpty) ...[
              SectionHeader('📦 Dalam Produksi (${inProd.length})'),
              ...inProd.map((po) => PoCard(
                po: po,
                onTap: () => Navigator.push(context,
                  MaterialPageRoute(builder: (_) => OrderDetailScreen(po: po))),
              )),
            ],
            const SizedBox(height: 80),
          ],
        ),
      ),
    );
  }
}
