import 'package:flutter/material.dart';
import '../../widgets/sidebar.dart';

class DaftarPesananScreen extends StatefulWidget {
  const DaftarPesananScreen({super.key});

  @override
  State<DaftarPesananScreen> createState() => _DaftarPesananScreenState();
}

class _DaftarPesananScreenState extends State<DaftarPesananScreen> {
  int selectedMenuIndex = 1;

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
                    'Daftar Pesanan',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Kelola semua pesanan Anda di sini.',
                    style: TextStyle(fontSize: 13, color: Colors.grey[600]),
                  ),
                  const SizedBox(height: 30),
                  // Data Table
                  Expanded(
                    child: Card(
                      child: SingleChildScrollView(
                        child: DataTable(
                          columns: const [
                            DataColumn(label: Text('No Pesanan')),
                            DataColumn(label: Text('Pelanggan')),
                            DataColumn(label: Text('Tanggal')),
                            DataColumn(label: Text('Status')),
                            DataColumn(label: Text('Total')),
                          ],
                          rows: [
                            DataRow(cells: [
                              const DataCell(Text('PES-001')),
                              const DataCell(Text('Budi Bag Store')),
                              const DataCell(Text('2026-05-21')),
                              DataCell(
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: Colors.orange[100],
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                  child: const Text('Pending', style: TextStyle(fontSize: 12, color: Colors.orange)),
                                ),
                              ),
                              const DataCell(Text('Rp 1.500.000')),
                            ]),
                            DataRow(cells: [
                              const DataCell(Text('PES-002')),
                              const DataCell(Text('Toko Maju Jaya')),
                              const DataCell(Text('2026-05-20')),
                              DataCell(
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                  decoration: BoxDecoration(
                                    color: Colors.green[100],
                                    borderRadius: BorderRadius.circular(4),
                                  ),
                                  child: const Text('Selesai', style: TextStyle(fontSize: 12, color: Colors.green)),
                                ),
                              ),
                              const DataCell(Text('Rp 2.000.000')),
                            ]),
                          ],
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