import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _RoleData {
  final String id;
  final String name;
  final String email;
  final IconData icon;
  final String subtitle;

  _RoleData({
    required this.id,
    required this.name,
    required this.email,
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
  String? _selectedRole;
  late AnimationController _backgroundAnimationController;
  late AnimationController _cardAnimationController;

  final List<_RoleData> roles = [
    _RoleData(
      id: 'admin',
      name: 'Administrator',
      email: 'admin@hutch.id',
      icon: Icons.admin_panel_settings_rounded,
      subtitle: 'AKSES PENUH',
    ),
    _RoleData(
      id: 'staff',
      name: 'Staf Penjualan',
      email: 'staf@hutch.id',
      icon: Icons.person_outline_rounded,
      subtitle: 'SALES',
    ),
    _RoleData(
      id: 'warehouse',
      name: 'Operator Gudang',
      email: 'gudang@hutch.id',
      icon: Icons.warehouse_rounded,
      subtitle: 'WAREHOUSE',
    ),
  ];

  @override
  void initState() {
    super.initState();
    _emailController = TextEditingController();
    _passwordController = TextEditingController();

    _backgroundAnimationController = AnimationController(
      duration: const Duration(seconds: 8),
      vsync: this,
    )..repeat();

    _cardAnimationController = AnimationController(
      duration: const Duration(milliseconds: 800),
      vsync: this,
    );
    _cardAnimationController.forward();
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    _backgroundAnimationController.dispose();
    _cardAnimationController.dispose();
    super.dispose();
  }

  void _selectRole(String roleId) {
    setState(() {
      _selectedRole = roleId;
      final role = roles.firstWhere((r) => r.id == roleId);
      _emailController.text = role.email;
      _passwordController.clear();
    });
  }

  void _handleLogin(BuildContext context) async {
    if (_selectedRole == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih role terlebih dahulu')),
      );
      return;
    }

    if (_formKey.currentState!.validate()) {
      final authProvider = Provider.of<AuthProvider>(context, listen: false);
      final success = await authProvider.login(
        _emailController.text,
        _passwordController.text,
      );

      if (success && context.mounted) {
        // Get the role display name
        final selectedRole = roles.firstWhere((r) => r.id == _selectedRole);
        final roleDisplay = selectedRole.name;

        // Show success notification
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Selamat datang, $roleDisplay! Login berhasil.'),
            backgroundColor: Colors.green[600],
            duration: const Duration(seconds: 2),
            action: SnackBarAction(
              label: 'Tutup',
              textColor: Colors.white,
              onPressed: () {},
            ),
          ),
        );

        // Navigate to home after a short delay
        await Future.delayed(const Duration(milliseconds: 500));
        if (context.mounted) {
          Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false);
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final isLargeScreen = size.width > 1000;

    return Scaffold(
      body: Stack(
        children: [
          // Animated Background
          _buildAnimatedBackground(),

          // Main Content
          SafeArea(
            child: Center(
              child: isLargeScreen
                  ? _buildDesktopLayout()
                  : _buildMobileLayout(),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildAnimatedBackground() {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.blue[900]!,
            Colors.blue[800]!,
            Colors.blue[700]!,
            Colors.blue[900]!,
          ],
        ),
      ),
      child: Stack(
        children: [
          // Animated Floating Shapes
          Positioned(
            top: -150,
            left: -100,
            child: AnimatedBuilder(
              animation: _backgroundAnimationController,
              builder: (context, child) {
                return Transform.translate(
                  offset: Offset(
                    30 * (_backgroundAnimationController.value - 0.5).abs() * 2,
                    -50 *
                        (_backgroundAnimationController.value - 0.5).abs() *
                        2,
                  ),
                  child: Container(
                    width: 400,
                    height: 400,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.blue[400]!.withOpacity(0.08),
                    ),
                    child: BackdropFilter(
                      filter: const ColorFilter.mode(
                        Colors.transparent,
                        BlendMode.multiply,
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
          Positioned(
            bottom: -100,
            right: -50,
            child: AnimatedBuilder(
              animation: _backgroundAnimationController,
              builder: (context, child) {
                return Transform.translate(
                  offset: Offset(
                    -30 * _backgroundAnimationController.value * 2,
                    30 * _backgroundAnimationController.value * 2,
                  ),
                  child: Container(
                    width: 300,
                    height: 300,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.cyan.withOpacity(0.08),
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

  Widget _buildDesktopLayout() {
    return SingleChildScrollView(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 40, vertical: 30),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // Left Side - Blue Section
            Expanded(
              flex: 45,
              child: FadeTransition(
                opacity: Tween<double>(begin: 0, end: 1).animate(
                  CurvedAnimation(
                    parent: _cardAnimationController,
                    curve: const Interval(0, 0.5),
                  ),
                ),
                child: SlideTransition(
                  position:
                      Tween<Offset>(
                        begin: const Offset(-0.3, 0),
                        end: Offset.zero,
                      ).animate(
                        CurvedAnimation(
                          parent: _cardAnimationController,
                          curve: Curves.easeOutCubic,
                        ),
                      ),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      // Logo Section
                      _buildLogoSection(),
                      const SizedBox(height: 48),
                      // Divider
                      Container(
                        height: 2,
                        width: 60,
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [
                              Colors.white.withOpacity(0),
                              Colors.white,
                              Colors.white.withOpacity(0),
                            ],
                          ),
                          borderRadius: BorderRadius.circular(2),
                        ),
                      ),
                      const SizedBox(height: 40),
                      // Role Selection
                      Text(
                        'Pilih Role Anda',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w700,
                          color: Colors.white,
                          letterSpacing: 0.8,
                        ),
                      ),
                      const SizedBox(height: 24),
                      ..._buildRoleCards(),
                    ],
                  ),
                ),
              ),
            ),
            const SizedBox(width: 50),
            // Right Side - White Form Section
            Expanded(
              flex: 55,
              child: FadeTransition(
                opacity: Tween<double>(begin: 0, end: 1).animate(
                  CurvedAnimation(
                    parent: _cardAnimationController,
                    curve: const Interval(0.3, 1),
                  ),
                ),
                child: SlideTransition(
                  position:
                      Tween<Offset>(
                        begin: const Offset(0.3, 0),
                        end: Offset.zero,
                      ).animate(
                        CurvedAnimation(
                          parent: _cardAnimationController,
                          curve: Curves.easeOutCubic,
                        ),
                      ),
                  child: Container(
                    padding: const EdgeInsets.all(48),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(24),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withOpacity(0.15),
                          blurRadius: 40,
                          offset: const Offset(0, 20),
                        ),
                        BoxShadow(
                          color: Colors.black.withOpacity(0.08),
                          blurRadius: 60,
                          offset: const Offset(0, 40),
                        ),
                      ],
                    ),
                    child: _buildLoginForm(),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMobileLayout() {
    return SingleChildScrollView(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Logo Section
            FadeTransition(
              opacity: Tween<double>(begin: 0, end: 1).animate(
                CurvedAnimation(
                  parent: _cardAnimationController,
                  curve: Curves.easeInOut,
                ),
              ),
              child: _buildLogoSection(),
            ),
            const SizedBox(height: 32),
            // Role Selection Label
            Text(
              'Pilih Role Anda',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w700,
                color: Colors.white,
                letterSpacing: 0.5,
              ),
            ),
            const SizedBox(height: 20),
            // Role Cards
            ..._buildRoleCards(),
            const SizedBox(height: 32),
            // Login Form Card
            Container(
              padding: const EdgeInsets.all(28),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(20),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.15),
                    blurRadius: 30,
                    offset: const Offset(0, 10),
                  ),
                ],
              ),
              child: _buildLoginForm(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildLogoSection() {
    return Container(
      padding: const EdgeInsets.all(28),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withOpacity(0.15),
            Colors.white.withOpacity(0.08),
          ],
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(0.25), width: 1.5),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.2),
            blurRadius: 30,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        children: [
          // Logo Image
          Container(
            width: 100,
            height: 100,
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [
                  Colors.white.withOpacity(0.2),
                  Colors.white.withOpacity(0.1),
                ],
              ),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(
                color: Colors.white.withOpacity(0.3),
                width: 1.5,
              ),
              boxShadow: [
                BoxShadow(
                  color: Colors.blue[400]!.withOpacity(0.3),
                  blurRadius: 25,
                  offset: const Offset(0, 10),
                ),
              ],
            ),
            child: Image.asset(
              'assets/images/hutch-logo.png',
              fit: BoxFit.contain,
            ),
          ),
          const SizedBox(height: 20),
          // Company Name
          Text(
            'HUTCH PRESTIGE',
            style: TextStyle(
              fontSize: 26,
              fontWeight: FontWeight.w900,
              color: Colors.white,
              letterSpacing: 1.2,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 8),
          // Tagline
          Text(
            'Bag Manufacturing & In-House Brand',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w500,
              color: Colors.white.withOpacity(0.9),
              letterSpacing: 0.3,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 6),
          // System Name
          Text(
            'Sistem Manajemen Pesanan',
            style: TextStyle(
              fontSize: 11,
              fontWeight: FontWeight.w400,
              color: Colors.white.withOpacity(0.75),
            ),
            textAlign: TextAlign.center,
          ),
        ],
      ),
    );
  }

  List<Widget> _buildRoleCards() {
    return roles.asMap().entries.map((entry) {
      final index = entry.key;
      final role = entry.value;
      final isSelected = _selectedRole == role.id;

      return ScaleTransition(
        scale: Tween<double>(begin: 0.8, end: 1).animate(
          CurvedAnimation(
            parent: _cardAnimationController,
            curve: Interval(
              0.1 + (index * 0.1),
              0.5 + (index * 0.1),
              curve: Curves.elasticOut,
            ),
          ),
        ),
        child: GestureDetector(
          onTap: () => _selectRole(role.id),
          child: Container(
            margin: const EdgeInsets.only(bottom: 16),
            decoration: BoxDecoration(
              gradient: isSelected
                  ? LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [
                        Colors.white.withOpacity(0.3),
                        Colors.white.withOpacity(0.15),
                      ],
                    )
                  : LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [
                        Colors.white.withOpacity(0.12),
                        Colors.white.withOpacity(0.06),
                      ],
                    ),
              borderRadius: BorderRadius.circular(14),
              border: Border.all(
                color: isSelected
                    ? Colors.white.withOpacity(0.6)
                    : Colors.white.withOpacity(0.25),
                width: isSelected ? 2 : 1.5,
              ),
              boxShadow: isSelected
                  ? [
                      BoxShadow(
                        color: Colors.blue[600]!.withOpacity(0.4),
                        blurRadius: 20,
                        offset: const Offset(0, 8),
                      ),
                    ]
                  : [],
            ),
            child: Padding(
              padding: const EdgeInsets.symmetric(vertical: 18, horizontal: 16),
              child: Row(
                children: [
                  // Icon
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: isSelected
                          ? Colors.white.withOpacity(0.25)
                          : Colors.white.withOpacity(0.12),
                      borderRadius: BorderRadius.circular(10),
                      border: Border.all(
                        color: Colors.white.withOpacity(0.15),
                        width: 1,
                      ),
                    ),
                    child: Icon(role.icon, size: 28, color: Colors.white),
                  ),
                  const SizedBox(width: 16),
                  // Text
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          role.name,
                          style: TextStyle(
                            fontSize: 15,
                            fontWeight: FontWeight.w700,
                            color: Colors.white,
                            letterSpacing: 0.3,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          role.subtitle,
                          style: TextStyle(
                            fontSize: 11,
                            fontWeight: FontWeight.w500,
                            color: Colors.white.withOpacity(0.7),
                            letterSpacing: 0.5,
                          ),
                        ),
                      ],
                    ),
                  ),
                  // Check Icon
                  if (isSelected)
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: Colors.white.withOpacity(0.2),
                        shape: BoxShape.circle,
                        border: Border.all(
                          color: Colors.white.withOpacity(0.4),
                          width: 1.5,
                        ),
                      ),
                      child: Icon(
                        Icons.check_rounded,
                        color: Colors.white,
                        size: 20,
                      ),
                    ),
                ],
              ),
            ),
          ),
        ),
      );
    }).toList();
  }

  Widget _buildLoginForm() {
    return Form(
      key: _formKey,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          // Header
          Text(
            'MASUK',
            style: TextStyle(
              fontSize: 28,
              fontWeight: FontWeight.w900,
              color: Colors.blue[900],
              letterSpacing: 0.5,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            _selectedRole != null
                ? 'Masukkan password Anda untuk melanjutkan'
                : 'Pilih role terlebih dahulu untuk login',
            style: TextStyle(
              fontSize: 13,
              fontWeight: FontWeight.w500,
              color: Colors.grey[600],
              height: 1.5,
            ),
          ),
          const SizedBox(height: 32),
          // Error Message
          Consumer<AuthProvider>(
            builder: (context, authProvider, _) {
              if (authProvider.errorMessage != null) {
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
                      Icon(
                        Icons.error_outline_rounded,
                        color: Colors.red[700],
                        size: 20,
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          authProvider.errorMessage!,
                          style: TextStyle(
                            color: Colors.red[700],
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
                            height: 1.3,
                          ),
                        ),
                      ),
                    ],
                  ),
                );
              }
              return const SizedBox.shrink();
            },
          ),
          // Email Field (Auto-filled based on selected role)
          if (_selectedRole != null)
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'EMAIL',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w800,
                    color: Colors.blue[900],
                    letterSpacing: 0.8,
                  ),
                ),
                const SizedBox(height: 10),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 14,
                    vertical: 2,
                  ),
                  decoration: BoxDecoration(
                    color: Colors.blue[50],
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: Colors.blue[200]!, width: 1.5),
                  ),
                  child: TextFormField(
                    controller: _emailController,
                    readOnly: true,
                    decoration: InputDecoration(
                      border: InputBorder.none,
                      prefixIcon: Icon(
                        Icons.email_outlined,
                        color: Colors.blue[600],
                        size: 20,
                      ),
                      contentPadding: const EdgeInsets.symmetric(
                        horizontal: 8,
                        vertical: 14,
                      ),
                    ),
                    style: TextStyle(
                      fontSize: 13,
                      fontWeight: FontWeight.w500,
                      color: Colors.blue[900],
                    ),
                  ),
                ),
              ],
            ),
          if (_selectedRole != null) const SizedBox(height: 22),
          // Password Field
          if (_selectedRole != null)
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  'PASSWORD',
                  style: TextStyle(
                    fontSize: 11,
                    fontWeight: FontWeight.w800,
                    color: Colors.blue[900],
                    letterSpacing: 0.8,
                  ),
                ),
                const SizedBox(height: 10),
                TextFormField(
                  controller: _passwordController,
                  obscureText: !_showPassword,
                  decoration: InputDecoration(
                    hintText: 'Masukkan password Anda',
                    hintStyle: TextStyle(color: Colors.grey[400], fontSize: 13),
                    prefixIcon: Icon(
                      Icons.lock_outline_rounded,
                      color: Colors.blue[600],
                      size: 20,
                    ),
                    suffixIcon: IconButton(
                      icon: Icon(
                        _showPassword
                            ? Icons.visibility_off_outlined
                            : Icons.visibility_outlined,
                        color: Colors.blue[600],
                        size: 20,
                      ),
                      onPressed: () {
                        setState(() {
                          _showPassword = !_showPassword;
                        });
                      },
                      splashRadius: 20,
                    ),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(10),
                      borderSide: BorderSide(
                        color: Colors.blue[200]!,
                        width: 1.5,
                      ),
                    ),
                    enabledBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(10),
                      borderSide: BorderSide(
                        color: Colors.blue[200]!,
                        width: 1.5,
                      ),
                    ),
                    focusedBorder: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(10),
                      borderSide: BorderSide(
                        color: Colors.blue[600]!,
                        width: 2,
                      ),
                    ),
                    filled: true,
                    fillColor: Colors.blue[50],
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 16,
                      vertical: 14,
                    ),
                  ),
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w500,
                    color: Colors.blue[900],
                  ),
                  validator: (value) {
                    if (value == null || value.isEmpty) {
                      return 'Password harus diisi';
                    }
                    if (value.length < 6) {
                      return 'Password minimal 6 karakter';
                    }
                    return null;
                  },
                ),
              ],
            ),
          if (_selectedRole != null) const SizedBox(height: 32),
          // Login Button
          if (_selectedRole != null)
            Consumer<AuthProvider>(
              builder: (context, authProvider, _) {
                return SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton.icon(
                    onPressed: authProvider.isLoading
                        ? null
                        : () => _handleLogin(context),
                    icon: authProvider.isLoading
                        ? SizedBox(
                            width: 18,
                            height: 18,
                            child: CircularProgressIndicator(
                              strokeWidth: 2.5,
                              valueColor: AlwaysStoppedAnimation<Color>(
                                Colors.white,
                              ),
                            ),
                          )
                        : const Icon(Icons.login_rounded, size: 20),
                    label: Text(
                      authProvider.isLoading
                          ? 'Sedang masuk...'
                          : 'MASUK SEKARANG',
                      style: const TextStyle(
                        fontWeight: FontWeight.w800,
                        fontSize: 14,
                        letterSpacing: 0.8,
                      ),
                    ),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.blue[700],
                      foregroundColor: Colors.white,
                      disabledBackgroundColor: Colors.grey[400],
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                      ),
                      elevation: 4,
                    ),
                  ),
                );
              },
            ),
          if (_selectedRole == null)
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.blue[50],
                borderRadius: BorderRadius.circular(10),
                border: Border.all(color: Colors.blue[200]!, width: 1.5),
              ),
              child: Row(
                children: [
                  Icon(
                    Icons.info_outline_rounded,
                    color: Colors.blue[600],
                    size: 20,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      'Silahkan pilih role Anda di sebelah kiri terlebih dahulu',
                      style: TextStyle(
                        color: Colors.blue[600],
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                        height: 1.4,
                      ),
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }
}
