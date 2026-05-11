import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../models/models.dart';
import 'main_screen.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailCtrl = TextEditingController(text: 'nayla@hutch.id');
  final _passCtrl = TextEditingController(text: '••••••••');
  UserRole _role = UserRole.stafPenjualan;
  bool _loading = false;
  bool _obscure = true;

  final _roleNames = {
    UserRole.stafPenjualan: 'Staf Penjualan',
    UserRole.pemilikUmkm: 'Pemilik UMKM',
    UserRole.operatorGudang: 'Operator Gudang',
    UserRole.administrator: 'Administrator',
  };

  void _login() async {
    setState(() => _loading = true);
    await Future.delayed(const Duration(milliseconds: 900));
    if (!mounted) return;
    setState(() => _loading = false);
    Navigator.pushReplacement(
        context, MaterialPageRoute(builder: (_) => MainScreen(role: _role)));
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppColors.navy,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Logo
                Text('hutch.id',
                    style: GoogleFonts.plusJakartaSans(
                      fontSize: 36,
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                      letterSpacing: -1,
                    )),
                const SizedBox(height: 4),
                Text('Bag Manufacturing & In-House Brand',
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 12, color: Colors.white54)),
                const SizedBox(height: 40),

                // Card
                Container(
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: const [
                      BoxShadow(
                          color: Color.fromRGBO(0, 0, 0, 0.2),
                          blurRadius: 30,
                          offset: Offset(0, 10)),
                    ],
                  ),
                  padding: const EdgeInsets.all(24),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('Masuk ke Sistem Operasional',
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 15,
                              fontWeight: FontWeight.w700,
                              color: AppColors.navy)),
                      const SizedBox(height: 4),
                      Text('Modul Manajemen Pesanan (PO)',
                          style: GoogleFonts.plusJakartaSans(
                              fontSize: 12, color: AppColors.gray)),
                      const SizedBox(height: 22),

                      // Email
                      Text('Email', style: _labelStyle),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: _emailCtrl,
                        keyboardType: TextInputType.emailAddress,
                        decoration: const InputDecoration(
                          hintText: 'email@hutch.id',
                          prefixIcon: Icon(Icons.email_outlined,
                              color: AppColors.gray, size: 18),
                        ),
                      ),
                      const SizedBox(height: 14),

                      // Password
                      Text('Password', style: _labelStyle),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: _passCtrl,
                        obscureText: _obscure,
                        decoration: InputDecoration(
                          hintText: 'Masukkan password',
                          prefixIcon: const Icon(Icons.lock_outline,
                              color: AppColors.gray, size: 18),
                          suffixIcon: IconButton(
                            icon: Icon(
                                _obscure
                                    ? Icons.visibility_outlined
                                    : Icons.visibility_off_outlined,
                                color: AppColors.gray,
                                size: 18),
                            onPressed: () =>
                                setState(() => _obscure = !_obscure),
                          ),
                        ),
                      ),
                      const SizedBox(height: 14),

                      // Role
                      Text('Login sebagai', style: _labelStyle),
                      const SizedBox(height: 6),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 14),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          border:
                              Border.all(color: AppColors.border, width: 1.5),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: DropdownButtonHideUnderline(
                          child: DropdownButton<UserRole>(
                            value: _role,
                            isExpanded: true,
                            style: GoogleFonts.plusJakartaSans(
                                fontSize: 13,
                                color: AppColors.navy,
                                fontWeight: FontWeight.w500),
                            items: UserRole.values
                                .map((r) => DropdownMenuItem(
                                    value: r, child: Text(_roleNames[r]!)))
                                .toList(),
                            onChanged: (v) => setState(() => _role = v!),
                          ),
                        ),
                      ),
                      const SizedBox(height: 22),

                      // Button
                      SizedBox(
                        width: double.infinity,
                        height: 48,
                        child: ElevatedButton(
                          onPressed: _loading ? null : _login,
                          child: _loading
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(
                                      color: Colors.white, strokeWidth: 2))
                              : Text('Masuk ke Sistem →',
                                  style: GoogleFonts.plusJakartaSans(
                                      fontSize: 14,
                                      fontWeight: FontWeight.w700)),
                        ),
                      ),
                      const SizedBox(height: 14),
                      Center(
                        child: Text(
                            'Session berbasis cookie · HTTPS · TLS 1.2+',
                            style: GoogleFonts.plusJakartaSans(
                                fontSize: 10.5, color: AppColors.gray)),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }

  TextStyle get _labelStyle => GoogleFonts.plusJakartaSans(
      fontSize: 12, fontWeight: FontWeight.w600, color: AppColors.navy);
}
