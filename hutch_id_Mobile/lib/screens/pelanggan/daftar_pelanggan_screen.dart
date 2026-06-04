import 'package:flutter/material.dart';
import '../../models/pelanggan_model.dart';
import '../../widgets/pelanggan_card.dart';
import '../../widgets/shimmer_loading.dart';
import '../../utils/responsive.dart';

class DaftarPelangganScreenWidget extends StatefulWidget {
  final List<Pelanggan> pelangganList;
  final String userRole;
  final bool isLoading;
  final Future<void> Function(String, String, String, String) onAdd;
  final Future<void> Function(String, String, String, String, String) onEdit;
  final Future<void> Function(String) onDelete;

  const DaftarPelangganScreenWidget({
    super.key,
    required this.pelangganList,
    required this.userRole,
    required this.isLoading,
    required this.onAdd,
    required this.onEdit,
    required this.onDelete,
  });

  @override
  State<DaftarPelangganScreenWidget> createState() => _DaftarPelangganScreenState();
}

class _DaftarPelangganScreenState extends State<DaftarPelangganScreenWidget> {
  late List<Pelanggan> pelangganList;
  late TextEditingController searchController;
  late List<Pelanggan> filteredList;

  @override
  void initState() {
    super.initState();
    pelangganList = List.from(widget.pelangganList);
    searchController = TextEditingController();
    filteredList = List.from(pelangganList);
  }

  @override
  void didUpdateWidget(covariant DaftarPelangganScreenWidget oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (oldWidget.pelangganList != widget.pelangganList) {
      setState(() {
        pelangganList = List.from(widget.pelangganList);
        searchPelanggan(searchController.text);
      });
    }
  }

  @override
  void dispose() {
    searchController.dispose();
    super.dispose();
  }

  void searchPelanggan(String query) {
    setState(() {
      if (query.isEmpty) {
        filteredList = List.from(pelangganList);
      } else {
        filteredList = pelangganList
            .where((p) => p.nama.toLowerCase().contains(query.toLowerCase()))
            .toList();
      }
    });
  }

