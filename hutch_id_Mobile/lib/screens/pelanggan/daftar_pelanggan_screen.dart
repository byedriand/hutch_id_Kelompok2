import 'package:flutter/material.dart';
import '../../models/pelanggan_model.dart';
import '../../widgets/pelanggan_card.dart';

class DaftarPelangganScreenWidget extends StatefulWidget {
  final List<Pelanggan> pelangganList;
  final Future<void> Function(String, String, String, String) onAdd;
  final Future<void> Function(String, String, String, String, String) onEdit;
  final Future<void> Function(String) onDelete;

  const DaftarPelangganScreenWidget({
    super.key,
    required this.pelangganList,
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
    TextEditingController namaCtrl = TextEditingController(text: pelanggan.nama);
    TextEditingController telpCtrl = TextEditingController(text: pelanggan.telepon);
    TextEditingController alamatCtrl = TextEditingController(text: pelanggan.alamat);
    TextEditingController emailCtrl = TextEditingController(text: pelanggan.email);

    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Edit Pelanggan'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(
                  controller: namaCtrl,
                  decoration: const InputDecoration(labelText: 'Nama Pelanggan'),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: telpCtrl,
                  decoration: const InputDecoration(labelText: 'Telepon'),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: alamatCtrl,
                  decoration: const InputDecoration(labelText: 'Alamat'),
                  maxLines: 2,
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: emailCtrl,
                  decoration: const InputDecoration(labelText: 'Email'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Batal'),
            ),
            TextButton(
              onPressed: () async {
                await widget.onEdit(
                  pelanggan.id,
                  namaCtrl.text,
                  telpCtrl.text,
                  alamatCtrl.text,
                  emailCtrl.text,
                );
                if (context.mounted) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('${namaCtrl.text} berhasil diperbarui')),
                  );
                }
              },
              child: const Text('Simpan'),
            ),
          ],
        );
      },
    );
  }

  void deletePelanggan(Pelanggan pelanggan) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Hapus Pelanggan'),
          content: Text('Apakah Anda yakin ingin menghapus ${pelanggan.nama}?'),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Batal'),
            ),
            TextButton(
              onPressed: () async {
                await widget.onDelete(pelanggan.id);
                if (context.mounted) {
                  Navigator.pop(context);
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(content: Text('${pelanggan.nama} berhasil dihapus')),
                  );
                }
              },
              child: const Text('Hapus', style: TextStyle(color: Colors.red)),
            ),
          ],
        );
      },
    );
  }

  void tambahPelanggan() {
    TextEditingController namaCtrl = TextEditingController();
    TextEditingController telpCtrl = TextEditingController();
    TextEditingController alamatCtrl = TextEditingController();
    TextEditingController emailCtrl = TextEditingController();

    showDialog(
      context: context,
      builder: (BuildContext context) {
        return AlertDialog(
          title: const Text('Tambah Pelanggan Baru'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(
                  controller: namaCtrl,
                  decoration: const InputDecoration(labelText: 'Nama Pelanggan'),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: telpCtrl,
                  decoration: const InputDecoration(labelText: 'Telepon'),
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: alamatCtrl,
                  decoration: const InputDecoration(labelText: 'Alamat'),
                  maxLines: 2,
                ),
                const SizedBox(height: 10),
                TextField(
                  controller: emailCtrl,
                  decoration: const InputDecoration(labelText: 'Email'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.pop(context),
              child: const Text('Batal'),
            ),
            TextButton(
              onPressed: () async {
                if (namaCtrl.text.isNotEmpty && emailCtrl.text.isNotEmpty) {
                  await widget.onAdd(
                    namaCtrl.text,
                    telpCtrl.text,
                    alamatCtrl.text,
                    emailCtrl.text,
                  );
                  if (context.mounted) {
                    Navigator.pop(context);
                    ScaffoldMessenger.of(context).showSnackBar(
                      SnackBar(content: Text('${namaCtrl.text} berhasil ditambahkan')),
                    );
                  }
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(content: Text('Nama dan Email harus diisi')),
                  );
                }
              },
              child: const Text('Tambah'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Container(
      color: Colors.grey[100],
      padding: const EdgeInsets.all(24),
      child: Column(
        children: [
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'Daftar Pelanggan',
                style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 4),
              Text(
                'Kelola data pelanggan yang dapat dibuat saat membuat PO.',
                style: TextStyle(fontSize: 13, color: Colors.grey[600]),
              ),
              const SizedBox(height: 20),
              Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: searchController,
                      onChanged: searchPelanggan,
                      decoration: InputDecoration(
                        hintText: 'Cari nama pelanggan...',
                        prefixIcon: const Icon(Icons.search, color: Colors.grey),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide(color: Colors.grey[300]!),
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide(color: Colors.grey[300]!),
                        ),
                        filled: true,
                        fillColor: Colors.white,
                        contentPadding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  ElevatedButton.icon(
                    onPressed: tambahPelanggan,
                    icon: const Icon(Icons.add, size: 18),
                    label: const Text('Tambah Pelanggan'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF2563eb),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                  ),
                ],
              ),
            ],
          ),
          const SizedBox(height: 20),
          Expanded(
            child: filteredList.isEmpty
                ? Center(
                    child: Text(
                      'Tidak ada pelanggan yang ditemukan',
                      style: TextStyle(color: Colors.grey[600]),
                    ),
                  )
                : GridView.builder(
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 3,
                      crossAxisSpacing: 20,
                      mainAxisSpacing: 20,
                      childAspectRatio: 0.9,
                    ),
                    itemCount: filteredList.length,
                    itemBuilder: (context, index) {
                      return PelangganCard(
                        pelanggan: filteredList[index],
                        onEdit: () => editPelanggan(filteredList[index]),
                        onDelete: () => deletePelanggan(filteredList[index]),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}