import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../utils/responsive.dart';
import 'auth/login_screen.dart';
import '../widgets/sidebar.dart';
import '../widgets/shimmer_loading.dart';
import 'pelanggan/daftar_pelanggan_screen.dart';
import 'pesanan/lihat_cetak_po_screen.dart';
import '../models/user_model.dart';
import '../models/pelanggan_model.dart';
import '../services/api_service.dart';

// Status mapping from API values to display labels
const Map<String, String> statusDisplayMap = {
  'draft': 'Draft',
  'menunggu_konfirmasi': 'Pending',
  'dikonfirmasi': 'Dikonfirmasi',
  'dalam_produksi': 'Proses',
  'siap_kirim': 'Siap Kirim',
  'selesai': 'Selesai',
  'dibatalkan': 'Dibatalkan',
  // Legacy support for old status values
  'Pending': 'Pending',
  'Proses': 'Proses',
  'Selesai': 'Selesai',
  'Draft': 'Draft',
};

const Map<String, IconData> statusIconMap = {
  'draft': Icons.drafts_outlined,
  'menunggu_konfirmasi': Icons.hourglass_top_outlined,
  'dikonfirmasi': Icons.check_outlined,
  'dalam_produksi': Icons.precision_manufacturing_outlined,
  'siap_kirim': Icons.local_shipping_outlined,
  'selesai': Icons.check_circle_outlined,
  'dibatalkan': Icons.cancel_outlined,
};

const Map<String, Color> statusColorMap = {
  'draft': Color(0xFF64748B),
  'menunggu_konfirmasi': Color(0xFFF59E0B),
  'dikonfirmasi': Color(0xFF06B6D4),
  'dalam_produksi': Color(0xFF3B82F6),
  'siap_kirim': Color(0xFF8B5CF6),
  'selesai': Color(0xFF10B981),
  'dibatalkan': Color(0xFFEF4444),
};

class AppNotification {
  final String id;
  final String title;
  final String body;
  final DateTime timestamp;
  final String type; // 'pesanan', 'pelanggan', 'arsip', 'sistem'
  bool isRead;

  AppNotification({
    required this.id,
    required this.title,
    required this.body,
    required this.timestamp,
    required this.type,
    this.isRead = false,
  });
}

class MainHomeScreen extends StatefulWidget {
  final User user;
  const MainHomeScreen({super.key, required this.user});

  @override
  State<MainHomeScreen> createState() => _MainHomeScreenState();
}

class _MainHomeScreenState extends State<MainHomeScreen> {
  int selectedMenuIndex = 0;
  bool _isLoading = true;

  int _totalPesanan = 0;
  int _totalPelanggan = 0;
  int _poPending = 0;
  int _poSelesai = 0;

  // Global shared state
  List<Pelanggan> pelangganList = [];
  List<Map<String, dynamic>> pesananList = [];
  List<Map<String, dynamic>> pdfFiles = [];
  List<AppNotification> notifications = [];

  // Filter state variables - synchronized with website
  String? _filterCari;
  String? _filterStatus;
  String? _filterDari;
  String? _filterSampai;
  int? _filterMinTotal;
  int? _filterMaxTotal;
  String? _filterProduk;
  bool _filterMultiItem = false;

  @override
  void initState() {
    super.initState();
    _initNotifications();
    _loadAllData();
  }

  void _initNotifications() {
    notifications = [
      AppNotification(
        id: 'init-1',
        title: 'Sistem Aktif 🚀',
        body: 'Sistem Manajemen HutchID berhasil dimuat.',
        timestamp: DateTime.now().subtract(const Duration(minutes: 30)),
        type: 'sistem',
        isRead: false,
      ),
      AppNotification(
        id: 'init-2',
        title: 'Koneksi Database Stabil 🌐',
        body: 'Berhasil terhubung ke API lokal di http://127.0.0.1:8000.',
        timestamp: DateTime.now().subtract(const Duration(hours: 2)),
        type: 'sistem',
        isRead: true,
      ),
    ];
  }

