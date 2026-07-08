class DashboardData {
  final int totalAktif;
  final int totalMenunggu;
  final int totalSiapKirim;
  final int totalSelesaiBulanIni;
  final int nilaiSelesaiBulanIni;

  DashboardData({
    required this.totalAktif,
    required this.totalMenunggu,
    required this.totalSiapKirim,
    required this.totalSelesaiBulanIni,
    required this.nilaiSelesaiBulanIni,
  });

  factory DashboardData.fromJson(Map<String, dynamic> json) {
    return DashboardData(
      totalAktif: json['total_aktif'] ?? 0,
      totalMenunggu: json['total_menunggu'] ?? 0,
      totalSiapKirim: json['total_siap_kirim'] ?? 0,
      totalSelesaiBulanIni: json['total_selesai_bulan_ini'] ?? 0,
      nilaiSelesaiBulanIni: json['nilai_selesai_bulan_ini'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'total_aktif': totalAktif,
      'total_menunggu': totalMenunggu,
      'total_siap_kirim': totalSiapKirim,
      'total_selesai_bulan_ini': totalSelesaiBulanIni,
      'nilai_selesai_bulan_ini': nilaiSelesaiBulanIni,
    };
  }
}
