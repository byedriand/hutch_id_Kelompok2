import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:image_picker/image_picker.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../models/user.dart';
import '../models/pesanan.dart';
import '../models/pelanggan.dart';
import '../models/produk.dart';
import '../models/arsip_pdf.dart';
import '../models/notifikasi.dart';
import '../models/dashboard.dart';

class ApiService {
  static final ApiService _instance = ApiService._internal();

  factory ApiService() {
    return _instance;
  }

  ApiService._internal();

  String? _token;
  late SharedPreferences _prefs;

  Future<void> init() async {
    _prefs = await SharedPreferences.getInstance();
    _token = _prefs.getString(AppConfig.tokenKey);
  }

  Future<Map<String, String>> _getHeaders({bool includeToken = true}) async {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (includeToken && _token != null) {
      headers['Authorization'] = 'Bearer $_token';
    }

    return headers;
  }

  // Authentication
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      debugPrint('🔐 Login attempt: $email to ${AppConfig.apiBaseUrl}/login');

      final response = await http
          .post(
            Uri.parse('${AppConfig.apiBaseUrl}/login'),
            headers: await _getHeaders(includeToken: false),
            body: jsonEncode({'email': email, 'password': password}),
          )
          .timeout(
            const Duration(seconds: 15),
            onTimeout: () {
              throw Exception(
                'Connection timeout. Backend tidak merespons dalam 15 detik.',
              );
            },
          );

