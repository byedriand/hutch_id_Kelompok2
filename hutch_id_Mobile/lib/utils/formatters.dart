import 'package:intl/intl.dart';

class CurrencyFormatter {
  static String formatRupiah(int value) {
    return NumberFormat.currency(
      locale: 'id_ID',
      symbol: 'Rp ',
      decimalDigits: 0,
    ).format(value);
  }

  static String formatRupiahCompact(int value) {
    if (value >= 1000000) {
      return '${(value / 1000000).toStringAsFixed(1)}M';
    } else if (value >= 1000) {
      return '${(value / 1000).toStringAsFixed(1)}K';
    }
    return value.toString();
  }

  static int parseRupiah(String value) {
    return int.tryParse(value.replaceAll(RegExp(r'[^0-9]'), '')) ?? 0;
  }
}

class DateFormatter {
  static String formatIndonesian(DateTime date) {
    const monthNames = [
      'Januari',
      'Februari',
      'Maret',
      'April',
      'Mei',
      'Juni',
      'Juli',
      'Agustus',
      'September',
      'Oktober',
      'November',
      'Desember',
    ];

    final day = date.day;
    final month = monthNames[date.month - 1];
    final year = date.year;

    return '$day $month $year';
  }

  static String formatIndonesianShort(DateTime date) {
    return DateFormat('d MMM yyyy', 'id_ID').format(date);
  }

  static String formatTimeAgo(DateTime dateTime) {
    final now = DateTime.now();
    final difference = now.difference(dateTime);

    if (difference.inSeconds < 60) {
      return 'Baru saja';
    } else if (difference.inMinutes < 60) {
      final minutes = difference.inMinutes;
      return '$minutes menit yang lalu';
    } else if (difference.inHours < 24) {
      final hours = difference.inHours;
      return '$hours jam yang lalu';
    } else if (difference.inDays < 7) {
      final days = difference.inDays;
      return '$days hari yang lalu';
    } else {
      return formatIndonesianShort(dateTime);
    }
  }
}

class StringFormatter {
  static String capitalize(String value) {
    if (value.isEmpty) return value;
    return value[0].toUpperCase() + value.substring(1).toLowerCase();
  }

  static String getInitials(String name) {
    final names = name.trim().split(' ');
    if (names.isEmpty) return '?';

    String initials = '';
    for (var i = 0; i < names.length && i < 2; i++) {
      if (names[i].isNotEmpty) {
        initials += names[i][0].toUpperCase();
      }
    }
    return initials;
  }

  static String truncateText(String text, int maxLength) {
    if (text.length <= maxLength) return text;
    return '${text.substring(0, maxLength)}...';
  }
}
