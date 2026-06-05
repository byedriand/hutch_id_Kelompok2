import 'package:flutter/material.dart';
import '../models/pelanggan_model.dart';
import '../utils/responsive.dart';

class PelangganCard extends StatelessWidget {
  final Pelanggan pelanggan;
  final VoidCallback onEdit;
  final VoidCallback onDelete;
  final bool showActions;

  const PelangganCard({
    super.key,
    required this.pelanggan,
    required this.onEdit,
    required this.onDelete,
    this.showActions = true,
  });

  @override
  Widget build(BuildContext context) {
    final padding = Responsive.padding(context);
    final bodySize = Responsive.bodyFontSize(context);
    final smallSize = Responsive.smallFontSize(context);
    final isMobile = Responsive.isMobile(context);

    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(Responsive.borderRadius(context)),
        color: Colors.white,
        border: Border.all(color: const Color(0xFFE2E8F0), width: 1),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.02),
            blurRadius: 16,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Padding(
        padding: EdgeInsets.all(padding * 0.75),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                CircleAvatar(
                  radius: isMobile ? 18 : 20,
                  backgroundColor: const Color(
                    0xFF3B82F6,
                  ).withValues(alpha: 0.08),
                  child: Text(
                    _getInitials(pelanggan.nama),
                    style: TextStyle(
                      color: const Color(0xFF2563EB),
                      fontWeight: FontWeight.bold,
                      fontSize: bodySize * 0.8,
                    ),
                  ),
                ),
                SizedBox(width: padding * 0.5),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        pelanggan.nama,
                        style: TextStyle(
                          fontSize: bodySize,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF0F172A),
                        ),
                        overflow: TextOverflow.ellipsis,
                        maxLines: 1,
                      ),
                      SizedBox(height: padding * 0.15),
                      Text(
                        pelanggan.telepon,
                        style: TextStyle(
                          fontSize: smallSize,
                          color: const Color(0xFF64748B),
                          fontWeight: FontWeight.w500,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                SizedBox(width: padding * 0.4),
                Container(
                  padding: EdgeInsets.symmetric(
                    horizontal: padding * 0.4,
                    vertical: padding * 0.15,
                  ),
                  decoration: BoxDecoration(
                    color: const Color(0xFF10B981).withValues(alpha: 0.08),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: const Color(0xFF10B981).withValues(alpha: 0.15),
                      width: 1,
                    ),
                  ),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.shopping_cart_outlined,
                        size: Responsive.smallIconSize(context) * 0.7,
                        color: const Color(0xFF059669),
                      ),
                      SizedBox(width: padding * 0.2),
                      Text(
                        '${pelanggan.jumlahPO} PO',
                        style: TextStyle(
                          color: const Color(0xFF059669),
                          fontSize: smallSize,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            SizedBox(height: padding * 0.75),
            Container(
              width: double.infinity,
              padding: EdgeInsets.all(padding * 0.6),
              decoration: BoxDecoration(
                color: const Color(0xFFF8FAFC),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: const Color(0xFFF1F5F9)),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Icon(
                        Icons.location_on_outlined,
                        size: Responsive.smallIconSize(context) * 0.8,
                        color: const Color(0xFF64748B),
                      ),
                      SizedBox(width: padding * 0.4),
                      Expanded(
                        child: Text(
                          pelanggan.alamat,
                          style: TextStyle(
                            fontSize: smallSize,
                            color: const Color(0xFF475569),
                            height: 1.4,
                          ),
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  SizedBox(height: padding * 0.4),
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Icon(
                        Icons.email_outlined,
                        size: Responsive.smallIconSize(context) * 0.8,
                        color: const Color(0xFF64748B),
                      ),
                      SizedBox(width: padding * 0.4),
                      Expanded(
                        child: Text(
                          pelanggan.email,
                          style: TextStyle(
                            fontSize: smallSize,
                            color: const Color(0xFF475569),
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            if (showActions) ...[
              SizedBox(height: padding * 0.75),
              isMobile
                  ? Column(
                      children: [
                        SizedBox(
                          width: double.infinity,
                          child: OutlinedButton.icon(
                            onPressed: onEdit,
                            icon: const Icon(Icons.edit_outlined, size: 14),
                            label: const Text('Edit'),
                            style: OutlinedButton.styleFrom(
                              foregroundColor: const Color(0xFF334155),
                              side: const BorderSide(color: Color(0xFFCBD5E1)),
                              padding: EdgeInsets.symmetric(
                                vertical: padding * 0.6,
                              ),
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                          ),
                        ),
                        SizedBox(height: padding * 0.4),
                        SizedBox(
                          width: double.infinity,
                          child: OutlinedButton.icon(
                            onPressed: onDelete,
                            icon: const Icon(Icons.delete_outline, size: 14),
                            label: const Text('Hapus'),
                            style: OutlinedButton.styleFrom(
                              side: const BorderSide(color: Color(0xFFFCA5A5)),
                              foregroundColor: const Color(0xFFEF4444),
                              padding: EdgeInsets.symmetric(
                                vertical: padding * 0.6,
                              ),
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                          ),
                        ),
                      ],
                    )
                  : Row(
                      children: [
                        Expanded(
                          child: OutlinedButton.icon(
                            onPressed: onEdit,
                            icon: const Icon(Icons.edit_outlined, size: 14),
                            label: const Text('Edit'),
                            style: OutlinedButton.styleFrom(
                              foregroundColor: const Color(0xFF334155),
                              side: const BorderSide(color: Color(0xFFCBD5E1)),
                              padding: EdgeInsets.symmetric(
                                vertical: padding * 0.6,
                              ),
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                          ),
                        ),
                        SizedBox(width: padding * 0.4),
                        Expanded(
                          child: OutlinedButton.icon(
                            onPressed: onDelete,
                            icon: const Icon(Icons.delete_outline, size: 14),
                            label: const Text('Hapus'),
                            style: OutlinedButton.styleFrom(
                              side: const BorderSide(color: Color(0xFFFCA5A5)),
                              foregroundColor: const Color(0xFFEF4444),
                              padding: EdgeInsets.symmetric(
                                vertical: padding * 0.6,
                              ),
                              elevation: 0,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(10),
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
            ],
          ],
        ),
      ),
    );
  }

  String _getInitials(String name) {
    List<String> names = name.trim().split(' ');
    String initials = '';
    int numWords = names.length > 2 ? 2 : names.length;
    for (var i = 0; i < numWords; i++) {
      if (names[i].isNotEmpty) {
        initials += names[i][0].toUpperCase();
      }
    }
    return initials.isEmpty ? 'P' : initials;
  }
}
