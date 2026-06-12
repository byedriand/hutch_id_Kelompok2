import 'package:flutter/foundation.dart';
import 'pelanggan.dart';
import 'produk.dart';

class DetailPesanan {
  final int? id;
  final int? pesananId;
  final int? produkId;
  final int? jumlah;
  final String? spesifikasi;
  final double? hargaSatuan;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final Produk? produk;

  DetailPesanan({
    this.id,
    this.pesananId,
    this.produkId,
    this.jumlah,
    this.spesifikasi,
    this.hargaSatuan,
    this.createdAt,
    this.updatedAt,
    this.produk,
  });

  factory DetailPesanan.fromJson(Map<String, dynamic> json) {
    // Parse jumlah
    int? jumlahValue;
    if (json['jumlah'] != null) {
      if (json['jumlah'] is String) {
        jumlahValue = int.tryParse(json['jumlah']);
      } else {
        jumlahValue = json['jumlah'];
      }
    }

    // Parse hargaSatuan
    double? hargaSatuanValue;
    if (json['harga_satuan'] != null) {
      if (json['harga_satuan'] is String) {
        hargaSatuanValue = double.tryParse(json['harga_satuan']);
      } else if (json['harga_satuan'] is int) {
        hargaSatuanValue = (json['harga_satuan'] as int).toDouble();
      } else if (json['harga_satuan'] is double) {
        hargaSatuanValue = json['harga_satuan'];
      }
    }

    // Parse produk
    Produk? produkValue;
    if (json['produk'] != null && json['produk'] is Map) {
      try {
        produkValue = Produk.fromJson(json['produk']);
      } catch (e) {
        debugPrint('Error parsing produk: $e');
      }
    }

    return DetailPesanan(
      id: json['id'] is String ? int.tryParse(json['id']) : json['id'],
      pesananId: json['pesanan_id'] is String
          ? int.tryParse(json['pesanan_id'])
          : json['pesanan_id'],
      produkId: json['produk_id'] is String
          ? int.tryParse(json['produk_id'])
          : json['produk_id'],
      jumlah: jumlahValue,
      spesifikasi: json['spesifikasi']?.toString(),
      hargaSatuan: hargaSatuanValue,
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'].toString())
          : null,
      updatedAt: json['updated_at'] != null
          ? DateTime.tryParse(json['updated_at'].toString())
          : null,
      produk: produkValue,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'pesanan_id': pesananId,
      'produk_id': produkId,
      'jumlah': jumlah,
      'spesifikasi': spesifikasi,
      'harga_satuan': hargaSatuan,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  double getTotalHarga() {
    return (hargaSatuan ?? 0) * (jumlah ?? 0);
  }
}

class Pesanan {
  final int? id;
  final int? pelangganId;
  final String? nomorPo;
  final DateTime? tanggalPesanan;
  final DateTime? tanggalPengiriman;
  final DateTime? tanggalDikirim;
  final String? nomorResi;
  final double? totalNilai;
  final String? status;
  final String? catatan;
  final String? alasanPembatalan;
  final int? createdBy;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  // Relations
  final Pelanggan? pelanggan;
  final List<DetailPesanan>? detailPesanan;

  Pesanan({
    this.id,
    this.pelangganId,
    this.nomorPo,
    this.tanggalPesanan,
    this.tanggalPengiriman,
    this.tanggalDikirim,
    this.nomorResi,
    this.totalNilai,
    this.status,
    this.catatan,
    this.alasanPembatalan,
    this.createdBy,
    this.createdAt,
    this.updatedAt,
    this.pelanggan,
    this.detailPesanan,
  });

  factory Pesanan.fromJson(Map<String, dynamic> json) {
    // Parse tanggal_pesanan - API returns format "YYYY-MM-DD"
    DateTime? tanggalPesananValue;
    if (json['tanggal_pesanan'] != null) {
      try {
        String tanggalStr = json['tanggal_pesanan'];
        // If it's just date (YYYY-MM-DD), append time
        if (!tanggalStr.contains(' ')) {
          tanggalStr = '$tanggalStr 00:00:00';
        }
        tanggalPesananValue = DateTime.parse(tanggalStr);
      } catch (e) {
        debugPrint('Error parsing tanggal_pesanan: $e');
      }
    }

    // Parse tanggal_pengiriman
    DateTime? tanggalPengirimanValue;
    if (json['tanggal_pengiriman'] != null) {
      try {
        String tanggalStr = json['tanggal_pengiriman'];
        if (!tanggalStr.contains(' ')) {
          tanggalStr = '$tanggalStr 00:00:00';
        }
        tanggalPengirimanValue = DateTime.parse(tanggalStr);
      } catch (e) {
        debugPrint('Error parsing tanggal_pengiriman: $e');
      }
    }

    // Parse tanggal_dikirim
    DateTime? tanggalDikirimValue;
    if (json['tanggal_dikirim'] != null) {
      try {
        String tanggalStr = json['tanggal_dikirim'];
        if (!tanggalStr.contains(' ')) {
          tanggalStr = '$tanggalStr 00:00:00';
        }
        tanggalDikirimValue = DateTime.parse(tanggalStr);
      } catch (e) {
        debugPrint('Error parsing tanggal_dikirim: $e');
      }
    }

    // Parse pelanggan
    Pelanggan? pelangganValue;
    if (json['pelanggan'] != null) {
      if (json['pelanggan'] is Map) {
        try {
          pelangganValue = Pelanggan.fromJson(json['pelanggan']);
        } catch (e) {
          debugPrint('Error parsing pelanggan object: $e');
        }
      }
    }

    // Parse totalNilai
    double? totalNilaiValue;
    if (json['total_nilai'] != null) {
      if (json['total_nilai'] is String) {
        totalNilaiValue = double.tryParse(json['total_nilai']);
      } else if (json['total_nilai'] is int) {
        totalNilaiValue = (json['total_nilai'] as int).toDouble();
      } else if (json['total_nilai'] is double) {
        totalNilaiValue = json['total_nilai'];
      }
    }

    // Parse createdBy
    int? createdByValue;
    if (json['created_by'] != null) {
      if (json['created_by'] is String) {
        createdByValue = int.tryParse(json['created_by']);
      } else {
        createdByValue = json['created_by'];
      }
    }

    return Pesanan(
      id: json['id'] is String ? int.tryParse(json['id']) : json['id'],
      pelangganId: json['pelanggan_id'] is String
          ? int.tryParse(json['pelanggan_id'])
          : json['pelanggan_id'],
      nomorPo: json['nomor_po']?.toString(),
      tanggalPesanan: tanggalPesananValue,
      tanggalPengiriman: tanggalPengirimanValue,
      tanggalDikirim: tanggalDikirimValue,
      nomorResi: json['nomor_resi']?.toString(),
      totalNilai: totalNilaiValue,
      status: json['status']?.toString(),
      catatan: json['catatan']?.toString(),
      alasanPembatalan: json['alasan_pembatalan']?.toString(),
      createdBy: createdByValue,
      createdAt: json['created_at'] != null
          ? DateTime.tryParse(json['created_at'].toString())
          : null,
      updatedAt: json['updated_at'] != null
          ? DateTime.tryParse(json['updated_at'].toString())
          : null,
      pelanggan: pelangganValue,
      detailPesanan: json['detail_pesanan'] != null
          ? (json['detail_pesanan'] as List)
                .map((item) => DetailPesanan.fromJson(item))
                .toList()
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'pelanggan_id': pelangganId,
      'nomor_po': nomorPo,
      'tanggal_pesanan': tanggalPesanan?.toIso8601String(),
      'tanggal_pengiriman': tanggalPengiriman?.toIso8601String(),
      'tanggal_dikirim': tanggalDikirim?.toIso8601String(),
      'nomor_resi': nomorResi,
      'total_nilai': totalNilai,
      'status': status,
      'catatan': catatan,
      'alasan_pembatalan': alasanPembatalan,
      'created_by': createdBy,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  String getStatusLabel() {
    switch (status) {
      case 'menunggu_konfirmasi':
        return 'Menunggu Konfirmasi';
      case 'dikonfirmasi':
        return 'Dikonfirmasi';
      case 'dalam_produksi':
        return 'Dalam Produksi';
      case 'siap_kirim':
        return 'Siap Kirim';
      case 'selesai':
        return 'Selesai';
      case 'dibatalkan':
        return 'Dibatalkan';
      default:
        return status ?? 'Unknown';
    }
  }
}
