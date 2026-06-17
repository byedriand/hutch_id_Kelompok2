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
      if (mounted) {
        Provider.of<ProdukProvider>(context, listen: false).fetchProduk();
      }
    });
  }

  @override
  Widget build(BuildContext context) {

    return Scaffold(
      backgroundColor: const Color(0xFFF1F5F9),
      appBar: AppBar(
        title: const Text(
          'HUTCH PRESTIGE',
          style: TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w900,
            color: Colors.white,
            letterSpacing: 0.4,
          ),
        ),
        elevation: 0,
        backgroundColor: const Color(0xFF0d1b2e),
        foregroundColor: Colors.white,
        iconTheme: const IconThemeData(color: Colors.white),
      ),
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
            child: Column(
              children: [
                // Header Section - Blue Gradient
                Container(
                  width: double.infinity,
                  padding: const EdgeInsets.fromLTRB(20, 20, 20, 24),
                  decoration: const BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [Color(0xFF1e40af), Color(0xFF2563eb)],
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(10),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.2),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: const Icon(
                          Icons.inventory_2_rounded,
                          color: Colors.white,
                          size: 26,
                        ),
                      ),
                      const SizedBox(width: 14),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Manajemen Produk',
                              style: TextStyle(
                                color: Colors.white,
                                fontSize: 20,
                                fontWeight: FontWeight.w900,
                                letterSpacing: 0.3,
                              ),
                            ),
                            const SizedBox(height: 3),
                            Text(
                              'Total ${produkProvider.produkList.length} produk terdaftar',
                              style: TextStyle(
                                color: Colors.white.withValues(alpha: 0.8),
                                fontSize: 12,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
                Expanded(
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
                ),
              ],
            ),
          );
        },
      ),
    );
  }

  Widget _buildProdukCard(BuildContext context, dynamic produk) {
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