  void _addNotification({
    required String title,
    required String body,
    required String type,
  }) {
    final newNotif = AppNotification(
      id: DateTime.now().millisecondsSinceEpoch.toString(),
      title: title,
      body: body,
      timestamp: DateTime.now(),
      type: type,
      isRead: false,
    );
    setState(() {
      notifications.insert(0, newNotif);
    });

    // Custom modern SnackBar
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(
              type == 'pesanan'
                  ? Icons.shopping_bag_rounded
                  : type == 'pelanggan'
                  ? Icons.person_add_rounded
                  : type == 'arsip'
                  ? Icons.picture_as_pdf_rounded
                  : Icons.notifications_active_rounded,
              color: Colors.white,
              size: 20,
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 13,
                      color: Colors.white,
                    ),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    body,
                    style: const TextStyle(fontSize: 11, color: Colors.white70),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ],
              ),
            ),
          ],
        ),
        backgroundColor: const Color(0xFF2563eb),
        behavior: SnackBarBehavior.floating,
        duration: const Duration(seconds: 4),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
        action: SnackBarAction(
          label: 'Lihat',
          textColor: Colors.white,
          onPressed: () {
            setState(() {
              selectedMenuIndex = 5; // index 5 will be Notifications tab
            });
          },
        ),
      ),
    );
  }

  Future<void> _loadLocalFallbackData() async {
    final prefs = await SharedPreferences.getInstance();

    // 1. Load Pelanggan
    final String? cachedPelanggan = prefs.getString('cached_pelanggan');
    List<Pelanggan> loadedPelanggan = [];
    if (cachedPelanggan != null) {
      try {
        final List decoded = jsonDecode(cachedPelanggan);
        loadedPelanggan = decoded
            .map((item) => Pelanggan.fromJson(item))
            .toList();
      } catch (_) {
        loadedPelanggan = [];
      }
    }
    if (loadedPelanggan.isEmpty) {
      // Seed default dummy pelanggan
      loadedPelanggan = [
        Pelanggan(
          id: '1',
          nama: 'CV. Indo Makmur',
          telepon: '08123456789',
          alamat: 'Jl. Industri No. 45, Jakarta',
          email: 'indomakmur@mail.com',
          jumlahPO: 5,
        ),
        Pelanggan(
          id: '2',
          nama: 'PT. Bagus Sentosa',
          telepon: '08777654321',
          alamat: 'Kawasan Ruko Harmoni Blok B/12, Surabaya',
          email: 'bagussentosa@mail.com',
          jumlahPO: 2,
        ),
        Pelanggan(
          id: '3',
          nama: 'Toko Berkah Jaya',
          telepon: '08998877665',
          alamat: 'Jl. Pasar Baru No. 8, Bandung',
          email: 'berkahjaya@mail.com',
          jumlahPO: 0,
        ),
      ];
      final List<Map<String, dynamic>> rawPelanggan = loadedPelanggan
          .map((p) => p.toJson())
          .toList();
      await prefs.setString('cached_pelanggan', jsonEncode(rawPelanggan));
    }

    // 2. Load Pesanan
    final String? cachedPesanan = prefs.getString('cached_pesanan');
    List<Map<String, dynamic>> loadedPesanan = [];
    if (cachedPesanan != null) {
      try {
        final List decoded = jsonDecode(cachedPesanan);
        loadedPesanan = decoded
            .map((item) => Map<String, dynamic>.from(item))
            .toList();
      } catch (_) {
        loadedPesanan = [];
      }
    }
    if (loadedPesanan.isEmpty) {
      // Seed default dummy pesanan with correct API status values
      loadedPesanan = [
        {
          'id': '1',
          'no': 'PO-001',
          'pelanggan': 'CV. Indo Makmur',
          'tanggal': '29 Mei 2026',
          'deskripsi': 'Pesanan Backpack Kanvas Hitam',
          'jumlah': 100,
          'harga': 125000,
          'total_nilai': 12500000,
          'status': 'selesai',
        },
        {
          'id': '2',
          'no': 'PO-002',
          'pelanggan': 'CV. Indo Makmur',
          'tanggal': '30 Mei 2026',
          'deskripsi': 'Pesanan Tote Bag Premium',
          'jumlah': 250,
          'harga': 45000,
          'total_nilai': 11250000,
          'status': 'dalam_produksi',
        },
        {
          'id': '3',
          'no': 'PO-003',
          'pelanggan': 'PT. Bagus Sentosa',
          'tanggal': '31 Mei 2026',
          'deskripsi': 'Pesanan Duffle Bag Travel',
          'jumlah': 50,
          'harga': 210000,
          'total_nilai': 10500000,
          'status': 'menunggu_konfirmasi',
        },
        {
          'id': '4',
          'no': 'PO-004',
          'pelanggan': 'PT. Bagus Sentosa',
          'tanggal': '01 Jun 2026',
          'deskripsi': 'Pesanan Pouch Kulit Minimalis',
          'jumlah': 500,
          'harga': 25000,
          'total_nilai': 12500000,
          'status': 'draft',
        },
      ];
      await prefs.setString('cached_pesanan', jsonEncode(loadedPesanan));
    }

    // 3. Load Arsip PDF
    final String? cachedPdf = prefs.getString('cached_pdf');
    List<Map<String, dynamic>> loadedPdf = [];
    if (cachedPdf != null) {
      try {
        final List decoded = jsonDecode(cachedPdf);
        loadedPdf = decoded
            .map((item) => Map<String, dynamic>.from(item))
            .toList();
      } catch (_) {
        loadedPdf = [];
      }
    }
    if (loadedPdf.isEmpty) {
      loadedPdf = [
        {
          'id': '1',
          'filename': 'PO-001_CV_Indo_Makmur.pdf',
          'path': 'storage/arsip/PO-001_CV_Indo_Makmur.pdf',
          'size': '1.2 MB',
          'tanggal': '29 Mei 2026',
        },
        {
          'id': '2',
          'filename': 'PO-002_CV_Indo_Makmur.pdf',
          'path': 'storage/arsip/PO-002_CV_Indo_Makmur.pdf',
          'size': '845 KB',
          'tanggal': '30 Mei 2026',
        },
      ];
      await prefs.setString('cached_pdf', jsonEncode(loadedPdf));
    }

    setState(() {
      pelangganList = loadedPelanggan;
      pesananList = loadedPesanan;
      pdfFiles = loadedPdf;

      _totalPesanan = loadedPesanan.length;
      _totalPelanggan = loadedPelanggan.length;
      // Use correct API status values
      _poPending = loadedPesanan
          .where(
            (p) =>
                p['status'] == 'menunggu_konfirmasi' ||
                p['status'] == 'dalam_produksi',
          )
          .length;
      _poSelesai = loadedPesanan.where((p) => p['status'] == 'selesai').length;
    });
  }

  Future<void> _loadAllData() async {
    setState(() => _isLoading = true);
    try {
      final dashboardData = await ApiService.getDashboard();
      final pelangganData = await ApiService.getPelanggan();
      final pesananData = await ApiService.getPesanan(
        cari: _filterCari,
        status: _filterStatus,
        dari: _filterDari,
        sampai: _filterSampai,
        minTotal: _filterMinTotal,
        maxTotal: _filterMaxTotal,
        produk: _filterProduk,
        multiItem: _filterMultiItem,
      );
      final pdfData = await ApiService.getArsipPdf();

      if (ApiService.isOffline ||
          (pelangganData.isEmpty && pesananData.isEmpty)) {
        await _loadLocalFallbackData();
      } else {
        // Cache data loaded from API
        final prefs = await SharedPreferences.getInstance();
        if (pelangganData.isNotEmpty) {
          await prefs.setString(
            'cached_pelanggan',
            jsonEncode(pelangganData.map((p) => p.toJson()).toList()),
          );
        }
        if (pesananData.isNotEmpty) {
          await prefs.setString('cached_pesanan', jsonEncode(pesananData));
        }
        if (pdfData.isNotEmpty) {
          await prefs.setString('cached_pdf', jsonEncode(pdfData));
        }

        setState(() {
          if (dashboardData != null) {
            // Map API field names to app field names
            _totalPesanan = dashboardData['total_aktif'] ?? 0;
            _totalPelanggan = pelangganData.length;
            _poPending = dashboardData['total_menunggu'] ?? 0;
            _poSelesai = dashboardData['total_selesai_bulan_ini'] ?? 0;
          }
          pelangganList = pelangganData;
          pesananList = pesananData;
          pdfFiles = pdfData;
        });
      }
    } catch (e) {
      debugPrint('Error loading data from API: $e');
      await _loadLocalFallbackData();
    } finally {
      setState(() => _isLoading = false);
    }
  }

  void _resetFilters() {
    setState(() {
      _filterCari = null;
      _filterStatus = null;
      _filterDari = null;
      _filterSampai = null;
      _filterMinTotal = null;
      _filterMaxTotal = null;
      _filterProduk = null;
      _filterMultiItem = false;
    });
    _loadAllData();
  }

  void _applyFilters() {
    _loadAllData();
  }

  @override
  Widget build(BuildContext context) {
    final bool isMobile = Responsive.isMobile(context);

    // Generate screens dynamically to ensure state changes are passed down correctly
    final List<Widget> screens = [
      DashboardScreenContent(
        totalPesanan: _totalPesanan,
        totalPelanggan: _totalPelanggan,
        poPending: _poPending,
        poSelesai: _poSelesai,
        user: widget.user,
        pesananList: pesananList,
        isLoading: _isLoading,
        onNavigate: (index) {
          setState(() {
            selectedMenuIndex = index;
          });
        },
      ),
      DaftarPesananScreenContent(
        pesananList: pesananList,
        pelangganList: pelangganList,
        userRole: widget.user.role,
        isLoading: _isLoading,
        // Filter parameters
        filterCari: _filterCari,
        filterStatus: _filterStatus,
        filterDari: _filterDari,
        filterSampai: _filterSampai,
        filterMinTotal: _filterMinTotal,
        filterMaxTotal: _filterMaxTotal,
        filterProduk: _filterProduk,
        filterMultiItem: _filterMultiItem,
        // Filter callbacks
        onFilterCariChanged: (value) => setState(() => _filterCari = value),
        onFilterStatusChanged: (value) => setState(() => _filterStatus = value),
        onFilterDariChanged: (value) => setState(() => _filterDari = value),
        onFilterSampaiChanged: (value) => setState(() => _filterSampai = value),
        onFilterMinTotalChanged: (value) =>
            setState(() => _filterMinTotal = value),
        onFilterMaxTotalChanged: (value) =>
            setState(() => _filterMaxTotal = value),
        onFilterProdukChanged: (value) => setState(() => _filterProduk = value),
        onFilterMultiItemChanged: (value) =>
            setState(() => _filterMultiItem = value),
        onApplyFilters: _applyFilters,
        onResetFilters: _resetFilters,
        onDelete: (id) async {
          final pesanan = pesananList.firstWhere(
            (p) => p['id'].toString() == id,
            orElse: () => <String, dynamic>{},
          );
          final String no = pesanan['no'] ?? 'PO-$id';
          final success = await ApiService.deletePesanan(id);
          if (success) {
            _addNotification(
              title: 'Pesanan Dihapus 🗑️',
              body: 'Pesanan $no berhasil dihapus dari sistem.',
              type: 'pesanan',
            );
            await _loadAllData();
          }
        },
        onStatusChanged: (id, newStatus) async {
          final pesanan = pesananList.firstWhere(
            (p) => p['id'].toString() == id,
            orElse: () => <String, dynamic>{},
          );
          final String no = pesanan['no'] ?? 'PO-$id';
          final success = await ApiService.updatePesananStatus(
            id,
            newStatus,
            catatan: 'Status diubah ke $newStatus',
          );
          if (success) {
            _addNotification(
              title: 'Status Pesanan Diperbarui 🔄',
              body: 'Status pesanan $no diubah menjadi $newStatus.',
              type: 'pesanan',
            );
            await _loadAllData();
          }
        },
      ),
      BuatPoScreenContent(
        pelangganList: pelangganList,
        onSave: (pelangganNama, items, status) async {
          final deskripsiGabung = items
              .map((i) => '${i['jumlah']}x ${i['deskripsi']}')
              .join(', ');
          final totalAmount = items.fold<int>(
            0,
            (sum, i) => sum + (i['jumlah'] as int) * (i['harga'] as int),
          );
          final result = await ApiService.createPesanan({
            'pelanggan_id': pelangganNama,
            'status': status,
            'total_nilai': totalAmount,
            'catatan': deskripsiGabung,
            'items': items,
          });
          if (result) {
            final String totalFormatted =
                'Rp ${totalAmount.toString().replaceAllMapped(RegExp(r"(\d{1,3})(?=(\d{3})+(?!\d))"), (Match m) => "${m[1].toString()}.")}';
            _addNotification(
              title: 'Pesanan Baru Dibuat 🎉',
              body:
                  'PO baru untuk $pelangganNama senilai $totalFormatted berhasil dibuat.',
              type: 'pesanan',
            );
            await _loadAllData();
            setState(() {
              selectedMenuIndex = 1;
            });
          }
        },
      ),
      DaftarPelangganScreenWidget(
        pelangganList: pelangganList,
        userRole: widget.user.role,
        isLoading: _isLoading,
        onAdd: (nama, telepon, alamat, email) async {
          final result = await ApiService.createPelanggan(
            nama,
            telepon,
            alamat,
            email,
          );
          if (result != null) {
            _addNotification(
              title: 'Pelanggan Baru Terdaftar 👤',
              body: 'Pelanggan baru bernama $nama berhasil ditambahkan.',
              type: 'pelanggan',
            );
            await _loadAllData();
          }
        },
        onEdit: (id, nama, telepon, alamat, email) async {
          final result = await ApiService.updatePelanggan(
            id,
            nama,
            telepon,
            alamat,
            email,
          );
          if (result != null) {
            _addNotification(
              title: 'Data Pelanggan Diperbarui ✏️',
              body: 'Data pelanggan $nama berhasil diperbarui.',
              type: 'pelanggan',
            );
            await _loadAllData();
          }
        },
        onDelete: (id) async {
          String pelangganNama = 'Pelanggan';
          try {
            final pelanggan = pelangganList.firstWhere(
              (p) => p.id.toString() == id,
            );
            pelangganNama = pelanggan.nama;
          } catch (_) {}
          final success = await ApiService.deletePelanggan(id);
          if (success) {
            _addNotification(
              title: 'Pelanggan Dihapus 🗑️',
              body: 'Pelanggan $pelangganNama berhasil dihapus dari sistem.',
              type: 'pelanggan',
            );
            await _loadAllData();
          }
        },
      ),
      ArsipPdfScreenContent(
        pdfFiles: pdfFiles,
        userRole: widget.user.role,
        isLoading: _isLoading,
        onDelete: (id) async {
          final file = pdfFiles.firstWhere(
            (f) => f['id'].toString() == id,
            orElse: () => <String, dynamic>{},
          );
          final String filename =
              file['filename'] ?? file['nama'] ?? 'Dokumen PDF';
          final success = await ApiService.deleteArsipPdf(id);
          if (success) {
            _addNotification(
              title: 'Arsip PDF Dihapus 🗑️',
              body: '$filename berhasil dihapus.',
              type: 'arsip',
            );
            await _loadAllData();
          }
        },
      ),
      NotifikasiScreenContent(
        notifications: notifications,
        onMarkAllAsRead: () {
          setState(() {
            for (var notif in notifications) {
              notif.isRead = true;
            }
          });
        },
        onClearAll: () {
          setState(() {
            notifications.clear();
          });
        },
        onMarkAsRead: (id) {
          setState(() {
            final idx = notifications.indexWhere((n) => n.id == id);
            if (idx != -1) {
              notifications[idx].isRead = true;
            }
          });
        },
        onDeleteNotification: (id) {
          setState(() {
            notifications.removeWhere((n) => n.id == id);
          });
        },
      ),
    ];

    // ── Mobile Layout ────────────────────────────────────────────────────────
    if (isMobile) {
      return Scaffold(
        backgroundColor: const Color(0xFFF8FAFC),
        body: Stack(
          fit: StackFit.expand,
          children: [
            AnimatedSwitcher(
              duration: const Duration(milliseconds: 350),
              switchInCurve: Curves.easeInOut,
              switchOutCurve: Curves.easeInOut,
              transitionBuilder: (Widget child, Animation<double> animation) {
                return FadeTransition(opacity: animation, child: child);
              },
              child: SafeArea(
                bottom: false,
                child: Container(
                  key: ValueKey<int>(selectedMenuIndex),
                  child: screens[selectedMenuIndex],
                ),
              ),
            ),
            if (_isLoading)
              const Positioned(
                top: 0,
                left: 0,
                right: 0,
                child: LinearProgressIndicator(
                  backgroundColor: Colors.transparent,
                  valueColor: AlwaysStoppedAnimation<Color>(Color(0xFF2563eb)),
                  minHeight: 3,
                ),
              ),
          ],
        ),
        bottomNavigationBar: _buildMobileBottomNav(),
      );
    }

    // ── Desktop Layout ───────────────────────────────────────────────────────
    return Scaffold(
      backgroundColor: const Color(0xFFF8FAFC),
      body: Row(
        children: [
          Sidebar(
            selectedIndex: selectedMenuIndex,
            user: widget.user,
            pesananBadgeCount: pesananList
                .where(
                  (p) =>
                      p['status'] == 'menunggu_konfirmasi' ||
                      p['status'] == 'dalam_produksi' ||
                      p['status'] == 'dikonfirmasi',
                )
                .length,
            notificationsBadgeCount: notifications
                .where((n) => !n.isRead)
                .length,
            onMenuSelected: (index) {
              setState(() {
                selectedMenuIndex = index;
              });
            },
            onLogout: _handleLogout,
          ),
          Expanded(
            child: Stack(
              fit: StackFit.expand,
              children: [
                AnimatedSwitcher(
                  duration: const Duration(milliseconds: 800),
                  switchInCurve: Curves.easeInOutCubic,
                  switchOutCurve: Curves.easeInOutCubic,
                  transitionBuilder:
                      (Widget child, Animation<double> animation) {
                        return FadeTransition(
                          opacity: animation,
                          child: SlideTransition(
                            position: Tween<Offset>(
                              begin: const Offset(0.04, 0.0),
                              end: Offset.zero,
                            ).animate(animation),
                            child: child,
                          ),
                        );
                      },
                  child: Container(
                    key: ValueKey<int>(selectedMenuIndex),
                    child: screens[selectedMenuIndex],
                  ),
                ),
                if (_isLoading)
                  const Positioned(
                    top: 0,
                    left: 0,
                    right: 0,
                    child: LinearProgressIndicator(
                      backgroundColor: Colors.transparent,
                      valueColor: AlwaysStoppedAnimation<Color>(
                        Color(0xFF2563eb),
                      ),
                      minHeight: 3,
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildMobileBottomNav() {
    final int badge = pesananList
        .where((p) => p['status'] == 'Pending' || p['status'] == 'Proses')
        .length;
    final int unreadNotifications = notifications
        .where((n) => !n.isRead)
        .length;
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.08),
            blurRadius: 16,
            offset: const Offset(0, -4),
          ),
        ],
      ),
      child: SafeArea(
        child: SizedBox(
          height: 65,
          child: Row(
            children: [
              _buildNavItem(0, Icons.dashboard_rounded, 'Dashboard'),
              _buildNavItem(
                1,
                Icons.shopping_cart_rounded,
                'Pesanan',
                badge: badge,
              ),
              if (widget.user.role == 'Administrator' ||
                  widget.user.role == 'Staf Penjualan')
                _buildNavItem(2, Icons.add_circle_rounded, 'Buat PO'),
              _buildNavItem(3, Icons.people_rounded, 'Pelanggan'),
              _buildNavItem(4, Icons.picture_as_pdf_rounded, 'Arsip'),
              _buildNavItem(
                5,
                Icons.notifications_rounded,
                'Notifikasi',
                badge: unreadNotifications,
              ),
              _buildLogoutNavItem(),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildNavItem(
    int index,
    IconData icon,
    String label, {
    int badge = 0,
  }) {
    final bool isSelected = selectedMenuIndex == index;
    return Expanded(
      child: GestureDetector(
        onTap: () => setState(() => selectedMenuIndex = index),
        child: Container(
          color: Colors.transparent,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Stack(
                clipBehavior: Clip.none,
                children: [
                  AnimatedContainer(
                    duration: const Duration(milliseconds: 200),
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: isSelected
                          ? const Color(0xFF2563eb).withValues(alpha: 0.12)
                          : Colors.transparent,
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Icon(
                      icon,
                      size: 22,
                      color: isSelected
                          ? const Color(0xFF2563eb)
                          : Colors.grey[400],
                    ),
                  ),
                  if (badge > 0)
                    Positioned(
                      top: -4,
                      right: -4,
                      child: Container(
                        padding: const EdgeInsets.all(3),
                        decoration: const BoxDecoration(
                          color: Colors.red,
                          shape: BoxShape.circle,
                        ),
                        child: Text(
                          '$badge',
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 9,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    ),
                ],
              ),
              const SizedBox(height: 2),
              Text(
                label,
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: isSelected ? FontWeight.w700 : FontWeight.w400,
                  color: isSelected
                      ? const Color(0xFF2563eb)
                      : Colors.grey[400],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  void _handleLogout() {
    showDialog(
      context: context,
      builder: (BuildContext ctx) {
        return Dialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(24),
          ),
          child: Container(
            padding: const EdgeInsets.all(28),
            decoration: BoxDecoration(
              color: Colors.white,
              borderRadius: BorderRadius.circular(24),
            ),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  width: 64,
                  height: 64,
                  decoration: BoxDecoration(
                    color: Colors.red.withValues(alpha: 0.1),
                    shape: BoxShape.circle,
                  ),
                  child: const Icon(
                    Icons.logout_rounded,
                    color: Colors.red,
                    size: 30,
                  ),
                ),
                const SizedBox(height: 16),
                const Text(
                  'Keluar dari Akun?',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF1e3a8a),
                  ),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Sesi Anda akan berakhir dan Anda akan diarahkan ke halaman login.',
                  textAlign: TextAlign.center,
                  style: TextStyle(fontSize: 13, color: Colors.grey),
                ),
                const SizedBox(height: 24),
                Row(
                  children: [
                    Expanded(
                      child: TextButton(
                        style: TextButton.styleFrom(
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                            side: const BorderSide(color: Color(0xFFE2E8F0)),
                          ),
                        ),
                        onPressed: () => Navigator.pop(ctx),
                        child: const Text(
                          'Batal',
                          style: TextStyle(
                            color: Color(0xFF64748B),
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: ElevatedButton(
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.red,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          elevation: 0,
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        onPressed: () {
                          Navigator.pop(ctx);
                          Navigator.of(context).pushReplacement(
                            PageRouteBuilder(
                              pageBuilder:
                                  (context, animation, secondaryAnimation) =>
                                      const LoginScreen(),
                              transitionsBuilder:
                                  (
                                    context,
                                    animation,
                                    secondaryAnimation,
                                    child,
                                  ) {
                                    return FadeTransition(
                                      opacity: animation,
                                      child: child,
                                    );
                                  },
                              transitionDuration: const Duration(
                                milliseconds: 500,
                              ),
                            ),
                          );
                        },
                        child: const Text(
                          'Ya, Keluar',
                          style: TextStyle(
                            color: Colors.white,
                            fontWeight: FontWeight.w600,
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
      },
    );
  }

  Widget _buildLogoutNavItem() {
    return Expanded(
      child: GestureDetector(
        onTap: () {
          showDialog(
            context: context,
            builder: (BuildContext ctx) {
              return AlertDialog(
                title: const Text('Keluar'),
                content: const Text('Apakah Anda yakin ingin keluar?'),
                actions: [
                  TextButton(
                    onPressed: () => Navigator.pop(ctx),
                    child: const Text('Batal'),
                  ),
                  TextButton(
                    onPressed: () {
                      Navigator.pop(ctx);
                      Navigator.of(context).pushReplacement(
                        PageRouteBuilder(
                          pageBuilder:
                              (context, animation, secondaryAnimation) =>
                                  const LoginScreen(),
                          transitionsBuilder:
                              (context, animation, secondaryAnimation, child) {
                                return FadeTransition(
                                  opacity: animation,
                                  child: child,
                                );
                              },
                          transitionDuration: const Duration(milliseconds: 500),
                        ),
                      );
                    },
                    child: const Text(
                      'Keluar',
                      style: TextStyle(color: Colors.red),
                    ),
                  ),
                ],
              );
            },
          );
        },
        child: Container(
          color: Colors.transparent,
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(6),
                decoration: BoxDecoration(
                  color: Colors.transparent,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(
                  Icons.logout_rounded,
                  size: 22,
                  color: Colors.red[400],
                ),
              ),
              const SizedBox(height: 2),
              Text(
                'Keluar',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w400,
                  color: Colors.red[400],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// =============================================================================
// Dashboard Content — Premium Redesign
// =============================================================================
class DashboardScreenContent extends StatefulWidget {
  final int totalPesanan;
  final int totalPelanggan;
  final int poPending;
  final int poSelesai;
  final User user;
  final List<Map<String, dynamic>> pesananList;
  final bool isLoading;
  final ValueChanged<int> onNavigate;

  const DashboardScreenContent({
    super.key,
    required this.totalPesanan,
    required this.totalPelanggan,
    required this.poPending,
    required this.poSelesai,
    required this.user,
    required this.pesananList,
    required this.isLoading,
    required this.onNavigate,
  });

  @override
  State<DashboardScreenContent> createState() => _DashboardScreenContentState();
}

class _DashboardScreenContentState extends State<DashboardScreenContent>
    with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late List<Animation<double>> _cardAnimations;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1000),
    );
    _cardAnimations = List.generate(4, (i) {
      return Tween<double>(begin: 0.0, end: 1.0).animate(
        CurvedAnimation(
          parent: _animController,
          curve: Interval(
            i * 0.1,
            (0.4 + i * 0.1).clamp(0.0, 1.0),
            curve: Curves.easeOutBack,
          ),
        ),
      );
    });
    _animController.forward();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  String _getGreeting() {
    final hour = DateTime.now().hour;
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
  }

  String _getInitials(String name) {
    List<String> names = name.split(' ');
    String initials = '';
    int numWords = names.length > 2 ? 2 : names.length;
    for (var i = 0; i < numWords; i++) {
      if (names[i].isNotEmpty) initials += names[i][0].toUpperCase();
    }
    return initials.isEmpty ? 'U' : initials;
  }

  String _getFormattedDate() {
    final now = DateTime.now();
    const dayNames = [
      'Senin',
      'Selasa',
      'Rabu',
      'Kamis',
      'Jumat',
      'Sabtu',
      'Minggu',
    ];
    const monthNames = [
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember',
    ];
    return '${dayNames[now.weekday - 1]}, ${now.day} ${monthNames[now.month - 1]} ${now.year}';
  }

  @override
  Widget build(BuildContext context) {
    return Responsive.isMobile(context)
        ? _buildMobileLayout(context)
        : _buildDesktopLayout(context);
  }

  // ─────────────────────────────────────────────────────────────────────────
  // DESKTOP LAYOUT
  // ─────────────────────────────────────────────────────────────────────────
  Widget _buildDesktopLayout(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final double width = constraints.maxWidth;
        int columns = 4;
        if (width < 750) {
          columns = 1;
        } else if (width < 1150) {
          columns = 2;
        }

        bool stackMiddleRow = width < 950;

        return Container(
          color: const Color(0xFFF8FAFC),
          child: Column(
            children: [
              _buildDesktopHeader(),
              Expanded(
                child: SingleChildScrollView(
                  physics: const BouncingScrollPhysics(),
                  padding: const EdgeInsets.all(28),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildKpiGrid(columns),
                      const SizedBox(height: 24),
                      if (stackMiddleRow) ...[
                        _buildStatusChartCard(),
                        const SizedBox(height: 24),
                        _buildRecentOrdersCard(),
                      ] else ...[
                        Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(flex: 5, child: _buildStatusChartCard()),
                            const SizedBox(width: 24),
                            Expanded(flex: 7, child: _buildRecentOrdersCard()),
                          ],
                        ),
                      ],
                      const SizedBox(height: 24),
                      _buildQuickActionsCard(width),
                    ],
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildDesktopHeader() {
    return Container(
      padding: const EdgeInsets.fromLTRB(32, 24, 32, 24),
      decoration: const BoxDecoration(
        color: Colors.white,
        border: Border(bottom: BorderSide(color: Color(0xFFE2E8F0))),
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 22,
            backgroundColor: const Color(0xFF3B82F6).withValues(alpha: 0.1),
            child: Text(
              _getInitials(widget.user.nama),
              style: const TextStyle(
                color: Color(0xFF2563EB),
                fontWeight: FontWeight.bold,
                fontSize: 13,
              ),
            ),
          ),
          const SizedBox(width: 16),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '${_getGreeting()}, ${widget.user.nama.split(' ').first}! 👋',
                style: const TextStyle(
                  color: Color(0xFF0F172A),
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 4),
              Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 2,
                    ),
                    decoration: BoxDecoration(
                      color: const Color(0xFF3B82F6).withValues(alpha: 0.08),
                      borderRadius: BorderRadius.circular(6),
                      border: Border.all(
                        color: const Color(0xFF3B82F6).withValues(alpha: 0.15),
                      ),
                    ),
                    child: Text(
                      widget.user.role,
                      style: const TextStyle(
                        color: Color(0xFF2563EB),
                        fontSize: 9,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  const Icon(
                    Icons.calendar_today_rounded,
                    color: Color(0xFF64748B),
                    size: 12,
                  ),
                  const SizedBox(width: 6),
                  Text(
                    _getFormattedDate(),
                    style: const TextStyle(
                      color: Color(0xFF64748B),
                      fontSize: 12,
                    ),
                  ),
                ],
              ),
            ],
          ),
          const Spacer(),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
            decoration: BoxDecoration(
              color: const Color(0xFFF1F5F9),
              borderRadius: BorderRadius.circular(10),
              border: Border.all(color: const Color(0xFFE2E8F0)),
            ),
            child: const Row(
              children: [
                Icon(
                  Icons.storefront_rounded,
                  color: Color(0xFF475569),
                  size: 16,
                ),
                SizedBox(width: 8),
                Text(
                  'HUTCHID',
                  style: TextStyle(
                    color: Color(0xFF1E293B),
                    fontSize: 11,
                    fontWeight: FontWeight.bold,
                    letterSpacing: 0.5,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildKpiGrid(int columns) {
    final List<Widget> kpis = [
      _buildKpiCard(
        0,
        'Total Pesanan',
        '${widget.totalPesanan}',
        Icons.shopping_cart_outlined,
        const Color(0xFF3B82F6),
        Icons.arrow_upward_rounded,
        () => widget.onNavigate(1),
      ),
      _buildKpiCard(
        1,
        'Total Pelanggan',
        '${widget.totalPelanggan}',
        Icons.people_outline_rounded,
        const Color(0xFF10B981),
        Icons.people_alt_rounded,
        () => widget.onNavigate(3),
      ),
      _buildKpiCard(
        2,
        'PO Pending',
        '${widget.poPending}',
        Icons.pending_actions_outlined,
        const Color(0xFFF59E0B),
        Icons.access_time_rounded,
        () => widget.onNavigate(1),
      ),
      _buildKpiCard(
        3,
        'PO Selesai',
        '${widget.poSelesai}',
        Icons.check_circle_outline_rounded,
        const Color(0xFF8B5CF6),
        Icons.verified_rounded,
        () => widget.onNavigate(1),
      ),
    ];

    if (columns == 4) {
      return Row(
        children:
            kpis
                .map((kpi) => Expanded(child: kpi))
                .expand((w) => [w, const SizedBox(width: 16)])
                .toList()
              ..removeLast(),
      );
    } else if (columns == 2) {
      return Column(
        children: [
          Row(
            children: [
              Expanded(child: kpis[0]),
              const SizedBox(width: 16),
              Expanded(child: kpis[1]),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(child: kpis[2]),
              const SizedBox(width: 16),
              Expanded(child: kpis[3]),
            ],
          ),
        ],
      );
    } else {
      return Column(
        children:
            kpis.expand((kpi) => [kpi, const SizedBox(height: 16)]).toList()
              ..removeLast(),
      );
    }
  }

  Widget _buildKpiCard(
    int idx,
    String title,
    String value,
    IconData icon,
    Color color,
    IconData trendIcon,
    VoidCallback onTap,
  ) {
    return AnimatedBuilder(
      animation: _cardAnimations[idx],
      builder: (context, child) => Transform.translate(
        offset: Offset(0, 30 * (1 - _cardAnimations[idx].value)),
        child: Opacity(
          opacity: _cardAnimations[idx].value.clamp(0.0, 1.0),
          child: child,
        ),
      ),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: const Color(0xFFE2E8F0)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.02),
              blurRadius: 16,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Material(
          color: Colors.transparent,
          borderRadius: BorderRadius.circular(20),
          child: InkWell(
            onTap: onTap,
            borderRadius: BorderRadius.circular(20),
            child: Padding(
              padding: const EdgeInsets.all(22),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: color.withValues(alpha: 0.08),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: color.withValues(alpha: 0.15),
                          ),
                        ),
                        child: Icon(icon, color: color, size: 20),
                      ),
                      Container(
                        padding: const EdgeInsets.all(6),
                        decoration: BoxDecoration(
                          color: color.withValues(alpha: 0.05),
                          shape: BoxShape.circle,
                        ),
                        child: Icon(trendIcon, color: color, size: 12),
                      ),
                    ],
                  ),
                  const SizedBox(height: 18),
                  widget.isLoading
                      ? const ShimmerLoading(
                          width: 60,
                          height: 32,
                          borderRadius: 6,
                        )
                      : Text(
                          value,
                          style: const TextStyle(
                            color: Color(0xFF0F172A),
                            fontSize: 32,
                            fontWeight: FontWeight.bold,
                            height: 1,
                          ),
                        ),
                  const SizedBox(height: 6),
                  Text(
                    title,
                    style: const TextStyle(
                      color: Color(0xFF64748B),
                      fontSize: 12,
                      fontWeight: FontWeight.w500,
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

  Widget _buildStatusChartCard() {
    final pending = widget.pesananList
        .where((p) => p['status'] == 'Pending')
        .length;
    final proses = widget.pesananList
        .where((p) => p['status'] == 'Proses')
        .length;
    final selesai = widget.pesananList
        .where((p) => p['status'] == 'Selesai')
        .length;
    final draft = widget.pesananList
        .where((p) => p['status'] == 'Draft')
        .length;
    final total = (pending + proses + selesai + draft).toDouble().clamp(
      1.0,
      double.infinity,
    );

    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 20,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: const Color(0xFFEFF6FF),
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: const Color(0xFFBFDBFE)),
                ),
                child: const Icon(
                  Icons.bar_chart_rounded,
                  color: Color(0xFF2563EB),
                  size: 18,
                ),
              ),
              const SizedBox(width: 10),
              const Text(
                'Status Pesanan',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF0F172A),
                ),
              ),
              const Spacer(),
              GestureDetector(
                onTap: () => widget.onNavigate(1),
                child: Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 5,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFFEFF6FF),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                      color: const Color(0xFFBFDBFE).withValues(alpha: 0.5),
                    ),
                  ),
                  child: const Text(
                    'Detail →',
                    style: TextStyle(
                      fontSize: 11,
                      color: Color(0xFF2563EB),
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
          _buildStatusBar('Pending', pending, total, const Color(0xFFF59E0B)),
          const SizedBox(height: 14),
          _buildStatusBar('Proses', proses, total, const Color(0xFF3B82F6)),
          const SizedBox(height: 14),
          _buildStatusBar('Selesai', selesai, total, const Color(0xFF10B981)),
          const SizedBox(height: 14),
          _buildStatusBar('Draft', draft, total, const Color(0xFF8B5CF6)),
        ],
      ),
    );
  }

  Widget _buildStatusBar(String label, int count, double total, Color color) {
    final ratio = (count / total).clamp(0.0, 1.0);
    return Row(
      children: [
        SizedBox(
          width: 72,
          child: Row(
            children: [
              Container(
                width: 8,
                height: 8,
                decoration: BoxDecoration(color: color, shape: BoxShape.circle),
              ),
              const SizedBox(width: 8),
              Text(
                label,
                style: const TextStyle(
                  fontSize: 12,
                  color: Color(0xFF475569),
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: ClipRRect(
            borderRadius: BorderRadius.circular(4),
            child: LinearProgressIndicator(
              value: ratio,
              backgroundColor: color.withValues(alpha: 0.08),
              valueColor: AlwaysStoppedAnimation<Color>(color),
              minHeight: 8,
            ),
          ),
        ),
        const SizedBox(width: 12),
        SizedBox(
          width: 24,
          child: Text(
            '$count',
            style: TextStyle(
              fontSize: 12,
              color: color,
              fontWeight: FontWeight.bold,
            ),
            textAlign: TextAlign.right,
          ),
        ),
      ],
    );
  }

  Widget _buildRecentOrdersCard() {
    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 20,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: const Color(0xFFEFF6FF),
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: const Color(0xFFBFDBFE)),
                ),
                child: const Icon(
                  Icons.receipt_long_rounded,
                  color: Color(0xFF2563EB),
                  size: 18,
                ),
              ),
              const SizedBox(width: 10),
              const Text(
                'Pesanan Terbaru',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF0F172A),
                ),
              ),
              const Spacer(),
              GestureDetector(
                onTap: () => widget.onNavigate(1),
                child: Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 10,
                    vertical: 5,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFFEFF6FF),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                      color: const Color(0xFFBFDBFE).withValues(alpha: 0.5),
                    ),
                  ),
                  child: const Text(
                    'Lihat Semua →',
                    style: TextStyle(
                      fontSize: 11,
                      color: Color(0xFF2563EB),
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          if (widget.isLoading)
            ...List.generate(
              4,
              (i) => Container(
                margin: const EdgeInsets.only(bottom: 10),
                child: const Row(
                  children: [
                    ShimmerLoading(width: 38, height: 38, borderRadius: 10),
                    SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          ShimmerLoading(width: 120, height: 13),
                          SizedBox(height: 6),
                          ShimmerLoading(width: 70, height: 10),
                        ],
                      ),
                    ),
                    ShimmerLoading(width: 65, height: 24, borderRadius: 20),
                  ],
                ),
              ),
            )
          else if (widget.pesananList.isEmpty)
            Container(
              padding: const EdgeInsets.all(32),
              child: const Center(
                child: Column(
                  children: [
                    Icon(
                      Icons.receipt_long_rounded,
                      color: Color(0xFFCBD5E1),
                      size: 40,
                    ),
                    SizedBox(height: 8),
                    Text(
                      'Belum ada pesanan',
                      style: TextStyle(color: Color(0xFF94A3B8)),
                    ),
                  ],
                ),
              ),
            )
          else
            ...widget.pesananList.take(5).map(_buildDesktopRecentItem),
        ],
      ),
    );
  }

  Widget _buildDesktopRecentItem(Map<String, dynamic> pesanan) {
    Color statusColor;
    switch (pesanan['status']) {
      case 'Proses':
        statusColor = const Color(0xFF3B82F6);
        break;
      case 'Selesai':
        statusColor = const Color(0xFF10B981);
        break;
      case 'Draft':
        statusColor = Colors.grey;
        break;
      default:
        statusColor = const Color(0xFFF59E0B);
    }
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: const Color(0xFFF8FAFC),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE2E8F0)),
      ),
      child: Row(
        children: [
          Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              color: statusColor.withValues(alpha: 0.08),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Icon(
              Icons.receipt_long_outlined,
              color: statusColor,
              size: 18,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  pesanan['pelanggan'] ?? 'Umum',
                  style: const TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF0F172A),
                  ),
                ),
                const SizedBox(height: 2),
                Text(
                  pesanan['no'] ?? '',
                  style: const TextStyle(
                    fontSize: 11,
                    color: Color(0xFF64748B),
                  ),
                ),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
            decoration: BoxDecoration(
              color: statusColor.withValues(alpha: 0.08),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: statusColor.withValues(alpha: 0.15)),
            ),
            child: Text(
              pesanan['status'] ?? 'Pending',
              style: TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.bold,
                color: statusColor,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDesktopQuickAction(
    IconData icon,
    String label,
    Color color,
    VoidCallback onTap, {
    bool expand = true,
  }) {
    final Widget content = Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(14),
        child: Container(
          padding: const EdgeInsets.symmetric(vertical: 16),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(14),
            border: Border.all(color: const Color(0xFFE2E8F0)),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withValues(alpha: 0.01),
                blurRadius: 10,
              ),
            ],
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Container(
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.08),
                  shape: BoxShape.circle,
                  border: Border.all(color: color.withValues(alpha: 0.15)),
                ),
                child: Icon(icon, color: color, size: 20),
              ),
              const SizedBox(height: 8),
              Text(
                label,
                style: const TextStyle(
                  fontSize: 12,
                  fontWeight: FontWeight.w600,
                  color: Color(0xFF334155),
                ),
                textAlign: TextAlign.center,
              ),
            ],
          ),
        ),
      ),
    );

    if (expand) {
      return Expanded(child: content);
    }
    return SizedBox(width: double.infinity, child: content);
  }

  Widget _buildQuickActionsCard(double width) {
    final bool stack = width < 750;

    final List<Widget> items = [
      if (widget.user.role == 'Administrator' ||
          widget.user.role == 'Staf Penjualan') ...[
        _buildDesktopQuickAction(
          Icons.add_circle_outline_rounded,
          'Buat PO Baru',
          const Color(0xFF3B82F6),
          () => widget.onNavigate(2),
          expand: !stack,
        ),
        if (!stack) const SizedBox(width: 12) else const SizedBox(height: 12),
        _buildDesktopQuickAction(
          Icons.person_add_outlined,
          'Tambah Pelanggan',
          const Color(0xFF10B981),
          () => widget.onNavigate(3),
          expand: !stack,
        ),
        if (!stack) const SizedBox(width: 12) else const SizedBox(height: 12),
      ],
      _buildDesktopQuickAction(
        Icons.list_alt_rounded,
        'Daftar Pesanan',
        const Color(0xFFF59E0B),
        () => widget.onNavigate(1),
        expand: !stack,
      ),
      if (!stack) const SizedBox(width: 12) else const SizedBox(height: 12),
      _buildDesktopQuickAction(
        Icons.picture_as_pdf_outlined,
        'Arsip PDF',
        const Color(0xFF8B5CF6),
        () => widget.onNavigate(4),
        expand: !stack,
      ),
      if (!stack) const SizedBox(width: 12) else const SizedBox(height: 12),
      _buildDesktopQuickAction(
        Icons.notifications_outlined,
        'Notifikasi',
        const Color(0xFFEF4444),
        () => widget.onNavigate(5),
        expand: !stack,
      ),
    ];

    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 20,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: const Color(0xFFFFFBEB),
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: const Color(0xFFFDE68A)),
                ),
                child: const Icon(
                  Icons.flash_on_rounded,
                  color: Color(0xFFD97706),
                  size: 18,
                ),
              ),
              const SizedBox(width: 10),
              const Text(
                'Aksi Cepat',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF0F172A),
                ),
              ),
            ],
          ),
          const SizedBox(height: 20),
          if (stack) Column(children: items) else Row(children: items),
        ],
      ),
    );
  }

  // ─────────────────────────────────────────────────────────────────────────
  // MOBILE LAYOUT
  // ─────────────────────────────────────────────────────────────────────────
  Widget _buildMobileLayout(BuildContext context) {
    final recentPesanan = widget.pesananList.take(3).toList();

    return Container(
      color: const Color(0xFFF8FAFC),
      child: SafeArea(
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Premium Minimalist Header
              Container(
                width: double.infinity,
                padding: const EdgeInsets.fromLTRB(20, 24, 20, 24),
                decoration: const BoxDecoration(
                  color: Colors.white,
                  border: Border(bottom: BorderSide(color: Color(0xFFE2E8F0))),
                ),
                child: Row(
                  children: [
                    CircleAvatar(
                      radius: 20,
                      backgroundColor: const Color(
                        0xFF3B82F6,
                      ).withValues(alpha: 0.1),
                      child: Text(
                        _getInitials(widget.user.nama),
                        style: const TextStyle(
                          color: Color(0xFF2563EB),
                          fontWeight: FontWeight.bold,
                          fontSize: 12,
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            '${_getGreeting()},',
                            style: const TextStyle(
                              color: Color(0xFF64748B),
                              fontSize: 11,
                            ),
                          ),
                          Text(
                            '${widget.user.nama.split(' ').first} 👋',
                            style: const TextStyle(
                              color: Color(0xFF0F172A),
                              fontSize: 18,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            _getFormattedDate(),
                            style: const TextStyle(
                              color: Color(0xFF94A3B8),
                              fontSize: 10,
                            ),
                          ),
                        ],
                      ),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 3,
                      ),
                      decoration: BoxDecoration(
                        color: const Color(0xFF3B82F6).withValues(alpha: 0.08),
                        borderRadius: BorderRadius.circular(6),
                        border: Border.all(
                          color: const Color(
                            0xFF3B82F6,
                          ).withValues(alpha: 0.15),
                        ),
                      ),
                      child: Text(
                        widget.user.role,
                        style: const TextStyle(
                          color: Color(0xFF2563EB),
                          fontSize: 9,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ],
                ),
              ),

              Padding(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'IKHTISAR KINERJA',
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF94A3B8),
                        letterSpacing: 1.5,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _buildKpiGrid(1),

                    const SizedBox(height: 24),
                    _buildStatusChartCard(),

                    const SizedBox(height: 24),
                    _buildMobileRecentOrdersCard(recentPesanan),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildMobileRecentOrdersCard(List<Map<String, dynamic>> recent) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 20,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              const Text(
                'Pesanan Terbaru',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF0F172A),
                ),
              ),
              GestureDetector(
                onTap: () => widget.onNavigate(1),
                child: const Text(
                  'Lihat Semua →',
                  style: TextStyle(
                    fontSize: 11,
                    color: Color(0xFF2563EB),
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          if (widget.isLoading)
            ...List.generate(
              3,
              (i) => Container(
                margin: const EdgeInsets.only(bottom: 10),
                child: const Row(
                  children: [
                    ShimmerLoading(width: 32, height: 32, borderRadius: 8),
                    SizedBox(width: 10),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          ShimmerLoading(width: 90, height: 11),
                          SizedBox(height: 4),
                          ShimmerLoading(width: 50, height: 8),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
            )
          else if (recent.isEmpty)
            const Center(
              child: Padding(
                padding: EdgeInsets.symmetric(vertical: 20),
                child: Text(
                  'Belum ada pesanan',
                  style: TextStyle(color: Color(0xFF94A3B8), fontSize: 12),
                ),
              ),
            )
          else
            ...recent.map(_buildDesktopRecentItem),
        ],
      ),
    );
  }
}

// Daftar Pesanan - Premium Redesign
class DaftarPesananScreenContent extends StatefulWidget {
  final List<Map<String, dynamic>> pesananList;
  final List<Pelanggan> pelangganList;
  final String userRole;
  final bool isLoading;
  final Future<void> Function(String) onDelete;
  final Future<void> Function(String, String) onStatusChanged;

  // Filter parameters and callbacks
  final String? filterCari;
  final String? filterStatus;
  final String? filterDari;
  final String? filterSampai;
  final int? filterMinTotal;
  final int? filterMaxTotal;
  final String? filterProduk;
  final bool filterMultiItem;
  final Function(String?) onFilterCariChanged;
  final Function(String?) onFilterStatusChanged;
  final Function(String?) onFilterDariChanged;
  final Function(String?) onFilterSampaiChanged;
  final Function(int?) onFilterMinTotalChanged;
  final Function(int?) onFilterMaxTotalChanged;
  final Function(String?) onFilterProdukChanged;
  final Function(bool) onFilterMultiItemChanged;
  final Function() onApplyFilters;
  final Function() onResetFilters;

  const DaftarPesananScreenContent({
    super.key,
    required this.pesananList,
    required this.pelangganList,
    required this.userRole,
    required this.isLoading,
    required this.onDelete,
    required this.onStatusChanged,
    this.filterCari,
    this.filterStatus,
    this.filterDari,
    this.filterSampai,
    this.filterMinTotal,
    this.filterMaxTotal,
    this.filterProduk,
    this.filterMultiItem = false,
    required this.onFilterCariChanged,
    required this.onFilterStatusChanged,
    required this.onFilterDariChanged,
    required this.onFilterSampaiChanged,
    required this.onFilterMinTotalChanged,
    required this.onFilterMaxTotalChanged,
    required this.onFilterProdukChanged,
    required this.onFilterMultiItemChanged,
    required this.onApplyFilters,
    required this.onResetFilters,
  });

  @override
  State<DaftarPesananScreenContent> createState() =>
      _DaftarPesananScreenContentState();
}

class _DaftarPesananScreenContentState
    extends State<DaftarPesananScreenContent> {
  String selectedStatusFilter = 'Semua';

  Widget _buildFilterForm(bool isMobile) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      padding: EdgeInsets.all(isMobile ? 12 : 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              const Icon(Icons.search, color: Color(0xFF2563EB), size: 18),
              const SizedBox(width: 8),
              const Text(
                'Filter & Pencarian',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF0F172A),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          // Filter inputs - responsive layout
          if (isMobile)
            // Single column for mobile
            Column(
              children: [
                // Search keyword
                TextField(
                  onChanged: widget.onFilterCariChanged,
                  controller: TextEditingController(text: widget.filterCari),
                  decoration: InputDecoration(
                    hintText: 'Cari PO, Pelanggan, Produk...',
                    prefixIcon: const Icon(Icons.search, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                ),
                const SizedBox(height: 12),
                // Status filter
                DropdownButtonFormField<String>(
                  value: widget.filterStatus,
                  onChanged: widget.onFilterStatusChanged,
                  isExpanded: true,
                  decoration: InputDecoration(
                    hintText: 'Status',
                    prefixIcon: const Icon(Icons.flag, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  items: const [
                    DropdownMenuItem(value: null, child: Text('Semua')),
                    DropdownMenuItem(
                      value: 'menunggu_konfirmasi',
                      child: Text('Menunggu'),
                    ),
                    DropdownMenuItem(
                      value: 'dikonfirmasi',
                      child: Text('Dikonfirmasi'),
                    ),
                    DropdownMenuItem(
                      value: 'dalam_produksi',
                      child: Text('Produksi'),
                    ),
                    DropdownMenuItem(
                      value: 'siap_kirim',
                      child: Text('Siap Kirim'),
                    ),
                    DropdownMenuItem(value: 'selesai', child: Text('Selesai')),
                    DropdownMenuItem(value: 'dibatalkan', child: Text('Batal')),
                  ],
                ),
                const SizedBox(height: 12),
                // Product name
                TextField(
                  onChanged: widget.onFilterProdukChanged,
                  controller: TextEditingController(text: widget.filterProduk),
                  decoration: InputDecoration(
                    hintText: 'Nama Produk',
                    prefixIcon: const Icon(Icons.shopping_bag, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                ),
                const SizedBox(height: 12),
                // Date from
                TextField(
                  onChanged: widget.onFilterDariChanged,
                  controller: TextEditingController(text: widget.filterDari),
                  decoration: InputDecoration(
                    hintText: 'Dari',
                    prefixIcon: const Icon(Icons.calendar_today, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  keyboardType: TextInputType.datetime,
                ),
                const SizedBox(height: 12),
                // Date until
                TextField(
                  onChanged: widget.onFilterSampaiChanged,
                  controller: TextEditingController(text: widget.filterSampai),
                  decoration: InputDecoration(
                    hintText: 'Sampai',
                    prefixIcon: const Icon(Icons.calendar_today, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  keyboardType: TextInputType.datetime,
                ),
                const SizedBox(height: 12),
                // Min Total
                TextField(
                  onChanged: (value) {
                    widget.onFilterMinTotalChanged(int.tryParse(value));
                  },
                  controller: TextEditingController(
                    text: widget.filterMinTotal?.toString(),
                  ),
                  decoration: InputDecoration(
                    hintText: 'Min (Rp)',
                    prefixIcon: const Icon(Icons.money, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  keyboardType: TextInputType.number,
                ),
                const SizedBox(height: 12),
                // Max Total
                TextField(
                  onChanged: (value) {
                    widget.onFilterMaxTotalChanged(int.tryParse(value));
                  },
                  controller: TextEditingController(
                    text: widget.filterMaxTotal?.toString(),
                  ),
                  decoration: InputDecoration(
                    hintText: 'Max (Rp)',
                    prefixIcon: const Icon(Icons.money, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  keyboardType: TextInputType.number,
                ),
              ],
            )
          else
            // Grid layout for desktop
            GridView.count(
              crossAxisCount: 3,
              childAspectRatio: 1 / 0.9,
              mainAxisSpacing: 12,
              crossAxisSpacing: 12,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              children: [
                // Search keyword
                TextField(
                  onChanged: widget.onFilterCariChanged,
                  controller: TextEditingController(text: widget.filterCari),
                  decoration: InputDecoration(
                    hintText: 'Cari PO, Pelanggan, Produk...',
                    prefixIcon: const Icon(Icons.search, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                ),
                // Status filter
                DropdownButtonFormField<String>(
                  value: widget.filterStatus,
                  onChanged: widget.onFilterStatusChanged,
                  isExpanded: true,
                  decoration: InputDecoration(
                    hintText: 'Status',
                    prefixIcon: const Icon(Icons.flag, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  items: const [
                    DropdownMenuItem(value: null, child: Text('Semua')),
                    DropdownMenuItem(
                      value: 'menunggu_konfirmasi',
                      child: Text('Menunggu'),
                    ),
                    DropdownMenuItem(
                      value: 'dikonfirmasi',
                      child: Text('Dikonfirmasi'),
                    ),
                    DropdownMenuItem(
                      value: 'dalam_produksi',
                      child: Text('Produksi'),
                    ),
                    DropdownMenuItem(
                      value: 'siap_kirim',
                      child: Text('Siap Kirim'),
                    ),
                    DropdownMenuItem(value: 'selesai', child: Text('Selesai')),
                    DropdownMenuItem(value: 'dibatalkan', child: Text('Batal')),
                  ],
                ),
                // Product name
                TextField(
                  onChanged: widget.onFilterProdukChanged,
                  controller: TextEditingController(text: widget.filterProduk),
                  decoration: InputDecoration(
                    hintText: 'Nama Produk',
                    prefixIcon: const Icon(Icons.shopping_bag, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                ),
                // Date from
                TextField(
                  onChanged: widget.onFilterDariChanged,
                  controller: TextEditingController(text: widget.filterDari),
                  decoration: InputDecoration(
                    hintText: 'Dari',
                    prefixIcon: const Icon(Icons.calendar_today, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  keyboardType: TextInputType.datetime,
                ),
                // Date until
                TextField(
                  onChanged: widget.onFilterSampaiChanged,
                  controller: TextEditingController(text: widget.filterSampai),
                  decoration: InputDecoration(
                    hintText: 'Sampai',
                    prefixIcon: const Icon(Icons.calendar_today, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  keyboardType: TextInputType.datetime,
                ),
                // Min Total
                TextField(
                  onChanged: (value) {
                    widget.onFilterMinTotalChanged(int.tryParse(value));
                  },
                  controller: TextEditingController(
                    text: widget.filterMinTotal?.toString(),
                  ),
                  decoration: InputDecoration(
                    hintText: 'Min (Rp)',
                    prefixIcon: const Icon(Icons.money, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  keyboardType: TextInputType.number,
                ),
                // Max Total
                TextField(
                  onChanged: (value) {
                    widget.onFilterMaxTotalChanged(int.tryParse(value));
                  },
                  controller: TextEditingController(
                    text: widget.filterMaxTotal?.toString(),
                  ),
                  decoration: InputDecoration(
                    hintText: 'Max (Rp)',
                    prefixIcon: const Icon(Icons.money, size: 18),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 10,
                    ),
                    isDense: true,
                  ),
                  keyboardType: TextInputType.number,
                ),
              ],
            ),
          const SizedBox(height: 12),
          // Multi-item checkbox
          Row(
            children: [
              Checkbox(
                value: widget.filterMultiItem,
                onChanged: (value) =>
                    widget.onFilterMultiItemChanged(value ?? false),
              ),
              const Text('Hanya Multi-Item', style: TextStyle(fontSize: 12)),
            ],
          ),
          const SizedBox(height: 12),
          // Action buttons
          Row(
            mainAxisAlignment: MainAxisAlignment.end,
            children: [
              ElevatedButton.icon(
                onPressed: widget.onResetFilters,
                icon: const Icon(Icons.refresh, size: 16),
                label: const Text('Reset'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFFF1F5F9),
                  foregroundColor: const Color(0xFF64748B),
                  side: const BorderSide(color: Color(0xFFE2E8F0)),
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 8,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(6),
                  ),
                ),
              ),
              const SizedBox(width: 8),
              ElevatedButton.icon(
                onPressed: widget.onApplyFilters,
                icon: const Icon(Icons.search, size: 16),
                label: const Text('Terapkan'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2563EB),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 8,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(6),
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildFilterChip(String label, int count, Color statusColor) {
    final bool isSelected = selectedStatusFilter == label;
    return Container(
      margin: const EdgeInsets.only(right: 8),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () {
            setState(() {
              selectedStatusFilter = label;
            });
          },
          borderRadius: BorderRadius.circular(20),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 150),
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
            decoration: BoxDecoration(
              color: isSelected
                  ? statusColor.withValues(alpha: 0.08)
                  : Colors.white,
              borderRadius: BorderRadius.circular(20),
              border: Border.all(
                color: isSelected
                    ? statusColor.withValues(alpha: 0.3)
                    : const Color(0xFFE2E8F0),
                width: isSelected ? 1.5 : 1,
              ),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  label,
                  style: TextStyle(
                    color: isSelected ? statusColor : const Color(0xFF64748B),
                    fontSize: 12,
                    fontWeight: isSelected ? FontWeight.bold : FontWeight.w500,
                  ),
                ),
                const SizedBox(width: 6),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 6,
                    vertical: 2,
                  ),
                  decoration: BoxDecoration(
                    color: isSelected ? statusColor : const Color(0xFFF1F5F9),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Text(
                    '$count',
                    style: TextStyle(
                      color: isSelected
                          ? Colors.white
                          : const Color(0xFF475569),
                      fontSize: 10,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final bool isMobile = Responsive.isMobile(context);

    // Filter logic
    final filteredPesanan = selectedStatusFilter == 'Semua'
        ? widget.pesananList
        : widget.pesananList
              .where((p) => (p['status'] ?? 'Pending') == selectedStatusFilter)
              .toList();

    // Stats for dynamic filters
    final totalAll = widget.pesananList.length;
    final totalDraft = widget.pesananList
        .where((p) => p['status'] == 'Draft')
        .length;
    final totalPending = widget.pesananList
        .where((p) => p['status'] == 'Pending')
        .length;
    final totalProses = widget.pesananList
        .where((p) => p['status'] == 'Proses')
        .length;
    final totalDalamProduksi = widget.pesananList
        .where((p) => p['status'] == 'Dalam Produksi')
        .length;
    final totalSelesai = widget.pesananList
        .where((p) => p['status'] == 'Selesai')
        .length;
    final totalDibatalkan = widget.pesananList
        .where((p) => p['status'] == 'Dibatalkan')
        .length;

    return LayoutBuilder(
      builder: (context, constraints) {
        final double width = constraints.maxWidth;
        int crossAxisCount = 3;
        double childAspectRatio = 1.35;

        if (width < 600) {
          crossAxisCount = 1;
          childAspectRatio = 1.6;
        } else if (width < 950) {
          crossAxisCount = 2;
          childAspectRatio = 1.25;
        }

        return Container(
          color: const Color(0xFFF8FAFC),
          padding: EdgeInsets.all(isMobile ? 16 : 28),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header title
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Daftar Pesanan',
                    style: TextStyle(
                      fontSize: 26,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                  SizedBox(height: 4),
                  Text(
                    'Kelola dan pantau status Purchase Order hutch.id secara real-time.',
                    style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                  ),
                ],
              ),
              const SizedBox(height: 20),

              // Advanced Filter Form
              _buildFilterForm(isMobile),
              const SizedBox(height: 20),

              // Horizontal scrolling modern filter chips
              SingleChildScrollView(
                scrollDirection: Axis.horizontal,
                physics: const BouncingScrollPhysics(),
                child: Row(
                  children: [
                    _buildFilterChip(
                      'Semua',
                      totalAll,
                      const Color(0xFF3B82F6),
                    ),
                    _buildFilterChip(
                      'Draft',
                      totalDraft,
                      const Color(0xFF64748B),
                    ),
                    _buildFilterChip(
                      'Pending',
                      totalPending,
                      const Color(0xFFF59E0B),
                    ),
                    _buildFilterChip(
                      'Proses',
                      totalProses,
                      const Color(0xFF3B82F6),
                    ),
                    _buildFilterChip(
                      'Dalam Produksi',
                      totalDalamProduksi,
                      const Color(0xFF8B5CF6),
                    ),
                    _buildFilterChip(
                      'Selesai',
                      totalSelesai,
                      const Color(0xFF10B981),
                    ),
                    _buildFilterChip(
                      'Dibatalkan',
                      totalDibatalkan,
                      const Color(0xFFEF4444),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              Expanded(
                child: widget.isLoading
                    ? GridView.builder(
                        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: crossAxisCount,
                          crossAxisSpacing: 16,
                          mainAxisSpacing: 16,
                          childAspectRatio: childAspectRatio,
                        ),
                        itemCount: 6,
                        itemBuilder: (context, index) => Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(16),
                            color: Colors.white,
                            border: Border.all(color: const Color(0xFFE2E8F0)),
                          ),
                          child: const Row(
                            children: [
                              ShimmerLoading(
                                width: 44,
                                height: 44,
                                borderRadius: 10,
                              ),
                              SizedBox(width: 16),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    ShimmerLoading(width: 120, height: 14),
                                    SizedBox(height: 8),
                                    ShimmerLoading(width: 180, height: 10),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      )
                    : filteredPesanan.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(20),
                              decoration: const BoxDecoration(
                                color: Color(0xFFF1F5F9),
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.receipt_long_outlined,
                                color: Color(0xFF94A3B8),
                                size: 48,
                              ),
                            ),
                            const SizedBox(height: 16),
                            Text(
                              selectedStatusFilter == 'Semua'
                                  ? 'Belum ada pesanan masuk'
                                  : 'Tidak ada pesanan dengan status $selectedStatusFilter',
                              style: const TextStyle(
                                color: Color(0xFF475569),
                                fontSize: 13,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ],
                        ),
                      )
                    : GridView.builder(
                        physics: const BouncingScrollPhysics(),
                        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: crossAxisCount,
                          crossAxisSpacing: 16,
                          mainAxisSpacing: 16,
                          childAspectRatio: childAspectRatio,
                        ),
                        itemCount: filteredPesanan.length,
                        itemBuilder: (context, index) {
                          final pesanan = filteredPesanan[index];

                          // Map status to color and icon dynamically
                          Color statusColor = const Color(0xFFF59E0B);
                          IconData statusIcon = Icons.hourglass_bottom_rounded;
                          if (pesanan['status'] == 'Proses') {
                            statusColor = const Color(0xFF3B82F6);
                            statusIcon = Icons.autorenew_rounded;
                          } else if (pesanan['status'] == 'Selesai') {
                            statusColor = const Color(0xFF10B981);
                            statusIcon = Icons.check_circle_outlined;
                          } else if (pesanan['status'] == 'Dalam Produksi') {
                            statusColor = const Color(0xFF8B5CF6);
                            statusIcon = Icons.precision_manufacturing_outlined;
                          } else if (pesanan['status'] == 'Draft') {
                            statusColor = const Color(0xFF64748B);
                            statusIcon = Icons.drafts_outlined;
                          } else if (pesanan['status'] == 'Dibatalkan') {
                            statusColor = const Color(0xFFEF4444);
                            statusIcon = Icons.cancel_outlined;
                          }

                          return Container(
                            decoration: BoxDecoration(
                              borderRadius: BorderRadius.circular(16),
                              color: Colors.white,
                              border: Border.all(
                                color: const Color(0xFFE2E8F0),
                                width: 1,
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withValues(alpha: 0.01),
                                  blurRadius: 16,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: ClipRRect(
                              borderRadius: BorderRadius.circular(16),
                              child: Stack(
                                children: [
                                  // Left Status Accent Strip
                                  Positioned(
                                    left: 0,
                                    top: 0,
                                    bottom: 0,
                                    child: Container(
                                      width: 4,
                                      color: statusColor,
                                    ),
                                  ),
                                  Padding(
                                    padding: const EdgeInsets.all(20),
                                    child: Column(
                                      crossAxisAlignment:
                                          CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          children: [
                                            Container(
                                              padding: const EdgeInsets.all(8),
                                              decoration: BoxDecoration(
                                                color: statusColor.withValues(
                                                  alpha: 0.08,
                                                ),
                                                borderRadius:
                                                    BorderRadius.circular(10),
                                                border: Border.all(
                                                  color: statusColor.withValues(
                                                    alpha: 0.15,
                                                  ),
                                                ),
                                              ),
                                              child: Icon(
                                                statusIcon,
                                                color: statusColor,
                                                size: 18,
                                              ),
                                            ),
                                            const SizedBox(width: 12),
                                            Expanded(
                                              child: Column(
                                                crossAxisAlignment:
                                                    CrossAxisAlignment.start,
                                                children: [
                                                  Text(
                                                    pesanan['pelanggan'] ??
                                                        'Umum',
                                                    style: const TextStyle(
                                                      fontSize: 14,
                                                      fontWeight:
                                                          FontWeight.bold,
                                                      color: Color(0xFF0F172A),
                                                    ),
                                                    overflow:
                                                        TextOverflow.ellipsis,
                                                  ),
                                                  const SizedBox(height: 2),
                                                  Text(
                                                    '${pesanan['no'] ?? ''} • ${pesanan['tanggal'] ?? ''}',
                                                    style: const TextStyle(
                                                      fontSize: 11,
                                                      color: Color(0xFF64748B),
                                                      fontWeight:
                                                          FontWeight.w500,
                                                    ),
                                                    overflow:
                                                        TextOverflow.ellipsis,
                                                  ),
                                                ],
                                              ),
                                            ),
                                          ],
                                        ),
                                        const Spacer(),
                                        Row(
                                          mainAxisAlignment:
                                              MainAxisAlignment.spaceBetween,
                                          children: [
                                            Column(
                                              crossAxisAlignment:
                                                  CrossAxisAlignment.start,
                                              children: [
                                                const Text(
                                                  'TOTAL PO',
                                                  style: TextStyle(
                                                    fontSize: 9,
                                                    fontWeight: FontWeight.bold,
                                                    color: Color(0xFF94A3B8),
                                                    letterSpacing: 0.5,
                                                  ),
                                                ),
                                                const SizedBox(height: 2),
                                                Text(
                                                  pesanan['total'] ?? 'Rp 0',
                                                  style: const TextStyle(
                                                    fontSize: 15,
                                                    fontWeight: FontWeight.bold,
                                                    color: Color(0xFF3B82F6),
                                                  ),
                                                ),
                                              ],
                                            ),
                                            Container(
                                              padding:
                                                  const EdgeInsets.symmetric(
                                                    horizontal: 10,
                                                    vertical: 4,
                                                  ),
                                              decoration: BoxDecoration(
                                                color: statusColor.withValues(
                                                  alpha: 0.08,
                                                ),
                                                borderRadius:
                                                    BorderRadius.circular(12),
                                                border: Border.all(
                                                  color: statusColor.withValues(
                                                    alpha: 0.15,
                                                  ),
                                                ),
                                              ),
                                              child: Text(
                                                pesanan['status'] ?? 'Pending',
                                                style: TextStyle(
                                                  fontSize: 10,
                                                  fontWeight: FontWeight.bold,
                                                  color: statusColor,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 12),
                                        const Divider(
                                          height: 1,
                                          color: Color(0xFFF1F5F9),
                                        ),
                                        const SizedBox(height: 12),
                                        Row(
                                          children: [
                                            Expanded(
                                              child: SizedBox(
                                                height: 32,
                                                child: OutlinedButton(
                                                  onPressed: () {
                                                    _showDetailDialog(
                                                      context,
                                                      index,
                                                      pesanan,
                                                    );
                                                  },
                                                  style: OutlinedButton.styleFrom(
                                                    foregroundColor:
                                                        const Color(0xFF334155),
                                                    side: const BorderSide(
                                                      color: Color(0xFFCBD5E1),
                                                      width: 1,
                                                    ),
                                                    shape: RoundedRectangleBorder(
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                            8,
                                                          ),
                                                    ),
                                                    padding: EdgeInsets.zero,
                                                  ),
                                                  child: const Text(
                                                    'Detail',
                                                    style: TextStyle(
                                                      fontSize: 11,
                                                      fontWeight:
                                                          FontWeight.bold,
                                                    ),
                                                  ),
                                                ),
                                              ),
                                            ),
                                            const SizedBox(width: 8),
                                            Expanded(
                                              child: SizedBox(
                                                height: 32,
                                                child: ElevatedButton(
                                                  onPressed: () {
                                                    Navigator.push(
                                                      context,
                                                      MaterialPageRoute(
                                                        builder: (context) =>
                                                            LihatCetakPoScreen(
                                                              pesanan: pesanan,
                                                              pelangganList: widget
                                                                  .pelangganList,
                                                            ),
                                                      ),
                                                    );
                                                  },
                                                  style: ElevatedButton.styleFrom(
                                                    backgroundColor:
                                                        const Color(0xFF3B82F6),
                                                    foregroundColor:
                                                        Colors.white,
                                                    elevation: 0,
                                                    shape: RoundedRectangleBorder(
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                            8,
                                                          ),
                                                    ),
                                                    padding: EdgeInsets.zero,
                                                  ),
                                                  child: const Text(
                                                    'Lihat PO',
                                                    style: TextStyle(
                                                      fontSize: 11,
                                                      fontWeight:
                                                          FontWeight.bold,
                                                    ),
                                                  ),
                                                ),
                                              ),
                                            ),
                                            if (widget.userRole ==
                                                    'Administrator' ||
                                                widget.userRole ==
                                                    'Staf Penjualan') ...[
                                              const SizedBox(width: 8),
                                              SizedBox(
                                                width: 32,
                                                height: 32,
                                                child: IconButton(
                                                  onPressed: () {
                                                    _showDeleteDialog(
                                                      context,
                                                      pesanan,
                                                    );
                                                  },
                                                  icon: const Icon(
                                                    Icons
                                                        .delete_outline_rounded,
                                                    size: 16,
                                                    color: Color(0xFFEF4444),
                                                  ),
                                                  style: IconButton.styleFrom(
                                                    backgroundColor:
                                                        const Color(
                                                          0xFFEF4444,
                                                        ).withValues(
                                                          alpha: 0.08,
                                                        ),
                                                    padding: EdgeInsets.zero,
                                                    shape: RoundedRectangleBorder(
                                                      borderRadius:
                                                          BorderRadius.circular(
                                                            8,
                                                          ),
                                                    ),
                                                  ),
                                                ),
                                              ),
                                            ],
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
              ),
            ],
          ),
        );
      },
    );
  }

  void _showDeleteDialog(BuildContext context, Map<String, dynamic> pesanan) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          title: const Row(
            children: [
              Icon(Icons.warning_amber_rounded, color: Color(0xFFEF4444)),
              SizedBox(width: 8),
              Text(
                'Hapus Pesanan',
                style: TextStyle(
                  color: Color(0xFF0F172A),
                  fontWeight: FontWeight.bold,
                  fontSize: 16,
                ),
              ),
            ],
          ),
          content: Text(
            'Apakah Anda yakin ingin menghapus pesanan ${pesanan['no']}? Tindakan ini tidak dapat dibatalkan.',
            style: const TextStyle(fontSize: 13, color: Color(0xFF475569)),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text(
                'Batal',
                style: TextStyle(
                  color: Color(0xFF64748B),
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
            ElevatedButton(
              onPressed: () async {
                Navigator.pop(context);
                await widget.onDelete(pesanan['id'].toString());
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFFEF4444),
                foregroundColor: Colors.white,
                elevation: 0,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
              child: const Text('Hapus'),
            ),
          ],
        );
      },
    );
  }

  void _showDetailDialog(
    BuildContext context,
    int index,
    Map<String, dynamic> pesanan,
  ) {
    String currentStatus = pesanan['status'] ?? 'menunggu_konfirmasi';
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return StatefulBuilder(
          builder: (context, setDialogState) {
            final double hargaUnit = (pesanan['harga'] ?? 100000).toDouble();
            final int qty = pesanan['jumlah'] ?? 10;
            final String deskripsi = pesanan['deskripsi'] ?? 'Produk Custom';

            return Dialog(
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(20),
              ),
              child: Container(
                padding: const EdgeInsets.all(24),
                constraints: const BoxConstraints(maxWidth: 450),
                child: SingleChildScrollView(
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(
                              color: const Color(
                                0xFF3B82F6,
                              ).withValues(alpha: 0.08),
                              shape: BoxShape.circle,
                              border: Border.all(
                                color: const Color(
                                  0xFF3B82F6,
                                ).withValues(alpha: 0.15),
                              ),
                            ),
                            child: const Icon(
                              Icons.receipt_long_outlined,
                              color: Color(0xFF2563EB),
                              size: 20,
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'DETAIL PURCHASE ORDER',
                                  style: TextStyle(
                                    color: Color(0xFF64748B),
                                    fontSize: 9,
                                    fontWeight: FontWeight.bold,
                                    letterSpacing: 1.5,
                                  ),
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  pesanan['no'] ?? 'PO Detail',
                                  style: const TextStyle(
                                    color: Color(0xFF0F172A),
                                    fontWeight: FontWeight.bold,
                                    fontSize: 16,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 20),
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: const Color(0xFFF8FAFC),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: const Color(0xFFE2E8F0)),
                        ),
                        child: Column(
                          children: [
                            _buildDetailRow(
                              'Pelanggan',
                              pesanan['pelanggan'] ?? 'Umum',
                            ),
                            const Divider(height: 16, color: Color(0xFFE2E8F0)),
                            _buildDetailRow(
                              'Tanggal',
                              pesanan['tanggal'] ?? '',
                            ),
                            const Divider(height: 16, color: Color(0xFFE2E8F0)),
                            _buildDetailRow('Deskripsi', deskripsi),
                            const Divider(height: 16, color: Color(0xFFE2E8F0)),
                            _buildDetailRow('Jumlah', '$qty Pcs'),
                            const Divider(height: 16, color: Color(0xFFE2E8F0)),
                            _buildDetailRow(
                              'Harga Satuan',
                              'Rp ${hargaUnit.toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (Match m) => '${m[1]}.')}/Pcs',
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 12,
                        ),
                        decoration: BoxDecoration(
                          color: const Color(
                            0xFF3B82F6,
                          ).withValues(alpha: 0.05),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(
                            color: const Color(
                              0xFF3B82F6,
                            ).withValues(alpha: 0.15),
                          ),
                        ),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            const Text(
                              'Total Pembayaran',
                              style: TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 12,
                                color: Color(0xFF1E293B),
                              ),
                            ),
                            Text(
                              pesanan['total'] ?? 'Rp 0',
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 15,
                                color: Color(0xFF2563EB),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 20),
                      const Text(
                        'Perbarui Status Pesanan',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 12,
                          color: Color(0xFF0F172A),
                        ),
                      ),
                      const SizedBox(height: 8),
                      DropdownButtonFormField<String>(
                        initialValue: currentStatus,
                        style: const TextStyle(
                          fontSize: 13,
                          color: Color(0xFF0F172A),
                        ),
                        decoration: InputDecoration(
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                            borderSide: const BorderSide(
                              color: Color(0xFFE2E8F0),
                            ),
                          ),
                          enabledBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                            borderSide: const BorderSide(
                              color: Color(0xFFE2E8F0),
                            ),
                          ),
                          focusedBorder: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(10),
                            borderSide: const BorderSide(
                              color: Color(0xFF3B82F6),
                              width: 1.5,
                            ),
                          ),
                          contentPadding: const EdgeInsets.symmetric(
                            horizontal: 12,
                            vertical: 10,
                          ),
                          filled: true,
                          fillColor: Colors.white,
                        ),
                        items:
                            [
                              'Draft',
                              'Pending',
                              'Proses',
                              'Dalam Produksi',
                              'Selesai',
                              'Dibatalkan',
                            ].map((status) {
                              const icons = {
                                'Draft': Icons.drafts_outlined,
                                'Pending': Icons.hourglass_top_outlined,
                                'Proses': Icons.autorenew_rounded,
                                'Dalam Produksi':
                                    Icons.precision_manufacturing_outlined,
                                'Selesai': Icons.check_circle_outlined,
                                'Dibatalkan': Icons.cancel_outlined,
                              };
                              const colors = {
                                'Draft': Color(0xFF64748B),
                                'Pending': Color(0xFFF59E0B),
                                'Proses': Color(0xFF3B82F6),
                                'Dalam Produksi': Color(0xFF8B5CF6),
                                'Selesai': Color(0xFF10B981),
                                'Dibatalkan': Color(0xFFEF4444),
                              };
                              return DropdownMenuItem<String>(
                                value: status,
                                child: Row(
                                  children: [
                                    Icon(
                                      icons[status] ?? Icons.circle,
                                      size: 14,
                                      color: colors[status] ?? Colors.grey,
                                    ),
                                    const SizedBox(width: 8),
                                    Text(
                                      status,
                                      style: const TextStyle(
                                        fontSize: 13,
                                        fontWeight: FontWeight.w500,
                                      ),
                                    ),
                                  ],
                                ),
                              );
                            }).toList(),
                        onChanged: (value) {
                          if (value != null)
                            setDialogState(() => currentStatus = value);
                        },
                      ),
                      const SizedBox(height: 20),
                      if ((pesanan['audit_trail'] as List? ?? [])
                          .isNotEmpty) ...[
                        const Text(
                          'Riwayat Aktivitas',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 12,
                            color: Color(0xFF0F172A),
                          ),
                        ),
                        const SizedBox(height: 8),
                        Container(
                          constraints: const BoxConstraints(maxHeight: 140),
                          child: ListView.builder(
                            shrinkWrap: true,
                            reverse: true,
                            itemCount: (pesanan['audit_trail'] as List).length,
                            itemBuilder: (ctx, i) {
                              final trail = Map<String, dynamic>.from(
                                (pesanan['audit_trail'] as List)[i],
                              );
                              final waktu = trail['waktu'] != null
                                  ? DateTime.tryParse(trail['waktu'].toString())
                                  : null;
                              final waktuStr = waktu != null
                                  ? '${waktu.day.toString().padLeft(2, '0')}/${waktu.month.toString().padLeft(2, '0')} ${waktu.hour.toString().padLeft(2, '0')}:${waktu.minute.toString().padLeft(2, '0')}'
                                  : '-';
                              return Padding(
                                padding: const EdgeInsets.only(bottom: 6),
                                child: Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Container(
                                      width: 6,
                                      height: 6,
                                      margin: const EdgeInsets.only(
                                        top: 4,
                                        right: 8,
                                      ),
                                      decoration: const BoxDecoration(
                                        color: Color(0xFF3B82F6),
                                        shape: BoxShape.circle,
                                      ),
                                    ),
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            '${trail['status'] ?? ''}  •  $waktuStr',
                                            style: const TextStyle(
                                              fontSize: 11,
                                              fontWeight: FontWeight.bold,
                                              color: Color(0xFF334155),
                                            ),
                                          ),
                                          if ((trail['catatan'] ?? '')
                                              .toString()
                                              .isNotEmpty)
                                            Text(
                                              trail['catatan'].toString(),
                                              style: const TextStyle(
                                                fontSize: 10,
                                                color: Color(0xFF64748B),
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
                        ),
                      ],
                      const SizedBox(height: 24),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.end,
                        children: [
                          TextButton(
                            onPressed: () => Navigator.pop(context),
                            child: const Text(
                              'Batal',
                              style: TextStyle(
                                color: Color(0xFF64748B),
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                          const SizedBox(width: 12),
                          ElevatedButton(
                            onPressed: () async {
                              await widget.onStatusChanged(
                                pesanan['id'].toString(),
                                currentStatus,
                              );
                              if (context.mounted) {
                                Navigator.pop(context);
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text(
                                      'Status pesanan ${pesanan['no']} diubah menjadi $currentStatus',
                                    ),
                                    behavior: SnackBarBehavior.floating,
                                    backgroundColor: const Color(0xFF10B981),
                                  ),
                                );
                              }
                            },
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF3B82F6),
                              foregroundColor: Colors.white,
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                              padding: const EdgeInsets.symmetric(
                                horizontal: 16,
                                vertical: 12,
                              ),
                            ),
                            child: const Text('Simpan Perubahan'),
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
      },
    );
  }

  Widget _buildDetailRow(String label, String value) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 90,
          child: Text(
            label,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              color: Color(0xFF64748B),
              fontSize: 11,
            ),
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              fontWeight: FontWeight.w600,
              color: Color(0xFF0F172A),
              fontSize: 11,
            ),
            textAlign: TextAlign.right,
          ),
        ),
      ],
    );
  }
}

// =============================================================================
// Buat PO — Multi-item · Auto PO Number · Cek Bahan Baku · Harga Dikunci (Premium)
// =============================================================================
class BuatPoScreenContent extends StatefulWidget {
  final List<Pelanggan> pelangganList;
  final Function(String, List<Map<String, dynamic>>, String) onSave;

  const BuatPoScreenContent({
    super.key,
    required this.pelangganList,
    required this.onSave,
  });

  @override
  State<BuatPoScreenContent> createState() => _BuatPoScreenContentState();
}

class _BuatPoScreenContentState extends State<BuatPoScreenContent> {
  final _formKey = GlobalKey<FormState>();
  String? selectedPelanggan;
  String? selectedStatus;
  bool _hargaDikunci = false;

  // Multi-item state
  final List<TextEditingController> _deskripsiCtrls = [TextEditingController()];
  final List<TextEditingController> _jumlahCtrls = [TextEditingController()];
  final List<TextEditingController> _hargaCtrls = [TextEditingController()];
  int get _itemCount => _deskripsiCtrls.length;

  @override
  void dispose() {
    for (var c in _deskripsiCtrls) {
      c.dispose();
    }
    for (var c in _jumlahCtrls) {
      c.dispose();
    }
    for (var c in _hargaCtrls) {
      c.dispose();
    }
    super.dispose();
  }

  void _addItem() {
    setState(() {
      _deskripsiCtrls.add(TextEditingController());
      _jumlahCtrls.add(TextEditingController());
      _hargaCtrls.add(TextEditingController());
    });
  }

  void _removeItem(int index) {
    if (_itemCount == 1) return;
    setState(() {
      _deskripsiCtrls[index].dispose();
      _jumlahCtrls[index].dispose();
      _hargaCtrls[index].dispose();
      _deskripsiCtrls.removeAt(index);
      _jumlahCtrls.removeAt(index);
      _hargaCtrls.removeAt(index);
    });
  }

  int get _totalAmount {
    int total = 0;
    for (int i = 0; i < _itemCount; i++) {
      total +=
          (int.tryParse(_jumlahCtrls[i].text) ?? 0) *
          (int.tryParse(_hargaCtrls[i].text) ?? 0);
    }
    return total;
  }

  int get _totalQty =>
      _jumlahCtrls.fold(0, (s, c) => s + (int.tryParse(c.text) ?? 0));

  String _fmt(int amount) =>
      'Rp ${amount.toString().replaceAllMapped(RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]}.')}';

  String _getPoPreview() {
    final now = DateTime.now();
    final d =
        '${now.year}${now.month.toString().padLeft(2, '0')}${now.day.toString().padLeft(2, '0')}';
    return 'PO-$d-XXX';
  }

  // ── Simulated bahan baku inventory ──────────────────────────────────────────
  static const List<Map<String, dynamic>> _bahanBaku = [
    {'nama': 'Kain Kanvas', 'satuan': 'Meter', 'stok': 500, 'per_item': 1.5},
    {'nama': 'Benang Jahit', 'satuan': 'Gulungan', 'stok': 80, 'per_item': 0.5},
    {'nama': 'Resleting YKK', 'satuan': 'Pcs', 'stok': 300, 'per_item': 2.0},
    {'nama': 'Tali Webbing', 'satuan': 'Meter', 'stok': 600, 'per_item': 0.8},
    {'nama': 'Gesper Plastik', 'satuan': 'Pcs', 'stok': 400, 'per_item': 1.0},
    {'nama': 'Label Merek', 'satuan': 'Lembar', 'stok': 1000, 'per_item': 1.0},
  ];

  void _showCekBahanBaku() {
    final qty = _totalQty;
    showDialog(
      context: context,
      builder: (ctx) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        child: Container(
          padding: const EdgeInsets.all(24),
          constraints: const BoxConstraints(maxWidth: 480),
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: const Color(0xFF3B82F6).withValues(alpha: 0.08),
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: const Color(
                            0xFF3B82F6,
                          ).withValues(alpha: 0.15),
                        ),
                      ),
                      child: const Icon(
                        Icons.inventory_2_outlined,
                        color: Color(0xFF2563EB),
                        size: 20,
                      ),
                    ),
                    const SizedBox(width: 12),
                    const Text(
                      'Cek Bahan Baku',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFF3B82F6).withValues(alpha: 0.05),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(
                      color: const Color(0xFF3B82F6).withValues(alpha: 0.15),
                    ),
                  ),
                  child: Row(
                    children: [
                      const Icon(
                        Icons.info_outline_rounded,
                        color: Color(0xFF2563EB),
                        size: 16,
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'Kebutuhan stok produksi terhitung otomatis untuk $qty unit tas.',
                          style: const TextStyle(
                            fontSize: 11,
                            color: Color(0xFF334155),
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                // Header
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 12,
                    vertical: 8,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFF1E293B),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: const Row(
                    children: [
                      Expanded(
                        flex: 3,
                        child: Text(
                          'Bahan Baku',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                      Expanded(
                        flex: 2,
                        child: Text(
                          'Tersedia',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                          textAlign: TextAlign.center,
                        ),
                      ),
                      Expanded(
                        flex: 2,
                        child: Text(
                          'Dibutuhkan',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                          textAlign: TextAlign.center,
                        ),
                      ),
                      Expanded(
                        flex: 2,
                        child: Text(
                          'Status',
                          style: TextStyle(
                            color: Colors.white,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                          ),
                          textAlign: TextAlign.center,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 6),
                ..._bahanBaku.map((b) {
                  final tersedia = b['stok'] as int;
                  final dibutuhkan = ((b['per_item'] as double) * qty).ceil();
                  final selisih = tersedia - dibutuhkan;
                  final ok = selisih >= 0;
                  return Container(
                    margin: const EdgeInsets.only(bottom: 4),
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 8,
                    ),
                    decoration: BoxDecoration(
                      color: ok
                          ? const Color(0xFFF0FDF4)
                          : const Color(0xFFFFF1F2),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: ok
                            ? const Color(0xFF86EFAC)
                            : const Color(0xFFFECACA),
                      ),
                    ),
                    child: Row(
                      children: [
                        Expanded(
                          flex: 3,
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                b['nama'] as String,
                                style: const TextStyle(
                                  fontSize: 11,
                                  fontWeight: FontWeight.bold,
                                  color: Color(0xFF0F172A),
                                ),
                              ),
                              Text(
                                b['satuan'] as String,
                                style: const TextStyle(
                                  fontSize: 9,
                                  color: Color(0xFF64748B),
                                ),
                              ),
                            ],
                          ),
                        ),
                        Expanded(
                          flex: 2,
                          child: Text(
                            '$tersedia',
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF334155),
                            ),
                            textAlign: TextAlign.center,
                          ),
                        ),
                        Expanded(
                          flex: 2,
                          child: Text(
                            '$dibutuhkan',
                            style: const TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF334155),
                            ),
                            textAlign: TextAlign.center,
                          ),
                        ),
                        Expanded(
                          flex: 2,
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                ok
                                    ? Icons.check_circle_outline_rounded
                                    : Icons.warning_amber_rounded,
                                size: 12,
                                color: ok ? Colors.green : Colors.red,
                              ),
                              const SizedBox(width: 2),
                              Text(
                                '${ok ? '+' : ''}$selisih',
                                style: TextStyle(
                                  fontSize: 10,
                                  fontWeight: FontWeight.bold,
                                  color: ok ? Colors.green : Colors.red,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  );
                }),
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: const Color(0xFFF0FDF4),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: const Color(0xFF86EFAC)),
                  ),
                  child: const Row(
                    children: [
                      Icon(
                        Icons.check_circle_rounded,
                        color: Colors.green,
                        size: 16,
                      ),
                      SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          'Semua bahan baku dalam kondisi aman untuk produksi.',
                          style: TextStyle(
                            fontSize: 11,
                            color: Color(0xFF166534),
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 24),
                Align(
                  alignment: Alignment.centerRight,
                  child: ElevatedButton(
                    onPressed: () => Navigator.pop(ctx),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF3B82F6),
                      foregroundColor: Colors.white,
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child: const Text('Tutup'),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  void _simpanPO() {
    if (!_formKey.currentState!.validate()) return;

    final List<Map<String, dynamic>> items = List.generate(_itemCount, (i) {
      final qty = int.parse(_jumlahCtrls[i].text);
      final harga = int.parse(_hargaCtrls[i].text);
      return {
        'deskripsi': _deskripsiCtrls[i].text.trim(),
        'jumlah': qty,
        'harga': harga,
        'subtotal': qty * harga,
      };
    });
    final total = _totalAmount;

    showDialog(
      context: context,
      builder: (ctx) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        child: Container(
          padding: const EdgeInsets.all(24),
          constraints: const BoxConstraints(maxWidth: 450),
          child: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: const Color(0xFF3B82F6).withValues(alpha: 0.08),
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: const Color(
                            0xFF3B82F6,
                          ).withValues(alpha: 0.15),
                        ),
                      ),
                      child: const Icon(
                        Icons.lock_outline_rounded,
                        color: Color(0xFF2563EB),
                        size: 20,
                      ),
                    ),
                    const SizedBox(width: 12),
                    const Text(
                      'Konfirmasi & Kunci Harga',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.all(10),
                  decoration: BoxDecoration(
                    color: const Color(0xFFFFF7ED),
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(color: const Color(0xFFFED7AA)),
                  ),
                  child: const Row(
                    children: [
                      Icon(
                        Icons.info_outline_rounded,
                        color: Colors.orange,
                        size: 14,
                      ),
                      SizedBox(width: 6),
                      Expanded(
                        child: Text(
                          'Harga akan dikunci secara permanen dan tidak dapat diubah.',
                          style: TextStyle(
                            fontSize: 11,
                            color: Colors.orange,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 16),
                _infoRow('Pelanggan', selectedPelanggan ?? ''),
                _infoRow(
                  'Status',
                  statusDisplayMap[selectedStatus ?? 'menunggu_konfirmasi'] ??
                      'Pending',
                ),
                _infoRow('Nomor PO', _getPoPreview()),
                const Divider(height: 16, color: Color(0xFFE2E8F0)),
                const Text(
                  'Rincian Item:',
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 12,
                    color: Color(0xFF0F172A),
                  ),
                ),
                const SizedBox(height: 8),
                ...items.map(
                  (item) => Container(
                    margin: const EdgeInsets.only(bottom: 6),
                    padding: const EdgeInsets.all(10),
                    decoration: BoxDecoration(
                      color: const Color(0xFFF8FAFC),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      children: [
                        Expanded(
                          child: Text(
                            '${item['deskripsi']}  (${item['jumlah']} pcs × ${_fmt(item['harga'])})',
                            style: const TextStyle(
                              fontSize: 11,
                              color: Color(0xFF334155),
                            ),
                          ),
                        ),
                        Text(
                          _fmt(item['subtotal']),
                          style: const TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF2563EB),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const Divider(height: 16, color: Color(0xFFE2E8F0)),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'TOTAL PEMBAYARAN',
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 12,
                        color: Color(0xFF0F172A),
                      ),
                    ),
                    Text(
                      _fmt(total),
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 15,
                        color: Color(0xFF10B981),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    TextButton(
                      onPressed: () => Navigator.pop(ctx),
                      child: const Text(
                        'Batal',
                        style: TextStyle(
                          color: Color(0xFF64748B),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                    const SizedBox(width: 12),
                    ElevatedButton(
                      onPressed: () {
                        Navigator.pop(ctx);
                        widget.onSave(
                          selectedPelanggan!,
                          items,
                          selectedStatus ?? 'Pending',
                        );
                        setState(() {
                          for (var c in _deskripsiCtrls) {
                            c.clear();
                          }
                          for (var c in _jumlahCtrls) {
                            c.clear();
                          }
                          for (var c in _hargaCtrls) {
                            c.clear();
                          }
                          selectedPelanggan = null;
                          selectedStatus = null;
                          _hargaDikunci = true;
                        });
                        Future.delayed(const Duration(seconds: 2), () {
                          if (mounted) setState(() => _hargaDikunci = false);
                        });
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF3B82F6),
                        foregroundColor: Colors.white,
                        elevation: 0,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                        padding: const EdgeInsets.symmetric(
                          horizontal: 16,
                          vertical: 12,
                        ),
                      ),
                      child: const Text('Simpan PO'),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _infoRow(String label, String value) => Padding(
    padding: const EdgeInsets.only(bottom: 6),
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          width: 80,
          child: Text(
            label,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              color: Color(0xFF64748B),
              fontSize: 12,
            ),
          ),
        ),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(
              color: Color(0xFF0F172A),
              fontWeight: FontWeight.bold,
              fontSize: 12,
            ),
          ),
        ),
      ],
    ),
  );

  InputDecoration _inputDeco(String label, IconData icon) => InputDecoration(
    labelText: label,
    labelStyle: const TextStyle(fontSize: 12, color: Color(0xFF64748B)),
    prefixIcon: Icon(icon, color: const Color(0xFF64748B), size: 18),
    border: OutlineInputBorder(
      borderRadius: BorderRadius.circular(10),
      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
    ),
    enabledBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(10),
      borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
    ),
    focusedBorder: OutlineInputBorder(
      borderRadius: BorderRadius.circular(10),
      borderSide: const BorderSide(color: Color(0xFF3B82F6), width: 1.5),
    ),
    filled: true,
    fillColor: Colors.white,
    contentPadding: const EdgeInsets.symmetric(vertical: 10, horizontal: 12),
  );

  @override
  Widget build(BuildContext context) {
    final bool isMobile = Responsive.isMobile(context);
    final int total = _totalAmount;
    final String poPreview = _getPoPreview();

    Color previewStatusColor = const Color(0xFFF59E0B);
    if (selectedStatus == 'Proses')
      previewStatusColor = const Color(0xFF3B82F6);
    if (selectedStatus == 'Selesai')
      previewStatusColor = const Color(0xFF10B981);
    if (selectedStatus == 'Dalam Produksi')
      previewStatusColor = const Color(0xFF8B5CF6);
    if (selectedStatus == 'Draft') previewStatusColor = const Color(0xFF64748B);

    // ── Form widget ──────────────────────────────────────────────────────────
    final formWidget = Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(18),
        color: Colors.white,
        border: Border.all(color: const Color(0xFFE2E8F0), width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.01),
            blurRadius: 16,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Data Pelanggan',
              style: TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.bold,
                color: Color(0xFF0F172A),
              ),
            ),
            const SizedBox(height: 12),
            // Pelanggan dropdown
            DropdownButtonFormField<String>(
              key: ValueKey('pelanggan_$selectedPelanggan'),
              initialValue: selectedPelanggan,
              hint: const Text('Pilih Pelanggan'),
              style: const TextStyle(fontSize: 13, color: Color(0xFF0F172A)),
              decoration: InputDecoration(
                labelText: 'Nama Pelanggan',
                prefixIcon: const Icon(
                  Icons.person_outline,
                  color: Color(0xFF64748B),
                ),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(
                    color: Color(0xFF3B82F6),
                    width: 1.5,
                  ),
                ),
                filled: true,
                fillColor: const Color(0xFFF8FAFC),
              ),
              items: widget.pelangganList
                  .map(
                    (p) => DropdownMenuItem(value: p.nama, child: Text(p.nama)),
                  )
                  .toList(),
              onChanged: (v) => setState(() => selectedPelanggan = v),
              validator: (v) => v == null ? 'Pelanggan harus dipilih' : null,
            ),
            const SizedBox(height: 22),
            Row(
              children: [
                const Expanded(
                  child: Text(
                    'Daftar Rincian Item',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                ),
                TextButton.icon(
                  onPressed: _addItem,
                  icon: const Icon(
                    Icons.add_circle_outline,
                    size: 16,
                    color: Color(0xFF2563EB),
                  ),
                  label: const Text(
                    'Tambah Item',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF2563EB),
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),
            // Item rows
            ...List.generate(
              _itemCount,
              (i) => Container(
                margin: const EdgeInsets.only(bottom: 12),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: const Color(0xFFF8FAFC),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFFE2E8F0)),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 8,
                            vertical: 3,
                          ),
                          decoration: BoxDecoration(
                            color: const Color(
                              0xFF3B82F6,
                            ).withValues(alpha: 0.08),
                            borderRadius: BorderRadius.circular(6),
                          ),
                          child: Text(
                            'Item ${i + 1}',
                            style: const TextStyle(
                              fontSize: 10,
                              fontWeight: FontWeight.bold,
                              color: Color(0xFF2563EB),
                            ),
                          ),
                        ),
                        const Spacer(),
                        if (_itemCount > 1)
                          GestureDetector(
                            onTap: () => _removeItem(i),
                            child: const Icon(
                              Icons.remove_circle_outline_rounded,
                              color: Color(0xFFEF4444),
                              size: 18,
                            ),
                          ),
                      ],
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _deskripsiCtrls[i],
                      onChanged: (_) => setState(() {}),
                      style: const TextStyle(fontSize: 13),
                      decoration: _inputDeco(
                        'Deskripsi Tas / Produk',
                        Icons.shopping_bag_outlined,
                      ),
                      validator: (v) => (v?.trim().isEmpty ?? true)
                          ? 'Tidak boleh kosong'
                          : null,
                    ),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        Expanded(
                          child: TextFormField(
                            controller: _jumlahCtrls[i],
                            onChanged: (_) => setState(() {}),
                            keyboardType: TextInputType.number,
                            style: const TextStyle(fontSize: 13),
                            decoration: _inputDeco(
                              'Jumlah (Pcs)',
                              Icons.numbers_rounded,
                            ),
                            validator: (v) {
                              if (v?.isEmpty ?? true) return 'Wajib';
                              if ((int.tryParse(v!) ?? 0) <= 0) return '> 0';
                              return null;
                            },
                          ),
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: TextFormField(
                            controller: _hargaCtrls[i],
                            onChanged: (_) => setState(() {}),
                            keyboardType: TextInputType.number,
                            style: const TextStyle(fontSize: 13),
                            decoration: _inputDeco(
                              'Harga Satuan (Rp)',
                              Icons.monetization_on_outlined,
                            ),
                            validator: (v) {
                              if (v?.isEmpty ?? true) return 'Wajib';
                              if ((int.tryParse(v!) ?? 0) <= 0) return '> 0';
                              return null;
                            },
                          ),
                        ),
                      ],
                    ),
                    if (_jumlahCtrls[i].text.isNotEmpty &&
                        _hargaCtrls[i].text.isNotEmpty) ...[
                      const SizedBox(height: 10),
                      Align(
                        alignment: Alignment.centerRight,
                        child: Text(
                          'Subtotal: ${_fmt((int.tryParse(_jumlahCtrls[i].text) ?? 0) * (int.tryParse(_hargaCtrls[i].text) ?? 0))}',
                          style: const TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF10B981),
                          ),
                        ),
                      ),
                    ],
                  ],
                ),
              ),
            ),
            const SizedBox(height: 4),
            // Cek Bahan Baku button
            OutlinedButton.icon(
              onPressed: _totalQty > 0 ? _showCekBahanBaku : null,
              icon: const Icon(Icons.inventory_2_outlined, size: 16),
              label: const Text(
                'Cek Ketersediaan Bahan Baku',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 12),
              ),
              style: OutlinedButton.styleFrom(
                foregroundColor: const Color(0xFF3B82F6),
                side: const BorderSide(color: Color(0xFF3B82F6), width: 1),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
                padding: const EdgeInsets.symmetric(
                  vertical: 12,
                  horizontal: 16,
                ),
                minimumSize: const Size(double.infinity, 44),
              ),
            ),
            const SizedBox(height: 16),
            // Status dropdown
            DropdownButtonFormField<String>(
              key: ValueKey('status_$selectedStatus'),
              initialValue: selectedStatus,
              hint: const Text('Pilih Status Awal'),
              style: const TextStyle(fontSize: 13, color: Color(0xFF0F172A)),
              decoration: InputDecoration(
                labelText: 'Status Awal PO',
                prefixIcon: const Icon(
                  Icons.flag_outlined,
                  color: Color(0xFF64748B),
                ),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(
                    color: Color(0xFF3B82F6),
                    width: 1.5,
                  ),
                ),
                filled: true,
                fillColor: const Color(0xFFF8FAFC),
              ),
              items: [
                'Draft',
                'Pending',
                'Proses',
                'Dalam Produksi',
                'Selesai',
              ].map((s) => DropdownMenuItem(value: s, child: Text(s))).toList(),
              onChanged: (v) => setState(() => selectedStatus = v),
              validator: (v) => v == null ? 'Status harus dipilih' : null,
            ),
            const SizedBox(height: 24),
            Row(
              children: [
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: _simpanPO,
                    icon: const Icon(Icons.save_outlined),
                    label: const Text(
                      'Simpan PO',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF3B82F6),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      elevation: 0,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () {
                      for (var c in _deskripsiCtrls) {
                        c.clear();
                      }
                      for (var c in _jumlahCtrls) {
                        c.clear();
                      }
                      for (var c in _hargaCtrls) {
                        c.clear();
                      }
                      setState(() {
                        selectedPelanggan = null;
                        selectedStatus = null;
                        _hargaDikunci = false;
                        while (_itemCount > 1) {
                          _removeItem(_itemCount - 1);
                        }
                      });
                    },
                    icon: const Icon(Icons.refresh_rounded),
                    label: const Text(
                      'Bersihkan',
                      style: TextStyle(fontWeight: FontWeight.bold),
                    ),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      side: const BorderSide(
                        color: Color(0xFFCBD5E1),
                        width: 1,
                      ),
                      foregroundColor: const Color(0xFF475569),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
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

    // ── Receipt Preview ──────────────────────────────────────────────────────
    final receiptPreview = Container(
      decoration: BoxDecoration(
        color: const Color(0xFF0F172A),
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.15),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Stack(
        children: [
          Positioned(
            right: -24,
            top: -24,
            child: Icon(
              Icons.receipt_long,
              size: 160,
              color: Colors.white.withValues(alpha: 0.02),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(6),
                          decoration: BoxDecoration(
                            color: Colors.blue.withValues(alpha: 0.2),
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: const Icon(
                            Icons.bolt,
                            color: Colors.blue,
                            size: 14,
                          ),
                        ),
                        const SizedBox(width: 8),
                        const Text(
                          'LIVE PREVIEW',
                          style: TextStyle(
                            color: Colors.blue,
                            fontSize: 10,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1.5,
                          ),
                        ),
                      ],
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 4,
                      ),
                      decoration: BoxDecoration(
                        color: previewStatusColor.withValues(alpha: 0.2),
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(
                          color: previewStatusColor.withValues(alpha: 0.4),
                        ),
                      ),
                      child: Text(
                        selectedStatus ?? 'Pending',
                        style: TextStyle(
                          color: previewStatusColor,
                          fontSize: 9,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 18),
                const Center(
                  child: Column(
                    children: [
                      Text(
                        'HUTCHID PRESTIGE',
                        style: TextStyle(
                          color: Colors.white,
                          fontSize: 13,
                          fontWeight: FontWeight.bold,
                          letterSpacing: 1,
                        ),
                      ),
                      SizedBox(height: 2),
                      Text(
                        'Purchase Order',
                        style: TextStyle(color: Colors.white30, fontSize: 9),
                      ),
                    ],
                  ),
                ),
                const SizedBox(height: 14),
                const Divider(color: Colors.white10),
                const SizedBox(height: 10),
                _previewRow('No. PO', poPreview, white: false),
                _previewRow('Pelanggan', selectedPelanggan ?? '-', white: true),
                _previewRow(
                  'Tanggal',
                  '${DateTime.now().day}/${DateTime.now().month}/${DateTime.now().year}',
                  white: false,
                ),
                const SizedBox(height: 10),
                const Divider(color: Colors.white10),
                const SizedBox(height: 8),
                // Items
                ...List.generate(_itemCount, (i) {
                  final d = _deskripsiCtrls[i].text.isNotEmpty
                      ? _deskripsiCtrls[i].text
                      : '(Item ${i + 1})';
                  final q = int.tryParse(_jumlahCtrls[i].text) ?? 0;
                  final h = int.tryParse(_hargaCtrls[i].text) ?? 0;
                  return Padding(
                    padding: const EdgeInsets.only(bottom: 5),
                    child: Row(
                      children: [
                        Expanded(
                          child: Text(
                            d,
                            style: const TextStyle(
                              color: Colors.white60,
                              fontSize: 10,
                            ),
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                        Text(
                          '$q × ${_fmt(h)}',
                          style: const TextStyle(
                            color: Colors.white30,
                            fontSize: 10,
                          ),
                        ),
                      ],
                    ),
                  );
                }),
                const SizedBox(height: 10),
                const Divider(color: Colors.white10),
                const SizedBox(height: 10),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    const Text(
                      'TOTAL BILL',
                      style: TextStyle(
                        color: Colors.white70,
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 0.5,
                      ),
                    ),
                    Text(
                      _fmt(total),
                      style: const TextStyle(
                        color: Color(0xFF10B981),
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ],
                ),
                if (_hargaDikunci) ...[
                  const SizedBox(height: 12),
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 10,
                      vertical: 6,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.green.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: Colors.green.withValues(alpha: 0.3),
                      ),
                    ),
                    child: const Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.lock_rounded, color: Colors.green, size: 12),
                        SizedBox(width: 4),
                        Text(
                          'HARGA DIKUNCI',
                          style: TextStyle(
                            color: Colors.green,
                            fontSize: 9,
                            fontWeight: FontWeight.bold,
                            letterSpacing: 1,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
                const SizedBox(height: 14),
                Center(
                  child: Column(
                    children: [
                      Container(
                        height: 28,
                        width: double.infinity,
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.03),
                          borderRadius: BorderRadius.circular(4),
                        ),
                        child: CustomPaint(painter: BarcodePainter()),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        poPreview,
                        style: const TextStyle(
                          color: Colors.white10,
                          fontSize: 8,
                          letterSpacing: 1,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );

    return Container(
      color: const Color(0xFFF8FAFC),
      padding: EdgeInsets.all(isMobile ? 16 : 28),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Buat PO Baru',
                style: TextStyle(
                  fontSize: 26,
                  fontWeight: FontWeight.bold,
                  color: Color(0xFF0F172A),
                ),
              ),
              SizedBox(height: 4),
              Text(
                'Terbitkan Purchase Order baru dengan live preview slip tagihan terperinci.',
                style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
              ),
            ],
          ),
          const SizedBox(height: 20),
          Expanded(
            child: isMobile
                ? SingleChildScrollView(
                    physics: const BouncingScrollPhysics(),
                    child: Column(
                      children: [
                        SizedBox(height: 440, child: receiptPreview),
                        const SizedBox(height: 16),
                        formWidget,
                      ],
                    ),
                  )
                : Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Expanded(
                        flex: 3,
                        child: SingleChildScrollView(
                          physics: const BouncingScrollPhysics(),
                          child: formWidget,
                        ),
                      ),
                      const SizedBox(width: 24),
                      Expanded(flex: 2, child: receiptPreview),
                    ],
                  ),
          ),
        ],
      ),
    );
  }

  Widget _previewRow(String label, String value, {required bool white}) =>
      Padding(
        padding: const EdgeInsets.only(bottom: 5),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              label,
              style: const TextStyle(
                color: Colors.white30,
                fontSize: 10,
                fontWeight: FontWeight.bold,
              ),
            ),
            Flexible(
              child: Text(
                value,
                style: TextStyle(
                  color: white ? Colors.white : Colors.white60,
                  fontSize: 10,
                  fontWeight: white ? FontWeight.bold : FontWeight.normal,
                ),
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ],
        ),
      );
}

class BarcodePainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final Paint paint = Paint()
      ..color = Colors.white.withValues(alpha: 0.15)
      ..strokeWidth = 2.0;

    double startX = 10.0;
    final double endX = size.width - 10.0;
    int index = 0;

    while (startX < endX) {
      final double width = (index % 3 == 0) ? 3.5 : 1.5;
      paint.strokeWidth = width;
      canvas.drawLine(
        Offset(startX, 4.0),
        Offset(startX, size.height - 4.0),
        paint,
      );
      startX += width + ((index % 2 == 0) ? 2.5 : 1.5);
      index++;
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

// Daftar Pelanggan Content Placeholder (Unused)
class DaftarPelangganScreenContent extends StatefulWidget {
  const DaftarPelangganScreenContent({super.key});

  @override
  State<DaftarPelangganScreenContent> createState() =>
      _DaftarPelangganScreenContentState();
}

class _DaftarPelangganScreenContentState
    extends State<DaftarPelangganScreenContent> {
  @override
  Widget build(BuildContext context) {
    return const SizedBox();
  }
}

// =============================================================================
// Arsip PDF — Premium Redesign
// =============================================================================
class ArsipPdfScreenContent extends StatefulWidget {
  final List<Map<String, dynamic>> pdfFiles;
  final String userRole;
  final bool isLoading;
  final Future<void> Function(String) onDelete;

  const ArsipPdfScreenContent({
    super.key,
    required this.pdfFiles,
    required this.userRole,
    required this.isLoading,
    required this.onDelete,
  });

  @override
  State<ArsipPdfScreenContent> createState() => _ArsipPdfScreenContentState();
}

class _ArsipPdfScreenContentState extends State<ArsipPdfScreenContent> {
  @override
  Widget build(BuildContext context) {
    final bool isMobile = Responsive.isMobile(context);

    return LayoutBuilder(
      builder: (context, constraints) {
        final double width = constraints.maxWidth;
        int crossAxisCount = 4;
        double childAspectRatio = 1.05;

        if (width < 600) {
          crossAxisCount = 2;
          childAspectRatio = 0.95;
        } else if (width < 950) {
          crossAxisCount = 3;
          childAspectRatio = 1.0;
        }

        return Container(
          color: const Color(0xFFF8FAFC),
          padding: EdgeInsets.all(isMobile ? 16 : 28),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Arsip PDF',
                    style: TextStyle(
                      fontSize: 26,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                  SizedBox(height: 4),
                  Text(
                    'Kelola arsip dokumen PDF Purchase Order Anda dengan mudah.',
                    style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                  ),
                ],
              ),
              const SizedBox(height: 20),

              // Modern Mockup Upload Zone
              Material(
                color: Colors.transparent,
                child: InkWell(
                  onTap: () {
                    ScaffoldMessenger.of(context).showSnackBar(
                      const SnackBar(
                        content: Row(
                          children: [
                            Icon(Icons.info_outline, color: Colors.white),
                            SizedBox(width: 8),
                            Text(
                              'Sistem lokal: Pilih berkas PDF untuk diunggah...',
                            ),
                          ],
                        ),
                        backgroundColor: Color(0xFF3B82F6),
                        behavior: SnackBarBehavior.floating,
                      ),
                    );
                  },
                  borderRadius: BorderRadius.circular(16),
                  child: Container(
                    width: double.infinity,
                    padding: const EdgeInsets.symmetric(
                      vertical: 24,
                      horizontal: 16,
                    ),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(16),
                      border: Border.all(
                        color: const Color(0xFF3B82F6).withValues(alpha: 0.2),
                        width: 1.5,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.01),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Column(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: const Color(
                              0xFF3B82F6,
                            ).withValues(alpha: 0.08),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.cloud_upload_outlined,
                            color: Color(0xFF3B82F6),
                            size: 24,
                          ),
                        ),
                        const SizedBox(height: 12),
                        const Text(
                          'Tarik & Lepas Berkas PDF di Sini',
                          style: TextStyle(
                            fontSize: 13,
                            fontWeight: FontWeight.bold,
                            color: Color(0xFF0F172A),
                          ),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Mendukung unggahan manual (Maksimal 15MB)',
                          style: TextStyle(
                            fontSize: 10,
                            color: Color(0xFF64748B),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              Expanded(
                child: widget.isLoading
                    ? GridView.builder(
                        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: crossAxisCount,
                          crossAxisSpacing: 16,
                          mainAxisSpacing: 16,
                          childAspectRatio: childAspectRatio,
                        ),
                        itemCount: 4,
                        itemBuilder: (context, index) => Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(16),
                            color: Colors.white,
                            border: Border.all(color: const Color(0xFFE2E8F0)),
                          ),
                          child: const Padding(
                            padding: EdgeInsets.all(16),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                ShimmerLoading(
                                  width: 36,
                                  height: 36,
                                  borderRadius: 8,
                                ),
                                SizedBox(height: 16),
                                ShimmerLoading(width: 100, height: 14),
                                SizedBox(height: 8),
                                ShimmerLoading(width: 60, height: 10),
                              ],
                            ),
                          ),
                        ),
                      )
                    : widget.pdfFiles.isEmpty
                    ? Center(
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Container(
                              padding: const EdgeInsets.all(20),
                              decoration: const BoxDecoration(
                                color: Color(0xFFF1F5F9),
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.picture_as_pdf_outlined,
                                color: Color(0xFF94A3B8),
                                size: 48,
                              ),
                            ),
                            const SizedBox(height: 16),
                            const Text(
                              'Belum ada arsip PDF PO',
                              style: TextStyle(
                                color: Color(0xFF475569),
                                fontSize: 13,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ],
                        ),
                      )
                    : GridView.builder(
                        physics: const BouncingScrollPhysics(),
                        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: crossAxisCount,
                          crossAxisSpacing: 16,
                          mainAxisSpacing: 16,
                          childAspectRatio: childAspectRatio,
                        ),
                        itemCount: widget.pdfFiles.length,
                        itemBuilder: (context, index) {
                          final pdf = widget.pdfFiles[index];
                          final String filename =
                              pdf['filename'] ?? pdf['nama'] ?? 'Dokumen PO';
                          final String size = pdf['size'] ?? '2.4 MB';
                          final String date =
                              pdf['tanggal'] ?? pdf['updated_at'] ?? '';

                          return Container(
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(
                                color: const Color(0xFFE2E8F0),
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withValues(alpha: 0.01),
                                  blurRadius: 10,
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
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      Container(
                                        padding: const EdgeInsets.all(8),
                                        decoration: BoxDecoration(
                                          color: const Color(
                                            0xFFEF4444,
                                          ).withValues(alpha: 0.08),
                                          borderRadius: BorderRadius.circular(
                                            10,
                                          ),
                                          border: Border.all(
                                            color: const Color(
                                              0xFFFCA5A5,
                                            ).withValues(alpha: 0.3),
                                          ),
                                        ),
                                        child: const Icon(
                                          Icons.picture_as_pdf,
                                          color: Color(0xFFEF4444),
                                          size: 20,
                                        ),
                                      ),
                                      if (widget.userRole == 'Administrator' ||
                                          widget.userRole == 'Staf Penjualan')
                                        IconButton(
                                          icon: const Icon(
                                            Icons.delete_outline_rounded,
                                            size: 16,
                                            color: Color(0xFFEF4444),
                                          ),
                                          style: IconButton.styleFrom(
                                            backgroundColor: const Color(
                                              0xFFEF4444,
                                            ).withValues(alpha: 0.05),
                                            padding: EdgeInsets.zero,
                                            shape: RoundedRectangleBorder(
                                              borderRadius:
                                                  BorderRadius.circular(8),
                                            ),
                                          ),
                                          onPressed: () {
                                            showDialog(
                                              context: context,
                                              builder: (context) => AlertDialog(
                                                shape: RoundedRectangleBorder(
                                                  borderRadius:
                                                      BorderRadius.circular(16),
                                                ),
                                                title: const Text(
                                                  'Hapus Arsip PDF',
                                                  style: TextStyle(
                                                    fontWeight: FontWeight.bold,
                                                    fontSize: 16,
                                                  ),
                                                ),
                                                content: Text(
                                                  'Apakah Anda yakin ingin menghapus arsip $filename?',
                                                  style: const TextStyle(
                                                    fontSize: 13,
                                                  ),
                                                ),
                                                actions: [
                                                  TextButton(
                                                    onPressed: () =>
                                                        Navigator.pop(context),
                                                    child: const Text(
                                                      'Batal',
                                                      style: TextStyle(
                                                        color: Color(
                                                          0xFF64748B,
                                                        ),
                                                        fontWeight:
                                                            FontWeight.w600,
                                                      ),
                                                    ),
                                                  ),
                                                  ElevatedButton(
                                                    onPressed: () async {
                                                      Navigator.pop(context);
                                                      await widget.onDelete(
                                                        pdf['id'].toString(),
                                                      );
                                                    },
                                                    style: ElevatedButton.styleFrom(
                                                      backgroundColor:
                                                          const Color(
                                                            0xFFEF4444,
                                                          ),
                                                      foregroundColor:
                                                          Colors.white,
                                                      elevation: 0,
                                                      shape: RoundedRectangleBorder(
                                                        borderRadius:
                                                            BorderRadius.circular(
                                                              10,
                                                            ),
                                                      ),
                                                    ),
                                                    child: const Text('Hapus'),
                                                  ),
                                                ],
                                              ),
                                            );
                                          },
                                        ),
                                    ],
                                  ),
                                  const Spacer(),
                                  Text(
                                    filename,
                                    style: const TextStyle(
                                      fontSize: 12,
                                      fontWeight: FontWeight.bold,
                                      color: Color(0xFF0F172A),
                                    ),
                                    maxLines: 2,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                  const SizedBox(height: 4),
                                  Row(
                                    mainAxisAlignment:
                                        MainAxisAlignment.spaceBetween,
                                    children: [
                                      Text(
                                        size,
                                        style: const TextStyle(
                                          fontSize: 10,
                                          color: Color(0xFF94A3B8),
                                        ),
                                      ),
                                      Text(
                                        date,
                                        style: const TextStyle(
                                          fontSize: 10,
                                          color: Color(0xFF94A3B8),
                                        ),
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          );
                        },
                      ),
              ),
            ],
          ),
        );
      },
    );
  }
}

// =============================================================================
// Notifikasi — Premium Redesign
// =============================================================================
class NotifikasiScreenContent extends StatefulWidget {
  final List<AppNotification> notifications;
  final VoidCallback onMarkAllAsRead;
  final VoidCallback onClearAll;
  final ValueChanged<String> onMarkAsRead;
  final ValueChanged<String> onDeleteNotification;

  const NotifikasiScreenContent({
    super.key,
    required this.notifications,
    required this.onMarkAllAsRead,
    required this.onClearAll,
    required this.onMarkAsRead,
    required this.onDeleteNotification,
  });

  @override
  State<NotifikasiScreenContent> createState() =>
      _NotifikasiScreenContentState();
}

class _NotifikasiScreenContentState extends State<NotifikasiScreenContent> {
  String selectedFilter = 'Semua';

  String _getTimeAgo(DateTime dateTime) {
    final Duration diff = DateTime.now().difference(dateTime);
    if (diff.inSeconds < 60) {
      return 'Baru saja';
    } else if (diff.inMinutes < 60) {
      return '${diff.inMinutes} menit lalu';
    } else if (diff.inHours < 24) {
      return '${diff.inHours} jam lalu';
    } else if (diff.inDays == 1) {
      return 'Kemarin';
    } else {
      return '${dateTime.day}/${dateTime.month}/${dateTime.year}';
    }
  }

  @override
  Widget build(BuildContext context) {
    final bool isMobile = Responsive.isMobile(context);

    // Filter notifications
    final filteredNotifications = widget.notifications.where((n) {
      if (selectedFilter == 'Semua') return true;
      if (selectedFilter == 'Pesanan') return n.type == 'pesanan';
      if (selectedFilter == 'Pelanggan') return n.type == 'pelanggan';
      if (selectedFilter == 'Sistem') return n.type == 'sistem';
      return true;
    }).toList();

    final int unreadCount = widget.notifications.where((n) => !n.isRead).length;

    return Container(
      color: const Color(0xFFF8FAFC),
      padding: EdgeInsets.all(isMobile ? 16 : 28),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header Row
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Notifikasi',
                    style: TextStyle(
                      fontSize: 26,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF0F172A),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    unreadCount > 0
                        ? 'Terdapat $unreadCount notifikasi baru belum dibaca.'
                        : 'Semua notifikasi Anda telah dibaca.',
                    style: TextStyle(
                      fontSize: 12,
                      color: unreadCount > 0
                          ? const Color(0xFF2563EB)
                          : const Color(0xFF64748B),
                      fontWeight: unreadCount > 0
                          ? FontWeight.bold
                          : FontWeight.normal,
                    ),
                  ),
                ],
              ),
              // Header actions
              if (widget.notifications.isNotEmpty)
                Row(
                  children: [
                    TextButton.icon(
                      onPressed: widget.onMarkAllAsRead,
                      icon: const Icon(Icons.done_all_rounded, size: 16),
                      label: const Text(
                        'Dibaca Semua',
                        style: TextStyle(fontSize: 12),
                      ),
                      style: TextButton.styleFrom(
                        foregroundColor: const Color(0xFF3B82F6),
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                      ),
                    ),
                    const SizedBox(width: 8),
                    OutlinedButton.icon(
                      onPressed: () {
                        showDialog(
                          context: context,
                          builder: (context) => AlertDialog(
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                            title: const Text(
                              'Bersihkan Notifikasi',
                              style: TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                              ),
                            ),
                            content: const Text(
                              'Hapus semua riwayat notifikasi Anda?',
                              style: TextStyle(fontSize: 13),
                            ),
                            actions: [
                              TextButton(
                                onPressed: () => Navigator.pop(context),
                                child: const Text(
                                  'Batal',
                                  style: TextStyle(
                                    color: Color(0xFF64748B),
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                              ),
                              ElevatedButton(
                                onPressed: () {
                                  Navigator.pop(context);
                                  widget.onClearAll();
                                },
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: const Color(0xFFEF4444),
                                  foregroundColor: Colors.white,
                                  elevation: 0,
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(10),
                                  ),
                                ),
                                child: const Text('Bersihkan'),
                              ),
                            ],
                          ),
                        );
                      },
                      icon: const Icon(Icons.delete_sweep_outlined, size: 16),
                      label: const Text(
                        'Bersihkan',
                        style: TextStyle(fontSize: 12),
                      ),
                      style: OutlinedButton.styleFrom(
                        foregroundColor: const Color(0xFFEF4444),
                        side: const BorderSide(color: Color(0xFFFCA5A5)),
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                      ),
                    ),
                  ],
                ),
            ],
          ),
          const SizedBox(height: 20),

          // Horizontal Filters Chips
          SingleChildScrollView(
            scrollDirection: Axis.horizontal,
            child: Row(
              children: ['Semua', 'Pesanan', 'Pelanggan', 'Sistem'].map((
                filter,
              ) {
                final bool isSelected = selectedFilter == filter;
                int count = 0;
                if (filter == 'Semua') {
                  count = widget.notifications.length;
                } else if (filter == 'Pesanan') {
                  count = widget.notifications
                      .where((n) => n.type == 'pesanan')
                      .length;
                } else if (filter == 'Pelanggan') {
                  count = widget.notifications
                      .where((n) => n.type == 'pelanggan')
                      .length;
                } else if (filter == 'Sistem') {
                  count = widget.notifications
                      .where((n) => n.type == 'sistem')
                      .length;
                }

                return Padding(
                  padding: const EdgeInsets.only(right: 8),
                  child: FilterChip(
                    label: Text('$filter ($count)'),
                    selected: isSelected,
                    onSelected: (bool val) {
                      setState(() {
                        selectedFilter = filter;
                      });
                    },
                    selectedColor: const Color(0xFFEFF6FF),
                    checkmarkColor: const Color(0xFF2563EB),
                    labelStyle: TextStyle(
                      fontSize: 12,
                      color: isSelected
                          ? const Color(0xFF2563EB)
                          : const Color(0xFF475569),
                      fontWeight: isSelected
                          ? FontWeight.bold
                          : FontWeight.normal,
                    ),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(20),
                    ),
                    side: BorderSide(
                      color: isSelected
                          ? const Color(0xFF3B82F6).withValues(alpha: 0.3)
                          : const Color(0xFFE2E8F0),
                    ),
                  ),
                );
              }).toList(),
            ),
          ),
          const SizedBox(height: 16),

          // Notification List
          Expanded(
            child: filteredNotifications.isEmpty
                ? Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(20),
                          decoration: const BoxDecoration(
                            color: Color(0xFFF1F5F9),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.notifications_off_outlined,
                            size: 48,
                            color: Color(0xFF94A3B8),
                          ),
                        ),
                        const SizedBox(height: 16),
                        const Text(
                          'Tidak ada notifikasi',
                          style: TextStyle(
                            color: Color(0xFF475569),
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  )
                : ListView.builder(
                    physics: const BouncingScrollPhysics(),
                    itemCount: filteredNotifications.length,
                    itemBuilder: (context, index) {
                      final notif = filteredNotifications[index];

                      // Custom styling properties per notification type
                      IconData typeIcon = Icons.notifications_none_rounded;
                      Color typeColor = const Color(0xFF3B82F6);
                      Color typeBg = const Color(
                        0xFF3B82F6,
                      ).withValues(alpha: 0.08);

                      if (notif.type == 'pesanan') {
                        typeIcon = Icons.shopping_bag_outlined;
                        typeColor = const Color(0xFF10B981);
                        typeBg = const Color(
                          0xFF10B981,
                        ).withValues(alpha: 0.08);
                      } else if (notif.type == 'pelanggan') {
                        typeIcon = Icons.person_outline_rounded;
                        typeColor = const Color(0xFF8B5CF6);
                        typeBg = const Color(
                          0xFF8B5CF6,
                        ).withValues(alpha: 0.08);
                      } else if (notif.type == 'arsip') {
                        typeIcon = Icons.picture_as_pdf_outlined;
                        typeColor = const Color(0xFFEF4444);
                        typeBg = const Color(
                          0xFFEF4444,
                        ).withValues(alpha: 0.08);
                      } else if (notif.type == 'sistem') {
                        typeIcon = Icons.settings_outlined;
                        typeColor = const Color(0xFF64748B);
                        typeBg = const Color(
                          0xFF64748B,
                        ).withValues(alpha: 0.08);
                      }

                      return InkWell(
                        onTap: () {
                          widget.onMarkAsRead(notif.id);
                        },
                        borderRadius: BorderRadius.circular(12),
                        child: AnimatedContainer(
                          duration: const Duration(milliseconds: 200),
                          margin: const EdgeInsets.only(bottom: 10),
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(12),
                            color: notif.isRead
                                ? Colors.white
                                : const Color(0xFFF8FAFC),
                            border: Border.all(
                              color: notif.isRead
                                  ? const Color(0xFFE2E8F0)
                                  : const Color(
                                      0xFF3B82F6,
                                    ).withValues(alpha: 0.15),
                              width: 1,
                            ),
                          ),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Type Icon
                              Container(
                                padding: const EdgeInsets.all(10),
                                decoration: BoxDecoration(
                                  color: typeBg,
                                  borderRadius: BorderRadius.circular(10),
                                ),
                                child: Icon(
                                  typeIcon,
                                  color: typeColor,
                                  size: 18,
                                ),
                              ),
                              const SizedBox(width: 16),

                              // Text Content
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Row(
                                      mainAxisAlignment:
                                          MainAxisAlignment.spaceBetween,
                                      children: [
                                        Expanded(
                                          child: Row(
                                            children: [
                                              if (!notif.isRead)
                                                Container(
                                                  margin: const EdgeInsets.only(
                                                    right: 6,
                                                  ),
                                                  width: 6,
                                                  height: 6,
                                                  decoration:
                                                      const BoxDecoration(
                                                        color: Color(
                                                          0xFF3B82F6,
                                                        ),
                                                        shape: BoxShape.circle,
                                                      ),
                                                ),
                                              Expanded(
                                                child: Text(
                                                  notif.title,
                                                  style: TextStyle(
                                                    fontWeight: FontWeight.bold,
                                                    fontSize: 13,
                                                    color: notif.isRead
                                                        ? const Color(
                                                            0xFF334155,
                                                          )
                                                        : const Color(
                                                            0xFF0F172A,
                                                          ),
                                                  ),
                                                  overflow:
                                                      TextOverflow.ellipsis,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        Text(
                                          _getTimeAgo(notif.timestamp),
                                          style: const TextStyle(
                                            fontSize: 10,
                                            color: Color(0xFF94A3B8),
                                            fontWeight: FontWeight.w500,
                                          ),
                                        ),
                                      ],
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      notif.body,
                                      style: const TextStyle(
                                        fontSize: 12,
                                        color: Color(0xFF64748B),
                                        height: 1.4,
                                      ),
                                    ),
                                  ],
                                ),
                              ),

                              const SizedBox(width: 12),

                              // Individual Delete Action Button
                              IconButton(
                                icon: const Icon(
                                  Icons.close_rounded,
                                  size: 16,
                                  color: Color(0xFF94A3B8),
                                ),
                                padding: EdgeInsets.zero,
                                constraints: const BoxConstraints(),
                                onPressed: () {
                                  widget.onDeleteNotification(notif.id);
                                },
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}
