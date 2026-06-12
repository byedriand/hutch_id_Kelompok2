import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/pesanan_provider.dart';
import '../../providers/pelanggan_provider.dart';
import '../../providers/produk_provider.dart';
import '../../widgets/custom_widgets.dart';

class PesananFormScreen extends StatefulWidget {
  final int? pesananId;

  const PesananFormScreen({super.key, this.pesananId});

  @override
  State<PesananFormScreen> createState() => _PesananFormScreenState();
}

class _PesananFormScreenState extends State<PesananFormScreen> {
  late TextEditingController _catatanController;
  late TextEditingController _spesifikasiController;
  late TextEditingController _jumlahController;

  String _selectedStatus = 'menunggu_konfirmasi';
  bool _isSubmitting = false;

  // For create new pesanan
  int? _selectedPelangganId;
  int? _selectedProdukId;
  DateTime _tanggalPesanan = DateTime.now();
  DateTime _tanggalPengiriman = DateTime.now().add(const Duration(days: 7));
  final List<Map<String, dynamic>> _orderItems = [];

  final dateFormat = DateFormat('dd MMM yyyy', 'id_ID');
  final numberFormat = NumberFormat('#,##0', 'id_ID');

  @override
  void initState() {
    super.initState();
    _catatanController = TextEditingController();
    _spesifikasiController = TextEditingController();
    _jumlahController = TextEditingController();

    if (widget.pesananId != null) {
      Future.microtask(() {
        final pesananProvider = Provider.of<PesananProvider>(
          context,
          listen: false,
        );
        pesananProvider.getPesananDetail(widget.pesananId!);
      });
    } else {
      // Load pelanggan and produk for create form
      Future.microtask(() {
        final pelangganProvider = Provider.of<PelangganProvider>(
          context,
          listen: false,
        );
        final produkProvider = Provider.of<ProdukProvider>(
          context,
          listen: false,
        );
        pelangganProvider.fetchPelanggan();
        produkProvider.fetchProduk();
      });
    }
  }

  @override
  void dispose() {
    _catatanController.dispose();
    _spesifikasiController.dispose();
    _jumlahController.dispose();
    super.dispose();
  }

  void _addOrderItem() {
    if (_selectedProdukId == null || _jumlahController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih produk dan masukkan jumlah')),
      );
      return;
    }

