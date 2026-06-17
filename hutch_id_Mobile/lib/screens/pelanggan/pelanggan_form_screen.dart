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

class _PelangganFormScreenState extends State<PelangganFormScreen> {
  late TextEditingController _namaController;
  late TextEditingController _emailController;
  late TextEditingController _teleponController;
  late TextEditingController _alamatController;
  late TextEditingController _catatanController;

  final _formKey = GlobalKey<FormState>();
  bool _isSubmitting = false;

  @override
  void initState() {
    super.initState();
    _namaController = TextEditingController();
    _emailController = TextEditingController();
    _teleponController = TextEditingController();
    _alamatController = TextEditingController();
    _catatanController = TextEditingController();

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
      final pelangganProvider = Provider.of<PelangganProvider>(
        context,
        listen: false,
      );
      final pelanggan = pelangganProvider.selectedPelanggan;

      if (pelanggan != null && _namaController.text.isEmpty) {
        _namaController.text = pelanggan.nama;
        _emailController.text = pelanggan.email ?? '';
        _teleponController.text = pelanggan.telepon ?? '';
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
    _alamatController.dispose();
    _catatanController.dispose();
    super.dispose();
  }

  void _handleSubmit() async {
    if (!_formKey.currentState!.validate()) {
      return;
    }

    setState(() {
      _isSubmitting = true;
    });

    final pelangganProvider = Provider.of<PelangganProvider>(
      context,
      listen: false,
    );

    final data = {
      'nama': _namaController.text,
      'email': _emailController.text.isEmpty ? null : _emailController.text,
      'telepon': _teleponController.text.isEmpty
          ? null
          : _teleponController.text,
      'alamat': _alamatController.text.isEmpty ? null : _alamatController.text,
      'catatan': _catatanController.text.isEmpty
          ? null
          : _catatanController.text,
    };

    bool success;
    if (widget.pelangganId != null) {
      success = await pelangganProvider.updatePelanggan(
        widget.pelangganId!,
        data,
      );
    } else {
      success = await pelangganProvider.createPelanggan(data);
    }

    setState(() {
      _isSubmitting = false;
    });

    if (success && mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            widget.pelangganId != null
                ? 'Pelanggan berhasil diupdate'
                : 'Pelanggan berhasil dibuat',
          ),
        ),
      );
      Navigator.pop(context);
    } else if (mounted) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            pelangganProvider.errorMessage ?? 'Gagal menyimpan pelanggan',
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        title: Text(
          widget.pelangganId != null ? 'Edit Pelanggan' : 'Tambah Pelanggan',
          style: const TextStyle(
            fontSize: 15,
            fontWeight: FontWeight.w800,
            color: Colors.white,
          ),
        ),
        elevation: 0,
        backgroundColor: const Color(0xFF0d1b2e),
        foregroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: Consumer<PelangganProvider>(
        builder: (context, pelangganProvider, _) {
          if (widget.pelangganId != null && pelangganProvider.isLoading) {
            return const LoadingWidget();
          }

          return SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Form(
              key: _formKey,
              child: Column(
                children: [
                  const SizedBox(height: 24),
                  CustomTextField(
                    label: 'Nama Pelanggan *',
                    hintText: 'Masukkan nama',
                    controller: _namaController,
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return 'Nama harus diisi';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  CustomTextField(
                    label: 'Email',
                    hintText: 'contoh@email.com',
                    controller: _emailController,
                    keyboardType: TextInputType.emailAddress,
                    validator: (value) {
                      if (value != null &&
                          value.isNotEmpty &&
                          !value.contains('@')) {
                        return 'Email tidak valid';
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  CustomTextField(
                    label: 'No. Telepon',
                    hintText: '08123456789',
                    controller: _teleponController,
                    keyboardType: TextInputType.phone,
                  ),
                  const SizedBox(height: 16),
                  CustomTextField(
                    label: 'Alamat',
                    hintText: 'Masukkan alamat lengkap',
                    controller: _alamatController,
                    maxLines: 3,
                  ),
                  const SizedBox(height: 16),
                  CustomTextField(
                    label: 'Catatan',
                    hintText: 'Tambahkan catatan pelanggan',
                    controller: _catatanController,
                    maxLines: 2,
                  ),
                  const SizedBox(height: 32),
                  CustomButton(
                    label: widget.pelangganId != null ? 'Update' : 'Simpan',
                    isLoading: _isSubmitting,
                    onPressed: _handleSubmit,
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
