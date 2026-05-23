import 'package:flutter/material.dart';
import '../../widgets/sidebar.dart';

class ArsipPdfScreen extends StatefulWidget {
  const ArsipPdfScreen({super.key});

  @override
  State<ArsipPdfScreen> createState() => _ArsipPdfScreenState();
}

class _ArsipPdfScreenState extends State<ArsipPdfScreen> {
  int selectedMenuIndex = 4;

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
                    'Arsip PDF',
                    style: TextStyle(fontSize: 28, fontWeight: FontWeight.bold),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    'Kelola arsip dokumen PDF Anda.',
                    style: TextStyle(fontSize: 13, color: Colors.grey[600]),
                  ),
                  const SizedBox(height: 30),
                  // File List
                  Expanded(
                    child: ListView.builder(
                      itemCount: 5,
                      itemBuilder: (context, index) {
                        return Card(
                          margin: const EdgeInsets.only(bottom: 12),
                          child: ListTile(
                            leading: const Icon(Icons.picture_as_pdf, color: Colors.red, size: 32),
                            title: Text('Dokumen_${index + 1}.pdf'),
                            subtitle: Text('Ukuran: ${(index + 1) * 250} KB'),
                            trailing: PopupMenuButton(
                              itemBuilder: (BuildContext context) => [
                                const PopupMenuItem(
                                  child: Text('Download'),
                                ),
                                const PopupMenuItem(
                                  child: Text('Hapus'),
                                ),
                              ],
                            ),
                          ),
                        );
                      },
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