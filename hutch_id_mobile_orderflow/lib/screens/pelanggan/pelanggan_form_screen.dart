import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/pelanggan_provider.dart';
import '../../widgets/custom_widgets.dart';

class PelangganFormScreen extends StatefulWidget {
  final int? pelangganId;

  const PelangganFormScreen({super.key, this.pelangganId});

  @override
  State<PelangganFormScreen> createState() => _PelangganFormScreenState();
}

class _PelangganFormScreenState extends State<PelangganFormScreen>
    with SingleTickerProviderStateMixin {
  late TextEditingController _namaController;
  late TextEditingController _emailController;
  late TextEditingController _teleponController;
  late TextEditingController _nomorWhatsappController;
  late TextEditingController _alamatController;
  late TextEditingController _catatanController;

  final _formKey = GlobalKey<FormState>();
  bool _isSubmitting = false;
  late AnimationController _animController;
  late Animation<double> _fadeAnim;
  late Animation<Offset> _slideAnim;

  // Colors matching website palette
  static const Color _primary = Color(0xFF0052A3);
  static const Color _primaryLight = Color(0xFF0066CC);
  static const Color _primaryBg = Color(0xFFE6F2FF);
  static const Color _border = Color(0xFFDBE5F1);
  static const Color _labelColor = Color(0xFF0052A3);
  static const Color _helperColor = Color(0xFF64748B);
  static const Color _errorColor = Color(0xFFEF4444);
  static const Color _bgField = Color(0xFFF8FBFF);

  @override
  void initState() {
    super.initState();
    _namaController = TextEditingController();
    _emailController = TextEditingController();
    _teleponController = TextEditingController();
    _nomorWhatsappController = TextEditingController();
    _alamatController = TextEditingController();
    _catatanController = TextEditingController();

    _animController = AnimationController(
      duration: const Duration(milliseconds: 500),
      vsync: this,
    );
    _fadeAnim = CurvedAnimation(parent: _animController, curve: Curves.easeOut);
    _slideAnim = Tween<Offset>(
      begin: const Offset(0, 0.06),
      end: Offset.zero,
    ).animate(CurvedAnimation(parent: _animController, curve: Curves.easeOut));

    _animController.forward();

    if (widget.pelangganId != null) {
      Future.microtask(() {
        if (mounted) {
          Provider.of<PelangganProvider>(
            context,
            listen: false,
          ).getPelangganDetail(widget.pelangganId!);
        }
      });
    }
  }

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (widget.pelangganId != null) {
      final provider = Provider.of<PelangganProvider>(context, listen: false);
      final pelanggan = provider.selectedPelanggan;
      if (pelanggan != null && _namaController.text.isEmpty) {
        _namaController.text = pelanggan.nama;
        _emailController.text = pelanggan.email ?? '';
        _teleponController.text = pelanggan.telepon ?? '';
        _nomorWhatsappController.text = pelanggan.nomorWhatsapp ?? '';
        _alamatController.text = pelanggan.alamat ?? '';
        _catatanController.text = pelanggan.catatan ?? '';
      }
    }
  }

  @override
  void dispose() {
    _namaController.dispose();
    _emailController.dispose();
    _teleponController.dispose();
    _nomorWhatsappController.dispose();
    _alamatController.dispose();
    _catatanController.dispose();
    _animController.dispose();
    super.dispose();
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() => _isSubmitting = true);

    final provider = Provider.of<PelangganProvider>(context, listen: false);
    final data = {
      'nama': _namaController.text,
      'email': _emailController.text.isEmpty ? null : _emailController.text,
      'telepon': _teleponController.text.isEmpty ? null : _teleponController.text,
      'nomor_whatsapp': _nomorWhatsappController.text.isEmpty
          ? null
          : _nomorWhatsappController.text,
      'alamat': _alamatController.text.isEmpty ? null : _alamatController.text,
      'catatan': _catatanController.text.isEmpty ? null : _catatanController.text,
    };

    bool success;
    if (widget.pelangganId != null) {
      success = await provider.updatePelanggan(widget.pelangganId!, data);
    } else {
      success = await provider.createPelanggan(data);
    }

    setState(() => _isSubmitting = false);

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.pelangganId != null
                ? 'Pelanggan berhasil diupdate'
                : 'Pelanggan berhasil disimpan',
          ),
          backgroundColor: const Color(0xFF059669),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
      );
      Navigator.pop(context);
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(provider.errorMessage ?? 'Gagal menyimpan pelanggan'),
          backgroundColor: _errorColor,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
      );
    }
  }

  // ─── Section Header (mirip website) ────────────────────────────────────────
  Widget _buildSectionHeader(IconData icon, String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 20),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: _primaryBg,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(icon, color: _primaryLight, size: 20),
              ),
              const SizedBox(width: 12),
              Text(
                title,
                style: const TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w800,
                  color: _primary,
                  letterSpacing: 0.1,
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          Stack(
            children: [
              Container(height: 2, color: Color(0x26006ACC)),
              Container(
                height: 2,
                width: 60,
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [_primary, _primaryLight],
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  // ─── Premium Form Field ────────────────────────────────────────────────────
  Widget _buildField({
    required String label,
    required TextEditingController controller,
    String? hintText,
    String? helperText,
    bool required = false,
    bool optional = false,
    TextInputType keyboardType = TextInputType.text,
    int maxLines = 1,
    String? Function(String?)? validator,
  }) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            Text(
              label.toUpperCase(),
              style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w800,
                color: _labelColor,
                letterSpacing: 0.8,
              ),
            ),
            if (required) ...[
              const SizedBox(width: 4),
              const Text(
                '*',
                style: TextStyle(
                  color: _errorColor,
                  fontWeight: FontWeight.w900,
                  fontSize: 13,
                ),
              ),
            ],
            if (optional) ...[
              const SizedBox(width: 6),
              Text(
                '(Opsional)',
                style: TextStyle(
                  fontSize: 10,
                  fontWeight: FontWeight.w600,
                  color: _helperColor.withValues(alpha: 0.8),
                  letterSpacing: 0,
                ),
              ),
            ],
          ],
        ),
        const SizedBox(height: 8),
        TextFormField(
          key: Key(label.toLowerCase().replaceAll(' ', '_')),
          controller: controller,
          keyboardType: keyboardType,
          maxLines: maxLines,
          validator: validator,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w500,
            color: Color(0xFF1E293B),
          ),
          decoration: InputDecoration(
            hintText: hintText,
            hintStyle: TextStyle(
              color: _border.withValues(alpha: 1.5),
              fontWeight: FontWeight.w500,
              fontSize: 14,
            ),
            filled: true,
            fillColor: _bgField,
            contentPadding: const EdgeInsets.symmetric(
              horizontal: 16,
              vertical: 14,
            ),
            border: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: _border, width: 2),
            ),
            enabledBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: _border, width: 2),
            ),
            focusedBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: _primaryLight, width: 2),
            ),
            errorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: _errorColor, width: 2),
            ),
            focusedErrorBorder: OutlineInputBorder(
              borderRadius: BorderRadius.circular(12),
              borderSide: const BorderSide(color: _errorColor, width: 2),
            ),
            errorStyle: const TextStyle(
              color: _errorColor,
              fontSize: 12,
              fontWeight: FontWeight.w600,
            ),
          ),
        ),
        if (helperText != null) ...[
          const SizedBox(height: 6),
          Text(
            helperText,
            style: const TextStyle(
              fontSize: 12,
              color: _helperColor,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ],
    );
  }

  @override
  Widget build(BuildContext context) {
    final isEdit = widget.pelangganId != null;

    return Scaffold(
      backgroundColor: const Color(0xFFEEF4FB),
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 2,
        shadowColor: _primary.withValues(alpha: 0.1),
        leading: IconButton(
          icon: const Icon(Icons.arrow_back_rounded, color: _primary),
          onPressed: () => Navigator.pop(context),
        ),
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              isEdit ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru',
              style: const TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w800,
                color: Color(0xFF0C2340),
              ),
            ),
            Text(
              isEdit
                  ? 'Perbarui data pelanggan'
                  : 'Simpan data pelanggan agar bisa dipilih saat membuat PO.',
              style: const TextStyle(
                fontSize: 11,
                fontWeight: FontWeight.w500,
                color: _helperColor,
              ),
            ),
          ],
        ),
      ),
      body: Consumer<PelangganProvider>(
        builder: (context, provider, _) {
          if (isEdit && provider.isLoading) {
            return const LoadingWidget();
          }

          return FadeTransition(
            opacity: _fadeAnim,
            child: SlideTransition(
              position: _slideAnim,
              child: SingleChildScrollView(
                padding: const EdgeInsets.fromLTRB(16, 20, 16, 32),
                child: Form(
                  key: _formKey,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.stretch,
                    children: [
                      // ── Section 1: Informasi Dasar ────────────────────────
                      Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: _primary.withValues(alpha: 0.1),
                            width: 1.5,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: _primary.withValues(alpha: 0.06),
                              blurRadius: 24,
                              offset: const Offset(0, 8),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildSectionHeader(
                              Icons.account_circle_rounded,
                              'Informasi Dasar Pelanggan',
                            ),
                            _buildField(
                              label: 'Nama Pelanggan',
                              controller: _namaController,
                              hintText: 'Contoh: PT. Jaya Sentosa',
                              helperText:
                                  'Masukkan nama lengkap pelanggan atau nama perusahaan',
                              required: true,
                              validator: (v) =>
                                  (v == null || v.isEmpty) ? 'Nama harus diisi' : null,
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 16),

                      // ── Section 2: Kontak & Alamat ────────────────────────
                      Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: _primary.withValues(alpha: 0.1),
                            width: 1.5,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: _primary.withValues(alpha: 0.06),
                              blurRadius: 24,
                              offset: const Offset(0, 8),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildSectionHeader(
                              Icons.location_on_rounded,
                              'Informasi Kontak & Alamat',
                            ),

                            // Telepon & Email side by side pada layar lebar,
                            // tumpuk pada layar sempit
                            LayoutBuilder(builder: (context, constraints) {
                              final wide = constraints.maxWidth > 480;
                              if (wide) {
                                return Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Expanded(
                                      child: _buildField(
                                        label: 'Nomor Telepon',
                                        controller: _teleponController,
                                        hintText: '0812xxxxxxx',
                                        helperText:
                                            'Nomor telepon yang bisa dihubungi',
                                        required: true,
                                        keyboardType: TextInputType.phone,
                                        validator: (v) =>
                                            (v == null || v.isEmpty)
                                                ? 'Nomor telepon harus diisi'
                                                : null,
                                      ),
                                    ),
                                    const SizedBox(width: 16),
                                    Expanded(
                                      child: _buildField(
                                        label: 'Email',
                                        controller: _emailController,
                                        hintText: 'email@contoh.com',
                                        helperText:
                                            'Alamat email untuk komunikasi lebih lanjut',
                                        optional: true,
                                        keyboardType: TextInputType.emailAddress,
                                        validator: (v) {
                                          if (v != null &&
                                              v.isNotEmpty &&
                                              !v.contains('@')) {
                                            return 'Email tidak valid';
                                          }
                                          return null;
                                        },
                                      ),
                                    ),
                                  ],
                                );
                              }
                              return Column(
                                children: [
                                  _buildField(
                                    label: 'Nomor Telepon',
                                    controller: _teleponController,
                                    hintText: '0812xxxxxxx',
                                    helperText:
                                        'Nomor telepon yang bisa dihubungi',
                                    required: true,
                                    keyboardType: TextInputType.phone,
                                    validator: (v) =>
                                        (v == null || v.isEmpty)
                                            ? 'Nomor telepon harus diisi'
                                            : null,
                                  ),
                                  const SizedBox(height: 18),
                                  _buildField(
                                    label: 'Email',
                                    controller: _emailController,
                                    hintText: 'email@contoh.com',
                                    helperText:
                                        'Alamat email untuk komunikasi lebih lanjut',
                                    optional: true,
                                    keyboardType: TextInputType.emailAddress,
                                    validator: (v) {
                                      if (v != null &&
                                          v.isNotEmpty &&
                                          !v.contains('@')) {
                                        return 'Email tidak valid';
                                      }
                                      return null;
                                    },
                                  ),
                                ],
                              );
                            }),

                            const SizedBox(height: 18),

                            _buildField(
                              label: 'No. WhatsApp',
                              controller: _nomorWhatsappController,
                              hintText: '0812xxxxxxx',
                              helperText: 'Nomor WhatsApp aktif pelanggan',
                              optional: true,
                              keyboardType: TextInputType.phone,
                            ),

                            const SizedBox(height: 18),

                            _buildField(
                              label: 'Alamat Lengkap',
                              controller: _alamatController,
                              hintText:
                                  'Contoh: Jl. Merdeka No. 123, Jakarta Selatan',
                              helperText:
                                  'Masukkan alamat lengkap untuk pengiriman atau referensi',
                              required: true,
                              maxLines: 3,
                              validator: (v) =>
                                  (v == null || v.isEmpty)
                                      ? 'Alamat harus diisi'
                                      : null,
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 16),

                      // ── Section 3: Catatan ─────────────────────────────────
                      Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: _primary.withValues(alpha: 0.1),
                            width: 1.5,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: _primary.withValues(alpha: 0.06),
                              blurRadius: 24,
                              offset: const Offset(0, 8),
                            ),
                          ],
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            _buildSectionHeader(
                              Icons.sticky_note_2_rounded,
                              'Catatan Tambahan',
                            ),
                            _buildField(
                              label: 'Catatan',
                              controller: _catatanController,
                              hintText:
                                  'Tambahkan catatan khusus tentang pelanggan ini',
                              helperText: 'Bersifat opsional, untuk referensi internal',
                              optional: true,
                              maxLines: 3,
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 28),

                      // ── Action Buttons ────────────────────────────────────
                      Container(
                        padding: const EdgeInsets.only(top: 20),
                        decoration: BoxDecoration(
                          border: Border(
                            top: BorderSide(
                              color: _primary.withValues(alpha: 0.1),
                              width: 2,
                            ),
                          ),
                        ),
                        child: Row(
                          children: [
                            // Batal
                            Expanded(
                              child: OutlinedButton.icon(
                                onPressed: _isSubmitting
                                    ? null
                                    : () => Navigator.pop(context),
                                icon: const Icon(Icons.arrow_back_rounded, size: 18),
                                label: const Text('Batal'),
                                style: OutlinedButton.styleFrom(
                                  foregroundColor: _helperColor,
                                  side: BorderSide(
                                    color: _primary.withValues(alpha: 0.25),
                                    width: 2,
                                  ),
                                  padding: const EdgeInsets.symmetric(vertical: 14),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  textStyle: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w700,
                                  ),
                                ),
                              ),
                            ),
                            const SizedBox(width: 12),
                            // Simpan
                            Expanded(
                              flex: 2,
                              child: ElevatedButton.icon(
                                onPressed: _isSubmitting ? null : _handleSubmit,
                                icon: _isSubmitting
                                    ? const SizedBox(
                                        width: 16,
                                        height: 16,
                                        child: CircularProgressIndicator(
                                          strokeWidth: 2,
                                          color: Colors.white,
                                        ),
                                      )
                                    : const Icon(Icons.check_rounded, size: 18),
                                label: Text(
                                  _isSubmitting
                                      ? 'Menyimpan...'
                                      : isEdit
                                          ? 'Update Pelanggan'
                                          : 'Simpan Pelanggan',
                                ),
                                style: ElevatedButton.styleFrom(
                                  backgroundColor: _primary,
                                  foregroundColor: Colors.white,
                                  elevation: 0,
                                  padding: const EdgeInsets.symmetric(vertical: 14),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12),
                                  ),
                                  textStyle: const TextStyle(
                                    fontSize: 14,
                                    fontWeight: FontWeight.w700,
                                  ),
                                  shadowColor: _primary.withValues(alpha: 0.35),
                                ),
                              ),
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
        },
      ),
    );
  }
}
