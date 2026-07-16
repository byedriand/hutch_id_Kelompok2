import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/auth_provider.dart';
import '../../providers/user_provider.dart';
import '../../models/user.dart';

class UserManagementScreen extends StatefulWidget {
  const UserManagementScreen({super.key});

  @override
  State<UserManagementScreen> createState() => _UserManagementScreenState();
}

class _UserManagementScreenState extends State<UserManagementScreen> {
  static const _blue = Color(0xFF1e40af);
  static const _blueDark = Color(0xFF1e3a8a);
  static const _red = Color(0xFFdc2626);
  static const _bgPage = Color(0xFFF1F5F9);

  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (mounted) {
        context.read<UserProvider>().fetchUsers();
      }
    });
  }

  // ─── Helper: label role ────────────────────────────────────────────────────
  String _roleLabel(String? role) {
    switch (role) {
      case 'staf_penjualan':
        return 'Staf Penjualan';
      case 'operator_gudang':
        return 'Operator Gudang';
      case 'administrator':
        return 'Administrator';
      case 'pemilik_umkm':
        return 'Pemilik UMKM';
      default:
        return 'Pengguna';
    }
  }

  Color _roleColor(String? role) {
    switch (role) {
      case 'administrator':
        return const Color(0xFF1e40af);
      case 'staf_penjualan':
        return const Color(0xFF0369a1);
      case 'operator_gudang':
        return const Color(0xFF0f766e);
      case 'pemilik_umkm':
        return const Color(0xFF7c3aed);
      default:
        return const Color(0xFF64748b);
    }
  }

  String _formatDate(DateTime? dt) {
    if (dt == null) return '-';
    return DateFormat('dd MMM yyyy HH:mm', 'id_ID').format(dt);
  }

  // ─── Dialog: Tambah / Edit pengguna ───────────────────────────────────────
  void _showUserDialog({User? editUser}) {
    final isEdit = editUser != null;
    final emailCtrl = TextEditingController(text: editUser?.email ?? '');
    final newPassCtrl = TextEditingController();
    String selectedRole = editUser?.role ?? 'staf_penjualan';
    bool obscureNewPass = true;
    final formKey = GlobalKey<FormState>();
    bool isSubmitting = false;
    const currentPasswordText = '••••••••';
    final roles = {
      'administrator': 'Administrator',
      'staf_penjualan': 'Staf Penjualan',
      'operator_gudang': 'Operator Gudang',
      'pemilik_umkm': 'Pemilik UMKM',
    };

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => StatefulBuilder(
        builder: (ctx, setDialogState) => AlertDialog(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(16),
          ),
          titlePadding: EdgeInsets.zero,
          title: Container(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 16),
            decoration: const BoxDecoration(
              color: _blue,
              borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
            ),
            child: Row(
              children: [
                Icon(
                  isEdit ? Icons.edit_rounded : Icons.person_add_rounded,
                  color: Colors.white,
                  size: 22,
                ),
                const SizedBox(width: 10),
                Text(
                  isEdit ? 'Edit Pengguna' : 'Tambah Pengguna',
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                    fontSize: 16,
                  ),
                ),
                const Spacer(),
                GestureDetector(
                  onTap: () => Navigator.of(ctx).pop(),
                  child: const Icon(
                    Icons.close,
                    color: Colors.white70,
                    size: 20,
                  ),
                ),
              ],
            ),
          ),
          content: SizedBox(
            width: double.maxFinite,
            child: SingleChildScrollView(
              child: Form(
                key: formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const SizedBox(height: 4),
                    // Email
                    _buildLabel('Email'),
                    const SizedBox(height: 6),
                    TextFormField(
                      controller: emailCtrl,
                      keyboardType: TextInputType.emailAddress,
                      decoration: _inputDecoration(
                        'Masukkan email',
                        Icons.email_outlined,
                      ),
                      validator: (v) {
                        if (v == null || v.trim().isEmpty)
                          return 'Email wajib diisi';
                        final emailRegex = RegExp(r'^[\w\.\-]+@[\w\-]+\.\w+');
                        if (!emailRegex.hasMatch(v.trim()))
                          return 'Format email tidak valid';
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),

                    // Role
                    _buildLabel('Role'),
                    const SizedBox(height: 6),
                    DropdownButtonFormField<String>(
                      value: selectedRole,
                      decoration: _inputDecoration(
                        'Pilih role',
                        Icons.badge_outlined,
                      ),
                      borderRadius: BorderRadius.circular(10),
                      items: roles.entries
                          .map(
                            (e) => DropdownMenuItem(
                              value: e.key,
                              child: Text(e.value),
                            ),
                          )
                          .toList(),
                      onChanged: (v) {
                        if (v != null) setDialogState(() => selectedRole = v);
                      },
                      validator: (v) =>
                          v == null || v.isEmpty ? 'Role wajib dipilih' : null,
                    ),
                    const SizedBox(height: 16),

                    // Password
                    if (isEdit) ...[
                      _buildLabel('Password saat ini'),
                      const SizedBox(height: 6),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 10,
                        ),
                        decoration: BoxDecoration(
                          border: Border.all(color: Colors.grey.shade300),
                          borderRadius: BorderRadius.circular(10),
                          color: Colors.grey.shade100,
                        ),
                        child: Row(
                          children: [
                            Icon(
                              Icons.lock_outline_rounded,
                              color: Colors.grey,
                              size: 18,
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: Text(
                                currentPasswordText,
                                style: const TextStyle(
                                  fontSize: 14,
                                  color: Colors.grey,
                                  letterSpacing: 2,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        'Password tersimpan dan tidak dapat ditampilkan',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey.shade600,
                        ),
                      ),
                      const SizedBox(height: 16),
                      _buildLabel('Password baru (opsional)'),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: newPassCtrl,
                        obscureText: obscureNewPass,
                        decoration:
                            _inputDecoration(
                              'Masukkan password baru',
                              Icons.lock_outline_rounded,
                            ).copyWith(
                              suffixIcon: Padding(
                                padding: const EdgeInsets.only(right: 8),
                                child: IconButton(
                                  icon: Icon(
                                    obscureNewPass
                                        ? Icons.visibility_off_outlined
                                        : Icons.visibility_outlined,
                                    color: Colors.grey,
                                    size: 20,
                                  ),
                                  onPressed: () => setDialogState(
                                    () => obscureNewPass = !obscureNewPass,
                                  ),
                                ),
                              ),
                            ),
                        validator: (v) {
                          if (v != null && v.isNotEmpty && v.length < 6) {
                            return 'Password minimal 6 karakter';
                          }
                          return null;
                        },
                      ),
                    ] else ...[
                      _buildLabel('Password'),
                      const SizedBox(height: 6),
                      TextFormField(
                        controller: newPassCtrl,
                        obscureText: obscureNewPass,
                        decoration:
                            _inputDecoration(
                              'Masukkan password',
                              Icons.lock_outline_rounded,
                            ).copyWith(
                              suffixIcon: Padding(
                                padding: const EdgeInsets.only(right: 8),
                                child: IconButton(
                                  icon: Icon(
                                    obscureNewPass
                                        ? Icons.visibility_off_outlined
                                        : Icons.visibility_outlined,
                                    color: Colors.grey,
                                    size: 20,
                                  ),
                                  onPressed: () => setDialogState(
                                    () => obscureNewPass = !obscureNewPass,
                                  ),
                                ),
                              ),
                            ),
                        validator: (v) {
                          if (v == null || v.isEmpty) {
                            return 'Password wajib diisi';
                          }
                          if (v.length < 6) {
                            return 'Password minimal 6 karakter';
                          }
                          return null;
                        },
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ),
          actions: [
            TextButton(
              onPressed: isSubmitting ? null : () => Navigator.of(ctx).pop(),
              child: const Text('Batal', style: TextStyle(color: Colors.grey)),
            ),
            ElevatedButton(
              onPressed: isSubmitting
                  ? null
                  : () async {
                      if (!formKey.currentState!.validate()) return;
                      setDialogState(() => isSubmitting = true);

                      final provider = context.read<UserProvider>();
                      Map<String, dynamic> result;

                      if (isEdit) {
                        result = await provider.updateUser(
                          userId: editUser!.id!,
                          email: emailCtrl.text.trim(),
                          role: selectedRole,
                          password: newPassCtrl.text.isEmpty
                              ? null
                              : newPassCtrl.text,
                        );
                      } else {
                        result = await provider.createUser(
                          email: emailCtrl.text.trim(),
                          role: selectedRole,
                          password: newPassCtrl.text,
                        );
                      }

                      if (!ctx.mounted) return;
                      Navigator.of(ctx).pop();

                      _showSnackBar(
                        result['message'] ?? 'Selesai',
                        success: result['success'] == true,
                      );
                    },
              style: ElevatedButton.styleFrom(
                backgroundColor: _blue,
                foregroundColor: Colors.white,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
                padding: const EdgeInsets.symmetric(
                  horizontal: 24,
                  vertical: 12,
                ),
              ),
              child: isSubmitting
                  ? const SizedBox(
                      width: 16,
                      height: 16,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        color: Colors.white,
                      ),
                    )
                  : Text(isEdit ? 'Simpan' : 'Tambah'),
            ),
          ],
        ),
      ),
    );
  }

  // ─── Dialog: Konfirmasi hapus ──────────────────────────────────────────────
  void _showDeleteDialog(User user) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Container(
              padding: const EdgeInsets.all(8),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                borderRadius: BorderRadius.circular(8),
              ),
              child: const Icon(
                Icons.delete_outline_rounded,
                color: _red,
                size: 22,
              ),
            ),
            const SizedBox(width: 12),
            const Text(
              'Hapus Pengguna',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.w700),
            ),
          ],
        ),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const Text(
              'Apakah Anda yakin ingin menghapus pengguna ini?',
              style: TextStyle(color: Color(0xFF475569)),
            ),
            const SizedBox(height: 12),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.red.shade100),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    user.email,
                    style: const TextStyle(
                      fontWeight: FontWeight.w600,
                      color: Color(0xFF0f172a),
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    _roleLabel(user.role),
                    style: TextStyle(color: Colors.red.shade700, fontSize: 12),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 8),
            const Text(
              'Tindakan ini tidak dapat dibatalkan.',
              style: TextStyle(color: _red, fontSize: 12),
            ),
          ],
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(),
            child: const Text('Batal', style: TextStyle(color: Colors.grey)),
          ),
          ElevatedButton(
            onPressed: () async {
              Navigator.of(ctx).pop();
              final result = await context.read<UserProvider>().deleteUser(
                user.id!,
              );
              _showSnackBar(
                result['message'] ?? 'Selesai',
                success: result['success'] == true,
              );
            },
            style: ElevatedButton.styleFrom(
              backgroundColor: _red,
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(10),
              ),
              padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            ),
            child: const Text('Hapus'),
          ),
        ],
      ),
    );
  }

  // ─── Snackbar helper ───────────────────────────────────────────────────────
  void _showSnackBar(String message, {bool success = true}) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Row(
          children: [
            Icon(
              success ? Icons.check_circle_outline : Icons.error_outline,
              color: Colors.white,
              size: 18,
            ),
            const SizedBox(width: 8),
            Expanded(child: Text(message)),
          ],
        ),
        backgroundColor: success ? const Color(0xFF16a34a) : _red,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        margin: const EdgeInsets.all(16),
      ),
    );
  }

  // ─── Input decoration helper ───────────────────────────────────────────────
  InputDecoration _inputDecoration(String hint, IconData icon) {
    return InputDecoration(
      hintText: hint,
      hintStyle: const TextStyle(color: Colors.grey, fontSize: 13),
      prefixIcon: Icon(icon, size: 18, color: Colors.grey),
      contentPadding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
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
        borderSide: const BorderSide(color: _blue, width: 1.5),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: const BorderSide(color: _red),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(10),
        borderSide: const BorderSide(color: _red, width: 1.5),
      ),
      filled: true,
      fillColor: const Color(0xFFF8FAFC),
    );
  }

  Widget _buildLabel(String text) {
    return Text(
      text,
      style: const TextStyle(
        fontSize: 13,
        fontWeight: FontWeight.w600,
        color: Color(0xFF374151),
      ),
    );
  }

  // ─── Card untuk satu user ──────────────────────────────────────────────────
  Widget _buildUserCard(User user, {required bool isSelf}) {
    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: const Color(0xFFE2E8F0)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.04),
            blurRadius: 8,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Row: Avatar + Email + Role Badge
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Avatar
                Container(
                  width: 40,
                  height: 40,
                  decoration: BoxDecoration(
                    color: _roleColor(user.role).withOpacity(0.12),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Center(
                    child: Text(
                      user.email.isNotEmpty ? user.email[0].toUpperCase() : '?',
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.w700,
                        color: _roleColor(user.role),
                      ),
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                // Email & role
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              user.email,
                              style: const TextStyle(
                                fontWeight: FontWeight.w600,
                                fontSize: 14,
                                color: Color(0xFF0f172a),
                              ),
                            ),
                          ),
                          if (isSelf)
                            Container(
                              padding: const EdgeInsets.symmetric(
                                horizontal: 6,
                                vertical: 2,
                              ),
                              decoration: BoxDecoration(
                                color: Colors.orange.shade50,
                                borderRadius: BorderRadius.circular(6),
                                border: Border.all(
                                  color: Colors.orange.shade200,
                                ),
                              ),
                              child: const Text(
                                'Anda',
                                style: TextStyle(
                                  fontSize: 10,
                                  color: Colors.orange,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ),
                        ],
                      ),
                      const SizedBox(height: 5),
                      // Role badge
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 8,
                          vertical: 3,
                        ),
                        decoration: BoxDecoration(
                          color: _roleColor(user.role),
                          borderRadius: BorderRadius.circular(6),
                        ),
                        child: Text(
                          _roleLabel(user.role),
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),
            // Dibuat pada
            Row(
              children: [
                const Icon(
                  Icons.calendar_today_outlined,
                  size: 13,
                  color: Color(0xFF94a3b8),
                ),
                const SizedBox(width: 4),
                Text(
                  'Dibuat: ${_formatDate(user.createdAt)}',
                  style: const TextStyle(
                    fontSize: 11,
                    color: Color(0xFF94a3b8),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),
            const Divider(height: 1, color: Color(0xFFF1F5F9)),
            const SizedBox(height: 10),
            // Action buttons: Edit & Hapus
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () => _showUserDialog(editUser: user),
                    icon: const Icon(Icons.edit_outlined, size: 15),
                    label: const Text('Edit', style: TextStyle(fontSize: 13)),
                    style: OutlinedButton.styleFrom(
                      foregroundColor: _blue,
                      side: const BorderSide(color: _blue),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                      padding: const EdgeInsets.symmetric(vertical: 9),
                    ),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: ElevatedButton.icon(
                    onPressed: isSelf ? null : () => _showDeleteDialog(user),
                    icon: const Icon(Icons.delete_outline_rounded, size: 15),
                    label: const Text('Hapus', style: TextStyle(fontSize: 13)),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: isSelf ? Colors.grey.shade300 : _red,
                      foregroundColor: isSelf ? Colors.grey : Colors.white,
                      disabledBackgroundColor: Colors.grey.shade200,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                      padding: const EdgeInsets.symmetric(vertical: 9),
                      elevation: 0,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  // ─── Build ─────────────────────────────────────────────────────────────────
  @override
  Widget build(BuildContext context) {
    final authProvider = context.read<AuthProvider>();
    final currentUser = authProvider.user;
    final isAdmin = currentUser?.role == 'administrator';

    return Scaffold(
      backgroundColor: _bgPage,
      appBar: AppBar(
        title: const Text(
          'Manajemen Pengguna',
          style: TextStyle(
            fontWeight: FontWeight.w700,
            fontSize: 17,
            color: Colors.white,
          ),
        ),
        backgroundColor: _blue,
        foregroundColor: Colors.white,
        elevation: 0,
        actions: [
          if (isAdmin)
            IconButton(
              icon: const Icon(Icons.refresh_rounded, size: 22),
              tooltip: 'Muat ulang',
              onPressed: () => context.read<UserProvider>().fetchUsers(),
            ),
        ],
      ),
      body: !isAdmin
          ? _buildAccessDenied()
          : Consumer<UserProvider>(
              builder: (context, provider, _) {
                if (provider.isLoading) {
                  return const Center(
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        CircularProgressIndicator(color: _blue),
                        SizedBox(height: 16),
                        Text(
                          'Memuat data pengguna...',
                          style: TextStyle(color: Color(0xFF64748b)),
                        ),
                      ],
                    ),
                  );
                }

                if (provider.errorMessage != null) {
                  return Center(
                    child: Padding(
                      padding: const EdgeInsets.all(32),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Icon(
                            Icons.error_outline,
                            color: _red,
                            size: 48,
                          ),
                          const SizedBox(height: 16),
                          Text(
                            provider.errorMessage!,
                            textAlign: TextAlign.center,
                            style: const TextStyle(
                              color: Color(0xFF475569),
                              fontSize: 14,
                            ),
                          ),
                          const SizedBox(height: 20),
                          ElevatedButton.icon(
                            onPressed: () => provider.fetchUsers(),
                            icon: const Icon(Icons.refresh_rounded, size: 18),
                            label: const Text('Coba Lagi'),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: _blue,
                              foregroundColor: Colors.white,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                }

                return RefreshIndicator(
                  color: _blue,
                  onRefresh: provider.fetchUsers,
                  child: CustomScrollView(
                    slivers: [
                      // ─ Header info ─
                      SliverToBoxAdapter(
                        child: Padding(
                          padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
                          child: Container(
                            padding: const EdgeInsets.all(14),
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                colors: [_blue, _blueDark],
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                              ),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Row(
                              children: [
                                const Icon(
                                  Icons.people_outline_rounded,
                                  color: Colors.white70,
                                  size: 28,
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      const Text(
                                        'Kelola akun dan peran pengguna.',
                                        style: TextStyle(
                                          color: Colors.white,
                                          fontWeight: FontWeight.w600,
                                          fontSize: 13,
                                        ),
                                      ),
                                      const SizedBox(height: 2),
                                      Text(
                                        'Admin dapat mereset email atau password.',
                                        style: TextStyle(
                                          color: Colors.white.withOpacity(0.75),
                                          fontSize: 11,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                // Total users badge
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 10,
                                    vertical: 6,
                                  ),
                                  decoration: BoxDecoration(
                                    color: Colors.white.withOpacity(0.2),
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Text(
                                    '${provider.users.length}',
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.w700,
                                      fontSize: 18,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),

                      // ─ Daftar user ─
                      SliverPadding(
                        padding: const EdgeInsets.fromLTRB(16, 14, 16, 80),
                        sliver: provider.users.isEmpty
                            ? SliverToBoxAdapter(
                                child: Center(
                                  child: Padding(
                                    padding: const EdgeInsets.only(top: 60),
                                    child: Column(
                                      children: [
                                        Icon(
                                          Icons.people_outline_rounded,
                                          size: 56,
                                          color: Colors.grey.shade300,
                                        ),
                                        const SizedBox(height: 12),
                                        const Text(
                                          'Belum ada pengguna terdaftar.',
                                          style: TextStyle(
                                            color: Color(0xFF94a3b8),
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                ),
                              )
                            : SliverList(
                                delegate: SliverChildBuilderDelegate((ctx, i) {
                                  final u = provider.users[i];
                                  final isSelf = u.id == currentUser?.id;
                                  return _buildUserCard(u, isSelf: isSelf);
                                }, childCount: provider.users.length),
                              ),
                      ),
                    ],
                  ),
                );
              },
            ),

      // ─ FAB Tambah Pengguna ─
      floatingActionButton: isAdmin
          ? FloatingActionButton.extended(key: const Key('FloatingActionButton'),
              onPressed: () => _showUserDialog(),
              backgroundColor: _blue,
              foregroundColor: Colors.white,
              icon: const Icon(Icons.person_add_rounded, size: 20),
              label: const Text(
                'Tambah',
                style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
              ),
              elevation: 4,
            )
          : null,
    );
  }

  // ─── Widget: akses ditolak (bukan admin) ──────────────────────────────────
  Widget _buildAccessDenied() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.orange.shade50,
                shape: BoxShape.circle,
              ),
              child: Icon(
                Icons.lock_outline_rounded,
                size: 48,
                color: Colors.orange.shade400,
              ),
            ),
            const SizedBox(height: 20),
            const Text(
              'Akses Terbatas',
              style: TextStyle(
                fontWeight: FontWeight.w700,
                fontSize: 18,
                color: Color(0xFF0f172a),
              ),
            ),
            const SizedBox(height: 10),
            const Text(
              'Fitur Manajemen Pengguna hanya dapat diakses oleh Administrator.',
              textAlign: TextAlign.center,
              style: TextStyle(color: Color(0xFF64748b), fontSize: 14),
            ),
          ],
        ),
      ),
    );
  }
}
