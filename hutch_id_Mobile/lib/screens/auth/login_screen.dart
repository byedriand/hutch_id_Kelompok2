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

class _LoginScreenState extends State<LoginScreen> {
  late TextEditingController _emailController;
  late TextEditingController _passwordController;
  final _formKey = GlobalKey<FormState>();
  bool _showPassword = false;
  String? _selectedRole;

  final List<_RoleData> roles = [
    _RoleData(
      id: 'admin',
      name: 'Administrator',
      email: 'admin@hutch.id',
      icon: Icons.workspace_premium_rounded,
      subtitle: 'AKSES PENUH',
    ),
    _RoleData(
      id: 'staff',
      name: 'Staf Penjualan',
      email: 'staf@hutch.id',
      icon: Icons.person_rounded,
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

    // Add listener to email controller to auto-select role based on email typed
    _emailController.addListener(() {
      final email = _emailController.text.trim().toLowerCase();
      final matchingRole = roles.cast<_RoleData?>().firstWhere(
        (r) => r?.email.toLowerCase() == email,
        orElse: () => null,
      );
      if (matchingRole != null && _selectedRole != matchingRole.id) {
        setState(() {
          _selectedRole = matchingRole.id;
        });
      }
    });
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
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
    String? resolvedRole = _selectedRole;
    if (resolvedRole == null) {
      final email = _emailController.text.trim().toLowerCase();
      final matchingRole = roles.cast<_RoleData?>().firstWhere(
        (r) => r?.email.toLowerCase() == email,
        orElse: () => null,
      );
      if (matchingRole != null) {
        resolvedRole = matchingRole.id;
      }
    }

    if (resolvedRole == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih role atau masukkan email yang terdaftar')),
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
        final selectedRole = roles.firstWhere((r) => r.id == resolvedRole);
        final roleDisplay = selectedRole.name;

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

        await Future.delayed(const Duration(milliseconds: 500));
        if (context.mounted) {
          Navigator.pushNamedAndRemoveUntil(context, '/home', (route) => false);
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9), // Light grey background
      body: SingleChildScrollView(
        child: Column(
          children: [
            // Top Section - Blue Gradient Header
            Container(
              width: double.infinity,
              padding: const EdgeInsets.fromLTRB(24, 40, 24, 32),
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Color(0xFF1D4ED8), // Blue 700
                    Color(0xFF2563EB), // Blue 600
                  ],
                ),
                borderRadius: BorderRadius.only(
                  bottomLeft: Radius.circular(24),
                  bottomRight: Radius.circular(24),
                ),
              ),
              child: Column(
                children: [
                  // Circular Logo
                  Container(
                    width: 76,
                    height: 76,
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.white.withValues(alpha: 0.15),
                      shape: BoxShape.circle,
                      border: Border.all(
                        color: Colors.white.withValues(alpha: 0.25),
                        width: 2,
                      ),
                    ),
                    child: Image.asset(
                      'assets/images/hutch-logo.png',
                      fit: BoxFit.contain,
                    ),
                  ),
                  const SizedBox(height: 16),
                  const Text(
                    'HUTCH PRESTIGE',
                    style: TextStyle(
                      fontSize: 22,
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                      letterSpacing: 1,
                    ),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Bag Manufacturing & In-House Brand',
                    style: TextStyle(
                      fontSize: 12,
                      fontWeight: FontWeight.bold,
                      color: Color(0xFF93C5FD),
                    ),
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    'Sistem Manajemen Pesanan',
                    style: TextStyle(
                      fontSize: 11,
                      color: Color(0xFFE2E8F0),
                    ),
                  ),
                  const SizedBox(height: 24),
                  
                  // Role Selection Header
                  const Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.person_outline_rounded, color: Colors.white, size: 16),
                      SizedBox(width: 6),
                      Text(
                        'Pilih Role Anda',
                        style: TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),

                  // Role Cards Stacked Vertically
                  ..._buildRoleCards(),
                ],
              ),
            ),

            // Bottom Section - White Form Container
            Padding(
              padding: const EdgeInsets.all(24.0),
              child: Container(
                padding: const EdgeInsets.all(24),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: BorderRadius.circular(20),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.05),
                      blurRadius: 15,
                      offset: const Offset(0, 5),
                    ),
                  ],
                ),
                child: _buildLoginForm(),
              ),
            ),
          ],
        ),
      ),
    );
  }

  List<Widget> _buildRoleCards() {
    return roles.map((role) {
      final isSelected = _selectedRole == role.id;

      return GestureDetector(
        onTap: () => _selectRole(role.id),
        child: Container(
          width: double.infinity,
          margin: const EdgeInsets.only(bottom: 12),
          decoration: BoxDecoration(
            gradient: isSelected
                ? const LinearGradient(
                    colors: [
                      Color(0xFF1E3A8A), // Blue 900
                      Color(0xFF1D4ED8), // Blue 700
                    ],
                  )
                : LinearGradient(
                    colors: [
                      Colors.white.withValues(alpha: 0.12),
                      Colors.white.withValues(alpha: 0.06),
                    ],
                  ),
            borderRadius: BorderRadius.circular(14),
            border: Border.all(
              color: isSelected
                  ? Colors.white
                  : Colors.white.withValues(alpha: 0.2),
              width: isSelected ? 2 : 1,
            ),
          ),
          child: Stack(
            children: [
              Padding(
                padding: const EdgeInsets.symmetric(vertical: 20, horizontal: 16),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    // Centered Icon
                    Icon(
                      role.icon,
                      size: 28,
                      color: isSelected ? Colors.white : const Color(0xFF93C5FD),
                    ),
                    const SizedBox(height: 8),
                    // Centered Name
                    Text(
                      role.name,
                      textAlign: TextAlign.center,
                      style: const TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w900,
                        color: Colors.white,
                      ),
                    ),
                    const SizedBox(height: 2),
                    // Centered Subtitle
                    Text(
                      role.subtitle,
                      textAlign: TextAlign.center,
                      style: TextStyle(
                        fontSize: 10,
                        fontWeight: FontWeight.bold,
                        color: isSelected
                            ? Colors.white.withValues(alpha: 0.7)
                            : const Color(0xFF93C5FD).withValues(alpha: 0.75),
                        letterSpacing: 0.5,
                      ),
                    ),
                  ],
                ),
              ),
              if (isSelected)
                const Positioned(
                  top: 12,
                  right: 12,
                  child: Icon(
                    Icons.check_circle_rounded,
                    color: Colors.white,
                    size: 18,
                  ),
                ),
            ],
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
          // Header "MASUK"
          Text(
            'MASUK',
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.w900,
              color: Colors.blue[900],
              letterSpacing: 0.5,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            'Masukkan email dan password Anda untuk melanjutkan',
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w500,
              color: Colors.grey[600],
            ),
          ),
          const SizedBox(height: 24),
          
          // Error Message
          Consumer<AuthProvider>(
            builder: (context, authProvider, _) {
              if (authProvider.errorMessage != null) {
                return Container(
                  margin: const EdgeInsets.only(bottom: 16),
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.red[50],
                    borderRadius: BorderRadius.circular(10),
                    border: Border.all(color: Colors.red[300]!, width: 1.5),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.error_outline_rounded, color: Colors.red[700], size: 18),
                      const SizedBox(width: 10),
                      Expanded(
                        child: Text(
                          authProvider.errorMessage!,
                          style: TextStyle(
                            color: Colors.red[700],
                            fontSize: 12,
                            fontWeight: FontWeight.w500,
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

          // Email Field
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(Icons.email_outlined, color: Colors.blue[800], size: 16),
                  const SizedBox(width: 6),
                  Text(
                    'EMAIL',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w900,
                      color: Colors.blue[900],
                      letterSpacing: 0.5,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _emailController,
                readOnly: false,
                keyboardType: TextInputType.emailAddress,
                decoration: InputDecoration(
                  hintText: 'Masukkan email Anda',
                  hintStyle: TextStyle(color: Colors.grey[400], fontSize: 13),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: Colors.blue[200]!, width: 1.5),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: Colors.blue[200]!, width: 1.5),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: Colors.blue[600]!, width: 2),
                  ),
                  filled: true,
                  fillColor: Colors.white,
                ),
                style: TextStyle(
                  fontSize: 13,
                  fontWeight: FontWeight.w500,
                  color: Colors.blue[900],
                ),
                validator: (value) {
                  if (value == null || value.isEmpty) {
                    return 'Email harus diisi';
                  }
                  if (!value.contains('@') || !value.contains('.')) {
                    return 'Format email tidak valid';
                  }
                  return null;
                },
              ),
            ],
          ),
          const SizedBox(height: 18),

          // Password Field
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Icon(Icons.lock_outline_rounded, color: Colors.blue[800], size: 16),
                  const SizedBox(width: 6),
                  Text(
                    'PASSWORD',
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.w900,
                      color: Colors.blue[900],
                      letterSpacing: 0.5,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              TextFormField(
                controller: _passwordController,
                obscureText: !_showPassword,
                decoration: InputDecoration(
                  hintText: 'Masukkan password Anda',
                  hintStyle: TextStyle(color: Colors.grey[400], fontSize: 13),
                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  suffixIcon: IconButton(
                    icon: Icon(
                      _showPassword ? Icons.visibility_off_outlined : Icons.visibility_outlined,
                      color: Colors.blue[600],
                      size: 20,
                    ),
                    onPressed: () {
                      setState(() {
                        _showPassword = !_showPassword;
                      });
                    },
                  ),
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: Colors.blue[200]!, width: 1.5),
                  ),
                  enabledBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: Colors.blue[200]!, width: 1.5),
                  ),
                  focusedBorder: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: BorderSide(color: Colors.blue[600]!, width: 2),
                  ),
                  filled: true,
                  fillColor: Colors.white,
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
          const SizedBox(height: 24),

          // Login Button
          Consumer<AuthProvider>(
            builder: (context, authProvider, _) {
              return SizedBox(
                width: double.infinity,
                height: 50,
                child: ElevatedButton.icon(
                  onPressed: authProvider.isLoading ? null : () => _handleLogin(context),
                  icon: authProvider.isLoading
                      ? const SizedBox(
                          width: 18,
                          height: 18,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                          ),
                        )
                      : const Icon(Icons.login_rounded, size: 20),
                  label: Text(
                    authProvider.isLoading ? 'Sedang masuk...' : 'MASUK SEKARANG',
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 14,
                      letterSpacing: 0.5,
                    ),
                  ),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF1D4ED8), // Blue 700
                    foregroundColor: Colors.white,
                    disabledBackgroundColor: Colors.grey[400],
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                    elevation: 2,
                  ),
                ),
              );
            },
          ),
          const SizedBox(height: 16),

          // Back to Landing page link
          Center(
            child: TextButton.icon(
              onPressed: () {
                Navigator.pushReplacementNamed(context, '/welcome');
              },
              icon: const Icon(Icons.arrow_back_rounded, size: 16),
              label: const Text(
                'Kembali ke Halaman Utama',
                style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13),
              ),
              style: TextButton.styleFrom(
                foregroundColor: Colors.blue[700],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
