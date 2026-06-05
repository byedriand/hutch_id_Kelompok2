import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../utils/responsive.dart';

class DashboardCard extends StatelessWidget {
  final String title;
  final String value;
  final IconData icon;
  final Color color;
  final String? subtitle;
  final VoidCallback? onTap;

  const DashboardCard({
    super.key,
    required this.title,
    required this.value,
    required this.icon,
    required this.color,
    this.subtitle,
    this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final padding = Responsive.padding(context);
    final iconSize = Responsive.smallIconSize(context);
    final titleSize = Responsive.subtitleFontSize(context);
    final valueSize = Responsive.titleFontSize(context) * 0.85;
    final subtitleSize = Responsive.smallFontSize(context);

    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: EdgeInsets.all(padding * 0.75),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [color.withOpacity(0.1), color.withOpacity(0.05)],
          ),
          border: Border.all(color: color.withOpacity(0.3), width: 1.5),
          borderRadius: BorderRadius.circular(Responsive.borderRadius(context)),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(0.1),
              blurRadius: 12,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Container(
                  padding: EdgeInsets.all(padding * 0.5),
                  decoration: BoxDecoration(
                    color: color.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(10),
                  ),
                  child: Icon(icon, color: color, size: iconSize),
                ),
                if (onTap != null)
                  Icon(
                    Icons.arrow_forward_ios,
                    size: Responsive.smallIconSize(context) * 0.7,
                    color: Colors.grey.shade400,
                  ),
              ],
            ),
            SizedBox(height: padding * 0.5),
            Text(
              value,
              style: TextStyle(
                fontSize: valueSize,
                fontWeight: FontWeight.bold,
                color: const Color(0xFF1e293b),
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
            SizedBox(height: padding * 0.25),
            Text(
              title,
              style: TextStyle(
                fontSize: titleSize,
                fontWeight: FontWeight.w600,
                color: Colors.grey.shade600,
                letterSpacing: 0.5,
              ),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
            if (subtitle != null) ...[
              SizedBox(height: padding * 0.25),
              Text(
                subtitle!,
                style: TextStyle(
                  fontSize: subtitleSize,
                  color: Colors.grey.shade500,
                ),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            ],
          ],
        ),
      ),
    );
  }
}

class DashboardOrderCard extends StatelessWidget {
  final String poNumber;
  final String customerName;
  final String status;
  final int totalValue;
  final DateTime deliveryDate;
  final int itemCount;
  final VoidCallback onTap;

  const DashboardOrderCard({
    super.key,
    required this.poNumber,
    required this.customerName,
    required this.status,
    required this.totalValue,
    required this.deliveryDate,
    required this.itemCount,
    required this.onTap,
  });

  Color _getStatusColor() {
    switch (status.toLowerCase()) {
      case 'menunggu_konfirmasi':
      case 'pending':
        return const Color(0xFFF59E0B);
      case 'dalam_produksi':
      case 'proses':
        return const Color(0xFF2D7DD2);
      case 'siap_kirim':
        return const Color(0xFF10B981);
      case 'selesai':
        return const Color(0xFF16A34A);
      default:
        return Colors.grey;
    }
  }

