import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import '../providers/notifikasi_provider.dart';
import '../providers/pesanan_provider.dart';

// ─── Warna sidebar ────────────────────────────────────────────────────────────
class SidebarColors {
  static const bg = Color(0xFF0d1b2e);
  static const header = Color(0xFF112240);
  static const itemSelected = Color(0xFF1a3a6e);
  static const border = Color(0xFF1e3a5f);
  static const text = Colors.white;
  static const subText = Color(0xFF8899bb);
  static const sectionLabel = Color(0xFF4a6080);
  static const chatbotStart = Color(0xFF0ea5e9);
  static const chatbotEnd = Color(0xFF0284c7);
  static const logoutBg = Color(0xFF1e2d42);
}

// ─── Model Menu Item ──────────────────────────────────────────────────────────
class SidebarMenuItem {
  final int index;
  final IconData icon;
  final IconData iconSelected;
  final String label;
  final String section; // 'menu' | 'admin'
  final String? badgeKey; // 'notifikasi' | 'pesanan' | null

  const SidebarMenuItem({
    required this.index,
    required this.icon,
    required this.iconSelected,
    required this.label,
    required this.section,
    this.badgeKey,
  });
}

// ─── AppSidebar (Sidebar Permanen) ───────────────────────────────────────────
class AppSidebar extends StatelessWidget {
  final int selectedIndex;
  final ValueChanged<int> onItemSelected;
  final List<SidebarMenuItem> menuItems;
  final VoidCallback? onChatBot;

  const AppSidebar({
    super.key,
    required this.selectedIndex,
    required this.onItemSelected,
    required this.menuItems,
    this.onChatBot,
  });

