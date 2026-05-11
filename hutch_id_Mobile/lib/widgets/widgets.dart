import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../theme/app_theme.dart';
import '../models/models.dart';

// ============================================================
// STATUS BADGE
// ============================================================
class StatusBadge extends StatelessWidget {
  final PoStatus status;
  const StatusBadge(this.status, {super.key});

  @override
  Widget build(BuildContext context) {
    final colors = _badgeColors(status);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
      decoration: BoxDecoration(
        color: colors.$1,
        borderRadius: BorderRadius.circular(20),
        border: status == PoStatus.selesai
            ? Border.all(color: const Color.fromRGBO(39, 156, 0, 0.3))
            : null,
      ),
      child: Text(
        '${status.emoji} ${status.label}',
        style: GoogleFonts.plusJakartaSans(
          fontSize: 11,
          fontWeight: FontWeight.w700,
          color: colors.$2,
        ),
      ),
    );
  }

  (Color, Color) _badgeColors(PoStatus s) {
    switch (s) {
      case PoStatus.menungguKonfirmasi:
        return (AppColors.waitBg, AppColors.waitFg);
      case PoStatus.dikonfirmasi:
        return (AppColors.confBg, AppColors.confFg);
      case PoStatus.dalamProduksi:
        return (AppColors.prodBg, AppColors.prodFg);
      case PoStatus.siapKirim:
        return (AppColors.readyBg, AppColors.readyFg);
      case PoStatus.selesai:
        return (AppColors.doneBg, AppColors.doneFg);
      case PoStatus.dibatalkan:
        return (AppColors.cancelBg, AppColors.cancelFg);
    }
  }
}

// ============================================================
// SECTION HEADER
// ============================================================
class SectionHeader extends StatelessWidget {
  final String title;
  final Widget? trailing;
  const SectionHeader(this.title, {super.key, this.trailing});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 18, 16, 10),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(title,
              style: GoogleFonts.plusJakartaSans(
                fontSize: 13,
                fontWeight: FontWeight.w700,
                color: AppColors.navy,
              )),
          if (trailing != null) trailing!,
        ],
      ),
    );
  }
}

// ============================================================
// STAT CARD
// ============================================================
class StatCard extends StatelessWidget {
  final String label;
  final String value;
  final String desc;
  final Color dotColor;
  final Color? valueColor;

  const StatCard({
    super.key,
    required this.label,
    required this.value,
    required this.desc,
    required this.dotColor,
    this.valueColor,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        Row(children: [
          Container(
              width: 8,
              height: 8,
              decoration:
                  BoxDecoration(color: dotColor, shape: BoxShape.circle)),
          const SizedBox(width: 6),
          Expanded(
              child: Text(
            label,
            style: GoogleFonts.plusJakartaSans(
                fontSize: 11,
                color: AppColors.gray,
                fontWeight: FontWeight.w500),
            maxLines: 1,
            overflow: TextOverflow.ellipsis,
          )),
        ]),
        const SizedBox(height: 6),
        Text(value,
            style: GoogleFonts.plusJakartaSans(
              fontSize: 26,
              fontWeight: FontWeight.w800,
              color: valueColor ?? AppColors.navy,
            )),
        const SizedBox(height: 2),
        Text(desc,
            style: GoogleFonts.plusJakartaSans(
                fontSize: 10.5, color: AppColors.gray)),
      ]),
    );
  }
}

// ============================================================
// PO LIST CARD
// ============================================================
class PoCard extends StatelessWidget {
  final PurchaseOrder po;
  final VoidCallback onTap;

  const PoCard({super.key, required this.po, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 5),
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: AppColors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: AppColors.border),
          boxShadow: const [
            BoxShadow(
                color: Color.fromRGBO(15, 23, 42, 0.04),
                blurRadius: 8,
                offset: Offset(0, 2)),
          ],
        ),
        child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            Text(po.nomorPo,
                style: GoogleFonts.firaCode(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: AppColors.accent)),
            StatusBadge(po.status),
          ]),
          const SizedBox(height: 8),
          Row(children: [
            const Icon(Icons.person_outline, size: 14, color: AppColors.gray),
            const SizedBox(width: 5),
            Expanded(
                child: Text(po.pelanggan.nama,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppColors.navy))),
          ]),
          const SizedBox(height: 4),
          Row(children: [
            const Icon(Icons.shopping_bag_outlined,
                size: 14, color: AppColors.gray),
            const SizedBox(width: 5),
            Text(
                po.items.isNotEmpty
                    ? '${po.items.first.produk} (${po.items.first.jumlah} pcs)'
                    : '-',
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, color: AppColors.gray)),
          ]),
          const SizedBox(height: 10),
          Row(mainAxisAlignment: MainAxisAlignment.spaceBetween, children: [
            Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text('Total Nilai',
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 10, color: AppColors.gray)),
              Text(_formatRupiah(po.totalNilai),
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 14,
                      fontWeight: FontWeight.w800,
                      color: AppColors.navy)),
            ]),
            Column(crossAxisAlignment: CrossAxisAlignment.end, children: [
              Text('Tgl Kirim',
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 10, color: AppColors.gray)),
              Text(_formatDate(po.tanggalKirim),
                  style: GoogleFonts.plusJakartaSans(
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                      color: AppColors.navy)),
            ]),
          ]),
        ]),
      ),
    );
  }

  String _formatRupiah(double v) {
    final s = v.toInt().toString();
    final buf = StringBuffer('Rp ');
    for (int i = 0; i < s.length; i++) {
      if (i > 0 && (s.length - i) % 3 == 0) buf.write('.');
      buf.write(s[i]);
    }
    return buf.toString();
  }

  String _formatDate(DateTime d) {
    const m = [
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'Mei',
      'Jun',
      'Jul',
      'Agu',
      'Sep',
      'Okt',
      'Nov',
      'Des'
    ];
    return '${d.day} ${m[d.month - 1]} ${d.year}';
  }
}