    final jumlah = int.tryParse(_jumlahController.text) ?? 0;
    if (jumlah <= 0) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Jumlah harus lebih dari 0')),
      );
      return;
    }

    setState(() {
      _orderItems.add({
        'produk_id': _selectedProdukId,
        'jumlah': jumlah,
        'spesifikasi': _spesifikasiController.text,
      });

      _selectedProdukId = null;
      _jumlahController.clear();
      _spesifikasiController.clear();
    });
  }

  void _removeOrderItem(int index) {
    setState(() {
      _orderItems.removeAt(index);
    });
  }

  Future<void> _selectDate(BuildContext context, bool isDeliveryDate) async {
    final pickedDate = await showDatePicker(
      context: context,
      initialDate: isDeliveryDate ? _tanggalPengiriman : _tanggalPesanan,
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (pickedDate != null) {
      setState(() {
        if (isDeliveryDate) {
          _tanggalPengiriman = pickedDate;
        } else {
          _tanggalPesanan = pickedDate;
        }
      });
    }
  }

  Future<void> _handleCreatePesanan() async {
    if (_selectedPelangganId == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih pelanggan terlebih dahulu')),
      );
      return;
    }

    if (_orderItems.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Tambahkan minimal satu produk')),
      );
      return;
    }

    setState(() {
      _isSubmitting = true;
    });

    final pesananProvider = Provider.of<PesananProvider>(
      context,
      listen: false,
    );

    final data = {
      'pelanggan_id': _selectedPelangganId,
      'tanggal_pesanan': _tanggalPesanan.toString().split(' ')[0],
      'tanggal_pengiriman': _tanggalPengiriman.toString().split(' ')[0],
      'items': _orderItems,
    };

    final success = await pesananProvider.createPesanan(data);

    setState(() {
      _isSubmitting = false;
    });

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Pesanan berhasil dibuat!'),
          backgroundColor: Color(0xFF10b981),
        ),
      );
      Future.delayed(const Duration(milliseconds: 500), () {
        Navigator.pop(context);
      });
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            pesananProvider.errorMessage ?? 'Gagal membuat pesanan',
          ),
        ),
      );
    }
  }

  Future<void> _handleUpdatePesanan() async {
    setState(() {
      _isSubmitting = true;
    });

    final pesananProvider = Provider.of<PesananProvider>(
      context,
      listen: false,
    );

    final success = await pesananProvider.updatePesananStatus(
      widget.pesananId!,
      _selectedStatus,
    );

    setState(() {
      _isSubmitting = false;
    });

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Status pesanan berhasil diupdate')),
      );
      Navigator.pop(context);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            pesananProvider.errorMessage ?? 'Gagal mengupdate pesanan',
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: const Color(0xFF2563eb),
        elevation: 0,
        title: Text(
          widget.pesananId != null ? 'Edit Pesanan' : 'Buat Pesanan Baru',
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w700,
          ),
        ),
      ),
      body: widget.pesananId != null ? _buildUpdateForm() : _buildCreateForm(),
    );
  }

  Widget _buildUpdateForm() {
    return Consumer<PesananProvider>(
      builder: (context, pesananProvider, _) {
        if (pesananProvider.isLoading) {
          return const LoadingWidget(message: 'Memuat pesanan...');
        }

        final pesanan = pesananProvider.selectedPesanan;

        if (pesanan == null) {
          return const Center(child: Text('Pesanan tidak ditemukan'));
        }

        if (_catatanController.text.isEmpty && pesanan.catatan != null) {
          _catatanController.text = pesanan.catatan ?? '';
        }
        _selectedStatus = pesanan.status ?? 'menunggu_konfirmasi';

        return SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: const Color(0xFFF0F9FF),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: const Color(0xFF93c5fd), width: 1),
                ),
                child: Row(
                  children: [
                    Icon(
                      Icons.info_outline_rounded,
                      color: const Color(0xFF1e40af),
                      size: 20,
                    ),
                    const SizedBox(width: 10),
                    Expanded(
                      child: Text(
                        'Nomor PO: ${pesanan.nomorPo}',
                        style: const TextStyle(
                          fontSize: 13,
                          color: Color(0xFF1e40af),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),
              const Text(
                'Perbarui Status',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: Color(0xFF0c2340),
                ),
              ),
              const SizedBox(height: 12),
              DropdownButtonFormField<String>(
                initialValue: _selectedStatus,
                items: const [
                  DropdownMenuItem(
                    value: 'menunggu_konfirmasi',
                    child: Text('Menunggu Konfirmasi'),
                  ),
                  DropdownMenuItem(
                    value: 'dikonfirmasi',
                    child: Text('Dikonfirmasi'),
                  ),
                  DropdownMenuItem(
                    value: 'dalam_produksi',
                    child: Text('Dalam Produksi'),
                  ),
                  DropdownMenuItem(
                    value: 'siap_kirim',
                    child: Text('Siap Kirim'),
                  ),
                  DropdownMenuItem(value: 'selesai', child: Text('Selesai')),
                  DropdownMenuItem(
                    value: 'dibatalkan',
                    child: Text('Dibatalkan'),
                  ),
                ],
                onChanged: (value) {
                  setState(() {
                    _selectedStatus = value ?? 'menunggu_konfirmasi';
                  });
                },
                decoration: InputDecoration(
                  border: OutlineInputBorder(
                    borderRadius: BorderRadius.circular(10),
                    borderSide: const BorderSide(color: Color(0xFFe5e7eb)),
                  ),
                  contentPadding: const EdgeInsets.symmetric(
                    horizontal: 14,
                    vertical: 14,
                  ),
                ),
              ),
              const SizedBox(height: 20),
              const Text(
                'Catatan',
                style: TextStyle(
                  fontSize: 15,
                  fontWeight: FontWeight.w700,
                  color: Color(0xFF0c2340),
                ),
              ),
              const SizedBox(height: 12),
              CustomTextField(
                label: '',
                hintText: 'Tambahkan catatan pesanan',
                controller: _catatanController,
                maxLines: 3,
              ),
              const SizedBox(height: 32),
              CustomButton(
                label: _isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan',
                isLoading: _isSubmitting,
                onPressed: () => _handleUpdatePesanan(),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildCreateForm() {
    return Consumer3<PelangganProvider, ProdukProvider, PesananProvider>(
      builder:
          (context, pelangganProvider, produkProvider, pesananProvider, _) {
            final pelangganList = pelangganProvider.pelangganList;
            final produkList = produkProvider.produkList;

            return SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Pelanggan Selection
                  const Text(
                    'Pilih Pelanggan',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF0c2340),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Container(
                    decoration: BoxDecoration(
                      border: Border.all(color: const Color(0xFFe5e7eb)),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: DropdownButton<int>(
                      value: _selectedPelangganId,
                      hint: const Text('Pilih pelanggan'),
                      isExpanded: true,
                      underline: Container(),
                      padding: const EdgeInsets.symmetric(
                        horizontal: 14,
                        vertical: 14,
                      ),
                      items: pelangganList
                          .map(
                            (p) => DropdownMenuItem<int>(
                              value: p.id,
                              child: Text(p.nama),
                            ),
                          )
                          .toList(),
                      onChanged: (value) {
                        setState(() {
                          _selectedPelangganId = value;
                        });
                      },
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Tanggal Pesanan
                  const Text(
                    'Tanggal Pesanan',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF0c2340),
                    ),
                  ),
                  const SizedBox(height: 12),
                  InkWell(
                    onTap: () => _selectDate(context, false),
                    child: Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        border: Border.all(color: const Color(0xFFe5e7eb)),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.calendar_today, color: Colors.grey[600]),
                          const SizedBox(width: 10),
                          Text(
                            dateFormat.format(_tanggalPesanan),
                            style: const TextStyle(fontSize: 14),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Tanggal Pengiriman
                  const Text(
                    'Tanggal Pengiriman',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF0c2340),
                    ),
                  ),
                  const SizedBox(height: 12),
                  InkWell(
                    onTap: () => _selectDate(context, true),
                    child: Container(
                      padding: const EdgeInsets.all(14),
                      decoration: BoxDecoration(
                        border: Border.all(color: const Color(0xFFe5e7eb)),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Row(
                        children: [
                          Icon(Icons.calendar_today, color: Colors.grey[600]),
                          const SizedBox(width: 10),
                          Text(
                            dateFormat.format(_tanggalPengiriman),
                            style: const TextStyle(fontSize: 14),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Product Selection
                  const Text(
                    'Tambahkan Produk',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w700,
                      color: Color(0xFF0c2340),
                    ),
                  ),
                  const SizedBox(height: 12),
                  Container(
                    decoration: BoxDecoration(
                      border: Border.all(color: const Color(0xFFe5e7eb)),
                      borderRadius: BorderRadius.circular(10),
                    ),
                    child: DropdownButton<int>(
                      value: _selectedProdukId,
                      hint: const Text('Pilih produk'),
                      isExpanded: true,
                      underline: Container(),
                      padding: const EdgeInsets.symmetric(
                        horizontal: 14,
                        vertical: 14,
                      ),
                      items: produkList
                          .map(
                            (p) => DropdownMenuItem<int>(
                              value: p.id,
                              child: Text(p.nama),
                            ),
                          )
                          .toList(),
                      onChanged: (value) {
                        setState(() {
                          _selectedProdukId = value;
                        });
                      },
                    ),
                  ),
                  const SizedBox(height: 14),
                  CustomTextField(
                    label: 'Jumlah',
                    hintText: '0',
                    controller: _jumlahController,
                    keyboardType: TextInputType.number,
                  ),
                  const SizedBox(height: 12),
                  CustomTextField(
                    label: 'Spesifikasi (Opsional)',
                    hintText: 'Contoh: Custom packaging, warna khusus',
                    controller: _spesifikasiController,
                    maxLines: 2,
                  ),
                  const SizedBox(height: 14),
                  ElevatedButton.icon(
                    onPressed: _addOrderItem,
                    icon: const Icon(Icons.add),
                    label: const Text('Tambah Item'),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF3b82f6),
                      foregroundColor: Colors.white,
                      minimumSize: const Size(double.infinity, 48),
                    ),
                  ),
                  const SizedBox(height: 20),

                  // Order Items List
                  if (_orderItems.isNotEmpty) ...[
                    const Text(
                      'Produk yang Ditambahkan',
                      style: TextStyle(
                        fontSize: 15,
                        fontWeight: FontWeight.w700,
                        color: Color(0xFF0c2340),
                      ),
                    ),
                    const SizedBox(height: 12),
                    ..._orderItems.asMap().entries.map((entry) {
                      final index = entry.key;
                      final item = entry.value;
                      final produk = produkList
                          .where((p) => p.id == item['produk_id'])
                          .firstOrNull;
                      final produkName = produk?.nama ?? 'Unknown Product';

                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          border: Border.all(color: const Color(0xFFe5e7eb)),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: Row(
                          children: [
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    produkName,
                                    style: const TextStyle(
                                      fontWeight: FontWeight.w600,
                                      fontSize: 13,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    'Jumlah: ${item['jumlah']}',
                                    style: const TextStyle(fontSize: 12),
                                  ),
                                  if ((item['spesifikasi'] as String?)
                                          ?.isNotEmpty ??
                                      false) ...[
                                    const SizedBox(height: 4),
                                    Text(
                                      'Spesifikasi: ${item['spesifikasi']}',
                                      style: const TextStyle(
                                        fontSize: 12,
                                        color: Colors.grey,
                                      ),
                                    ),
                                  ],
                                ],
                              ),
                            ),
                            IconButton(
                              icon: const Icon(
                                Icons.close,
                                color: Colors.red,
                                size: 20,
                              ),
                              onPressed: () => _removeOrderItem(index),
                              padding: EdgeInsets.zero,
                              constraints: const BoxConstraints(),
                            ),
                          ],
                        ),
                      );
                    }),
                    const SizedBox(height: 20),
                  ],

                  // Submit Button
                  CustomButton(
                    label: _isSubmitting
                        ? 'Membuat Pesanan...'
                        : 'Buat Pesanan',
                    isLoading: _isSubmitting,
                    onPressed: () => _handleCreatePesanan(),
                  ),
                  const SizedBox(height: 20),
                ],
              ),
            );
          },
    );
  }
}
