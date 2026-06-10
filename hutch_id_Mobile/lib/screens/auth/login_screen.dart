import 'dart:ui';
import 'dart:async';
import 'dart:math';
import 'package:flutter/material.dart';
import '../../models/user_model.dart';
import '../main_home_screen.dart';
import '../../services/api_service.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen>
    with TickerProviderStateMixin {
  late TextEditingController emailController;
  late TextEditingController passwordController;
  bool _obscurePassword = true;
  bool _isLoading = false;
  String? _selectedRole;
  late AnimationController _logoController;
  late AnimationController _cardController;

  // Demo users dari database
  final List<User> users = [
    User(
      id: '1',
      nama: 'Staf Penjualan',
      role: 'staf_penjualan',
      deskripsi: 'Sales & Penjualan',
      email: 'staf@hutch.id',
      password: 'password',
    ),
    User(
      id: '2',
      nama: 'Operator Gudang',
      role: 'operator_gudang',
      deskripsi: 'Manajemen Gudang',
      email: 'gudang@hutch.id',
      password: 'password',
    ),
    User(
      id: '3',
      nama: 'Administrator',
      role: 'administrator',
      deskripsi: 'Akses Penuh Admin',
      email: 'admin@hutch.id',
      password: 'password',
    ),
  ];

  @override
  void initState() {
    super.initState();
    emailController = TextEditingController();
    passwordController = TextEditingController();

    _selectedRole = 'Staf Penjualan';
    emailController.text = users[0].email; // staf@hutch.id
    passwordController.text = users[0].password; // password

    _logoController = AnimationController(
      duration: const Duration(seconds: 3),
      vsync: this,
    )..repeat(reverse: true);

    _cardController = AnimationController(
      duration: const Duration(milliseconds: 600),
      vsync: this,
    )..forward();
  }

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    _logoController.dispose();
    _cardController.dispose();
    super.dispose();
  }

  Future<void> login() async {
    String email = emailController.text.trim();
    String password = passwordController.text;

    if (email.isEmpty || password.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Email dan Password harus diisi'),
          behavior: SnackBarBehavior.floating,
          backgroundColor: Colors.redAccent,
        ),
      );
      return;
    }

    setState(() => _isLoading = true);

    User? user;

    try {
      user = await ApiService.login(email, password);
    } catch (_) {
      user = null;
    }

    if (user == null) {
      try {
        final matched = users.firstWhere(
          (u) => u.email == email && u.password == password,
        );
        user = matched;
      } catch (_) {
        user = null;
      }
    }

    setState(() => _isLoading = false);

    if (user != null) {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Selamat datang, ${user.nama}!'),
          behavior: SnackBarBehavior.floating,
          backgroundColor: const Color(0xFF10B981),
        ),
      );

      Navigator.of(context).pushReplacement(
        PageRouteBuilder(
          pageBuilder: (context, animation, secondaryAnimation) =>
              MainHomeScreen(user: user!),
          transitionsBuilder: (context, animation, secondaryAnimation, child) {
            return FadeTransition(
              opacity: animation,
              child: SlideTransition(
                position:
                    Tween<Offset>(
                      begin: const Offset(0.0, 0.1),
                      end: Offset.zero,
                    ).animate(
                      CurvedAnimation(
                        parent: animation,
                        curve: Curves.easeOutCubic,
                      ),
                    ),
                child: child,
              ),
            );
          },
          transitionDuration: const Duration(milliseconds: 600),
        ),
      );
    } else {
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Email atau Password salah'),
          backgroundColor: Colors.redAccent,
          behavior: SnackBarBehavior.floating,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0a2463),
      body: Stack(
        children: [
          // Animated gradient background
          Container(
            decoration: const BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  Color(0xFF0a2463),
                  Color(0xFF247ba0),
                  Color(0xFF1b4965),
                  Color(0xFF0f3460),
                ],
                stops: [0.0, 0.25, 0.5, 0.75],
              ),
            ),
          ),

          // Animated background shapes
          _buildAnimatedShapes(),

          // Main content
          SafeArea(
            child: SingleChildScrollView(
              physics: const BouncingScrollPhysics(),
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 30),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const SizedBox(height: 20),
                  _buildAnimatedLogo(),
                  const SizedBox(height: 30),
                  _buildBrandText(),
                  const SizedBox(height: 40),
                  _buildLoginCard(),
                  const SizedBox(height: 20),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }

  // ─── ANIMATED BACKGROUND SHAPES ────────────────────────────────────────────
  Widget _buildAnimatedShapes() {
    return Stack(
      children: [
        // Shape 1 - Top Left
        Positioned(
          top: -100,
          left: -80,
          child: _buildAnimatedShape(
            size: 300,
            color: const Color(0xFF2d7dd2),
            delay: 0,
          ),
        ),
        // Shape 2 - Bottom Right
        Positioned(
          bottom: -100,
          right: -80,
          child: _buildAnimatedShape(
            size: 350,
            color: const Color(0xFF00d4ff),
            delay: 2,
          ),
        ),
        // Shape 3 - Center Left
        Positioned(
          top: MediaQuery.of(context).size.height * 0.4,
          left: -50,
          child: _buildAnimatedShape(
            size: 200,
            color: const Color(0xFF1e88e5),
            delay: 4,
          ),
        ),
      ],
    );
  }

  Widget _buildAnimatedShape({
    required double size,
    required Color color,
    required int delay,
  }) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        color: color.withValues(alpha: 0.08),
        boxShadow: [
          BoxShadow(
            color: color.withValues(alpha: 0.1),
            blurRadius: 100,
            spreadRadius: 50,
          ),
        ],
      ),
    );
  }

  // ─── ANIMATED LOGO ─────────────────────────────────────────────────────────
  Widget _buildAnimatedLogo() {
    return ScaleTransition(
      scale: Tween(begin: 0.8, end: 1.0).animate(
        CurvedAnimation(parent: _cardController, curve: Curves.easeOutBack),
      ),
      child: Container(
        width: 100,
        height: 100,
        decoration: BoxDecoration(
          gradient: const LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [Color(0xFF2575d7), Color(0xFF1e88e5)],
          ),
          borderRadius: BorderRadius.circular(24),
          boxShadow: [
            BoxShadow(
              color: const Color(0xFF2575d7).withValues(alpha: 0.3),
              blurRadius: 30,
              spreadRadius: 5,
            ),
          ],
          border: Border.all(
            color: Colors.white.withValues(alpha: 0.2),
            width: 2,
          ),
        ),
        child: Stack(
          children: [
            // Shimmer effect
            Positioned.fill(
              child: Container(
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(24),
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      Colors.white.withValues(alpha: 0.15),
                      Colors.white.withValues(alpha: 0.0),
                    ],
                  ),
                ),
              ),
            ),
            // Logo icon with animation
            Center(
              child: AnimatedBuilder(
                animation: _logoController,
                builder: (context, child) {
                  return Transform.translate(
                    offset: Offset(0, sin(_logoController.value * 3.14) * 5),
                    child: Transform.rotate(
                      angle: _logoController.value * 0.5,
                      child: const Icon(
                        Icons.shopping_bag_rounded,
                        size: 50,
                        color: Colors.white,
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }

  // ─── BRAND TEXT ────────────────────────────────────────────────────────────
  Widget _buildBrandText() {
    return FadeTransition(
      opacity: _cardController,
      child: Column(
        children: [
          const Text(
            'hutch.id',
            style: TextStyle(
              fontSize: 32,
              fontWeight: FontWeight.bold,
              color: Colors.white,
              letterSpacing: 2,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'BAG MANUFACTURING & IN-HOUSE BRAND',
            style: TextStyle(
              fontSize: 10,
              fontWeight: FontWeight.w600,
              color: Colors.white.withValues(alpha: 0.7),
              letterSpacing: 1.2,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            'Order Flow Management System',
            style: TextStyle(
              fontSize: 13,
              color: Colors.white.withValues(alpha: 0.6),
            ),
          ),
        ],
      ),
    );
  }

  // ─── LOGIN CARD ────────────────────────────────────────────────────────────
  Widget _buildLoginCard() {
    return SlideTransition(
      position: Tween<Offset>(begin: const Offset(0, 0.3), end: Offset.zero)
          .animate(
            CurvedAnimation(
              parent: _cardController,
              curve: Curves.easeOutCubic,
            ),
          ),
      child: Container(
        decoration: BoxDecoration(
          color: Colors.white.withValues(alpha: 0.08),
          borderRadius: BorderRadius.circular(24),
          border: Border.all(
            color: Colors.white.withValues(alpha: 0.15),
            width: 1.5,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.3),
              blurRadius: 40,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(24),
          child: BackdropFilter(
            filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
            child: Padding(
              padding: const EdgeInsets.all(28),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Masuk Portal',
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                      letterSpacing: 0.5,
                    ),
                  ),
                  const SizedBox(height: 6),
                  Text(
                    'Pilih akun atau masukkan kredensial Anda',
                    style: TextStyle(
                      fontSize: 13,
                      color: Colors.white.withValues(alpha: 0.6),
                    ),
                  ),
                  const SizedBox(height: 24),

                  _buildRoleSelection(),

                  const SizedBox(height: 24),
                  _buildEmailField(),
                  const SizedBox(height: 14),
                  _buildPasswordField(),

                  const SizedBox(height: 24),
                  _buildLoginButton(),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }

  // ─── ROLE SELECTION CARDS ──────────────────────────────────────────────────
  Widget _buildRoleSelection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          'Pilih Akun',
          style: TextStyle(
            fontSize: 12,
            fontWeight: FontWeight.w600,
            color: Colors.white.withValues(alpha: 0.7),
            letterSpacing: 0.5,
          ),
        ),
        const SizedBox(height: 12),
        Column(
          children: users.map((user) {
            final isSelected = _selectedRole == user.role;
            return GestureDetector(
              onTap: () {
                setState(() {
                  _selectedRole = user.role;
                  emailController.text = user.email;
                  passwordController.text = user.password;
                });
              },
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 300),
                margin: const EdgeInsets.only(bottom: 10),
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  gradient: isSelected
                      ? const LinearGradient(
                          colors: [Color(0xFF2575d7), Color(0xFF1e88e5)],
                        )
                      : null,
                  color: isSelected
                      ? null
                      : Colors.white.withValues(alpha: 0.05),
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(
                    color: isSelected
                        ? Colors.white.withValues(alpha: 0.3)
                        : Colors.white.withValues(alpha: 0.1),
                    width: 1.5,
                  ),
                  boxShadow: isSelected
                      ? [
                          BoxShadow(
                            color: const Color(
                              0xFF2575d7,
                            ).withValues(alpha: 0.3),
                            blurRadius: 15,
                            spreadRadius: 2,
                          ),
                        ]
                      : null,
                ),
                child: Row(
                  children: [
                    Container(
                      width: 44,
                      height: 44,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: Colors.white.withValues(alpha: 0.15),
                        border: Border.all(
                          color: Colors.white.withValues(alpha: 0.3),
                        ),
                      ),
                      child: Icon(
                        user.role == 'Administrator'
                            ? Icons.admin_panel_settings_rounded
                            : user.role == 'Staf Penjualan'
                            ? Icons.trending_up_rounded
                            : Icons.warehouse_rounded,
                        color: Colors.white,
                        size: 22,
                      ),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            user.nama,
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.w600,
                              color: Colors.white,
                            ),
                          ),
                          const SizedBox(height: 2),
                          Text(
                            user.deskripsi,
                            style: TextStyle(
                              fontSize: 11,
                              color: Colors.white.withValues(alpha: 0.6),
                            ),
                          ),
                        ],
                      ),
                    ),
                    if (isSelected)
                      Container(
                        width: 24,
                        height: 24,
                        decoration: const BoxDecoration(
                          shape: BoxShape.circle,
                          color: Colors.white,
                        ),
                        child: const Icon(
                          Icons.check,
                          color: Color(0xFF2575d7),
                          size: 14,
                        ),
                      ),
                  ],
                ),
              ),
            );
          }).toList(),
        ),
      ],
    );
  }

  // ─── EMAIL FIELD ───────────────────────────────────────────────────────────
  Widget _buildEmailField() {
    return _buildGlassTextField(
      controller: emailController,
      label: 'Alamat Email',
      hint: 'Masukkan email',
      icon: Icons.email_outlined,
      keyboardType: TextInputType.emailAddress,
    );
  }

  // ─── PASSWORD FIELD ────────────────────────────────────────────────────────
  Widget _buildPasswordField() {
    return _buildGlassTextField(
      controller: passwordController,
      label: 'Kata Sandi',
      hint: 'Masukkan password',
      icon: Icons.lock_outlined,
      obscureText: _obscurePassword,
      onToggleObscure: () =>
          setState(() => _obscurePassword = !_obscurePassword),
    );
  }

  Widget _buildGlassTextField({
    required TextEditingController controller,
    required String label,
    required String hint,
    required IconData icon,
    bool obscureText = false,
    VoidCallback? onToggleObscure,
    TextInputType? keyboardType,
  }) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: 0.85),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(
          color: Colors.white.withValues(alpha: 0.3),
          width: 1,
        ),
      ),
      child: TextField(
        controller: controller,
        obscureText: obscureText,
        keyboardType: keyboardType,
        style: const TextStyle(
          color: Color(0xFF0a2463),
          fontSize: 14,
          fontWeight: FontWeight.w500,
        ),
        decoration: InputDecoration(
          labelText: label,
          labelStyle: TextStyle(
            color: const Color(0xFF0a2463).withValues(alpha: 0.6),
            fontSize: 12,
            fontWeight: FontWeight.w500,
          ),
          hintText: hint,
          hintStyle: TextStyle(
            color: const Color(0xFF0a2463).withValues(alpha: 0.4),
            fontSize: 12,
          ),
          prefixIcon: Icon(
            icon,
            color: const Color(0xFF0a2463).withValues(alpha: 0.5),
            size: 18,
          ),
          suffixIcon: GestureDetector(
                  onTap: onToggleObscure,
                  child: Icon(
                    obscureText
                        ? Icons.visibility_off_outlined
                        : Icons.visibility_outlined,
                    color: const Color(0xFF0a2463).withValues(alpha: 0.5),
                    size: 18,
                  ),
                ),
          border: InputBorder.none,
          contentPadding: const EdgeInsets.symmetric(
            horizontal: 16,
            vertical: 14,
          ),
          floatingLabelBehavior: FloatingLabelBehavior.auto,
        ),
      ),
    );
  }

  // ─── LOGIN BUTTON ──────────────────────────────────────────────────────────
  Widget _buildLoginButton() {
    return Container(
      width: double.infinity,
      height: 52,
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          colors: [Color(0xFF2575d7), Color(0xFF1e88e5)],
        ),
        borderRadius: BorderRadius.circular(14),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF2575d7).withValues(alpha: 0.4),
            blurRadius: 20,
            spreadRadius: 2,
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: _isLoading ? null : login,
          borderRadius: BorderRadius.circular(14),
          child: Center(
            child: _isLoading
                ? SizedBox(
                    height: 24,
                    width: 24,
                    child: CircularProgressIndicator(
                      color: Colors.white.withValues(alpha: 0.9),
                      strokeWidth: 2.5,
                    ),
                  )
                : const Text(
                    'Masuk',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.bold,
                      color: Colors.white,
                      letterSpacing: 0.5,
                    ),
                  ),
          ),
        ),
      ),
    );
  }
}
