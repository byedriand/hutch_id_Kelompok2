import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:intl/intl.dart';
import '../../providers/produk_provider.dart';
import '../../widgets/custom_widgets.dart';

class ProdukDetailScreen extends StatefulWidget {
  final int produkId;

  const ProdukDetailScreen({super.key, required this.produkId});

  @override
  State<ProdukDetailScreen> createState() => _ProdukDetailScreenState();
}

class _ProdukDetailScreenState extends State<ProdukDetailScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      if (mounted) {
        Provider.of<ProdukProvider>(
          context,
          listen: false,
        ).getProdukDetail(widget.produkId);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    final formatter = NumberFormat('#,##0', 'id_ID');
    final dateFormat = DateFormat('dd MMMM yyyy', 'id_ID');

    return Scaffold(
      appBar: AppBar(title: const Text('Detail Produk')),
      body: Consumer<ProdukProvider>(
        builder: (context, produkProvider, _) {
          if (produkProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat detail produk...');
          }

          final produk = produkProvider.selectedProduk;
          if (produk == null) {
            return const Center(
              child: EmptyStateWidget(message: 'Produk tidak ditemukan'),
            );
          }

          return SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Image
                Container(
                  width: double.infinity,
                  height: 250,
                  color: Colors.grey[200],
                  child: produk.foto != null
                      ? Image.network(
                          produk.foto!,
                          fit: BoxFit.cover,
                          errorBuilder: (context, error, stackTrace) {
                            return Icon(
                              Icons.image_not_supported,
                              color: Colors.grey[400],
                              size: 64,
                            );
                          },
                        )
                      : Icon(
                          Icons.inventory_2,
                          color: Colors.grey[400],
                          size: 64,
                        ),
                ),

                // Content
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Nama & Kategori
                      Text(
                        produk.nama,
                        style: const TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Harga Card
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.green[50],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.green[200]!),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Harga',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[600],
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              produk.hargaJual != null
                                  ? 'Rp ${formatter.format(produk.hargaJual)}'
                                  : 'N/A',
                              style: const TextStyle(
                                fontSize: 28,
                                fontWeight: FontWeight.bold,
                                color: Colors.green,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Stok Card
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.orange[50],
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.orange[200]!),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Stok Tersedia',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.grey[600],
                              ),
                            ),
                            const SizedBox(height: 8),
                            Text(
                              produk.stok != null
                                  ? '${produk.stok} unit'
                                  : 'N/A',
                              style: TextStyle(
                                fontSize: 24,
                                fontWeight: FontWeight.bold,
                                color: Colors.orange[700],
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // Keterangan
                      if (produk.keterangan != null &&
                          produk.keterangan!.isNotEmpty) ...[
                        const Text(
                          'Keterangan',
                          style: TextStyle(
                            fontSize: 16,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          produk.keterangan!,
                          style: TextStyle(
                            fontSize: 14,
                            color: Colors.grey[700],
                            height: 1.5,
                          ),
                        ),
                        const SizedBox(height: 24),
                      ],

                      // Information
                      const Text(
                        'Informasi Produk',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 12),
                      Card(
                        child: Padding(
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            children: [
                              _buildInfoRow(
                                'ID Produk',
                                produk.id?.toString() ?? '-',
                              ),
                              const SizedBox(height: 12),
                              _buildInfoRow(
                                'Harga Jual',
                                produk.hargaJual != null
                                    ? 'Rp ${formatter.format(produk.hargaJual)}'
                                    : '-',
                              ),
                              const SizedBox(height: 12),
                              _buildInfoRow(
                                'Stok',
                                produk.stok != null
                                    ? '${produk.stok} unit'
                                    : '-',
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Timeline
                      if (produk.createdAt != null)
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Timeline',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            const SizedBox(height: 12),
                            Card(
                              child: Padding(
                                padding: const EdgeInsets.all(16),
                                child: _buildTimelineRow(
                                  'Dibuat',
                                  dateFormat.format(produk.createdAt!),
                                ),
                              ),
                            ),
                          ],
                        ),
                    ],
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: TextStyle(fontSize: 13, color: Colors.grey[600])),
        Text(
          value,
          style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w500),
        ),
      ],
    );
  }

  Widget _buildTimelineRow(String label, String value) {
    return Row(
      children: [
        Icon(Icons.access_time, size: 16, color: Colors.grey[600]),
        const SizedBox(width: 8),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              label,
              style: TextStyle(fontSize: 12, color: Colors.grey[600]),
            ),
            const SizedBox(height: 4),
            Text(
              value,
              style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w500),
            ),
          ],
        ),
      ],
    );
  }
}