  @override
  Widget build(BuildContext context) {
    return Consumer3<AuthProvider, NotifikasiProvider, PesananProvider>(
      builder: (context, authProvider, notifProvider, pesananProvider, _) {
        final user = authProvider.user;
        final userName = user?.name ?? 'Administrator';
        final userRole = user?.role ?? 'admin';
        final roleDisplay = _getRoleDisplay(userRole);

        // Badge counts
        final notifCount = notifProvider.notifikasiList.length;
        final pesananCount = pesananProvider.pesananList
            .where((p) => p.status == 'menunggu_konfirmasi')
            .length;

        return Container(
          width: 220,
          decoration: const BoxDecoration(
            color: SidebarColors.bg,
            border: Border(
              right: BorderSide(color: SidebarColors.border, width: 1),
            ),
          ),
          child: Column(
            children: [
              _buildHeader(),
              Expanded(
                child: SingleChildScrollView(
                  padding: const EdgeInsets.symmetric(vertical: 8),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildSectionLabel('MENU'),
                      ...menuItems
                          .where((item) => item.section == 'menu')
                          .map((item) => _buildMenuItem(
                                item: item,
                                isSelected: selectedIndex == item.index,
                                badgeCount: item.badgeKey == 'notifikasi'
                                    ? notifCount
                                    : item.badgeKey == 'pesanan'
                                        ? pesananCount
                                        : 0,
                                onTap: () => onItemSelected(item.index),
                              )),
                      if (menuItems.any((i) => i.section == 'admin')) ...[
                        const SizedBox(height: 4),
                        _buildSectionLabel('ADMIN'),
                        ...menuItems
                            .where((item) => item.section == 'admin')
                            .map((item) => _buildMenuItem(
                                  item: item,
                                  isSelected: selectedIndex == item.index,
                                  badgeCount: 0,
                                  onTap: () => onItemSelected(item.index),
                                )),
                      ],
                    ],
                  ),
                ),
              ),
              _buildFooter(
                context,
                userName: userName,
                roleDisplay: roleDisplay,
              ),
            ],
          ),
        );
      },
    );
  }

  // ── Header ────────────────────────────────────────────────────────────────
  Widget _buildHeader() {
    return Container(
      height: 72,
      padding: const EdgeInsets.symmetric(horizontal: 16),
      decoration: const BoxDecoration(
        color: SidebarColors.header,
        border: Border(
          bottom: BorderSide(color: SidebarColors.border, width: 1),
        ),
      ),
      child: Row(
        children: [
          Container(
            width: 42,
            height: 42,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFF3b82f6), Color(0xFF1e40af)],
              ),
              borderRadius: BorderRadius.circular(10),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFF3b82f6).withValues(alpha: 0.3),
                  blurRadius: 8,
                  offset: const Offset(0, 3),
                ),
              ],
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(10),
              child: Image.asset(
                'assets/images/hutch-logo.png',
                fit: BoxFit.contain,
                errorBuilder: (context, error, stackTrace) => const Icon(
                  Icons.shopping_bag_rounded,
                  color: Colors.white,
                  size: 24,
                ),
              ),
            ),
          ),
          const SizedBox(width: 12),
          const Expanded(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'HUTCH PRESTIGE',
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w900,
                    color: SidebarColors.text,
                    letterSpacing: 0.5,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
                SizedBox(height: 2),
                Text(
                  'Modul Manajemen',
                  style: TextStyle(
                    fontSize: 10,
                    fontWeight: FontWeight.w500,
                    color: SidebarColors.subText,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  // ── Section Label ─────────────────────────────────────────────────────────
  Widget _buildSectionLabel(String label) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(20, 14, 16, 6),
      child: Text(
        label,
        style: const TextStyle(
          fontSize: 10,
          fontWeight: FontWeight.w700,
          color: SidebarColors.sectionLabel,
          letterSpacing: 1.2,
        ),
      ),
    );
  }

  // ── Menu Item ─────────────────────────────────────────────────────────────
  Widget _buildMenuItem({
    required SidebarMenuItem item,
    required bool isSelected,
    required int badgeCount,
    required VoidCallback onTap,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 2),
      child: Material(
        color: Colors.transparent,
        borderRadius: BorderRadius.circular(10),
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(10),
          hoverColor: const Color(0xFF142d55),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 180),
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 11),
            decoration: BoxDecoration(
              color: isSelected ? SidebarColors.itemSelected : Colors.transparent,
              borderRadius: BorderRadius.circular(10),
              border: isSelected
                  ? Border.all(
                      color: const Color(0xFF2d5abf).withValues(alpha: 0.4),
                      width: 1,
                    )
                  : null,
            ),
            child: Row(
              children: [
                Container(
                  width: 32,
                  height: 32,
                  decoration: BoxDecoration(
                    color: isSelected
                        ? const Color(0xFF2d5abf).withValues(alpha: 0.4)
                        : const Color(0xFF1e3a5f).withValues(alpha: 0.6),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Icon(
                    isSelected ? item.iconSelected : item.icon,
                    size: 16,
                    color: isSelected ? Colors.white : SidebarColors.subText,
                  ),
                ),
                const SizedBox(width: 11),
                Expanded(
                  child: Text(
                    item.label,
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight:
                          isSelected ? FontWeight.w700 : FontWeight.w500,
                      color: isSelected
                          ? Colors.white
                          : const Color(0xFFb0c4de),
                    ),
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
                if (badgeCount > 0)
                  Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 7, vertical: 2),
                    decoration: BoxDecoration(
                      color: Colors.red[600],
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: Text(
                      badgeCount > 99 ? '99+' : '$badgeCount',
                      style: const TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                        color: Colors.white,
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

  // ── Footer ────────────────────────────────────────────────────────────────
  Widget _buildFooter(
    BuildContext context, {
    required String userName,
    required String roleDisplay,
  }) {
    return Container(
      decoration: const BoxDecoration(
        border:
            Border(top: BorderSide(color: SidebarColors.border, width: 1)),
      ),
      child: Column(
        children: [
          // User info
          Padding(
            padding: const EdgeInsets.fromLTRB(14, 14, 14, 10),
            child: Row(
              children: [
                Stack(
                  children: [
                    Container(
                      width: 38,
                      height: 38,
                      decoration: BoxDecoration(
                        gradient: const LinearGradient(
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                          colors: [Color(0xFF3b82f6), Color(0xFF1d4ed8)],
                        ),
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: const Color(0xFF2d5abf),
                          width: 2,
                        ),
                      ),
                      child: const Icon(
                        Icons.person_rounded,
                        color: Colors.white,
                        size: 22,
                      ),
                    ),
                    Positioned(
                      bottom: 1,
                      right: 1,
                      child: Container(
                        width: 10,
                        height: 10,
                        decoration: BoxDecoration(
                          color: const Color(0xFF22c55e),
                          shape: BoxShape.circle,
                          border: Border.all(
                              color: SidebarColors.bg, width: 1.5),
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        userName,
                        style: const TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                        ),
                        overflow: TextOverflow.ellipsis,
                      ),
                      Text(
                        roleDisplay,
                        style: const TextStyle(
                          fontSize: 10,
                          color: SidebarColors.subText,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),

          // ChatBot AI Button
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 3),
            child: Material(
              color: Colors.transparent,
              borderRadius: BorderRadius.circular(10),
              child: InkWell(
                onTap: onChatBot ?? () {},
                borderRadius: BorderRadius.circular(10),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 11),
                  decoration: BoxDecoration(
                    gradient: const LinearGradient(
                      colors: [
                        SidebarColors.chatbotStart,
                        SidebarColors.chatbotEnd,
                      ],
                    ),
                    borderRadius: BorderRadius.circular(10),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(0xFF0ea5e9).withValues(alpha: 0.3),
                        blurRadius: 8,
                        offset: const Offset(0, 3),
                      ),
                    ],
                  ),
                  child: const Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.smart_toy_rounded,
                          color: Colors.white, size: 16),
                      SizedBox(width: 8),
                      Text(
                        'ChatBot AI',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),

          // Keluar Button
          Padding(
            padding: const EdgeInsets.fromLTRB(10, 4, 10, 14),
            child: Material(
              color: Colors.transparent,
              borderRadius: BorderRadius.circular(10),
              child: InkWell(
                onTap: () => _showLogoutConfirmDialog(context),
                borderRadius: BorderRadius.circular(10),
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.symmetric(vertical: 11),
                  decoration: BoxDecoration(
                    color: SidebarColors.logoutBg,
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: SidebarColors.border, width: 1),
                  ),
                  child: const Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.logout_rounded,
                          color: SidebarColors.subText, size: 16),
                      SizedBox(width: 8),
                      Text(
                        'Keluar',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.w600,
                          color: SidebarColors.subText,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }

  String _getRoleDisplay(String role) {
    switch (role) {
      case 'administrator':
        return 'Admin';
      case 'staf_penjualan':
        return 'Staf Penjualan';
      case 'operator_gudang':
        return 'Operator Gudang';
      default:
        return role;
    }
  }
}

void _showLogoutConfirmDialog(BuildContext context) {
  showDialog(
    context: context,
    builder: (ctx) => Dialog(
      shape:
          RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.red[50],
                shape: BoxShape.circle,
              ),
              child: Icon(Icons.logout_rounded,
                  color: Colors.red[700], size: 32),
            ),
            const SizedBox(height: 16),
            const Text(
              'Keluar dari Aplikasi?',
              style:
                  TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 8),
            const Text(
              'Apakah Anda yakin ingin keluar?',
              textAlign: TextAlign.center,
              style: TextStyle(fontSize: 13, color: Colors.grey),
            ),
            const SizedBox(height: 24),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () => Navigator.pop(ctx),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      side: BorderSide(color: Colors.grey[300]!),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child: const Text('Batal'),
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: FilledButton(
                    onPressed: () async {
                      Navigator.pop(ctx);
                      await Provider.of<AuthProvider>(context,
                              listen: false)
                          .logout();
                      if (context.mounted) {
                        Navigator.pushNamedAndRemoveUntil(
                          context,
                          '/welcome',
                          (route) => false,
                        );
                      }
                    },
                    style: FilledButton.styleFrom(
                      backgroundColor: Colors.red[700],
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child: const Text('Keluar'),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    ),
  );
}

// ─── Responsive Scaffold dengan Sidebar ──────────────────────────────────────
class SidebarScaffold extends StatelessWidget {
  final int selectedIndex;
  final ValueChanged<int> onItemSelected;
  final Widget body;
  final List<SidebarMenuItem> menuItems;
  final VoidCallback? onChatBot;
  final List<Widget>? appBarActions;

  const SidebarScaffold({
    super.key,
    required this.selectedIndex,
    required this.onItemSelected,
    required this.body,
    required this.menuItems,
    this.onChatBot,
    this.appBarActions,
  });

  @override
  Widget build(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    final isDesktop = width >= 1100;
    final isTablet = width >= 700 && width < 1100;

    if (isDesktop) return _desktop(context);
    if (isTablet) return _tablet(context);
    return _mobile(context);
  }

  // ── Desktop: sidebar permanen ─────────────────────────────────────────────
  Widget _desktop(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF0F4F8),
      body: Row(
        children: [
          AppSidebar(
            selectedIndex: selectedIndex,
            onItemSelected: onItemSelected,
            menuItems: menuItems,
            onChatBot: onChatBot,
          ),
          Expanded(child: body),
        ],
      ),
    );
  }

  // ── Tablet: rail mini ─────────────────────────────────────────────────────
  Widget _tablet(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF0F4F8),
      body: Row(
        children: [
          _MiniRail(
            selectedIndex: selectedIndex,
            onItemSelected: onItemSelected,
            menuItems: menuItems,
            onChatBot: onChatBot,
          ),
          Expanded(child: body),
        ],
      ),
    );
  }

  // ── Mobile: Drawer ────────────────────────────────────────────────────────
  Widget _mobile(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF0F4F8),
      appBar: _MobileAppBar(
        actions: appBarActions,
      ),
      drawer: Drawer(
        width: 240,
        backgroundColor: Colors.transparent,
        elevation: 0,
        child: AppSidebar(
          selectedIndex: selectedIndex,
          onItemSelected: (i) {
            Navigator.pop(context);
            onItemSelected(i);
          },
          menuItems: menuItems,
          onChatBot: onChatBot,
        ),
      ),
      body: body,
    );
  }
}

// ─── Mini Rail (Tablet) ───────────────────────────────────────────────────────
class _MiniRail extends StatelessWidget {
  final int selectedIndex;
  final ValueChanged<int> onItemSelected;
  final List<SidebarMenuItem> menuItems;
  final VoidCallback? onChatBot;

  const _MiniRail({
    required this.selectedIndex,
    required this.onItemSelected,
    required this.menuItems,
    this.onChatBot,
  });

  @override
  Widget build(BuildContext context) {
    return Consumer<NotifikasiProvider>(
      builder: (context, notifProvider, _) {
        final notifCount = notifProvider.notifikasiList.length;

        return Container(
          width: 68,
          color: SidebarColors.bg,
          child: Column(
            children: [
              // Logo header
              Container(
                height: 72,
                color: SidebarColors.header,
                child: Center(
                  child: Container(
                    width: 38,
                    height: 38,
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFF3b82f6), Color(0xFF1e40af)],
                      ),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: Image.asset(
                        'assets/images/hutch-logo.png',
                        fit: BoxFit.contain,
                        errorBuilder: (context, error, stackTrace) => const Icon(
                          Icons.shopping_bag_rounded,
                          color: Colors.white,
                          size: 22,
                        ),
                      ),
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 8),
              // Items
              Expanded(
                child: SingleChildScrollView(
                  child: Column(
                    children: menuItems.map((item) {
                      final isSelected = selectedIndex == item.index;
                      final badge = item.badgeKey == 'notifikasi'
                          ? notifCount
                          : 0;
                      return Tooltip(
                        message: item.label,
                        preferBelow: false,
                        child: Padding(
                          padding: const EdgeInsets.symmetric(
                              vertical: 3, horizontal: 9),
                          child: InkWell(
                            onTap: () => onItemSelected(item.index),
                            borderRadius: BorderRadius.circular(10),
                            child: AnimatedContainer(
                              duration: const Duration(milliseconds: 180),
                              width: 48,
                              height: 48,
                              decoration: BoxDecoration(
                                color: isSelected
                                    ? SidebarColors.itemSelected
                                    : Colors.transparent,
                                borderRadius: BorderRadius.circular(10),
                              ),
                              child: Stack(
                                children: [
                                  Center(
                                    child: Icon(
                                      isSelected
                                          ? item.iconSelected
                                          : item.icon,
                                      size: 20,
                                      color: isSelected
                                          ? Colors.white
                                          : SidebarColors.subText,
                                    ),
                                  ),
                                  if (badge > 0)
                                    Positioned(
                                      top: 6,
                                      right: 6,
                                      child: Container(
                                        width: 16,
                                        height: 16,
                                        decoration: BoxDecoration(
                                          color: Colors.red[600],
                                          shape: BoxShape.circle,
                                        ),
                                        child: Center(
                                          child: Text(
                                            badge > 9 ? '9+' : '$badge',
                                            style: const TextStyle(
                                              fontSize: 8,
                                              fontWeight: FontWeight.w700,
                                              color: Colors.white,
                                            ),
                                          ),
                                        ),
                                      ),
                                    ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      );
                    }).toList(),
                  ),
                ),
              ),
              // Logout
              Padding(
                padding: const EdgeInsets.only(bottom: 12),
                child: Tooltip(
                  message: 'Keluar',
                  child: InkWell(
                    onTap: () => _showLogoutConfirmDialog(context),
                    borderRadius: BorderRadius.circular(10),
                    child: const Padding(
                      padding: EdgeInsets.all(12),
                      child: Icon(
                        Icons.logout_rounded,
                        color: SidebarColors.subText,
                        size: 22,
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}

// ─── Mobile AppBar ────────────────────────────────────────────────────────────
class _MobileAppBar extends StatelessWidget implements PreferredSizeWidget {
  final List<Widget>? actions;

  const _MobileAppBar({this.actions});

  @override
  Size get preferredSize => const Size.fromHeight(kToolbarHeight);

  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: SidebarColors.bg,
      elevation: 0,
      iconTheme: const IconThemeData(color: Colors.white),
      title: Row(
        children: [
          Container(
            width: 32,
            height: 32,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFF3b82f6), Color(0xFF1e40af)],
              ),
              borderRadius: BorderRadius.circular(8),
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: Image.asset(
                'assets/images/hutch-logo.png',
                fit: BoxFit.contain,
                errorBuilder: (context, error, stackTrace) => const Icon(
                  Icons.shopping_bag_rounded,
                  color: Colors.white,
                  size: 18,
                ),
              ),
            ),
          ),
          const SizedBox(width: 10),
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'HUTCH PRESTIGE',
                style: TextStyle(
                  fontSize: 14,
                  fontWeight: FontWeight.w900,
                  color: Colors.white,
                  letterSpacing: 0.4,
                ),
              ),
              Text(
                'Modul Manajemen',
                style: TextStyle(
                  fontSize: 10,
                  color: SidebarColors.subText,
                ),
              ),
            ],
          ),
        ],
      ),
      actions: actions != null
          ? [...actions!, const SizedBox(width: 8)]
          : [const SizedBox(width: 8)],
    );
  }
}
