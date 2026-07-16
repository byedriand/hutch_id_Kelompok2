import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/produk_provider.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/custom_widgets.dart';
import 'package:intl/intl.dart';

class ProdukListScreen extends StatefulWidget {
  const ProdukListScreen({super.key});

  @override
  State<ProdukListScreen> createState() => _ProdukListScreenState();
}

class _ProdukListScreenState extends State<ProdukListScreen> {
  final TextEditingController _searchController = TextEditingController();
  String _searchQuery = '';

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
  void dispose() {
    _searchController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final userRole = Provider.of<AuthProvider>(context).user?.role ?? '';
    final canAddProduct = userRole == 'staf_penjualan' || userRole == 'administrator';

    return Scaffold(
      appBar: AppBar(title: const Text('Produk'), elevation: 0),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(16),
            child: TextField(
              controller: _searchController,
              decoration: InputDecoration(
                hintText: 'Cari nama produk...',
                prefixIcon: const Icon(Icons.search),
                suffixIcon: _searchQuery.isNotEmpty
                    ? IconButton(
                        icon: const Icon(Icons.clear),
                        onPressed: () {
                          _searchController.clear();
                          setState(() => _searchQuery = '');
                        },
                      )
                    : null,
                filled: true,
                fillColor: Colors.grey[100],
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide.none,
                ),
                contentPadding: const EdgeInsets.symmetric(vertical: 0),
              ),
              onChanged: (value) {
                setState(() => _searchQuery = value.trim().toLowerCase());
              },
            ),
          ),
          Expanded(
            child: Consumer<ProdukProvider>(
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

          final filteredList = _searchQuery.isEmpty
              ? produkProvider.produkList
              : produkProvider.produkList
                  .where((p) => p.nama.toLowerCase().contains(_searchQuery))
                  .toList();

          if (produkProvider.produkList.isEmpty) {
            return const Center(
              child: EmptyStateWidget(
                message: 'Belum ada produk',
                icon: Icons.inventory_2_outlined,
              ),
            );
          }

          if (filteredList.isEmpty) {
            return const Center(
              child: EmptyStateWidget(
                message: 'Produk tidak ditemukan',
                icon: Icons.search_off,
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
                childAspectRatio: 0.62,
              ),
              itemCount: filteredList.length,
              itemBuilder: (context, index) {
                final produk = filteredList[index];
                return _buildProdukCard(context, produk);
              },
            ),
          );
        },
            ),
          ),
        ],
      ),
      floatingActionButton: canAddProduct
          ? FloatingActionButton(key: const Key('FloatingActionButton'),
              onPressed: () async {
                await Navigator.pushNamed(context, '/produk-staf-tambah');
                if (mounted) {
                  Provider.of<ProdukProvider>(
                    context,
                    listen: false,
                  ).fetchProduk();
                }
              },
              backgroundColor: const Color(0xFF1e40af),
              child: const Icon(Icons.add, color: Colors.white),
            )
          : null,
    );
  }

  Widget _buildProdukCard(BuildContext context, dynamic produk) {
    final formatter = NumberFormat('#,##0', 'id_ID');

    return Card(key: const Key('produk_card'),
      clipBehavior: Clip.antiAlias,
      elevation: 2,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
      child: InkWell(
        onTap: () {
          Navigator.pushNamed(context, '/produk-detail', arguments: produk.id);
        },
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image/Placeholder
            Expanded(
              flex: 3,
              child: Container(
                width: double.infinity,
                color: Colors.blue[50],
                child: produk.fotoUrl != null
                    ? Image.network(
                        produk.fotoUrl!,
                        fit: BoxFit.cover,
                        loadingBuilder: (context, child, progress) {
                          if (progress == null) return child;
                          return const Center(
                            child: SizedBox(
                              width: 22,
                              height: 22,
                              child: CircularProgressIndicator(strokeWidth: 2),
                            ),
                          );
                        },
                        errorBuilder: (context, error, stackTrace) {
                          debugPrint(
                            '[FOTO PRODUK GAGAL] nama="${produk.nama}" url="${produk.fotoUrl}" error=$error',
                          );
                          return Icon(
                            Icons.image_not_supported_outlined,
                            color: Colors.blue[300],
                            size: 36,
                          );
                        },
                      )
                    : Icon(Icons.inventory_2, color: Colors.blue[700], size: 40),
              ),
            ),
            // Content
            Expanded(
              flex: 2,
              child: Padding(
                padding: const EdgeInsets.fromLTRB(10, 8, 10, 10),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      produk.nama,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                        height: 1.2,
                      ),
                    ),
                    if (produk.kategori != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 2),
                        child: Text(
                          produk.kategori!,
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                          style: TextStyle(fontSize: 11, color: Colors.grey[600]),
                        ),
                      ),
                    const Spacer(),
                    if (produk.harga != null)
                      Text(
                        'Rp ${formatter.format(produk.harga)}',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: const TextStyle(
                          fontSize: 13,
                          fontWeight: FontWeight.bold,
                          color: Color(0xFF16a34a),
                        ),
                      ),
                    if (produk.stok != null)
                      Text(
                        'Stok: ${produk.stok}',
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                        style: TextStyle(fontSize: 10.5, color: Colors.grey[600]),
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