      debugPrint('✅ Response status: ${response.statusCode}');
      debugPrint('📦 Response body: ${response.body}');

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        if (data['token'] != null) {
          _token = data['token'];
          await _prefs.setString(AppConfig.tokenKey, _token!);
          await _prefs.setString(AppConfig.userKey, jsonEncode(data['user']));
          await _prefs.setBool(AppConfig.isLoggedInKey, true);
          debugPrint('🎉 Login berhasil!');
          return {'success': true, 'user': User.fromJson(data['user'])};
        } else {
          return {
            'success': false,
            'message': data['message'] ?? 'Token tidak diterima dari server',
          };
        }
      } else if (response.statusCode == 401 || response.statusCode == 422) {
        final data = jsonDecode(response.body);
        return {
          'success': false,
          'message': data['message'] ?? 'Email atau password salah',
        };
      } else {
        return {
          'success': false,
          'message': 'Error ${response.statusCode}: ${response.body}',
        };
      }
    } catch (e) {
      debugPrint('❌ Login error: $e');
      return {'success': false, 'message': 'Gagal terhubung: $e'};
    }
  }

  Future<bool> logout() async {
    try {
      await http.post(
        Uri.parse('${AppConfig.apiBaseUrl}/logout'),
        headers: await _getHeaders(),
      );

      _token = null;
      await _prefs.remove(AppConfig.tokenKey);
      await _prefs.remove(AppConfig.userKey);
      await _prefs.setBool(AppConfig.isLoggedInKey, false);
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<User?> getProfile() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.apiBaseUrl}/profile'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return User.fromJson(data['data'] ?? data);
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  // Dashboard
  Future<DashboardData?> getDashboard() async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.apiBaseUrl}/dashboard'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return DashboardData.fromJson(data['data'] ?? data);
      }
      return null;
    } catch (e) {
      debugPrint('Error fetching dashboard: $e');
      return null;
    }
  }

  // Pesanan (Orders)
  Future<List<Pesanan>> getPesanan({
    String? status,
    int? page,
    int? limit,
  }) async {
    try {
      String url = '${AppConfig.apiBaseUrl}/pesanan';
      final params = <String, dynamic>{};

      if (status != null) params['status'] = status;
      if (page != null) params['page'] = page;
      if (limit != null) params['limit'] = limit;

      if (params.isNotEmpty) {
        url += '?${params.entries.map((e) => '${e.key}=${e.value}').join('&')}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        // Handle both wrapped and direct array responses
        List<dynamic> pesananList;
        if (data is List) {
          pesananList = data;
        } else if (data is Map && data['data'] != null) {
          pesananList = data['data'] ?? [];
        } else {
          pesananList = [];
        }

        return pesananList.map((item) => Pesanan.fromJson(item)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching pesanan: $e');
      return [];
    }
  }

  Future<Pesanan?> getPesananDetail(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.apiBaseUrl}/pesanan/$id'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Pesanan.fromJson(data['data'] ?? data);
      }
      return null;
    } catch (e) {
      debugPrint('Error fetching pesanan detail: $e');
      return null;
    }
  }

  Future<Pesanan?> createPesanan(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.apiBaseUrl}/pesanan'),
        headers: await _getHeaders(),
        body: jsonEncode(data),
      );

      if (response.statusCode == 201) {
        final responseData = jsonDecode(response.body);
        return Pesanan.fromJson(responseData['data'] ?? responseData);
      }
      debugPrint(
        'Error creating pesanan: status ${response.statusCode}, body: ${response.body}',
      );
      return null;
    } catch (e) {
      debugPrint('Error creating pesanan: $e');
      return null;
    }
  }

  Future<Map<String, dynamic>> updatePesananStatus(
    int id,
    String status, {
    String? alasanPembatalan,
  }) async {
    try {
      final body = {
        'status': status,
        if (alasanPembatalan != null && alasanPembatalan.isNotEmpty)
          'alasan_pembatalan': alasanPembatalan,
      };
      final response = await http
          .patch(
            Uri.parse('${AppConfig.apiBaseUrl}/pesanan/$id/status'),
            headers: await _getHeaders(),
            body: jsonEncode(body),
          )
          .timeout(
            // Saat status diubah ke 'siap_kirim'/'selesai', backend generate
            // PDF lalu kirim WhatsApp (WhatsAppService) sebelum merespons,
            // jadi butuh timeout lebih longgar daripada request biasa.
            const Duration(seconds: 45),
            onTimeout: () {
              throw Exception(
                'Koneksi timeout. Server tidak merespons dalam 45 detik.',
              );
            },
          );

      // Backend (PesananController::updateStatus) mengembalikan JSON berisi
      // 'whatsapp_sent', 'pdf_sent', dan 'whatsapp_message' saat status diubah
      // ke 'siap_kirim'/'selesai' (notifikasi WhatsApp otomatis dikirim server
      // -side). Field-field ini perlu diteruskan ke UI supaya mobile bisa
      // menampilkan info yang sama seperti popup "Status Diperbarui" di
      // website.
      if (response.statusCode == 200) {
        try {
          final data = jsonDecode(response.body);
          return {
            'success': true,
            'message': data['message'] as String?,
            'whatsapp_sent': data['whatsapp_sent'] == true,
            'pdf_sent': data['pdf_sent'] == true,
            'whatsapp_message': data['whatsapp_message'] as String?,
          };
        } catch (_) {
          return {'success': true};
        }
      }

      // Backend (PesananController::updateStatus) mengembalikan pesan error
      // spesifik (mis. "harus Dalam Produksi dulu", "nomor WA tidak valid",
      // dll) lewat field 'message' atau 'errors' saat validasi/izin gagal.
      String? message;
      try {
        final data = jsonDecode(response.body);
        message = data['message'] as String?;
        if (message == null && data['errors'] is Map) {
          final errors = data['errors'] as Map;
          if (errors.isNotEmpty) {
            final firstError = errors.values.first;
            if (firstError is List && firstError.isNotEmpty) {
              message = firstError.first.toString();
            }
          }
        }
      } catch (_) {}

      return {'success': false, 'message': message};
    } catch (e) {
      debugPrint('Error updating pesanan status: $e');
      return {'success': false, 'message': 'Error: $e'};
    }
  }

  Future<bool> deletePesanan(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${AppConfig.apiBaseUrl}/pesanan/$id'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        return true;
      }

      debugPrint(
        'Error deleting pesanan: status ${response.statusCode}, body: ${response.body}',
      );
      return false;
    } catch (e) {
      debugPrint('Error deleting pesanan: $e');
      return false;
    }
  }

  /// Mengambil PDF pesanan (surat jalan/PO) dalam bentuk base64 dari endpoint
  /// `/api/pesanan/{id}/pdf` (apiDownloadPdf di backend, memang dibuat khusus
  /// untuk konsumsi mobile). Mengembalikan null jika gagal/tidak punya akses.
  Future<Map<String, dynamic>?> downloadPesananPdf(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.apiBaseUrl}/pesanan/$id/pdf'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        if (data['success'] == true && data['pdf'] != null) {
          final filename =
              (data['filename'] as String?) ??
              '${data['nomor_po'] ?? 'pesanan'}.pdf';
          return {'base64': data['pdf'] as String, 'filename': filename};
        }
      }
      debugPrint(
        'Gagal mengambil PDF pesanan: status ${response.statusCode}, body: ${response.body}',
      );
      return null;
    } catch (e) {
      debugPrint('Error downloading pesanan PDF: $e');
      return null;
    }
  }

  // Pelanggan (Customers)
  Future<List<Pelanggan>> getPelanggan({int? page, int? limit}) async {
    try {
      String url = '${AppConfig.apiBaseUrl}/pelanggan';
      final params = <String, dynamic>{};

      if (page != null) params['page'] = page;
      if (limit != null) params['limit'] = limit;

      if (params.isNotEmpty) {
        url += '?${params.entries.map((e) => '${e.key}=${e.value}').join('&')}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        // Handle both wrapped and direct array responses
        List<dynamic> pelangganList;
        if (data is List) {
          pelangganList = data;
        } else if (data is Map && data['data'] != null) {
          pelangganList = data['data'] ?? [];
        } else {
          pelangganList = [];
        }

        return pelangganList.map((item) => Pelanggan.fromJson(item)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching pelanggan: $e');
      return [];
    }
  }

  Future<Pelanggan?> getPelangganDetail(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.apiBaseUrl}/pelanggan/$id'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Pelanggan.fromJson(data['data'] ?? data);
      }
      return null;
    } catch (e) {
      debugPrint('Error fetching pelanggan detail: $e');
      return null;
    }
  }

  Future<Pelanggan?> createPelanggan(Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.apiBaseUrl}/pelanggan'),
        headers: await _getHeaders(),
        body: jsonEncode(data),
      );

      if (response.statusCode == 201) {
        final responseData = jsonDecode(response.body);
        return Pelanggan.fromJson(responseData['data'] ?? responseData);
      }
      return null;
    } catch (e) {
      debugPrint('Error creating pelanggan: $e');
      return null;
    }
  }

  Future<Pelanggan?> updatePelanggan(int id, Map<String, dynamic> data) async {
    try {
      final response = await http.put(
        Uri.parse('${AppConfig.apiBaseUrl}/pelanggan/$id'),
        headers: await _getHeaders(),
        body: jsonEncode(data),
      );

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        return Pelanggan.fromJson(responseData['data'] ?? responseData);
      }
      return null;
    } catch (e) {
      debugPrint('Error updating pelanggan: $e');
      return null;
    }
  }

  Future<bool> deletePelanggan(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${AppConfig.apiBaseUrl}/pelanggan/$id'),
        headers: await _getHeaders(),
      );

      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Error deleting pelanggan: $e');
      return false;
    }
  }

  // Produk (Products)
  Future<List<Produk>> getProduk({int? page, int? limit}) async {
    try {
      String url = '${AppConfig.apiBaseUrl}/produk';
      final params = <String, dynamic>{};

      if (page != null) params['page'] = page;
      if (limit != null) params['limit'] = limit;

      if (params.isNotEmpty) {
        url += '?${params.entries.map((e) => '${e.key}=${e.value}').join('&')}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        // Handle both wrapped and direct array responses
        List<dynamic> produkList;
        if (data is List) {
          produkList = data;
        } else if (data is Map && data['data'] != null) {
          produkList = data['data'] ?? [];
        } else {
          produkList = [];
        }

        return produkList.map((item) => Produk.fromJson(item)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching produk: $e');
      return [];
    }
  }

  Future<Produk?> getProdukDetail(int id) async {
    try {
      final response = await http.get(
        Uri.parse('${AppConfig.apiBaseUrl}/produk/$id'),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        return Produk.fromJson(data['data'] ?? data);
      }
      return null;
    } catch (e) {
      debugPrint('Error fetching produk detail: $e');
      return null;
    }
  }

  Future<Map<String, dynamic>> createProduk(
    Map<String, dynamic> data,
    dynamic imageFile,
  ) async {
    try {
      final uri = Uri.parse('${AppConfig.apiBaseUrl}/produk');
      final request = http.MultipartRequest('POST', uri);

      // Add headers
      final headers = await _getHeaders(includeToken: true);
      headers.remove('Content-Type');
      request.headers.addAll(headers);

      // Add form fields
      request.fields['nama'] = data['nama'] ?? '';
      request.fields['harga_jual'] = (data['harga_jual'] ?? 0).toString();
      if (data['keterangan'] != null &&
          data['keterangan'].toString().isNotEmpty) {
        request.fields['keterangan'] = data['keterangan'].toString();
      }

      // Add image file if provided
      if (imageFile != null) {
        if (imageFile is XFile) {
          final bytes = await imageFile.readAsBytes();
          request.files.add(
            http.MultipartFile.fromBytes(
              'foto',
              bytes,
              filename: imageFile.name,
            ),
          );
        } else {
          final file = imageFile as dynamic;
          if (file.path != null) {
            request.files.add(
              await http.MultipartFile.fromPath('foto', file.path),
            );
          }
        }
      }

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();

      debugPrint(
        'Create Produk Response: ${response.statusCode} - $responseBody',
      );

      final isSuccess =
          response.statusCode == 201 || response.statusCode == 200;
      return {
        'success': isSuccess,
        'statusCode': response.statusCode,
        'message': responseBody,
      };
    } catch (e) {
      debugPrint('Error creating produk: $e');
      return {'success': false, 'statusCode': null, 'message': e.toString()};
    }
  }

  /// Update info produk (nama, harga_jual, keterangan, foto).
  /// Endpoint khusus Staf Penjualan / Administrator / Pemilik UMKM,
  /// SESUAI staffUpdate() di web ProdukController. TIDAK untuk update stok
  /// (lihat updateProdukStok untuk itu, khusus Operator Gudang).
  /// CATATAN: path endpoint ini mengikuti pola createProduk
  /// ('/produk/staff/store'); jika berbeda di routes/api.php milikmu,
  /// sesuaikan path '/produk/$produkId/staff/update' di bawah ini.
  Future<Map<String, dynamic>> updateProduk(
    int produkId,
    Map<String, dynamic> data,
    dynamic imageFile,
  ) async {
    try {
      final uri = Uri.parse('${AppConfig.apiBaseUrl}/produk/$produkId');
      final request = http.MultipartRequest('POST', uri);
      // Laravel method spoofing untuk multipart (route biasanya PUT/PATCH)
      request.fields['_method'] = 'PUT';

      final headers = await _getHeaders(includeToken: true);
      headers.remove('Content-Type');
      request.headers.addAll(headers);

      if (data['nama'] != null) request.fields['nama'] = data['nama'];
      if (data['harga_jual'] != null) {
        request.fields['harga_jual'] = data['harga_jual'].toString();
      }
      if (data['keterangan'] != null) {
        request.fields['keterangan'] = data['keterangan'].toString();
      }

      if (imageFile != null) {
        if (imageFile is XFile) {
          final bytes = await imageFile.readAsBytes();
          request.files.add(
            http.MultipartFile.fromBytes(
              'foto',
              bytes,
              filename: imageFile.name,
            ),
          );
        } else {
          final file = imageFile as dynamic;
          if (file.path != null) {
            request.files.add(
              await http.MultipartFile.fromPath('foto', file.path),
            );
          }
        }
      }

      final response = await request.send();
      final responseBody = await response.stream.bytesToString();
      debugPrint(
        'Update Produk Response: ${response.statusCode} - $responseBody',
      );

      final isSuccess =
          response.statusCode == 200 || response.statusCode == 201;
      return {
        'success': isSuccess,
        'statusCode': response.statusCode,
        'message': responseBody,
      };
    } catch (e) {
      debugPrint('Error updating produk: $e');
      return {'success': false, 'statusCode': null, 'message': e.toString()};
    }
  }

  /// Hapus produk. Sesuai web staffDestroy(): hanya Staf Penjualan,
  /// Administrator, Pemilik UMKM yang boleh menghapus (bukan Operator Gudang).
  /// Memanggil endpoint API: DELETE /api/produk/{id} (ProdukController::apiDestroy()).
  Future<bool> deleteProduk(int produkId) async {
    try {
      final response = await http.delete(
        Uri.parse('${AppConfig.apiBaseUrl}/produk/$produkId'),
        headers: await _getHeaders(includeToken: true),
      );
      debugPrint(
        'Delete Produk Response: ${response.statusCode} - ${response.body}',
      );
      return response.statusCode == 200 || response.statusCode == 204;
    } catch (e) {
      debugPrint('Error deleting produk: $e');
      return false;
    }
  }

  /// Kirim notifikasi "stok kurang" ke Operator Gudang saat staf membuat PO
  /// dan menemukan produk dengan stok tidak mencukupi.
  /// Sesuai web NotifikasiController::storeStokKurangDraft().
  /// CATATAN: path endpoint ini perlu dicocokkan dengan routes/api.php asli
  /// (nama method web: storeStokKurangDraft).
  Future<bool> sendStokKurangNotifikasi({
    String? nomorPo,
    required List<Map<String, dynamic>> detailKurang,
  }) async {
    try {
      final response = await http.post(
        Uri.parse('${AppConfig.apiBaseUrl}/notifikasi/stok-kurang'),
        headers: await _getHeaders(includeToken: true),
        body: jsonEncode({'nomor_po': nomorPo, 'detail_kurang': detailKurang}),
      );
      debugPrint(
        'Stok Kurang Notif Response: ${response.statusCode} - ${response.body}',
      );
      return response.statusCode == 200 || response.statusCode == 201;
    } catch (e) {
      debugPrint('Error sending stok kurang notifikasi: $e');
      return false;
    }
  }

  /// Update stok produk (dipakai Operator Gudang). Sesuai web
  /// ProdukController::apiUpdateStok() -> POST /produk/{produk}/stok.
  /// Backend otomatis menyelesaikan (resolve) notifikasi 'stok_kurang' yang
  /// terkait produk ini begitu stok baru sudah cukup.
  Future<bool> updateProdukStok(
    int produkId,
    int stok, {
    String? keterangan,
  }) async {
    try {
      final uri = Uri.parse('${AppConfig.apiBaseUrl}/produk/$produkId/stok');
      final headers = await _getHeaders(includeToken: true);

      final body = jsonEncode({
        'stok': stok,
        if (keterangan != null && keterangan.isNotEmpty)
          'keterangan': keterangan,
      });

      debugPrint('📦 Update Produk Stok: $produkId to $stok - URL: $uri');

      final response = await http.post(uri, headers: headers, body: body);

      debugPrint(
        'Update Stok Response: ${response.statusCode} - ${response.body}',
      );

      return response.statusCode == 200 || response.statusCode == 201;
    } catch (e) {
      debugPrint('Error updating produk stok: $e');
      return false;
    }
  }

  // Arsip PDF
  Future<List<ArsipPdf>> getArsipPdf({int? page, int? limit}) async {
    try {
      String url = '${AppConfig.apiBaseUrl}/arsip-pdf';
      final params = <String, dynamic>{};

      if (page != null) params['page'] = page;
      if (limit != null) params['limit'] = limit;

      if (params.isNotEmpty) {
        url += '?${params.entries.map((e) => '${e.key}=${e.value}').join('&')}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        // Handle both wrapped and direct array responses
        List<dynamic> arsipList;
        if (data is List) {
          arsipList = data;
        } else if (data is Map && data['data'] != null) {
          arsipList = data['data'] ?? [];
        } else {
          arsipList = [];
        }

        return arsipList.map((item) => ArsipPdf.fromJson(item)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching arsip: $e');
      return [];
    }
  }

  Future<bool> deleteArsipPdf(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${AppConfig.apiBaseUrl}/arsip-pdf/$id'),
        headers: await _getHeaders(),
      );

      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Error deleting arsip: $e');
      return false;
    }
  }

  // Notifikasi (Notifications)
  Future<List<Notifikasi>> getNotifikasi({int? page, int? limit}) async {
    try {
      String url = '${AppConfig.apiBaseUrl}/notifikasi';
      final params = <String, dynamic>{};

      if (page != null) params['page'] = page;
      if (limit != null) params['limit'] = limit;

      if (params.isNotEmpty) {
        url += '?${params.entries.map((e) => '${e.key}=${e.value}').join('&')}';
      }

      final response = await http.get(
        Uri.parse(url),
        headers: await _getHeaders(),
      );

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);

        // Handle both wrapped and direct array responses
        List<dynamic> notifikasiList;
        if (data is List) {
          notifikasiList = data;
        } else if (data is Map && data['data'] != null) {
          notifikasiList = data['data'] ?? [];
        } else {
          notifikasiList = [];
        }
        return notifikasiList.map((item) => Notifikasi.fromJson(item)).toList();
      }
      return [];
    } catch (e) {
      debugPrint('Error fetching notifikasi: $e');
      return [];
    }
  }

  /// Tandai satu notifikasi sudah dibaca. Sesuai web
  /// NotifikasiController::markAsRead() -> PATCH /notifikasi/{id}/baca.
  Future<bool> markNotifikasiAsRead(int id) async {
    try {
      final uri = Uri.parse('${AppConfig.apiBaseUrl}/notifikasi/$id/baca');
      final response = await http.patch(
        uri,
        headers: await _getHeaders(includeToken: true),
      );
      debugPrint(
        'Mark Notifikasi Read Response: ${response.statusCode} - ${response.body}',
      );
      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Error marking notifikasi as read: $e');
      return false;
    }
  }

  /// Tandai semua notifikasi (untuk role user saat ini) sudah dibaca.
  Future<bool> markAllNotifikasiAsRead() async {
    try {
      final uri = Uri.parse(
        '${AppConfig.apiBaseUrl}/notifikasi/tandai-semua-dibaca',
      );
      final response = await http.patch(
        uri,
        headers: await _getHeaders(includeToken: true),
      );
      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Error marking all notifikasi as read: $e');
      return false;
    }
  }

  /// Hapus satu notifikasi. Sesuai web NotifikasiController::destroy() ->
  /// DELETE /notifikasi/{id}.
  Future<bool> deleteNotifikasi(int id) async {
    try {
      final uri = Uri.parse('${AppConfig.apiBaseUrl}/notifikasi/$id');
      final response = await http.delete(
        uri,
        headers: await _getHeaders(includeToken: true),
      );
      debugPrint(
        'Delete Notifikasi Response: ${response.statusCode} - ${response.body}',
      );
      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Error deleting notifikasi: $e');
      return false;
    }
  }

  /// Kirim pesan ke Hutch AI Assistant (chatbot).
  /// Sesuai web: ChatbotController::sendMessage() -> POST /api/chatbot/message.
  /// Backend mencoba respon dari N8N terlebih dahulu, lalu fallback ke
  /// respon lokal (rule-based) bila N8N tidak tersedia.
  Future<Map<String, dynamic>> sendChatbotMessage(String message) async {
    try {
      final response = await http
          .post(
            Uri.parse('${AppConfig.apiBaseUrl}/chatbot/message'),
            headers: await _getHeaders(includeToken: true),
            body: jsonEncode({'message': message}),
          )
          .timeout(
            const Duration(seconds: 30),
            onTimeout: () {
              throw Exception('Chatbot tidak merespons dalam 30 detik.');
            },
          );

      debugPrint(
        'Chatbot Response: ${response.statusCode} - ${response.body}',
      );

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['reply'] != null) {
        return {'success': true, 'reply': data['reply']};
      }

      return {
        'success': false,
        'reply':
            data['reply'] ??
            'Maaf, saya tidak dapat memproses pesan Anda saat ini. Silakan coba lagi.',
      };
    } catch (e) {
      debugPrint('Error sending chatbot message: $e');
      return {
        'success': false,
        'reply': '❌ Terjadi kesalahan. Silakan coba lagi.',
      };
    }
  }

  // Helper Methods
  bool get isLoggedIn => _token != null;

  String? get token => _token;

  Future<void> clearToken() async {
    _token = null;
    await _prefs.remove(AppConfig.tokenKey);
  }
}