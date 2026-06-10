import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../models/pelanggan_model.dart';
import '../../services/api_service.dart';

class BuatPoScreen extends StatefulWidget {
  final List<Pelanggan> pelangganList;

  const BuatPoScreen({super.key, required this.pelangganList});

  @override
  State<BuatPoScreen> createState() => _BuatPoScreenState();
}

class _BuatPoScreenState extends State<BuatPoScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _customerSearchCtrl;
  late TextEditingController _deliveryDateCtrl;
  late TextEditingController _notesCtrl;

  Pelanggan? _selectedPelanggan;
  DateTime? _selectedDate;
  final List<Map<String, dynamic>> _items = [];
  List<Map<String, dynamic>> _produkList = [];
  bool _isLoadingProduk = false;

  @override
  void initState() {
    super.initState();
    _customerSearchCtrl = TextEditingController();
    _deliveryDateCtrl = TextEditingController();
    _notesCtrl = TextEditingController();
    _loadProduk();
  }

  @override
  void dispose() {
    _customerSearchCtrl.dispose();
    _deliveryDateCtrl.dispose();
    _notesCtrl.dispose();
    super.dispose();
  }

  Future<void> _loadProduk() async {
    setState(() => _isLoadingProduk = true);
    try {
      final produk = await ApiService.getProduk();
      setState(() {
        _produkList = produk;
      });
    } catch (e) {
      debugPrint('Error loading produk: $e');
    } finally {
      setState(() => _isLoadingProduk = false);
    }
  }

  void _selectCustomer(Pelanggan pelanggan) {
    setState(() {
      _selectedPelanggan = pelanggan;
      _customerSearchCtrl.text = pelanggan.nama;
    });
  }

  Future<void> _selectDeliveryDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );

    if (picked != null && picked != _selectedDate) {
      setState(() {
        _selectedDate = picked;
        _deliveryDateCtrl.text = DateFormat(
          'd MMM yyyy',
          'id_ID',
        ).format(picked);
      });
    }
  }

  void _addItem() {
    setState(() {
      _items.add({
        'produk_id': null,
        'produk_nama': '',
        'jumlah': 1,
        'harga_satuan': 0,
        'subtotal': 0,
      });
    });
  }

  void _removeItem(int index) {
    if (_items.length > 1) {
      setState(() {
        _items.removeAt(index);
      });
    }
  }

  void _updateItem(int index, String key, dynamic value) {
    setState(() {
      _items[index][key] = value;
      if (key == 'jumlah' || key == 'harga_satuan') {
        _items[index]['subtotal'] =
            (_items[index]['jumlah'] ?? 1) *
            (_items[index]['harga_satuan'] ?? 0);
      }
    });
  }

  int _getTotalItems() {
    return _items.fold(0, (sum, item) => sum + (item['jumlah'] as int? ?? 0));
  }

  int _getTotalValue() {
    return _items.fold(0, (sum, item) => sum + (item['subtotal'] as int? ?? 0));
  }

  String _formatCurrency(int value) {
    return NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    ).format(value);
  }

  Future<void> _savePO() async {
    if (_formKey.currentState!.validate()) {
      if (_selectedPelanggan == null) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Pilih pelanggan terlebih dahulu')),
        );
        return;
      }

      if (_items.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Tambahkan minimal 1 item')),
        );
        return;
      }

      try {
        final poData = {
          'pelanggan_id': _selectedPelanggan!.id,
          'tanggal_pengiriman': _deliveryDateCtrl.text,
          'items': _items,
          'total_nilai': _getTotalValue(),
          'catatan': _notesCtrl.text,
          'status': 'draft',
        };

        await ApiService.createPesanan(poData);

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(
              content: Text('PO berhasil dibuat'),
              backgroundColor: Colors.green,
            ),
          );
          Navigator.pop(context);
        }
      } catch (e) {
        if (mounted) {
          ScaffoldMessenger.of(
            context,
          ).showSnackBar(SnackBar(content: Text('Error: $e')));
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final totalItems = _getTotalItems();
    final totalValue = _getTotalValue();

    return Scaffold(
      appBar: AppBar(
        title: const Text(
          'Buat PO Baru',
          style: TextStyle(fontWeight: FontWeight.bold),
        ),
        elevation: 0,
        backgroundColor: Colors.transparent,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Customer Section
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Informasi Pelanggan',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: Colors.blue,
                        ),
                      ),
                      const SizedBox(height: 16),
                      TextFormField(
                        controller: _customerSearchCtrl,
                        decoration: InputDecoration(
                          labelText: 'Cari Pelanggan',
                          prefixIcon: const Icon(
                            Icons.search,
                            color: Colors.blue,
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          hintText: 'Ketik nama pelanggan...',
                        ),
                        onChanged: (value) {
                          setState(() {});
                        },
                        validator: (value) {
                          if (_selectedPelanggan == null) {
                            return 'Pilih pelanggan dari list';
                          }
                          return null;
                        },
                      ),
                      if (_customerSearchCtrl.text.isNotEmpty &&
                          _selectedPelanggan == null)
                        Padding(
                          padding: const EdgeInsets.only(top: 8),
                          child: Container(
                            constraints: const BoxConstraints(maxHeight: 200),
                            decoration: BoxDecoration(
                              border: Border.all(color: Colors.grey.shade300),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: ListView(
                              shrinkWrap: true,
                              children: widget.pelangganList
                                  .where(
                                    (p) => p.nama.toLowerCase().contains(
                                      _customerSearchCtrl.text.toLowerCase(),
                                    ),
                                  )
                                  .map(
                                    (pelanggan) => ListTile(
                                      title: Text(pelanggan.nama),
                                      subtitle: Text(pelanggan.telepon),
                                      onTap: () => _selectCustomer(pelanggan),
                                    ),
                                  )
                                  .toList(),
                            ),
                          ),
                        )
                      else if (_selectedPelanggan != null)
                        Padding(
                          padding: const EdgeInsets.only(top: 12),
                          child: Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.blue.shade50,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.blue.shade200),
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  _selectedPelanggan!.nama,
                                  style: const TextStyle(
                                    fontWeight: FontWeight.bold,
                                    fontSize: 14,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  'Telp: ${_selectedPelanggan!.telepon}',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey.shade600,
                                  ),
                                ),
                                Text(
                                  'Email: ${_selectedPelanggan!.email}',
                                  style: TextStyle(
                                    fontSize: 12,
                                    color: Colors.grey.shade600,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Delivery Date Section
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Tanggal Pengiriman',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: Colors.blue,
                        ),
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        controller: _deliveryDateCtrl,
                        readOnly: true,
                        decoration: InputDecoration(
                          labelText: 'Pilih Tanggal',
                          prefixIcon: const Icon(
                            Icons.calendar_today,
                            color: Colors.blue,
                          ),
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                          hintText: 'Pilih tanggal pengiriman',
                        ),
                        onTap: _selectDeliveryDate,
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return 'Pilih tanggal pengiriman';
                          }
                          return null;
                        },
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Items Section
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'Item Pesanan',
                            style: TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: Colors.blue,
                            ),
                          ),
                          ElevatedButton.icon(
                            onPressed: _addItem,
                            icon: const Icon(Icons.add),
                            label: const Text('Tambah Item'),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.blue,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(8),
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      if (_items.isEmpty)
                        const Center(
                          child: Text(
                            'Belum ada item. Tekan tombol "Tambah Item" untuk memulai.',
                            style: TextStyle(color: Colors.grey),
                          ),
                        )
                      else
                        ListView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          itemCount: _items.length,
                          itemBuilder: (context, index) {
                            final item = _items[index];
                            final produk = item['produk_id'] != null
                                ? _produkList.firstWhere(
                                    (p) => p['id'] == item['produk_id'],
                                    orElse: () => {},
                                  )
                                : null;
                            return _buildItemCard(index, item, produk);
                          },
                        ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Summary Section
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                color: Colors.blue.shade50,
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Ringkasan Pesanan',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: Colors.blue,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'Total Item',
                            style: TextStyle(fontSize: 13, color: Colors.grey),
                          ),
                          Text(
                            '$totalItems Pcs',
                            style: const TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                        ],
                      ),
                      const Divider(height: 12),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Text(
                            'Total Nilai',
                            style: TextStyle(fontSize: 13, color: Colors.grey),
                          ),
                          Text(
                            _formatCurrency(totalValue),
                            style: const TextStyle(
                              fontSize: 13,
                              fontWeight: FontWeight.bold,
                              color: Colors.blue,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 16),

              // Notes Section
              Card(
                elevation: 2,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Catatan Khusus (Opsional)',
                        style: TextStyle(
                          fontSize: 14,
                          fontWeight: FontWeight.bold,
                          color: Colors.blue,
                        ),
                      ),
                      const SizedBox(height: 12),
                      TextFormField(
                        controller: _notesCtrl,
                        maxLines: 4,
                        decoration: InputDecoration(
                          hintText: 'Catatan tambahan untuk pesanan ini...',
                          border: OutlineInputBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Action Buttons
              Row(
                children: [
                  SizedBox(width: 12),
                  Expanded(flex: 1, child: Container()),
                  SizedBox(width: 12),
                  Expanded(flex: 1, child: Container()),
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.pop(context),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      child: const Text('Batal'),
                    ),
                  ),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: _savePO,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.blue,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      child: const Text(
                        'Simpan PO',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 16),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildItemCard(int index, Map<String, dynamic> item, dynamic produk) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        border: Border.all(color: Colors.grey.shade300),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Text(
                'Item ${index + 1}',
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 12,
                ),
              ),
              IconButton(
                onPressed: () => _removeItem(index),
                icon: const Icon(Icons.delete, color: Colors.red, size: 18),
                padding: EdgeInsets.zero,
                constraints: const BoxConstraints(),
              ),
            ],
          ),
          const SizedBox(height: 8),
          DropdownButtonFormField<int>(
            initialValue: item['produk_id'],
            onChanged: (value) => _updateItem(index, 'produk_id', value),
            decoration: InputDecoration(
              labelText: 'Nama Produk',
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 8,
              ),
            ),
            items: [
              const DropdownMenuItem(
                value: null,
                child: Text('-- Pilih Produk --'),
              ),
              ..._produkList.map(
                (p) => DropdownMenuItem(
                  value: p['id'],
                  child: Text(p['nama'] ?? ''),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: TextFormField(
                  initialValue: item['jumlah'].toString(),
                  keyboardType: TextInputType.number,
                  decoration: InputDecoration(
                    labelText: 'Qty',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 8,
                    ),
                  ),
                  onChanged: (value) =>
                      _updateItem(index, 'jumlah', int.tryParse(value) ?? 1),
                ),
              ),
              Expanded(
                child: TextFormField(
                  initialValue: _formatCurrency(item['harga_satuan'] as int),
                  readOnly: true,
                  decoration: InputDecoration(
                    labelText: 'Harga Satuan',
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    contentPadding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 8,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          TextFormField(
            initialValue: _formatCurrency(item['subtotal'] as int),
            readOnly: true,
            decoration: InputDecoration(
              labelText: 'Subtotal',
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(8),
              ),
              contentPadding: const EdgeInsets.symmetric(
                horizontal: 12,
                vertical: 8,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
