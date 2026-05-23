import 'package:flutter/material.dart';
import '../../models/user_model.dart';
import '../main_home_screen.dart';
import '../../services/api_service.dart';
import '../../utils/responsive.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  late TextEditingController emailController;
  late TextEditingController passwordController;
  bool _obscurePassword = true;
  bool _isLoading = false;

  // Data user dummy untuk login
  final List<User> users = [
    User(
      id: '1',
      nama: 'Administrator',
      role: 'Administrator',
      deskripsi: 'Akses Penuh',
      email: 'admin@hutchprestige.com',
      password: 'admin123',
    ),
    User(
      id: '2',
      nama: 'Pemilik UMKM',
      role: 'Pemilik UMKM',
      deskripsi: 'Owner',
      email: 'owner@hutchprestige.com',
      password: 'owner123',
    ),
    User(
      id: '3',
      nama: 'Staf Penjualan',
      role: 'Staf Penjualan',
      deskripsi: 'Sales',
      email: 'sales@hutchprestige.com',
      password: 'sales123',
    ),
    User(
      id: '4',
      nama: 'Operator Gudang',
      role: 'Operator Gudang',
      deskripsi: 'Warehouse',
      email: 'warehouse@hutchprestige.com',
      password: 'warehouse123',
    ),
  ];

  @override
  void initState() {
    super.initState();
    emailController = TextEditingController();
    passwordController = TextEditingController();
  }

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  Future<void> login() async {
    String email = emailController.text.trim();
    String password = passwordController.text;

    if (email.isEmpty || password.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Email dan Password harus diisi')),
      );
      return;
    }

    setState(() => _isLoading = true);

    User? user = await ApiService.login(email, password);

    setState(() => _isLoading = false);

    if (user != null) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Selamat datang, ${user.nama}!')),
      );

      Navigator.of(context).pushReplacement(
        PageRouteBuilder(
          pageBuilder: (context, animation, secondaryAnimation) =>
              MainHomeScreen(user: user),
          transitionsBuilder: (context, animation, secondaryAnimation, child) {
            return FadeTransition(
              opacity: animation,
              child: SlideTransition(
                position: Tween<Offset>(
                  begin: const Offset(0.0, 0.05),
                  end: Offset.zero,
                ).animate(CurvedAnimation(
                  parent: animation,
                  curve: Curves.easeInOutCubic,
                )),
                child: child,
              ),
            );
          },
          transitionDuration: const Duration(milliseconds: 800),
        ),
      );
    } else {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Email atau Password salah'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final bool mobile = Responsive.isMobile(context);
    return Scaffold(
      body: mobile ? _buildMobileLayout() : _buildDesktopLayout(),
    );
  }

  // ─── DESKTOP LAYOUT ────────────────────────────────────────────────────────
  Widget _buildDesktopLayout() {
    return Row(
      children: [
        // Bagian kiri - Logo dan Role Selection
        Expanded(
          flex: 1,
          child: Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFF1e3a8a), Color(0xFF2563eb)],
              ),
            ),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                _buildLogoSection(),
                const SizedBox(height: 40),
                const Text(
                  'Pilih Role Anda',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: Colors.white,
                  ),
                ),
                const SizedBox(height: 20),
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 20),
                  child: Column(
                    children: [
                      _buildRoleCard('Administrator', 'Akses Penuh', Icons.admin_panel_settings),
                      const SizedBox(height: 12),
                      _buildRoleCard('Pemilik UMKM', 'Owner', Icons.person),
                      const SizedBox(height: 12),
                      _buildRoleCard('Staf Penjualan', 'Sales', Icons.people),
                      const SizedBox(height: 12),
                      _buildRoleCard('Operator Gudang', 'Warehouse', Icons.store),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
        // Bagian kanan - Form Login
        Expanded(
          flex: 1,
          child: Container(
            color: Colors.grey[50],
            padding: const EdgeInsets.all(40),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Login',
                  style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 30),
                _buildEmailField(),
                const SizedBox(height: 20),
                _buildPasswordField(),
                const SizedBox(height: 30),
                _buildLoginButton(),
                const SizedBox(height: 20),
                _buildDemoCredentials(),
              ],
            ),
          ),
        ),
      ],
    );
  }

  // ─── MOBILE LAYOUT ─────────────────────────────────────────────────────────
  Widget _buildMobileLayout() {
    return Container(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topCenter,
          end: Alignment.bottomCenter,
          colors: [Color(0xFF1e3a8a), Color(0xFF2563eb), Color(0xFFEFF6FF)],
          stops: [0.0, 0.35, 0.35],
        ),
      ),
      child: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            children: [
              // Header / Logo
              Padding(
                padding: const EdgeInsets.fromLTRB(24, 32, 24, 24),
                child: _buildLogoSection(compact: true),
              ),

              // Card form login
              Container(
                margin: const EdgeInsets.symmetric(horizontal: 20),
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.12),
                      blurRadius: 24,
                      offset: const Offset(0, 8),
                    ),
                  ],
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Masuk ke Akun',
                      style: TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.bold,
                        color: Color(0xFF1e3a8a),
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Pilih role atau masukkan kredensial',
                      style: TextStyle(fontSize: 13, color: Colors.grey[500]),
                    ),
                    const SizedBox(height: 20),

                    // Role quick-select chips
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: users.map((u) => _buildRoleChip(u)).toList(),
                    ),
                    const SizedBox(height: 20),
                    const Divider(),
                    const SizedBox(height: 16),

                    _buildEmailField(),
                    const SizedBox(height: 16),
                    _buildPasswordField(),
                    const SizedBox(height: 24),
                    _buildLoginButton(),
                    const SizedBox(height: 16),
                    _buildDemoCredentials(),
                  ],
                ),
              ),

              const SizedBox(height: 32),
            ],
          ),
        ),
      ),
    );
  }

  // ─── SHARED COMPONENTS ─────────────────────────────────────────────────────

  Widget _buildLogoSection({bool compact = false}) {
    return Column(
      children: [
        Container(
          width: compact ? 72 : 100,
          height: compact ? 72 : 100,
          decoration: BoxDecoration(
            color: Colors.white10,
            borderRadius: BorderRadius.circular(20),
          ),
          child: Icon(
            Icons.shopping_bag,
            size: compact ? 42 : 60,
            color: Colors.white,
          ),
        ),
        SizedBox(height: compact ? 12 : 20),
        Text(
          'HUTCHID',
          style: TextStyle(
            fontSize: compact ? 26 : 32,
            fontWeight: FontWeight.bold,
            color: Colors.white,
            letterSpacing: 2,
          ),
        ),
        const SizedBox(height: 4),
        const Text(
          'Bag Manufacturing & In-House Brand',
          style: TextStyle(fontSize: 12, color: Colors.white70),
          textAlign: TextAlign.center,
        ),
        const SizedBox(height: 2),
        const Text(
          'Sistem Manajemen Pesanan',
          style: TextStyle(fontSize: 11, color: Colors.white60),
        ),
      ],
    );
  }

  Widget _buildEmailField() {
    return TextField(
      controller: emailController,
      keyboardType: TextInputType.emailAddress,
      decoration: InputDecoration(
        labelText: 'Email',
        hintText: 'Masukkan email Anda',
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFF2563eb), width: 2),
        ),
        prefixIcon: const Icon(Icons.email, color: Color(0xFF2563eb)),
        filled: true,
        fillColor: const Color(0xFFF8FAFC),
      ),
    );
  }

  Widget _buildPasswordField() {
    return TextField(
      controller: passwordController,
      obscureText: _obscurePassword,
      decoration: InputDecoration(
        labelText: 'Password',
        hintText: 'Masukkan password Anda',
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: BorderSide(color: Colors.grey[300]!),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFF2563eb), width: 2),
        ),
        prefixIcon: const Icon(Icons.lock, color: Color(0xFF2563eb)),
        suffixIcon: IconButton(
          icon: Icon(
            _obscurePassword ? Icons.visibility_off : Icons.visibility,
            color: Colors.grey,
          ),
          onPressed: () => setState(() => _obscurePassword = !_obscurePassword),
        ),
        filled: true,
        fillColor: const Color(0xFFF8FAFC),
      ),
    );
  }

  Widget _buildLoginButton() {
    return SizedBox(
      width: double.infinity,
      height: 50,
      child: ElevatedButton(
        onPressed: _isLoading ? null : login,
        style: ElevatedButton.styleFrom(
          backgroundColor: const Color(0xFF2563eb),
          disabledBackgroundColor: Colors.grey[300],
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          elevation: 2,
        ),
        child: _isLoading
            ? const SizedBox(
                height: 22,
                width: 22,
                child: CircularProgressIndicator(
                  color: Colors.white,
                  strokeWidth: 2.5,
                ),
              )
            : const Text(
                'Masuk Sekarang',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: Colors.white,
                  letterSpacing: 0.5,
                ),
              ),
      ),
    );
  }

  Widget _buildDemoCredentials() {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.blue[50],
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: Colors.blue[200]!),
      ),
      child: const Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(Icons.info_outline, size: 14, color: Color(0xFF2563eb)),
              SizedBox(width: 6),
              Text(
                'Demo Credentials:',
                style: TextStyle(
                  fontSize: 11,
                  fontWeight: FontWeight.w700,
                  color: Color(0xFF1e3a8a),
                ),
              ),
            ],
          ),
          SizedBox(height: 6),
          Text('📧 admin@hutchprestige.com', style: TextStyle(fontSize: 11)),
          Text('🔑 admin123', style: TextStyle(fontSize: 11)),
        ],
      ),
    );
  }

  // Role chip untuk mobile
  Widget _buildRoleChip(User user) {
    const Map<String, IconData> roleIcons = {
      'Administrator': Icons.admin_panel_settings,
      'Pemilik UMKM': Icons.person,
      'Staf Penjualan': Icons.people,
      'Operator Gudang': Icons.store,
    };
    return GestureDetector(
      onTap: () {
        setState(() {
          emailController.text = user.email;
          passwordController.text = user.password;
        });
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Role dipilih: ${user.role}'),
            duration: const Duration(milliseconds: 800),
            behavior: SnackBarBehavior.floating,
          ),
        );
      },
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
        decoration: BoxDecoration(
          color: const Color(0xFFEFF6FF),
          borderRadius: BorderRadius.circular(20),
          border: Border.all(color: const Color(0xFF2563eb).withValues(alpha: 0.3)),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(roleIcons[user.role] ?? Icons.person, size: 14, color: const Color(0xFF2563eb)),
            const SizedBox(width: 6),
            Text(
              user.role,
              style: const TextStyle(
                fontSize: 12,
                color: Color(0xFF1e3a8a),
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Role card untuk desktop
  Widget _buildRoleCard(String role, String deskripsi, IconData icon) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: () {
          try {
            final user = users.firstWhere((u) => u.role == role);
            setState(() {
              emailController.text = user.email;
              passwordController.text = user.password;
            });
            ScaffoldMessenger.of(context).showSnackBar(
              SnackBar(
                content: Text('Form diisi untuk role: ${user.role}'),
                duration: const Duration(milliseconds: 500),
              ),
            );
          } catch (e) {
            // Ignore
          }
        },
        borderRadius: BorderRadius.circular(8),
        child: Container(
          padding: const EdgeInsets.all(12),
          decoration: BoxDecoration(
            color: Colors.white10,
            border: Border.all(color: Colors.white24),
            borderRadius: BorderRadius.circular(8),
          ),
          child: Row(
            children: [
              Icon(icon, color: Colors.white, size: 24),
              const SizedBox(width: 12),
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    role,
                    style: const TextStyle(
                      color: Colors.white,
                      fontWeight: FontWeight.w600,
                      fontSize: 12,
                    ),
                  ),
                  Text(
                    deskripsi,
                    style: const TextStyle(color: Colors.white70, fontSize: 11),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}