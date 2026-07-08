import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';

class LandingScreen extends StatefulWidget {
  const LandingScreen({super.key});
  @override
  State<LandingScreen> createState() => _LandingScreenState();
}

class _LandingScreenState extends State<LandingScreen>
    with TickerProviderStateMixin {
  final _scrollCtrl = ScrollController();
  late AnimationController _heroCtrl;
  late AnimationController _bgCtrl;
  late Animation<double> _heroOpacity;
  late Animation<Offset> _heroSlide;

  static const _features = [
    _Feature(
      'Manajemen Pesanan',
      Icons.receipt_long_rounded,
      'Buat PO baru dengan nomor otomatis, tambah produk dalam satu pesanan, dan cetak dokumen PDF. Harga dikunci saat pesanan disimpan.',
      ['Pembuatan PO otomatis', 'Cetak dokumen PDF', 'Pelacakan status pesanan'],
      Color(0xFF3b82f6),
    ),
    _Feature(
      'Inventori Pintar',
      Icons.inventory_2_rounded,
      'Verifikasi stok bahan baku otomatis saat PO dibuat. Lihat stok tersedia, kebutuhan produksi, dan selisih kekurangan secara real-time.',
      ['Verifikasi bahan baku otomatis', 'Monitoring stok real-time', 'Notifikasi stok menipis'],
      Color(0xFF10b981),
    ),
    _Feature(
      'Manajemen Pelanggan',
      Icons.people_rounded,
      'CRUD data pelanggan lengkap dengan pencarian otomatis saat membuat PO. Riwayat pemesanan tersimpan untuk pengelolaan yang lebih mudah.',
      ['CRUD data pelanggan', 'Pencarian otomatis', 'Riwayat pemesanan tersimpan'],
      Color(0xFF8b5cf6),
    ),
    _Feature(
      'Dashboard Analitik',
      Icons.bar_chart_rounded,
      'Ringkasan PO aktif, pesanan menunggu konfirmasi, status produksi, dan pesanan siap kirim — semua diperbarui secara real-time.',
      ['Ringkasan pesanan aktif', 'Monitoring status produksi', 'Data real-time'],
      Color(0xFFf59e0b),
    ),
    _Feature(
      'Asisten AI',
      Icons.smart_toy_rounded,
      'Terintegrasi dengan workflow N8N untuk proses otomatis, pencarian informasi sistem, dan notifikasi pintar agar operasional lebih efisien.',
      ['Workflow otomatis', 'Pencarian informasi sistem', 'Dukungan notifikasi pintar'],
      Color(0xFFec4899),
    ),
    _Feature(
      'Keamanan Enterprise',
      Icons.security_rounded,
      'RBAC dengan 4 tingkat pengguna, audit trail perubahan status pesanan, autentikasi berbasis sesi, dan tautan PDF berkala terbatas waktu.',
      ['RBAC 4 tingkat pengguna', 'Audit trail aktivitas', 'Tautan PDF terbatas waktu'],
      Color(0xFF14b8a6),
    ),
  ];

 static const _team = [
    _Member('Nayla Rabia Gustari',    'Project Manager',    'https://github.com/nayyut',             'https://www.instagram.com/naylagstr',         'assets/images/team/nayla.jpeg'),
    _Member('Adrian Ronald Daga',     'Backend', 'https://github.com/byedriand',          'https://www.instagram.com/byedriand',         'assets/images/team/adrian.jpeg'),
    _Member('Muhamad Alvin Ramadhan', 'Frontend', 'https://github.com/alvinzyz',           'https://www.instagram.com/muhamadalvinrmdhn', 'assets/images/team/alvin.jpeg'),
    _Member('Sopyan Rinakdhi',        'QA Tester Mobile',          'https://github.com/Sopyanrnldhi',       'https://www.instagram.com/sopyanrnldhi',      'assets/images/team/sopyan.jpeg'),
    _Member('Eka Febryanto',          'QA Tester Website',          'https://github.com/EkaFebryanto',       'https://www.tiktok.com/',     'assets/images/team/eka.jpeg'),
    _Member('Julia Habibah',          'Sistem Analyst',      'https://github.com/bibajulia40-eng',    'https://www.instagram.com/juliahabibahh_',    'assets/images/team/julia.jpeg'),
    _Member('Akbar',                  'QA Tester Mobile',          'https://github.com/namaakbar44-collab', 'https://www.instagram.com/hunters_00000',     'assets/images/team/akbar.jpeg'),
  ];


  @override
  void initState() {
    super.initState();
    SystemChrome.setSystemUIOverlayStyle(SystemUiOverlayStyle.light);
    _bgCtrl = AnimationController(duration: const Duration(seconds: 8), vsync: this)..repeat();
    _heroCtrl = AnimationController(duration: const Duration(milliseconds: 900), vsync: this);
    _heroOpacity = Tween<double>(begin: 0, end: 1)
        .animate(CurvedAnimation(parent: _heroCtrl, curve: Curves.easeOut));
    _heroSlide = Tween<Offset>(begin: const Offset(0, 0.12), end: Offset.zero)
        .animate(CurvedAnimation(parent: _heroCtrl, curve: Curves.easeOutCubic));
    _heroCtrl.forward();
  }

  @override
  void dispose() {
    _scrollCtrl.dispose();
    _heroCtrl.dispose();
    _bgCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0f172a),
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [Color(0xFF0f172a), Color(0xFF1e3a5f), Color(0xFF0f172a)],
          ),
        ),
        child: CustomScrollView(
          controller: _scrollCtrl,
          slivers: [
            SliverAppBar(
              pinned: true,
              floating: false,
              backgroundColor: const Color(0xFF0f172a).withValues(alpha: 0.95),
              elevation: 0,
              leading: Padding(
                padding: const EdgeInsets.all(10),
                child: Image.asset(
                  'assets/images/hutch-logo.png',
                  fit: BoxFit.contain,
                  errorBuilder: (_, __, ___) =>
                      const Icon(Icons.business_rounded, color: Colors.white),
                ),
              ),
              title: const Text(
                'HUTCH PRESTIGE',
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w900,
                  fontSize: 15,
                  letterSpacing: 1.5,
                ),
              ),
              actions: _buildAppBarActions(),
            ),
            SliverList(
              delegate: SliverChildListDelegate([
                _buildHero(),
                _buildStats(),
                _buildFeatures(),
                _buildTujuan(),
                _buildTarget(),
                _buildKeunggulan(),
                _buildAbout(),
                _buildTeam(),
                _buildFooter(),
              ]),
            ),
          ],
        ),
      ),
    );
  }

  List<Widget> _buildAppBarActions() => [];

  Widget _buildHero() {
    return SlideTransition(
      position: _heroSlide,
      child: FadeTransition(
        opacity: _heroOpacity,
        child: Container(
          width: double.infinity,
          padding: EdgeInsets.fromLTRB(
              28, 40, 28, 64),
          child: Column(
            children: [
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 7),
                decoration: BoxDecoration(
                  color: const Color(0xFF00d4ff).withValues(alpha: 0.10),
                  borderRadius: BorderRadius.circular(50),
                  border: Border.all(
                      color: const Color(0xFF00d4ff).withValues(alpha: 0.30)),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Container(
                      width: 7, height: 7,
                      decoration: const BoxDecoration(
                          shape: BoxShape.circle, color: Color(0xFF22c55e)),
                    ),
                    const SizedBox(width: 8),
                    const Text(
                      'Hutch Mobile · v1.0',
                      style: TextStyle(
                        color: Color(0xFF00d4ff),
                        fontSize: 11,
                        fontWeight: FontWeight.w700,
                        letterSpacing: 0.3,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 32),
              Container(
                width: 104, height: 104,
                padding: const EdgeInsets.all(20),
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: RadialGradient(colors: [
                    Colors.white.withValues(alpha: 0.16),
                    Colors.white.withValues(alpha: 0.04),
                  ]),
                  border: Border.all(
                      color: Colors.white.withValues(alpha: 0.28), width: 2),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF3b82f6).withValues(alpha: 0.55),
                      blurRadius: 60, spreadRadius: 12,
                    ),
                  ],
                ),
                child: Image.asset(
                  'assets/images/hutch-logo.png',
                  fit: BoxFit.contain,
                  errorBuilder: (_, __, ___) => const Icon(
                      Icons.business_rounded, color: Colors.white, size: 52),
                ),
              ),
              const SizedBox(height: 34),
              // ── Hero Title (sesuai website) ──
              ShaderMask(
                shaderCallback: (bounds) => const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  stops: [0.0, 0.5, 1.0],
                  colors: [Colors.white, Color(0xFF00d4ff), Color(0xFF2d7dd2)],
                ).createShader(bounds),
                child: const Text(
                  'Platform Manajemen\nPesanan dan Produksi\nInternal Hutch.id',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    fontSize: 30,
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                    height: 1.25,
                    letterSpacing: -0.5,
                  ),
                ),
              ),
              const SizedBox(height: 20),
              Text(
                'Hutch.id merupakan aplikasi yang dikembangkan khusus untuk mendukung kebutuhan operasional internal perusahaan. Digunakan untuk mengelola proses bisnis, memantau informasi, serta meningkatkan kolaborasi antar bagian.',
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 13,
                  color: Colors.white.withValues(alpha: 0.75),
                  height: 1.75,
                ),
              ),
              const SizedBox(height: 32),
              Container(
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    colors: [Color(0xFF2d7dd2), Color(0xFF00d4ff)],
                  ),
                  borderRadius: BorderRadius.circular(12),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFF2d7dd2).withValues(alpha: 0.45),
                      blurRadius: 24, spreadRadius: 0, offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: ElevatedButton.icon(
                  onPressed: () => Navigator.pushNamed(context, '/login'),
                  icon: const Icon(Icons.arrow_forward_rounded, size: 18),
                  label: const Text('Login'),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.transparent,
                    shadowColor: Colors.transparent,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 16),
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12)),
                    textStyle: const TextStyle(
                        fontWeight: FontWeight.w700, fontSize: 16),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildStats() {
    final stats = [
      ('6+', 'Fitur Unggulan'),
      ('4', 'Role Pengguna'),
      ('Real-time', 'Monitoring'),
    ];
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 20, vertical: 8),
      padding: const EdgeInsets.symmetric(vertical: 22, horizontal: 16),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.05),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: Colors.white.withValues(alpha: 0.1)),
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: stats.map((s) => Column(
          children: [
            Text(s.$1,
                style: const TextStyle(
                    fontSize: 20, fontWeight: FontWeight.w900,
                    color: Color(0xFF60a5fa))),
            const SizedBox(height: 4),
            Text(s.$2,
                style: TextStyle(
                    fontSize: 11,
                    color: Colors.white.withValues(alpha: 0.6),
                    fontWeight: FontWeight.w500)),
          ],
        )).toList(),
      ),
    );
  }

  Widget _buildFeatures() {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 44, 20, 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _sectionLabel('FITUR UNGGULAN'),
          const SizedBox(height: 10),
          const Text(
            'Semua yang Anda\nbutuhkan ada di sini',
            style: TextStyle(
                fontSize: 24, fontWeight: FontWeight.w800,
                color: Colors.white, height: 1.3),
          ),
          const SizedBox(height: 24),
          ...List.generate(
              _features.length, (i) => _FeatureCard(feature: _features[i], index: i)),
        ],
      ),
    );
  }

  // ── TUJUAN DIBANGUN ──────────────────────────────────────────────────────────
  Widget _buildTujuan() {
    final steps = [
      (Icons.do_not_disturb_alt_rounded, 'Proses manual & lambat', const Color(0xFFef4444)),
      (Icons.arrow_downward_rounded,     '',                        const Color(0xFF64748b)),
      (Icons.device_hub_rounded,        'Sistem terpusat & terintegrasi', const Color(0xFF3b82f6)),
      (Icons.arrow_downward_rounded,     '',                        const Color(0xFF64748b)),
      (Icons.bolt_rounded,              'Operasional cepat & akurat', const Color(0xFF10b981)),
    ];

    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 40, 20, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _sectionLabel('TUJUAN DIBANGUN'),
          const SizedBox(height: 10),
          const Text(
            'Mengapa sistem\nini dibuat?',
            style: TextStyle(
                fontSize: 24, fontWeight: FontWeight.w800,
                color: Colors.white, height: 1.3),
          ),
          const SizedBox(height: 16),
          Text(
            'Mengatasi proses operasional manual dengan menghadirkan platform terpusat — satu sistem untuk semua divisi, semua data, semua koordinasi.',
            style: TextStyle(
                fontSize: 13,
                color: Colors.white.withValues(alpha: 0.68),
                height: 1.7),
          ),
          const SizedBox(height: 24),
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(22),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  const Color(0xFF2d7dd2).withValues(alpha: 0.12),
                  const Color(0xFF00d4ff).withValues(alpha: 0.06),
                ],
              ),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(
                  color: const Color(0xFF2d7dd2).withValues(alpha: 0.25)),
            ),
            child: Column(
              children: steps.map((s) {
                if (s.$2.isEmpty) {
                  return Padding(
                    padding: const EdgeInsets.symmetric(vertical: 4),
                    child: Icon(s.$1, color: s.$3, size: 18),
                  );
                }
                return Container(
                  margin: const EdgeInsets.symmetric(vertical: 4),
                  padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
                  decoration: BoxDecoration(
                    color: s.$3.withValues(alpha: 0.08),
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: s.$3.withValues(alpha: 0.22)),
                  ),
                  child: Row(
                    children: [
                      Container(
                        width: 36, height: 36,
                        decoration: BoxDecoration(
                          color: s.$3.withValues(alpha: 0.15),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Icon(s.$1, color: s.$3, size: 18),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(s.$2,
                            style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w600,
                                fontSize: 13)),
                      ),
                    ],
                  ),
                );
              }).toList(),
            ),
          ),
        ],
      ),
    );
  }

  // ── TARGET PENGGUNA ───────────────────────────────────────────────────────────
  Widget _buildTarget() {
    final roles = [
      (Icons.admin_panel_settings_rounded, 'Admin',
          'Kelola sistem, data pengguna, dan pantau aktivitas seluruh divisi.',
          const Color(0xFF2d7dd2)),
      (Icons.bar_chart_rounded, 'Staff Penjualan',
          'Proses pesanan, data pelanggan, dan transaksi penjualan secara terstruktur.',
          const Color(0xFF8b5cf6)),
      (Icons.store_rounded, 'Operator Gudang',
          'Verifikasi stok, pengiriman, dan perbarui inventori langsung dari platform.',
          const Color(0xFF10b981)),
    ];

    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 40, 20, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _sectionLabel('TARGET PENGGUNA'),
          const SizedBox(height: 10),
          const Text(
            'Siapa yang\nmenggunakannya?',
            style: TextStyle(
                fontSize: 24, fontWeight: FontWeight.w800,
                color: Colors.white, height: 1.3),
          ),
          const SizedBox(height: 20),
          ...roles.map((r) => Container(
                margin: const EdgeInsets.only(bottom: 12),
                padding: const EdgeInsets.all(18),
                decoration: BoxDecoration(
                  color: Colors.white.withValues(alpha: 0.04),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(color: r.$4.withValues(alpha: 0.28)),
                ),
                child: Row(
                  children: [
                    Container(
                      width: 48, height: 48,
                      decoration: BoxDecoration(
                        color: r.$4.withValues(alpha: 0.14),
                        borderRadius: BorderRadius.circular(14),
                      ),
                      child: Icon(r.$1, color: r.$4, size: 24),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(r.$2,
                              style: const TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.w700,
                                  fontSize: 14)),
                          const SizedBox(height: 4),
                          Text(r.$3,
                              style: TextStyle(
                                  color: Colors.white.withValues(alpha: 0.6),
                                  fontSize: 12,
                                  height: 1.5)),
                        ],
                      ),
                    ),
                  ],
                ),
              )),
        ],
      ),
    );
  }

  // ── KEUNGGULAN ────────────────────────────────────────────────────────────────
  Widget _buildKeunggulan() {
    final items = [
      (Icons.storage_rounded,          'Terpusat',     'Semua data ada dalam satu sistem yang dapat diakses oleh pihak berwenang.',   const Color(0xFF3b82f6)),
      (Icons.speed_rounded,            'Efisien',      'Kurangi proses manual & duplikasi sehingga tim fokus pada hal bernilai.',     const Color(0xFF10b981)),
      (Icons.group_work_rounded,       'Kolaboratif',  'Koordinasi antar admin, penjualan, dan gudang tanpa hambatan komunikasi.',     const Color(0xFF8b5cf6)),
      (Icons.lock_rounded,             'Aman',         'Hak akses disesuaikan peran — setiap user hanya melihat data relevannya.',     const Color(0xFFf59e0b)),
    ];

    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 40, 20, 0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          _sectionLabel('KEUNGGULAN'),
          const SizedBox(height: 10),
          const Text(
            '4 Pilar utama\nsistem ini',
            style: TextStyle(
                fontSize: 24, fontWeight: FontWeight.w800,
                color: Colors.white, height: 1.3),
          ),
          const SizedBox(height: 20),
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Expanded(
                child: Column(
                  children: items.sublist(0, 2).map((item) => Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: item.$4.withValues(alpha: 0.07),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: item.$4.withValues(alpha: 0.25)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          width: 40, height: 40,
                          decoration: BoxDecoration(
                            color: item.$4.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(11),
                          ),
                          child: Icon(item.$1, color: item.$4, size: 20),
                        ),
                        const SizedBox(height: 10),
                        Text(item.$2,
                            style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w700,
                                fontSize: 14)),
                        const SizedBox(height: 6),
                        Text(item.$3,
                            style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.58),
                                fontSize: 11,
                                height: 1.5),
                            softWrap: true),
                      ],
                    ),
                  )).toList(),
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  children: items.sublist(2, 4).map((item) => Container(
                    margin: const EdgeInsets.only(bottom: 12),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: item.$4.withValues(alpha: 0.07),
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(color: item.$4.withValues(alpha: 0.25)),
                    ),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          width: 40, height: 40,
                          decoration: BoxDecoration(
                            color: item.$4.withValues(alpha: 0.15),
                            borderRadius: BorderRadius.circular(11),
                          ),
                          child: Icon(item.$1, color: item.$4, size: 20),
                        ),
                        const SizedBox(height: 10),
                        Text(item.$2,
                            style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w700,
                                fontSize: 14)),
                        const SizedBox(height: 6),
                        Text(item.$3,
                            style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.58),
                                fontSize: 11,
                                height: 1.5),
                            softWrap: true),
                      ],
                    ),
                  )).toList(),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ── ABOUT US ──────────────────────────────────────────────────────────────────
  Widget _buildAbout() {
    final pills = [
      (Icons.calendar_today_rounded,  'Tahun 2026'),
      (Icons.school_rounded,          'Universitas Kebangsaan RI'),
      (Icons.laptop_mac_rounded,      'Sistem Informasi'),
      (Icons.group_rounded,           '7 Mahasiswa'),
      (Icons.assignment_rounded,      'Proyek UAS'),
    ];

    return Container(
      margin: const EdgeInsets.fromLTRB(20, 40, 20, 0),
      padding: const EdgeInsets.all(26),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            const Color(0xFF2d7dd2).withValues(alpha: 0.10),
            const Color(0xFF00d4ff).withValues(alpha: 0.04),
          ],
        ),
        borderRadius: BorderRadius.circular(22),
        border: Border.all(
            color: const Color(0xFF2d7dd2).withValues(alpha: 0.22)),
      ),
      child: Column(
        children: [
          // Divider bar
          Container(
            width: 40, height: 3,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                  colors: [Color(0xFF2d7dd2), Color(0xFF00d4ff)]),
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          const SizedBox(height: 18),
          _sectionLabel('ABOUT US'),
          const SizedBox(height: 14),
          const Text(
            'Tentang Sistem Ini',
            style: TextStyle(
                fontSize: 22, fontWeight: FontWeight.w900, color: Colors.white),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 14),
          Text(
            'Sistem informasi ini dikembangkan untuk membantu operasional Hutch.id agar lebih terstruktur, efisien, dan terintegrasi — dari pengelolaan data hingga koordinasi antar divisi dalam satu platform.',
            textAlign: TextAlign.center,
            style: TextStyle(
                fontSize: 13,
                color: Colors.white.withValues(alpha: 0.72),
                height: 1.8),
          ),
          const SizedBox(height: 6),
          Text(
            'Dikembangkan sebagai proyek Ujian Akhir Semester oleh 7 mahasiswa Program Studi Sistem Informasi Universitas Kebangsaan Republik Indonesia.',
            textAlign: TextAlign.center,
            style: TextStyle(
                fontSize: 13,
                color: Colors.white.withValues(alpha: 0.72),
                height: 1.8),
          ),
          const SizedBox(height: 20),
          // Pills
          Wrap(
            spacing: 8,
            runSpacing: 8,
            alignment: WrapAlignment.center,
            children: pills.map((p) => Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: const Color(0xFF2d7dd2).withValues(alpha: 0.12),
                    borderRadius: BorderRadius.circular(50),
                    border: Border.all(
                        color: const Color(0xFF2d7dd2).withValues(alpha: 0.28)),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(p.$1,
                          size: 12,
                          color: const Color(0xFF00d4ff).withValues(alpha: 0.8)),
                      const SizedBox(width: 5),
                      Text(p.$2,
                          style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.8),
                              fontSize: 11,
                              fontWeight: FontWeight.w600)),
                    ],
                  ),
                )).toList(),
          ),
        ],
      ),
    );
  }

  Widget _buildTeam() {
    final row1 = _team.take(4).toList();
    final row2 = _team.skip(4).toList();

    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 40, 16, 20),
      child: Column(
        children: [
          _sectionLabel('TIM KAMI'),
          const SizedBox(height: 10),
          const Text(
            'Orang-orang di balik Hutch.id',
            textAlign: TextAlign.center,
            style: TextStyle(
                fontSize: 22, fontWeight: FontWeight.w800,
                color: Colors.white, height: 1.3),
          ),
          const SizedBox(height: 32),

          // ── Baris 1: 4 anggota dengan stagger ──
          LayoutBuilder(builder: (context, constraints) {
            final pillW = (constraints.maxWidth - (3 * 10)) / 4;
            return Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: List.generate(row1.length, (i) {
                return SizedBox(
                  width: pillW,
                  child: Padding(
                    padding: EdgeInsets.only(
                      left: i == 0 ? 0 : 5,
                      right: i == row1.length - 1 ? 0 : 5,
                      top: i.isOdd ? 30 : 0,
                    ),
                    child: _MemberPill(member: row1[i], pillWidth: pillW - 10),
                  ),
                );
              }),
            );
          }),

          const SizedBox(height: 24),

          // ── Baris 2: 3 anggota di tengah ──
          LayoutBuilder(builder: (context, constraints) {
            final pillW = (constraints.maxWidth - (3 * 10)) / 4;
            return Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(row2.length, (i) {
                return SizedBox(
                  width: pillW,
                  child: Padding(
                    padding: EdgeInsets.only(
                      left: i == 0 ? 0 : 5,
                      right: i == row2.length - 1 ? 0 : 5,
                      top: i == 1 ? 30 : 0,
                    ),
                    child: _MemberPill(member: row2[i], pillWidth: pillW - 10),
                  ),
                );
              }),
            );
          }),
        ],
      ),
    );
  }

  Widget _buildFooter() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.symmetric(vertical: 28, horizontal: 24),
      margin: const EdgeInsets.only(top: 20),
      decoration: BoxDecoration(
        border: Border(
            top: BorderSide(color: Colors.white.withValues(alpha: 0.08))),
      ),
      child: Column(
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              SizedBox(
                width: 26, height: 26,
                child: Image.asset(
                  'assets/images/hutch-logo.png',
                  fit: BoxFit.contain,
                  errorBuilder: (_, __, ___) => const Icon(
                      Icons.business_rounded, color: Colors.white, size: 20),
                ),
              ),
              const SizedBox(width: 10),
              const Text(
                'HUTCH PRESTIGE',
                style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w800,
                    fontSize: 13,
                    letterSpacing: 1),
              ),
            ],
          ),
          const SizedBox(height: 10),
          Text(
            '© 2026 Hutch.id — Platform Manajemen Pesanan',
            style: TextStyle(
                color: Colors.white.withValues(alpha: 0.4), fontSize: 11),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  Widget _sectionLabel(String text) => Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
        decoration: BoxDecoration(
          color: const Color(0xFF2563eb).withValues(alpha: 0.2),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(
              color: const Color(0xFF3b82f6).withValues(alpha: 0.4)),
        ),
        child: Text(
          text,
          style: const TextStyle(
              color: Color(0xFF60a5fa),
              fontSize: 11,
              fontWeight: FontWeight.w700,
              letterSpacing: 1.2),
        ),
      );
}