  String _formatCurrency(int value) {
    return NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    ).format(value);
  }

  String _getStatusLabel() {
    final map = {
      'menunggu_konfirmasi': '⏳ Menunggu',
      'dalam_produksi': '🔄 Produksi',
      'siap_kirim': '✓ Siap Kirim',
      'selesai': '✓ Selesai',
      'pending': '⏳ Menunggu',
      'proses': '🔄 Proses',
      'draft': '📝 Draft',
    };
    return map[status.toLowerCase()] ?? status;
  }

  @override
  Widget build(BuildContext context) {
    final padding = Responsive.padding(context);
    final bodySize = Responsive.bodyFontSize(context);
    final smallSize = Responsive.smallFontSize(context);

    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: EdgeInsets.all(padding * 0.75),
        margin: EdgeInsets.only(bottom: padding * 0.4),
        decoration: BoxDecoration(
          color: Colors.white,
          border: Border.all(color: Colors.grey.shade200, width: 1),
          borderRadius: BorderRadius.circular(Responsive.borderRadius(context)),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.04),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        poNumber,
                        style: TextStyle(
                          fontSize: bodySize,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF2D7DD2),
                          letterSpacing: 0.5,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                      SizedBox(height: padding * 0.1),
                      Text(
                        customerName,
                        style: TextStyle(
                          fontSize: smallSize,
                          color: Colors.grey.shade600,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                SizedBox(width: padding * 0.5),
                Container(
                  padding: EdgeInsets.symmetric(
                    horizontal: padding * 0.4,
                    vertical: padding * 0.2,
                  ),
                  decoration: BoxDecoration(
                    color: _getStatusColor().withOpacity(0.1),
                    borderRadius: BorderRadius.circular(6),
                  ),
                  child: Text(
                    _getStatusLabel(),
                    style: TextStyle(
                      fontSize: smallSize * 0.9,
                      fontWeight: FontWeight.bold,
                      color: _getStatusColor(),
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
            SizedBox(height: padding * 0.5),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Total Nilai',
                        style: TextStyle(
                          fontSize: smallSize * 0.9,
                          color: Colors.grey.shade500,
                        ),
                      ),
                      Text(
                        _formatCurrency(totalValue),
                        style: TextStyle(
                          fontSize: bodySize,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF1e293b),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                SizedBox(width: padding * 0.5),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.end,
                    children: [
                      Text(
                        'Target Pengiriman',
                        style: TextStyle(
                          fontSize: smallSize * 0.9,
                          color: Colors.grey.shade500,
                        ),
                      ),
                      Text(
                        DateFormat('d MMM yyyy', 'id_ID').format(deliveryDate),
                        style: TextStyle(
                          fontSize: bodySize,
                          fontWeight: FontWeight.bold,
                          color: const Color(0xFF1e293b),
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class PelangganBadge extends StatelessWidget {
  final String nama;
  final String telepon;
  final int poCount;

  const PelangganBadge({
    super.key,
    required this.nama,
    required this.telepon,
    required this.poCount,
  });

  @override
  Widget build(BuildContext context) {
    final padding = Responsive.padding(context);
    final bodySize = Responsive.bodyFontSize(context);
    final smallSize = Responsive.smallFontSize(context);
    final isMobile = Responsive.isMobile(context);
    final initial = nama.isNotEmpty ? nama[0].toUpperCase() : '?';

    return Container(
      padding: EdgeInsets.all(padding * 0.6),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border.all(color: Colors.grey.shade200),
        borderRadius: BorderRadius.circular(Responsive.borderRadius(context)),
        boxShadow: [
          BoxShadow(color: Colors.black.withOpacity(0.04), blurRadius: 8),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: isMobile ? 36 : 40,
            height: isMobile ? 36 : 40,
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFF0066cc), Color(0xFF0052a3)],
              ),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Center(
              child: Text(
                initial,
                style: TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                  fontSize: bodySize,
                ),
              ),
            ),
          ),
          SizedBox(width: padding * 0.6),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  nama,
                  style: TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: bodySize,
                    color: const Color(0xFF1e293b),
                  ),
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
                Text(
                  telepon,
                  style: TextStyle(
                    fontSize: smallSize,
                    color: Colors.grey.shade600,
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
              vertical: padding * 0.2,
            ),
            decoration: BoxDecoration(
              color: const Color(0xFF0066cc).withOpacity(0.1),
              borderRadius: BorderRadius.circular(6),
            ),
            child: Text(
              '$poCount PO',
              style: TextStyle(
                fontSize: smallSize,
                fontWeight: FontWeight.bold,
                color: const Color(0xFF0066cc),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
