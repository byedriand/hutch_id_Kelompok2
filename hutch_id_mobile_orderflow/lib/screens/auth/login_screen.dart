import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _RoleData {
  final String id;       // role id sesuai API (administrator, staf_penjualan, dll)
  final String apiRole;  // nilai role di database
  final String name;
  final String hint;     // email hint/contoh
  final IconData icon;
  final String subtitle;

  const _RoleData({
    required this.id,
    required this.apiRole,
    required this.name,
    required this.hint,
    required this.icon,
    required this.subtitle,
  });
}

class _LoginScreenState extends State<LoginScreen>
    with TickerProviderStateMixin {
  late TextEditingController _emailController;
  late TextEditingController _passwordController;
  final _formKey = GlobalKey<FormState>();
  bool _showPassword = false;
  String? _selectedRoleId;
  late AnimationController _bgAnim;
  late AnimationController _cardAnim;

  static const List<_RoleData> _roles = [
    _RoleData(
      id: 'administrator',
      apiRole: 'administrator',
      name: 'Administrator',
      hint: 'admin@hutch.id',
      icon: Icons.shield_rounded,
      subtitle: 'AKSES MONITORING',
    ),
    _RoleData(
      id: 'staf_penjualan',
      apiRole: 'staf_penjualan',
      name: 'Staf Penjualan',
      hint: 'staf@hutch.id',
      icon: Icons.person_outline_rounded,
      subtitle: 'SALES',
    ),
    _RoleData(
      id: 'operator_gudang',
      apiRole: 'operator_gudang',
      name: 'Operator Gudang',
      hint: 'gudang@hutch.id',
      icon: Icons.warehouse_rounded,
      subtitle: 'WAREHOUSE',
    ),
  ];

  _RoleData? get _selectedRole =>
      _selectedRoleId == null
          ? null
          : _roles.firstWhere((r) => r.id == _selectedRoleId);

  @override
  void initState() {
    super.initState();
    _emailController = TextEditingController();
    _passwordController = TextEditingController();

    _bgAnim = AnimationController(
      duration: const Duration(seconds: 8),
      vsync: this,
    )..repeat();

    _cardAnim = AnimationController(
      duration: const Duration(milliseconds: 700),
      vsync: this,
    )..forward();
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    _bgAnim.dispose();
    _cardAnim.dispose();
    super.dispose();
  }

  void _selectRole(_RoleData role) {
    setState(() {
      _selectedRoleId = role.id;
      _emailController.clear();
      _passwordController.clear();
    });
    // clear error on role change
    Provider.of<AuthProvider>(context, listen: false).clearError();
  }

  void _goToLanding() {
    if (Navigator.canPop(context)) {
      Navigator.pop(context);
    } else {
      Navigator.pushReplacementNamed(context, '/landing');
    }
  }

  Future<void> _handleLogin() async {
    if (_selectedRoleId == null) {
      _showErrorDialog('Pilih Role', 'Silakan pilih role Anda terlebih dahulu sebelum login.');
      return;
    }
    if (!_formKey.currentState!.validate()) return;

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    final roleName = _selectedRole?.name ?? '';
    final success = await authProvider.login(
      _emailController.text.trim(),
      _passwordController.text,
      roleName: roleName,
    );

    if (!mounted) return;

    if (success) {
      // Validasi role: cocokkan role yang dipilih dengan role user dari API
      final loggedUser = authProvider.user;
      if (loggedUser != null && loggedUser.role != _selectedRoleId) {
        await authProvider.logout();
        if (!mounted) return;
        _showErrorDialog(
          'Role Tidak Sesuai',
          'Role yang Anda pilih tidak sesuai dengan akun ini.\n\nSilakan pilih role yang benar.',
        );
        return;
      }
      // AuthGate di app.dart mendengarkan isLoggedIn dan akan redirect.
      // Navigasi eksplisit di sini sebagai safety-net agar tidak stuck.
      if (!mounted) return;
      Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false);
    }
  }

  void _showErrorDialog(String title, String message) {
    showDialog(
      context: context,
      builder: (_) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        child: Padding(
          padding: const EdgeInsets.all(28),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 68,
                height: 68,
                decoration: BoxDecoration(
                  color: Colors.red[50],
                  shape: BoxShape.circle,
                ),
                child: Icon(Icons.error_outline_rounded,
                    color: Colors.red[600], size: 40),
              ),
              const SizedBox(height: 16),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.w800,
                  color: Color(0xFF1e3a5f),
                ),
              ),
              const SizedBox(height: 10),
              Text(
                message,
                textAlign: TextAlign.center,
                style: TextStyle(
                    fontSize: 13, color: Colors.grey[600], height: 1.5),
              ),
              const SizedBox(height: 20),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.of(context).pop(),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: Colors.red[600],
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  child: const Text('Tutup',
                      style: TextStyle(fontWeight: FontWeight.w700)),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // ─────────────────────────────────────────────
  // BUILD
  // ─────────────────────────────────────────────

  @override
  Widget build(BuildContext context) {
    final w = MediaQuery.of(context).size.width;
    return Scaffold(
      body: GestureDetector(
        onTap: () => FocusScope.of(context).unfocus(),
        child: Stack(
        children: [
          _buildBackground(),
          SafeArea(
            child: Center(
              child: w > 900
                  ? _buildDesktopLayout()
                  : _buildMobileLayout(),
            ),
          ),
        ],
      ),
    );
  }

  // ── Background ──
  Widget _buildBackground() {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF1e3a5f), Color(0xFF1d4ed8), Color(0xFF1e3a5f)],
        ),
      ),
      child: Stack(
        children: [
          _floatingCircle(top: -150, left: -100, size: 380, opacity: 0.07,
              anim: _bgAnim, forward: true),
          _floatingCircle(bottom: -100, right: -50, size: 300, opacity: 0.06,
              anim: _bgAnim, forward: false),
        ],
      ),
    );
  }

  Widget _floatingCircle({
    double? top, double? bottom, double? left, double? right,
    required double size, required double opacity,
    required AnimationController anim, required bool forward,
  }) {
    return Positioned(
      top: top, bottom: bottom, left: left, right: right,
      child: AnimatedBuilder(
        animation: anim,
        builder: (_, __) => Transform.translate(
          offset: Offset(
            (forward ? 1 : -1) * 20 * (anim.value - 0.5).abs() * 2,
            (forward ? -1 : 1) * 30 * (anim.value - 0.5).abs() * 2,
          ),
          child: Container(
            width: size, height: size,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: Colors.white.withValues(alpha: opacity),
            ),
          ),
        ),
      ),
    );
  }

  // ── Desktop (website-style side-by-side) ──
  Widget _buildDesktopLayout() {
    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 32),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          Expanded(flex: 45, child: _buildLeftPanel()),
          const SizedBox(width: 48),
          Expanded(flex: 55, child: _buildRightPanel()),
        ],
      ),
    );
  }

  // ── Mobile (stacked, sama konsep dengan website) ──
  Widget _buildMobileLayout() {
    return SingleChildScrollView(
      padding: const EdgeInsets.fromLTRB(20, 32, 20, 32),
      child: Column(
        children: [
          _buildBrandCard(),
          const SizedBox(height: 24),
          _buildRoleSection(),
          const SizedBox(height: 24),
          _buildFormCard(),
        ],
      ),
    );
  }

  // ─────────────────────────────────────────────
  // PANEL KIRI — logo + pilih role
  // ─────────────────────────────────────────────

  Widget _buildLeftPanel() {
    return FadeTransition(
      opacity: Tween<double>(begin: 0, end: 1).animate(
        CurvedAnimation(parent: _cardAnim, curve: const Interval(0, 0.6)),
      ),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          _buildBrandCard(),
          const SizedBox(height: 36),
          _buildRoleSection(),
        ],
      ),
    );
  }

  Widget _buildBrandCard() {
    return Container(
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withValues(alpha: 0.14),
            Colors.white.withValues(alpha: 0.07),
          ],
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withValues(alpha: 0.22), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.18),
            blurRadius: 30,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Column(
        children: [
          // Logo
          Container(
            width: 90, height: 90,
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  Colors.white.withValues(alpha: 0.2),
                  Colors.white.withValues(alpha: 0.1),
                ],
              ),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: Colors.white.withValues(alpha: 0.28), width: 1.5),
            ),
            child: Image.asset(
                'assets/images/hutch-logo.png',
                fit: BoxFit.contain,
                errorBuilder: (_, __, ___) => const Icon(
                    Icons.business_rounded,
                    color: Colors.white,
                    size: 48)),
          ),
          const SizedBox(height: 18),
          const Text('HUTCH PRESTIGE',
              style: TextStyle(
                fontSize: 24, fontWeight: FontWeight.w900,
                color: Colors.white, letterSpacing: 1.2,
              )),
          const SizedBox(height: 6),
          Text('Bag Manufacturing & In-House Brand',
              style: TextStyle(
                fontSize: 12, fontWeight: FontWeight.w500,
                color: Colors.white.withValues(alpha: 0.88),
              ),
              textAlign: TextAlign.center),
          const SizedBox(height: 4),
          Text('Sistem Manajemen Pesanan',
              style: TextStyle(
                fontSize: 11,
                color: Colors.white.withValues(alpha: 0.65),
              )),
        ],
      ),
    );
  }

  Widget _buildRoleSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.person_pin_rounded,
                color: Colors.white.withValues(alpha: 0.85), size: 18),
            const SizedBox(width: 8),
            Text('Pilih Role Anda',
                style: TextStyle(
                  fontSize: 15, fontWeight: FontWeight.w700,
                  color: Colors.white.withValues(alpha: 0.95),
                  letterSpacing: 0.4,
                )),
          ],
        ),
        const SizedBox(height: 18),
        ..._roles.asMap().entries.map((e) => _buildRoleCard(e.key, e.value)),
      ],
    );
  }

  Widget _buildRoleCard(int index, _RoleData role) {
    final isSelected = _selectedRoleId == role.id;
    return ScaleTransition(
      scale: Tween<double>(begin: 0.85, end: 1).animate(
        CurvedAnimation(
          parent: _cardAnim,
          curve: Interval(0.1 + index * 0.1, 0.55 + index * 0.1,
              curve: Curves.elasticOut),
        ),
      ),
      child: GestureDetector(
        onTap: () => _selectRole(role),
        child: Container(
          margin: const EdgeInsets.only(bottom: 14),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: isSelected
                  ? [
                      Colors.white.withValues(alpha: 0.28),
                      Colors.white.withValues(alpha: 0.14),
                    ]
                  : [
                      Colors.white.withValues(alpha: 0.1),
                      Colors.white.withValues(alpha: 0.05),
                    ],
            ),
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: isSelected
                  ? Colors.white.withValues(alpha: 0.65)
                  : Colors.white.withValues(alpha: 0.22),
              width: isSelected ? 2 : 1.5,
            ),
            boxShadow: isSelected
                ? [
                    BoxShadow(
                      color: const Color(0xFF2563eb).withValues(alpha: 0.4),
                      blurRadius: 18,
                      offset: const Offset(0, 6),
                    ),
                  ]
                : [],
          ),
          child: Padding(
            padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 16),
            child: Row(
              children: [
                // Icon circle
                Container(
                  padding: const EdgeInsets.all(11),
                  decoration: BoxDecoration(
                    color: isSelected
                        ? Colors.white.withValues(alpha: 0.22)
                        : Colors.white.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(
                        color: Colors.white.withValues(alpha: 0.18)),
                  ),
                  child: Icon(role.icon, size: 26, color: Colors.white),
                ),
                const SizedBox(width: 14),
                // Labels
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(role.name,
                          style: const TextStyle(
                            fontSize: 14, fontWeight: FontWeight.w700,
                            color: Colors.white, letterSpacing: 0.2,
                          )),
                      const SizedBox(height: 3),
                      Text(role.subtitle,
                          style: TextStyle(
                            fontSize: 11, fontWeight: FontWeight.w500,
                            color: Colors.white.withValues(alpha: 0.68),
                            letterSpacing: 0.5,
                          )),
                    ],
                  ),
                ),
                // Checkmark
                if (isSelected)
                  Container(
                    padding: const EdgeInsets.all(6),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.18),
                      shape: BoxShape.circle,
                      border: Border.all(
                          color: Colors.white.withValues(alpha: 0.4), width: 1.5),
                    ),
                    child: const Icon(Icons.check_rounded,
                        color: Colors.white, size: 16),
                  ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  // ─────────────────────────────────────────────
  // PANEL KANAN — form login
  // ─────────────────────────────────────────────

  Widget _buildRightPanel() {
    return FadeTransition(
      opacity: Tween<double>(begin: 0, end: 1).animate(
        CurvedAnimation(parent: _cardAnim, curve: const Interval(0.3, 1)),
      ),
      child: SlideTransition(
        position: Tween<Offset>(
          begin: const Offset(0.2, 0),
          end: Offset.zero,
        ).animate(
          CurvedAnimation(parent: _cardAnim, curve: Curves.easeOutCubic),
        ),
        child: _buildFormCard(),
      ),
    );
  }

  Widget _buildFormCard() {
    return Container(
      padding: const EdgeInsets.all(32),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(22),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.14),
            blurRadius: 40,
            offset: const Offset(0, 18),
          ),
        ],
      ),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header
            const Text('MASUK',
                style: TextStyle(
                  fontSize: 26, fontWeight: FontWeight.w900,
                  color: Color(0xFF1e3a5f), letterSpacing: 0.4,
                )),
            const SizedBox(height: 6),
            Text(
              _selectedRoleId != null
                  ? 'Masukkan email dan password untuk melanjutkan'
                  : 'Pilih role terlebih dahulu untuk login',
              style: TextStyle(
                  fontSize: 13, color: Colors.grey[500], height: 1.5),
            ),
            const SizedBox(height: 28),

            // Error banner dari API
            Consumer<AuthProvider>(
              builder: (_, auth, __) {
                if (auth.errorMessage == null) return const SizedBox.shrink();
                return Container(
                  margin: const EdgeInsets.only(bottom: 20),
                  padding: const EdgeInsets.all(14),
                  decoration: BoxDecoration(
                    color: Colors.red[50],
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: Colors.red[300]!, width: 1.5),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.error_outline_rounded,
                          color: Colors.red[700], size: 20),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(auth.errorMessage!,
                            style: TextStyle(
                                color: Colors.red[700],
                                fontSize: 13,
                                fontWeight: FontWeight.w500)),
                      ),
                    ],
                  ),
                );
              },
            ),

            // Email field
            _fieldLabel('EMAIL'),
            const SizedBox(height: 8),
            Semantics(
              identifier: 'email',
              child: TextFormField(
                controller: _emailController,
                keyboardType: TextInputType.emailAddress,
                enabled: _selectedRoleId != null,
                decoration: _inputDecoration(
                  hint: _selectedRole?.hint ?? 'email@hutch.id',
                  icon: Icons.email_outlined,
                ),
                style: const TextStyle(
                    fontSize: 14, color: Color(0xFF1e3a5f),
                    fontWeight: FontWeight.w500),
                validator: (v) {
                  if (v == null || v.trim().isEmpty) return 'Email harus diisi';
                  if (!v.contains('@')) return 'Format email tidak valid';
                  return null;
                },
              ),
            ),
            const SizedBox(height: 20),

            // Password field
            _fieldLabel('PASSWORD'),
            const SizedBox(height: 8),
            Semantics(
              identifier: 'password',
              child: TextFormField(
                controller: _passwordController,
                obscureText: !_showPassword,
                enabled: _selectedRoleId != null,
                decoration: _inputDecoration(
                  hint: 'Masukkan password Anda',
                  icon: Icons.lock_outline_rounded,
                  suffix: IconButton(
                    icon: Icon(
                      _showPassword
                          ? Icons.visibility_outlined
                          : Icons.visibility_off_outlined,
                      color: Colors.grey[400],
                      size: 20,
                    ),
                    onPressed: () => setState(() => _showPassword = !_showPassword),
                    padding: EdgeInsets.zero,
                    constraints: const BoxConstraints(),
                  ),
                ),
                style: const TextStyle(
                    fontSize: 14, color: Color(0xFF1e3a5f),
                    fontWeight: FontWeight.w500),
                validator: (v) {
                  if (v == null || v.isEmpty) return 'Password harus diisi';
                  if (v.length < 6) return 'Password minimal 6 karakter';
                  return null;
                },
              ),
            ),
            const SizedBox(height: 28),

            // Tombol login
            Consumer<AuthProvider>(
              builder: (_, auth, __) => SizedBox(
                width: double.infinity,
                height: 52,
                child: ElevatedButton.icon(
                  onPressed: (_selectedRoleId == null || auth.isLoading)
                      ? null
                      : _handleLogin,
                  icon: auth.isLoading
                      ? const SizedBox(
                          width: 18, height: 18,
                          child: CircularProgressIndicator(
                              strokeWidth: 2.5,
                              color: Colors.white),
                        )
                      : const Icon(Icons.login_rounded, size: 20),
                  label: Text(
                    auth.isLoading ? 'Sedang masuk...' : 'MASUK SEKARANG',
                    style: const TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 14,
                        letterSpacing: 0.6),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF2563eb),
                    foregroundColor: Colors.white,
                    disabledBackgroundColor: Colors.grey[300],
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    elevation: 4,
                  ),
                ),
              ),
            ),

            // Info jika role belum dipilih
            if (_selectedRoleId == null) ...[
              const SizedBox(height: 20),
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: const Color(0xFFeff6ff),
                  borderRadius: BorderRadius.circular(10),
                  border: Border.all(color: const Color(0xFFbfdbfe), width: 1.5),
                ),
                child: Row(
                  children: [
                    const Icon(Icons.info_outline_rounded,
                        color: Color(0xFF2563eb), size: 20),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        'Silakan pilih role Anda terlebih dahulu',
                        style: TextStyle(
                          color: Colors.blue[700],
                          fontSize: 13,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
            const SizedBox(height: 20),
            // Tombol kembali ke landing page
            SizedBox(
              width: double.infinity,
              child: TextButton.icon(
                onPressed: _goToLanding,
                icon: Icon(Icons.arrow_back_rounded,
                    size: 16, color: Colors.grey[500]),
                label: Text(
                  'Kembali ke Halaman Utama',
                  style: TextStyle(
                      color: Colors.grey[500],
                      fontSize: 13,
                      fontWeight: FontWeight.w500),
                ),
                style: TextButton.styleFrom(
                  padding: const EdgeInsets.symmetric(vertical: 12),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                    side: BorderSide(color: Colors.grey[300]!),
                  ),
                  backgroundColor: Colors.grey[50],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _fieldLabel(String text) => Text(
        text,
        style: const TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w800,
          color: Color(0xFF1e3a5f),
          letterSpacing: 0.8,
        ),
      );

  InputDecoration _inputDecoration({
    required String hint,
    required IconData icon,
    Widget? suffix,
  }) {
    final enabled = _selectedRoleId != null;
    return InputDecoration(
      hintText: hint,
      hintStyle: TextStyle(color: Colors.grey[400], fontSize: 13),
      prefixIcon: Icon(icon,
          color: enabled ? const Color(0xFF2563eb) : Colors.grey[400],
          size: 20),
      suffixIcon: suffix,
      filled: true,
      fillColor: enabled ? const Color(0xFFeff6ff) : Colors.grey[100],
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: const BorderSide(color: Color(0xFFbfdbfe), width: 1.5),
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: const BorderSide(color: Color(0xFFbfdbfe), width: 1.5),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: const BorderSide(color: Color(0xFF2563eb), width: 2),
      ),
      disabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: BorderSide(color: Colors.grey[200]!, width: 1),
      ),
      contentPadding:
          const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
    );
  }
}