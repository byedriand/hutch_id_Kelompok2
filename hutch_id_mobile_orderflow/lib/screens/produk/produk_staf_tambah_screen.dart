import 'dart:typed_data';

import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:image_picker/image_picker.dart';

import '../../providers/produk_provider.dart';
import '../../widgets/custom_widgets.dart';
import '../../models/produk.dart';

class ProdukStafTambahScreen extends StatefulWidget {
  /// Jika diisi, layar ini berjalan dalam mode EDIT untuk produk tsb.
  /// Jika null, layar berjalan dalam mode TAMBAH produk baru.
  final Produk? produkToEdit;

  const ProdukStafTambahScreen({super.key, this.produkToEdit});

  @override
  State<ProdukStafTambahScreen> createState() => _ProdukStafTambahScreenState();
}

class _ProdukStafTambahScreenState extends State<ProdukStafTambahScreen> {
  late TextEditingController _namaController;
  late TextEditingController _hargaJualController;
  late TextEditingController _keteranganController;

  XFile? _selectedImage;
  Uint8List? _selectedImageBytes;
  final ImagePicker _imagePicker = ImagePicker();
  bool _isSubmitting = false;

  bool get _isEditMode => widget.produkToEdit != null;

  @override
  void initState() {
    super.initState();
    final p = widget.produkToEdit;
    _namaController = TextEditingController(text: p?.nama ?? '');
    _hargaJualController = TextEditingController(
      text: p?.hargaJual != null ? p!.hargaJual!.toStringAsFixed(0) : '',
    );
    _keteranganController = TextEditingController(text: p?.keterangan ?? '');
  }

  @override
  void dispose() {
    _namaController.dispose();
    _hargaJualController.dispose();
    _keteranganController.dispose();
    super.dispose();
  }

