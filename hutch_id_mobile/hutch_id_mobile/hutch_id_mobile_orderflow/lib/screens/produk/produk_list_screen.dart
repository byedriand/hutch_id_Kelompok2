import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/produk_provider.dart';
import '../../widgets/custom_widgets.dart';
import 'package:intl/intl.dart';

class ProdukListScreen extends StatefulWidget {
  const ProdukListScreen({super.key});

  @override
  State<ProdukListScreen> createState() => _ProdukListScreenState();
}

class _ProdukListScreenState extends State<ProdukListScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() {
      Provider.of<ProdukProvider>(context, listen: false).fetchProduk();
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Produk'), elevation: 0),
      body: Consumer<ProdukProvider>(
        builder: (context, produkProvider, _) {
          if (produkProvider.isLoading) {
            return const LoadingWidget(message: 'Memuat produk...');
          }

          if (produkProvider.errorMessage != null) {
            return Center(
              child: EmptyStateWidget(
                message: produkProvider.errorMessage!,
                onRetry: () {
                  produkProvider.fetchProduk();
                },
              ),
            );
          }

          if (produkProvider.produkList.isEmpty) {
            return const Center(
              child: EmptyStateWidget(
                message: 'Belum ada produk',
                icon: Icons.inventory_2_outlined,
              ),
            );
          }

          return RefreshIndicator(
            onRefresh: () => produkProvider.fetchProduk(),
            child: GridView.builder(
              padding: const EdgeInsets.all(16),
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                mainAxisSpacing: 16,
                crossAxisSpacing: 16,
                childAspectRatio: 0.8,
              ),
              itemCount: produkProvider.produkList.length,
              itemBuilder: (context, index) {
                final produk = produkProvider.produkList[index];
                return _buildProdukCard(context, produk);
              },
            ),
          );
        },
      ),
    );
  }

  Widget _buildProdukCard(context, produk) {
    final formatter = NumberFormat('#,##0', 'id_ID');

    return Card(
      child: InkWell(
        onTap: () {
          Navigator.pushNamed(context, '/produk-detail', arguments: produk.id);
        },
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image/Placeholder
            Container(
              width: double.infinity,
              height: 150,
              decoration: BoxDecoration(
                color: Colors.blue[100],
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(8),
                  topRight: Radius.circular(8),
                ),
              ),
              child: produk.foto != null
                  ? Image.network(
                      produk.foto!,
                      fit: BoxFit.cover,
                      errorBuilder: (context, error, stackTrace) {
                        return Icon(
                          Icons.image_not_supported,
                          color: Colors.blue[300],
                        );
                      },
                    )
                  : Icon(Icons.inventory_2, color: Colors.blue[700], size: 48),
            ),
            // Content
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          produk.nama,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                          style: const TextStyle(
                            fontSize: 14,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                        if (produk.kategori != null) ...[
                          const SizedBox(height: 4),
                          Text(
                            produk.kategori!,
                            style: TextStyle(
                              fontSize: 12,
                              color: Colors.grey[600],
                            ),
                          ),
                        ],
                      ],
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        if (produk.harga != null)
                          Text(
                            'Rp ${formatter.format(produk.harga)}',
                            style: const TextStyle(
                              fontSize: 14,
                              fontWeight: FontWeight.bold,
                              color: Colors.green,
                            ),
                          ),
                        if (produk.stok != null)
                          Text(
                            'Stok: ${produk.stok}',
                            style: TextStyle(
                              fontSize: 11,
                              color: Colors.grey[600],
                            ),
                          ),
                      ],
                    ),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
