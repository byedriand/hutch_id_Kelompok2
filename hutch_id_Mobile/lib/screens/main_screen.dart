import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../models/models.dart';
import 'dashboard_screen.dart';
import 'order_list_screen.dart';
import 'combined_screens.dart';

class MainScreen extends StatefulWidget {
  final UserRole role;
  const MainScreen({super.key, required this.role});

  @override
  State<MainScreen> createState() => _MainScreenState();
}

class _MainScreenState extends State<MainScreen> {
  int _idx = 0;

  late final List<Widget> _screens;

  @override
  void initState() {
    super.initState();
    _screens = [
      DashboardScreen(role: widget.role, onNavigate: _go),
      OrderListScreen(role: widget.role),
      CreatePoScreen(role: widget.role, onSaved: () => _go(1)),
      CustomerScreen(role: widget.role),
    ];
  }

  void _go(int idx) => setState(() => _idx = idx);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: IndexedStack(index: _idx, children: _screens),
      bottomNavigationBar: Container(
        decoration: const BoxDecoration(
          boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 12)],
        ),
        child: BottomNavigationBar(
          currentIndex: _idx,
          onTap: (i) => setState(() => _idx = i),
          backgroundColor: AppColors.navy,
          selectedItemColor: Colors.white,
          unselectedItemColor: Colors.white54,
          selectedLabelStyle: GoogleFonts.plusJakartaSans(
              fontSize: 10.5, fontWeight: FontWeight.w700),
          unselectedLabelStyle: GoogleFonts.plusJakartaSans(
              fontSize: 10.5, fontWeight: FontWeight.w500),
          type: BottomNavigationBarType.fixed,
          items: [
            const BottomNavigationBarItem(
              icon: Icon(Icons.dashboard_outlined),
              activeIcon: Icon(Icons.dashboard),
              label: 'Dashboard',
            ),
            BottomNavigationBarItem(
              icon: Stack(children: [
                const Icon(Icons.list_alt_outlined),
                Positioned(
                    right: 0,
                    top: 0,
                    child: Container(
                      width: 8,
                      height: 8,
                      decoration: const BoxDecoration(
                          color: Colors.redAccent, shape: BoxShape.circle),
                    )),
              ]),
              activeIcon: const Icon(Icons.list_alt),
              label: 'Pesanan',
            ),
            const BottomNavigationBarItem(
              icon: Icon(Icons.add_circle_outline),
              activeIcon: Icon(Icons.add_circle),
              label: 'Buat PO',
            ),
            const BottomNavigationBarItem(
              icon: Icon(Icons.people_outline),
              activeIcon: Icon(Icons.people),
              label: 'Pelanggan',
            ),
          ],
        ),
      ),
    );
  }
}