// ─── Data classes ──────────────────────────────────────────────────────────────

class _Feature {
  final String title, description;
  final IconData icon;
  final List<String> bullets;
  final Color color;
  const _Feature(this.title, this.icon, this.description, this.bullets, this.color);
}

class _Member {
  final String name, role, githubUrl, instagramUrl, photoAsset;
  const _Member(this.name, this.role, this.githubUrl, this.instagramUrl, this.photoAsset);
}

// ─── Feature Card ──────────────────────────────────────────────────────────────

class _FeatureCard extends StatelessWidget {
  final _Feature feature;
  final int index;
  const _FeatureCard({required this.feature, required this.index});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.05),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: feature.color.withValues(alpha: 0.22)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: feature.color.withValues(alpha: 0.14),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(feature.icon, color: feature.color, size: 22),
              ),
              const Spacer(),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                decoration: BoxDecoration(
                  color: feature.color.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  '0${index + 1}',
                  style: TextStyle(
                      color: feature.color,
                      fontSize: 11,
                      fontWeight: FontWeight.w800),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          Text(feature.title,
              style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w700,
                  fontSize: 15)),
          const SizedBox(height: 8),
          Text(feature.description,
              style: TextStyle(
                  color: Colors.white.withValues(alpha: 0.62),
                  fontSize: 12,
                  height: 1.6)),
          const SizedBox(height: 14),
          ...feature.bullets.map((b) => Padding(
                padding: const EdgeInsets.only(bottom: 6),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Icon(Icons.check_circle_rounded,
                        size: 14, color: feature.color),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(b,
                          style: TextStyle(
                              fontSize: 12,
                              color: Colors.white.withValues(alpha: 0.75),
                              fontWeight: FontWeight.w500)),
                    ),
                  ],
                ),
              )),
        ],
      ),
    );
  }
}

