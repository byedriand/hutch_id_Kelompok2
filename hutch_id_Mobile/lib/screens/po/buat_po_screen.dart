import 'package:flutter/material.dart';
import '../../widgets/sidebar.dart';

class BuatPoScreen extends StatefulWidget {
  const BuatPoScreen({super.key});

  @override
  State<BuatPoScreen> createState() => _BuatPoScreenState();
}

class _BuatPoScreenState extends State<BuatPoScreen> {
  int selectedMenuIndex = 2;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Row(
        children: [
          Sidebar(
            selectedIndex: selectedMenuIndex,
            onMenuSelected: (index) {
              setState(() {
                selectedMenuIndex = index;
              });
            },
          ),
          Expanded(
            child: Container(
              color: Colors.grey[100],
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Buat PO Baru',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Buat Purchase Order baru untuk pelanggan.',
                    style: TextStyle(fontSize: 13, color: Colors.grey[600]),
                  ),
                  const SizedBox(height: 30),
                  // Form
                  Expanded(
                    child: SingleChildScrollView(
                      child: Card(
                        child: Padding(
                          padding: const EdgeInsets.all(24),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Text(
                                'Data Pelanggan',
                                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                              ),
                              const SizedBox(height: 16),
                              const TextField(
                                decoration: InputDecoration(
                                  labelText: 'Pilih Pelanggan',
                                  border: OutlineInputBorder(),
                                  prefixIcon: Icon(Icons.person),
                                ),
                              ),
                              const SizedBox(height: 20),
                              const Text(
                                'Detail PO',
                                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                              ),
                              const SizedBox(height: 16),
                              const TextField(
                                decoration: InputDecoration(
                                  labelText: 'Deskripsi Produk',
                                  border: OutlineInputBorder(),
                                  prefixIcon: Icon(Icons.shopping_bag),
                                ),
                                maxLines: 3,
                              ),
                              const SizedBox(height: 16),
                              const TextField(
                                decoration: InputDecoration(
                                  labelText: 'Jumlah',
                                  border: OutlineInputBorder(),
                                  prefixIcon: Icon(Icons.numbers),
                                ),
                              ),
                              const SizedBox(height: 20),
                              SizedBox(
                                width: double.infinity,
                                height: 48,
                                child: ElevatedButton(
                                  onPressed: () {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      const SnackBar(content: Text('PO berhasil dibuat')),
                                    );
                                  },
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: const Color(0xFF2563eb),
                                  ),
                                  child: const Text('Simpan PO'),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}