// ============================================================
// INFO ROW
// ============================================================
class InfoRow extends StatelessWidget {
  final String label;
  final String value;
  final bool isCode;
  const InfoRow(
      {super.key,
      required this.label,
      required this.value,
      this.isCode = false});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 130,
            child: Text(label,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, color: AppColors.gray)),
          ),
          Expanded(
              child: Text(
            value,
            textAlign: TextAlign.right,
            style: isCode
                ? GoogleFonts.firaCode(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: AppColors.accent)
                : GoogleFonts.plusJakartaSans(
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                    color: AppColors.navy),
          )),
        ],
      ),
    );
  }
}

// ============================================================
// SECTION CARD WRAPPER
// ============================================================
class SectionCard extends StatelessWidget {
  final String title;
  final Widget child;
  final Widget? trailing;
  const SectionCard(
      {super.key, required this.title, required this.child, this.trailing});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
      decoration: BoxDecoration(
        color: AppColors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: AppColors.border),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(title,
                    style: GoogleFonts.plusJakartaSans(
                        fontSize: 13,
                        fontWeight: FontWeight.w700,
                        color: AppColors.navy)),
                if (trailing != null) trailing!,
              ],
            ),
          ),
          const Divider(),
          child,
        ],
      ),
    );
  }
}

// ============================================================
// CUSTOM APP BAR
// ============================================================
PreferredSizeWidget hutchAppBar({
  required String title,
  String? subtitle,
  List<Widget>? actions,
  bool showBack = true,
  BuildContext? context,
}) {
  return PreferredSize(
    preferredSize: const Size.fromHeight(58),
    child: AppBar(
      backgroundColor: AppColors.navy,
      foregroundColor: AppColors.white,
      elevation: 0,
      automaticallyImplyLeading: showBack,
      leading: showBack && context != null
          ? IconButton(
              icon: const Icon(Icons.arrow_back_ios, size: 18),
              onPressed: () => Navigator.of(context).pop())
          : null,
      title: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(title,
              style: GoogleFonts.plusJakartaSans(
                  fontSize: 16,
                  fontWeight: FontWeight.w700,
                  color: Colors.white)),
          if (subtitle != null)
            Text(subtitle,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 10.5, color: Colors.white54)),
        ],
      ),
      actions: actions,
    ),
  );
}

// ============================================================
// BUTTON HELPERS
// ============================================================
class HutchButton extends StatelessWidget {
  final String label;
  final VoidCallback? onPressed;
  final Color? bg;
  final Color? fg;
  final IconData? icon;
  final bool outlined;
  final bool fullWidth;

  const HutchButton({
    super.key,
    required this.label,
    this.onPressed,
    this.bg,
    this.fg,
    this.icon,
    this.outlined = false,
    this.fullWidth = false,
  });

  @override
  Widget build(BuildContext context) {
    final bgColor = bg ?? AppColors.accent;
    final fgColor = fg ?? Colors.white;

    final style = outlined
        ? OutlinedButton.styleFrom(
            foregroundColor: bgColor,
            side: BorderSide(color: bgColor),
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
            textStyle: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700, fontSize: 13),
          )
        : ElevatedButton.styleFrom(
            backgroundColor: bgColor,
            foregroundColor: fgColor,
            elevation: 0,
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 11),
            textStyle: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700, fontSize: 13),
          );

    final child = icon != null
        ? Row(mainAxisSize: MainAxisSize.min, children: [
            Icon(icon, size: 16),
            const SizedBox(width: 6),
            Text(label)
          ])
        : Text(label);

    final btn = outlined
        ? OutlinedButton(onPressed: onPressed, style: style, child: child)
        : ElevatedButton(onPressed: onPressed, style: style, child: child);

    return fullWidth ? SizedBox(width: double.infinity, child: btn) : btn;
  }
}

// ============================================================
// STOCK ROW
// ============================================================
class StockRow extends StatelessWidget {
  final StockItem item;
  const StockRow(this.item, {super.key});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 9),
      child: Row(children: [
        Expanded(
            flex: 3,
            child: Text(item.bahan,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, color: AppColors.navy))),
        Expanded(
            flex: 2,
            child: Text('${item.kebutuhan} ${item.satuan}',
                textAlign: TextAlign.center,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, color: AppColors.gray))),
        Expanded(
            flex: 2,
            child: Text('${item.tersedia} ${item.satuan}',
                textAlign: TextAlign.center,
                style: GoogleFonts.plusJakartaSans(
                    fontSize: 12, color: AppColors.gray))),
        Expanded(
            flex: 1,
            child: item.cukup
                ? const Icon(Icons.check_circle,
                    color: AppColors.green, size: 18)
                : Column(children: [
                    const Icon(Icons.cancel, color: AppColors.red, size: 18),
                    Text('${item.selisih}',
                        style: GoogleFonts.plusJakartaSans(
                            fontSize: 9.5,
                            color: AppColors.red,
                            fontWeight: FontWeight.w700)),
                  ])),
      ]),
    );
  }
}