  void editPelanggan(Pelanggan pelanggan) {
    final formKey = GlobalKey<FormState>();
    TextEditingController namaCtrl = TextEditingController(text: pelanggan.nama);
    TextEditingController telpCtrl = TextEditingController(text: pelanggan.telepon);
    TextEditingController alamatCtrl = TextEditingController(text: pelanggan.alamat);
    TextEditingController emailCtrl = TextEditingController(text: pelanggan.email);

    showDialog(
      context: context,
      builder: (BuildContext context) {
        return Dialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          child: Container(
            padding: const EdgeInsets.all(24),
            constraints: const BoxConstraints(maxWidth: 450),
            child: SingleChildScrollView(
              child: Form(
                key: formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Ubah Data Pelanggan',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Perbarui informasi kontak pelanggan Anda di bawah ini.',
                      style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                    ),
                    const SizedBox(height: 20),
                    _buildDialogField(
                      controller: namaCtrl,
                      label: 'Nama Pelanggan',
                      icon: Icons.person_outline,
                      validator: (value) => value == null || value.trim().isEmpty
                          ? 'Nama tidak boleh kosong'
                          : null,
                    ),
                    const SizedBox(height: 12),
                    _buildDialogField(
                      controller: telpCtrl,
                      label: 'Nomor Telepon',
                      icon: Icons.phone_outlined,
                      keyboardType: TextInputType.phone,
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Telepon tidak boleh kosong';
                        }
                        if (!RegExp(r'^\d+$').hasMatch(value.trim())) {
                          return 'Telepon hanya boleh berisi angka';
                        }
                        if (value.trim().length < 8 || value.trim().length > 15) {
                          return 'Telepon minimal 8 dan maksimal 15 digit';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 12),
                    _buildDialogField(
                      controller: alamatCtrl,
                      label: 'Alamat Lengkap',
                      icon: Icons.location_on_outlined,
                      maxLines: 2,
                      validator: (value) => value == null || value.trim().isEmpty
                          ? 'Alamat tidak boleh kosong'
                          : null,
                    ),
                    const SizedBox(height: 12),
                    _buildDialogField(
                      controller: emailCtrl,
                      label: 'Alamat Email',
                      icon: Icons.email_outlined,
                      keyboardType: TextInputType.emailAddress,
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Email tidak boleh kosong';
                        }
                        if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$')
                            .hasMatch(value.trim())) {
                          return 'Format email tidak valid';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        TextButton(
                          onPressed: () => Navigator.pop(context),
                          child: const Text('Batal', style: TextStyle(color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
                        ),
                        const SizedBox(width: 12),
                        ElevatedButton(
                          onPressed: () async {
                            if (formKey.currentState!.validate()) {
                              await widget.onEdit(
                                pelanggan.id,
                                namaCtrl.text.trim(),
                                telpCtrl.text.trim(),
                                alamatCtrl.text.trim(),
                                emailCtrl.text.trim(),
                              );
                              if (context.mounted) {
                                Navigator.pop(context);
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text('${namaCtrl.text} berhasil diperbarui'),
                                    behavior: SnackBarBehavior.floating,
                                    backgroundColor: const Color(0xFF10B981),
                                  ),
                                );
                              }
                            }
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF3B82F6),
                            foregroundColor: Colors.white,
                            elevation: 0,
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                          child: const Text('Simpan Perubahan'),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }

  void deletePelanggan(Pelanggan pelanggan) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.warning_amber_rounded, color: Colors.red),
              SizedBox(width: 8),
              Text('Hapus Pelanggan', style: TextStyle(color: Color(0xFF0F172A), fontWeight: FontWeight.bold, fontSize: 16)),
            ],
          ),
          content: Text(
            'Apakah Anda yakin ingin menghapus pelanggan ${pelanggan.nama}? Tindakan ini akan menghapus semua riwayat terkait.',
            style: const TextStyle(fontSize: 13, color: Color(0xFF475569)),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Batal', style: TextStyle(color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
            ),
            ElevatedButton(
              onPressed: () async {
                await widget.onDelete(pelanggan.id);
                if (context.mounted) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text('${pelanggan.nama} berhasil dihapus'),
                      behavior: SnackBarBehavior.floating,
                      backgroundColor: Colors.redAccent,
                    ),
                  );
                }
              },
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.red,
                foregroundColor: Colors.white,
                elevation: 0,
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
              ),
              child: const Text('Hapus'),
            ),
          ],
        );
      },
    );
  }

  void tambahPelanggan() {
    final formKey = GlobalKey<FormState>();
    TextEditingController namaCtrl = TextEditingController();
    TextEditingController telpCtrl = TextEditingController();
    TextEditingController alamatCtrl = TextEditingController();
    TextEditingController emailCtrl = TextEditingController();

    showDialog(
      context: context,
      builder: (BuildContext context) {
        return Dialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
          child: Container(
            padding: const EdgeInsets.all(24),
            constraints: const BoxConstraints(maxWidth: 450),
            child: SingleChildScrollView(
              child: Form(
                key: formKey,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text(
                      'Tambah Pelanggan Baru',
                      style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                    ),
                    const SizedBox(height: 4),
                    const Text(
                      'Masukkan data profil pelanggan baru ke dalam sistem.',
                      style: TextStyle(fontSize: 11, color: Color(0xFF64748B)),
                    ),
                    const SizedBox(height: 20),
                    _buildDialogField(
                      controller: namaCtrl,
                      label: 'Nama Pelanggan',
                      icon: Icons.person_outline,
                      validator: (value) => value == null || value.trim().isEmpty
                          ? 'Nama tidak boleh kosong'
                          : null,
                    ),
                    const SizedBox(height: 12),
                    _buildDialogField(
                      controller: telpCtrl,
                      label: 'Nomor Telepon',
                      icon: Icons.phone_outlined,
                      keyboardType: TextInputType.phone,
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Telepon tidak boleh kosong';
                        }
                        if (!RegExp(r'^\d+$').hasMatch(value.trim())) {
                          return 'Telepon hanya boleh berisi angka';
                        }
                        if (value.trim().length < 8 || value.trim().length > 15) {
                          return 'Telepon minimal 8 dan maksimal 15 digit';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 12),
                    _buildDialogField(
                      controller: alamatCtrl,
                      label: 'Alamat Lengkap',
                      icon: Icons.location_on_outlined,
                      maxLines: 2,
                      validator: (value) => value == null || value.trim().isEmpty
                          ? 'Alamat tidak boleh kosong'
                          : null,
                    ),
                    const SizedBox(height: 12),
                    _buildDialogField(
                      controller: emailCtrl,
                      label: 'Alamat Email',
                      icon: Icons.email_outlined,
                      keyboardType: TextInputType.emailAddress,
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Email tidak boleh kosong';
                        }
                        if (!RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$')
                            .hasMatch(value.trim())) {
                          return 'Format email tidak valid';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 24),
                    Row(
                      mainAxisAlignment: MainAxisAlignment.end,
                      children: [
                        TextButton(
                          onPressed: () => Navigator.pop(context),
                          child: const Text('Batal', style: TextStyle(color: Color(0xFF64748B), fontWeight: FontWeight.w600)),
                        ),
                        const SizedBox(width: 12),
                        ElevatedButton(
                          onPressed: () async {
                            if (formKey.currentState!.validate()) {
                              await widget.onAdd(
                                namaCtrl.text.trim(),
                                telpCtrl.text.trim(),
                                alamatCtrl.text.trim(),
                                emailCtrl.text.trim(),
                              );
                              if (context.mounted) {
                                Navigator.pop(context);
                                ScaffoldMessenger.of(context).showSnackBar(
                                  SnackBar(
                                    content: Text('${namaCtrl.text} berhasil ditambahkan'),
                                    behavior: SnackBarBehavior.floating,
                                    backgroundColor: const Color(0xFF10B981),
                                  ),
                                );
                              }
                            }
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: const Color(0xFF3B82F6),
                            foregroundColor: Colors.white,
                            elevation: 0,
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                          ),
                          child: const Text('Tambah Pelanggan'),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildDialogField({
    required TextEditingController controller,
    required String label,
    required IconData icon,
    int maxLines = 1,
    TextInputType? keyboardType,
    FormFieldValidator<String>? validator,
  }) {
    return TextFormField(
      controller: controller,
      maxLines: maxLines,
      keyboardType: keyboardType,
      style: const TextStyle(fontSize: 13, color: Color(0xFF0F172A)),
      decoration: InputDecoration(
        labelText: label,
        labelStyle: const TextStyle(fontSize: 11, color: Color(0xFF64748B)),
        prefixIcon: Icon(icon, size: 18, color: const Color(0xFF64748B)),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFFCBD5E1)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFFE2E8F0)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(10),
          borderSide: const BorderSide(color: Color(0xFF3B82F6), width: 1.5),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
      ),
      validator: validator,
    );
  }

  @override
  Widget build(BuildContext context) {
    final bool isMobile = Responsive.isMobile(context);
    
    return LayoutBuilder(
      builder: (context, constraints) {
        final double width = constraints.maxWidth;
        int crossAxisCount = 3;
        double childAspectRatio = 0.95;
        
        if (width < 600) {
          crossAxisCount = 1;
          childAspectRatio = 1.6;
        } else if (width < 950) {
          crossAxisCount = 2;
          childAspectRatio = 1.05;
        }

        return Container(
          color: const Color(0xFFF8FAFC),
          padding: EdgeInsets.all(isMobile ? 16 : 28),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Header
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text(
                          'Daftar Pelanggan',
                          style: TextStyle(fontSize: 26, fontWeight: FontWeight.bold, color: Color(0xFF0F172A)),
                        ),
                        const SizedBox(height: 4),
                        const Text(
                          'Kelola data pelanggan yang digunakan untuk penerbitan Purchase Order.',
                          style: TextStyle(fontSize: 12, color: Color(0xFF64748B)),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 20),

              // Search & Add Actions bar
              Row(
                children: [
                  Expanded(
                    child: Container(
                      decoration: BoxDecoration(
                        boxShadow: [
                          BoxShadow(
                            color: Colors.black.withValues(alpha: 0.01),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: TextField(
                        controller: searchController,
                        onChanged: searchPelanggan,
                        style: const TextStyle(fontSize: 13),
                        decoration: InputDecoration(
                          hintText: 'Cari nama pelanggan...',
                          hintStyle: const TextStyle(color: Color(0xFF94A3B8), fontSize: 13),
                          prefixIcon: const Icon(Icons.search_rounded, color: Color(0xFF64748B), size: 18),
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
                            borderSide: const BorderSide(color: Color(0xFF3B82F6), width: 1.5),
                          ),
                          filled: true,
                          fillColor: Colors.white,
                          contentPadding: const EdgeInsets.symmetric(vertical: 12),
                        ),
                      ),
                    ),
                  ),
                  if (widget.userRole == 'Administrator' || widget.userRole == 'Staf Penjualan') ...[
                    const SizedBox(width: 12),
                    ElevatedButton.icon(
                      onPressed: tambahPelanggan,
                      icon: const Icon(Icons.add, size: 16),
                      label: const Text('Tambah Pelanggan', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: const Color(0xFF3B82F6),
                        foregroundColor: Colors.white,
                        padding: EdgeInsets.symmetric(horizontal: 16, vertical: isMobile ? 14 : 16),
                        elevation: 0,
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                      ),
                    ),
                  ],
                ],
              ),
              const SizedBox(height: 24),

              // Customer Grid
              Expanded(
                child: widget.isLoading
                    ? GridView.builder(
                        gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: crossAxisCount,
                          crossAxisSpacing: 16,
                          mainAxisSpacing: 16,
                          childAspectRatio: childAspectRatio,
                        ),
                        itemCount: 6,
                        itemBuilder: (context, index) {
                          return Container(
                            padding: const EdgeInsets.all(20),
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(16),
                              border: Border.all(color: const Color(0xFFE2E8F0)),
                            ),
                            child: const Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                ShimmerLoading(width: 40, height: 40, borderRadius: 20),
                                SizedBox(height: 16),
                                ShimmerLoading(width: 120, height: 14),
                                SizedBox(height: 8),
                                ShimmerLoading(width: 80, height: 10),
                                SizedBox(height: 8),
                                ShimmerLoading(width: 140, height: 10),
                              ],
                            ),
                          );
                        },
                      )
                    : filteredList.isEmpty
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(20),
                                  decoration: const BoxDecoration(color: Color(0xFFF1F5F9), shape: BoxShape.circle),
                                  child: const Icon(Icons.people_outline_rounded, color: Color(0xFF94A3B8), size: 48),
                                ),
                                const SizedBox(height: 16),
                                const Text(
                                  'Tidak ada pelanggan ditemukan',
                                  style: TextStyle(color: Color(0xFF475569), fontSize: 13, fontWeight: FontWeight.bold),
                                ),
                              ],
                            ),
                          )
                        : GridView.builder(
                            physics: const BouncingScrollPhysics(),
                            gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                              crossAxisCount: crossAxisCount,
                              crossAxisSpacing: 16,
                              mainAxisSpacing: 16,
                              childAspectRatio: childAspectRatio,
                            ),
                            itemCount: filteredList.length,
                            itemBuilder: (context, index) {
                              return PelangganCard(
                                pelanggan: filteredList[index],
                                onEdit: () => editPelanggan(filteredList[index]),
                                onDelete: () => deletePelanggan(filteredList[index]),
                                showActions: widget.userRole == 'Administrator' || widget.userRole == 'Staf Penjualan',
                              );
                            },
                          ),
              ),
            ],
          ),
        );
      }
    );
  }
}