// ─── Member Pill (layout seperti website) ─────────────────────────────────────

class _MemberPill extends StatelessWidget {
  final _Member member;
  final double? pillWidth;
  const _MemberPill({required this.member, this.pillWidth});

  Color get _roleColor {
    switch (member.role) {
      case 'Project Manager':    return const Color(0xFFf59e0b);
      case 'Frontend / Backend': return const Color(0xFF3b82f6);
      case 'QA Tester':          return const Color(0xFF10b981);
      case 'Sistem Analis':      return const Color(0xFF8b5cf6);
      default:                   return const Color(0xFF64748b);
    }
  }

  String get _initials => member.name
      .trim()
      .split(' ')
      .take(2)
      .map((w) => w[0].toUpperCase())
      .join();

  Future<void> _openUrl(String url) async {
    final uri = Uri.parse(url);
    try {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    } catch (e) {
      debugPrint('Error launching URL: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        // ── Pill Portrait (sama seperti website) ──
        AspectRatio(
          aspectRatio: 3 / 4.2,
          child: Container(
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(100),
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  const Color(0xFF2d7dd2).withValues(alpha: 0.30),
                  const Color(0xFF00d4ff).withValues(alpha: 0.12),
                ],
              ),
              border: Border.all(
                color: const Color(0xFF00d4ff).withValues(alpha: 0.22),
                width: 1,
              ),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.35),
                  blurRadius: 18, spreadRadius: 1,
                  offset: const Offset(0, 8),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(100),
              child: Stack(
                fit: StackFit.expand,
                children: [
                  // Foto
                  Image.asset(
                    member.photoAsset,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                          colors: [
                            const Color(0xFF2d7dd2).withValues(alpha: 0.35),
                            const Color(0xFF00d4ff).withValues(alpha: 0.15),
                          ],
                        ),
                      ),
                      child: Center(
                        child: Text(
                          _initials,
                          style: TextStyle(
                            color: Colors.white.withValues(alpha: 0.75),
                            fontSize: 28,
                            fontWeight: FontWeight.w900,
                          ),
                        ),
                      ),
                    ),
                  ),
                  // Gradient overlay bawah (seperti website)
                  Positioned.fill(
                    child: DecoratedBox(
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                          stops: const [0.62, 1.0],
                          colors: [
                            Colors.transparent,
                            const Color(0xFF060d1a).withValues(alpha: 0.92),
                          ],
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),

        const SizedBox(height: 10),

        // ── Nama ──
        Text(
          member.name.trim(),
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w700,
            fontSize: 10,
            height: 1.35,
          ),
          textAlign: TextAlign.center,
          maxLines: 3,
          overflow: TextOverflow.ellipsis,
        ),
        const SizedBox(height: 5),

        // ── Role badge — wrap 2 baris jika perlu ──
        Container(
          width: double.infinity,
          padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 4),
          decoration: BoxDecoration(
            color: const Color(0xFF00d4ff).withValues(alpha: 0.10),
            borderRadius: BorderRadius.circular(50),
            border: Border.all(
              color: const Color(0xFF00d4ff).withValues(alpha: 0.25),
            ),
          ),
          child: Text(
            member.role,
            style: const TextStyle(
              color: Color(0xFF00d4ff),
              fontSize: 7,
              fontWeight: FontWeight.w800,
              letterSpacing: 0.3,
              height: 1.3,
            ),
            textAlign: TextAlign.center,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
        ),
        const SizedBox(height: 8),

        // ── Social buttons ──
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            _socialBtn(FontAwesomeIcons.instagram, member.instagramUrl),
            const SizedBox(width: 6),
            _socialBtn(FontAwesomeIcons.github, member.githubUrl),
          ],
        ),
      ],
    );
  }

  Widget _socialBtn(IconData icon, String url) {
    return GestureDetector(
      onTap: () => _openUrl(url),
      child: Container(
        width: 26, height: 26,
        decoration: BoxDecoration(
          color: const Color(0xFF2d7dd2).withValues(alpha: 0.12),
          borderRadius: BorderRadius.circular(7),
          border: Border.all(
            color: const Color(0xFF2d7dd2).withValues(alpha: 0.30),
          ),
        ),
        child: Center(
          child: FaIcon(icon, size: 11, color: Colors.white70),
        ),
      ),
    );
  }
}