  Future<void> _pickImage() async {
    try {
      final pickedFile = await _imagePicker.pickImage(
        source: ImageSource.gallery,
        maxWidth: 1024,
        maxHeight: 1024,
        imageQuality: 85,
      );

      if (pickedFile != null) {
        final bytes = await pickedFile.readAsBytes();
        setState(() {
          _selectedImage = pickedFile;
          _selectedImageBytes = bytes;
        });
      }
    } catch (e) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Error picking image: $e')));
    }
  }

  void _removeImage() {
    setState(() {
      _selectedImage = null;
      _selectedImageBytes = null;
    });
  }

  Future<void> _handleSubmit() async {
    if (_namaController.text.isEmpty) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Nama produk harus diisi')));
      return;
    }

    if (_hargaJualController.text.isEmpty) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Harga jual harus diisi')));
      return;
    }

    setState(() {
      _isSubmitting = true;
    });

    try {
      final produkProvider = Provider.of<ProdukProvider>(
        context,
        listen: false,
      );

      final formData = {
        'nama': _namaController.text,
        'harga_jual': int.parse(_hargaJualController.text),
        'keterangan': _keteranganController.text.isNotEmpty
            ? _keteranganController.text
            : null,
      };

      final success = _isEditMode
          ? await produkProvider.updateProduk(
              widget.produkToEdit!.id!,
              formData,
              _selectedImage,
            )
          : await produkProvider.createProduk(formData, _selectedImage);

      setState(() {
        _isSubmitting = false;
      });

      if (success && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              _isEditMode
                  ? 'Produk berhasil diperbarui!'
                  : 'Produk berhasil ditambahkan!',
            ),
            backgroundColor: const Color(0xFF10b981),
          ),
        );
        Future.delayed(const Duration(milliseconds: 500), () {
          if (mounted) Navigator.pop(context);
        });
      } else if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(
              produkProvider.errorMessage ??
                  (_isEditMode
                      ? 'Gagal memperbarui produk'
                      : 'Gagal menambahkan produk'),
            ),
          ),
        );
      }
    } catch (e) {
      setState(() {
        _isSubmitting = false;
      });
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(SnackBar(content: Text('Error: $e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: const Color(0xFF2563eb),
        elevation: 0,
        title: Text(
          _isEditMode ? 'Edit Produk' : 'Tambah Produk Baru',
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header Info
            Container(
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: const Color(0xFFF0F9FF),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFF93c5fd), width: 1.5),
              ),
              child: Row(
                children: [
                  Icon(
                    Icons.info_outline_rounded,
                    color: const Color(0xFF1e40af),
                    size: 20,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      _isEditMode
                          ? 'Ubah data produk lalu simpan perubahan'
                          : 'Isi form di bawah untuk menambah produk baru',
                      style: TextStyle(
                        fontSize: 13,
                        color: const Color(0xFF1e40af),
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 28),

            // Nama Produk
            const Text(
              'Nama Produk',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _namaController,
              decoration: InputDecoration(
                hintText: 'Contoh: Tas Kulit Premium',
                prefixIcon: const Icon(Icons.tag, color: Color(0xFF93c5fd)),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFe5e7eb)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFe5e7eb)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(
                    color: Color(0xFF2563eb),
                    width: 2,
                  ),
                ),
              ),
            ),
            const SizedBox(height: 20),

            // Harga Jual
            const Text(
              'Harga Jual (Rp)',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _hargaJualController,
              keyboardType: TextInputType.number,
              decoration: InputDecoration(
                hintText: '0',
                prefixText: 'Rp ',
                prefixIcon: const Icon(
                  Icons.money_rounded,
                  color: Color(0xFF93c5fd),
                ),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFe5e7eb)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFe5e7eb)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(
                    color: Color(0xFF2563eb),
                    width: 2,
                  ),
                ),
              ),
            ),
            const SizedBox(height: 20),

            // Keterangan
            const Text(
              'Keterangan Produk',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 10),
            TextField(
              controller: _keteranganController,
              maxLines: 4,
              decoration: InputDecoration(
                hintText: 'Deskripsi detail produk, bahan, fitur, dll...',
                prefixIcon: const Icon(
                  Icons.note_rounded,
                  color: Color(0xFF93c5fd),
                ),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFe5e7eb)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFe5e7eb)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(
                    color: Color(0xFF2563eb),
                    width: 2,
                  ),
                ),
                alignLabelWithHint: true,
              ),
            ),
            const SizedBox(height: 20),

            // Foto Produk
            const Text(
              'Foto Produk',
              style: TextStyle(fontSize: 15, fontWeight: FontWeight.w700),
            ),
            const SizedBox(height: 10),
            if (_selectedImage == null && widget.produkToEdit?.fotoUrl != null)
              Column(
                children: [
                  Container(
                    width: double.infinity,
                    height: 180,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: const Color(0xFFe5e7eb),
                        width: 2,
                      ),
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: Image.network(
                        widget.produkToEdit!.fotoUrl!,
                        fit: BoxFit.cover,
                        errorBuilder: (context, error, stackTrace) =>
                            const Icon(Icons.image_not_supported),
                      ),
                    ),
                  ),
                  const SizedBox(height: 8),
                  TextButton.icon(
                    onPressed: _pickImage,
                    icon: const Icon(Icons.edit),
                    label: const Text('Ganti Foto'),
                  ),
                ],
              )
            else if (_selectedImage == null)
              GestureDetector(
                onTap: _pickImage,
                child: Container(
                  width: double.infinity,
                  height: 180,
                  decoration: BoxDecoration(
                    color: const Color(0xFFF8FAFC),
                    border: Border.all(
                      color: const Color(0xFFe5e7eb),
                      width: 2,
                      style: BorderStyle.solid,
                    ),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.cloud_upload_outlined,
                        size: 48,
                        color: const Color(0xFF93c5fd),
                      ),
                      const SizedBox(height: 12),
                      const Text(
                        'Klik atau drag foto di sini',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.w600,
                          color: Color(0xFF64748b),
                        ),
                      ),
                      const SizedBox(height: 4),
                      const Text(
                        'JPG, PNG, GIF (Max 5MB)',
                        style: TextStyle(
                          fontSize: 12,
                          color: Color(0xFF94a3b8),
                        ),
                      ),
                    ],
                  ),
                ),
              )
            else
              Column(
                children: [
                  Container(
                    width: double.infinity,
                    height: 200,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(
                        color: const Color(0xFF10b981),
                        width: 2,
                      ),
                    ),
                    child: ClipRRect(
                      borderRadius: BorderRadius.circular(10),
                      child: _selectedImageBytes != null
                          ? Image.memory(
                              _selectedImageBytes!,
                              fit: BoxFit.cover,
                            )
                          : const Center(
                              child: Text('Tidak dapat menampilkan foto'),
                            ),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(
                        Icons.check_circle,
                        color: const Color(0xFF10b981),
                        size: 18,
                      ),
                      const SizedBox(width: 8),
                      const Text(
                        'Foto siap untuk di-upload',
                        style: TextStyle(
                          fontSize: 13,
                          color: Color(0xFF10b981),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  ElevatedButton.icon(
                    onPressed: _removeImage,
                    icon: const Icon(Icons.delete_outline),
                    label: const Text('Hapus Foto'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFFfef2f2),
                      foregroundColor: const Color(0xFFdc2626),
                      elevation: 0,
                    ),
                  ),
                ],
              ),
            const SizedBox(height: 32),

            // Submit Button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _isSubmitting ? null : _handleSubmit,
                icon: _isSubmitting
                    ? SizedBox(
                        width: 18,
                        height: 18,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation<Color>(
                            _isSubmitting ? Colors.white : Colors.blue,
                          ),
                        ),
                      )
                    : const Icon(Icons.check_circle),
                label: Text(
                  _isSubmitting
                      ? 'Menyimpan...'
                      : (_isEditMode
                            ? 'Simpan Perubahan'
                            : 'Simpan Produk Baru'),
                  style: const TextStyle(
                    fontWeight: FontWeight.w700,
                    fontSize: 15,
                  ),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color(0xFF2563eb),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  elevation: 0,
                  disabledBackgroundColor: const Color(0xFFd1d5db),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
