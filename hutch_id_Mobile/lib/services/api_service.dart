import 'dart:async';
import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../config/app_config.dart';
import '../models/user_model.dart';
import '../models/pelanggan_model.dart';

class ApiService {
  static String get baseUrl => AppConfig.baseUrl;

  static String? _token;
  static bool isOffline = false;

  static Future<void> init() async {
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
  }

  static Map<String, String> _getHeaders() {
    final headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };
    if (_token != null) {
      headers['Authorization'] = 'Bearer $_token';
    }
    return headers;
  }

  // Auth
  static Future<User?> login(String email, String password) async {
    try {
      final response = await http
          .post(
            Uri.parse('$baseUrl/login'),
            headers: _getHeaders(),
            body: jsonEncode({'email': email, 'password': password}),
          )
          .timeout(
            const Duration(
              seconds: 3,
            ), // Timeout 3 detik, kalau backend mati langsung fallback
            onTimeout: () => http.Response('{"error":"timeout"}', 408),
          );

      if (response.statusCode == 200) {
        isOffline = false;
        final data = jsonDecode(response.body);
        _token = data['token'];

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);

        return User.fromJson(data['user']);
      } else {
        isOffline = true;
      }
    } catch (e) {
      isOffline = true;
      debugPrint('Login error (offline/timeout): $e');
    }
    return null;
  }

  static Future<bool> logout() async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/logout'),
        headers: _getHeaders(),
      );

      _token = null;
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('auth_token');

      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Logout error: $e');
    }
    return false;
  }

  // ── Local-CRUD helpers (offline fallback) ────────────────────────────────
  static Future<List<Pelanggan>> _getLocalPelangganList() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final String? cached = prefs.getString('cached_pelanggan');
      if (cached != null) {
        final List decoded = jsonDecode(cached);
        return decoded.map((item) => Pelanggan.fromJson(item)).toList();
      }
    } catch (e) {
      debugPrint('_getLocalPelangganList error: $e');
    }
    return [];
  }

  static Future<void> _saveLocalPelangganList(List<Pelanggan> list) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(
        'cached_pelanggan',
        jsonEncode(list.map((p) => p.toJson()).toList()),
      );
    } catch (e) {
      debugPrint('_saveLocalPelangganList error: $e');
    }
  }

  // Dashboard
  static Future<Map<String, dynamic>?> getDashboard() async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/dashboard'), headers: _getHeaders())
          .timeout(
            const Duration(seconds: 5),
            onTimeout: () => http.Response('{"error":"timeout"}', 408),
          );
      if (response.statusCode == 200) {
        isOffline = false;
        return jsonDecode(response.body);
      } else {
        isOffline = true;
      }
    } catch (e) {
      isOffline = true;
      debugPrint('Dashboard error: $e');
    }
    return null;
  }

  // Pelanggan
  static Future<List<Pelanggan>> getPelanggan() async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/pelanggan'), headers: _getHeaders())
          .timeout(
            const Duration(seconds: 5),
            onTimeout: () => http.Response('{"error":"timeout"}', 408),
          );
      if (response.statusCode == 200) {
        isOffline = false;
        final dynamic decoded = jsonDecode(response.body);

        // Handle both API response with "value" key and raw array
        List list = [];
        if (decoded is Map && decoded.containsKey('value')) {
          list = decoded['value'] ?? [];
        } else if (decoded is List) {
          list = decoded;
        }

        return list.map((item) => Pelanggan.fromJson(item)).toList();
      } else {
        isOffline = true;
      }
    } catch (e) {
      isOffline = true;
      debugPrint('Get pelanggan error: $e');
    }
    return [];
  }

  static Future<Pelanggan?> createPelanggan(
    String nama,
    String telepon,
    String alamat,
    String email,
  ) async {
    if (!isOffline) {
      try {
        final response = await http
            .post(
              Uri.parse('$baseUrl/pelanggan'),
              headers: _getHeaders(),
              body: jsonEncode({
                'nama': nama,
                'telepon': telepon,
                'alamat': alamat,
                'email': email,
              }),
            )
            .timeout(
              const Duration(seconds: 5),
              onTimeout: () => http.Response('{"error":"timeout"}', 408),
            );
        if (response.statusCode == 201) {
          return Pelanggan.fromJson(jsonDecode(response.body));
        } else {
          isOffline = true;
        }
      } catch (e) {
        isOffline = true;
        debugPrint('Create pelanggan error: $e');
      }
    }
    // ── Offline fallback: save locally ───────────────────────────────────
    final list = await _getLocalPelangganList();
    final newId = 'local_${DateTime.now().millisecondsSinceEpoch}';
    final newPelanggan = Pelanggan(
      id: newId,
      nama: nama,
      telepon: telepon,
      alamat: alamat,
      email: email,
      jumlahPO: 0,
    );
    list.add(newPelanggan);
    await _saveLocalPelangganList(list);
    debugPrint('createPelanggan offline: saved locally with id=$newId');
    return newPelanggan;
  }

  static Future<Pelanggan?> updatePelanggan(
    String id,
    String nama,
    String telepon,
    String alamat,
    String email,
  ) async {
    if (!isOffline) {
      try {
        final response = await http
            .put(
              Uri.parse('$baseUrl/pelanggan/$id'),
              headers: _getHeaders(),
              body: jsonEncode({
                'nama': nama,
                'telepon': telepon,
                'alamat': alamat,
                'email': email,
              }),
            )
            .timeout(
              const Duration(seconds: 5),
              onTimeout: () => http.Response('{"error":"timeout"}', 408),
            );
        if (response.statusCode == 200) {
          return Pelanggan.fromJson(jsonDecode(response.body));
        } else {
          isOffline = true;
        }
      } catch (e) {
        isOffline = true;
        debugPrint('Update pelanggan error: $e');
      }
    }
    // ── Offline fallback: update locally ─────────────────────────────────
    final list = await _getLocalPelangganList();
    final idx = list.indexWhere((p) => p.id == id);
    if (idx != -1) {
      final updated = Pelanggan(
        id: id,
        nama: nama,
        telepon: telepon,
        alamat: alamat,
        email: email,
        jumlahPO: list[idx].jumlahPO,
      );
      list[idx] = updated;
      await _saveLocalPelangganList(list);
      debugPrint('updatePelanggan offline: updated locally id=$id');
      return updated;
    }
    return null;
  }

  static Future<bool> deletePelanggan(String id) async {
    if (!isOffline) {
      try {
        final response = await http
            .delete(Uri.parse('$baseUrl/pelanggan/$id'), headers: _getHeaders())
            .timeout(
              const Duration(seconds: 5),
              onTimeout: () => http.Response('{"error":"timeout"}', 408),
            );
        if (response.statusCode == 200) return true;
        isOffline = true;
      } catch (e) {
        isOffline = true;
        debugPrint('Delete pelanggan error: $e');
      }
    }
    // ── Offline fallback: delete locally ─────────────────────────────────
    final list = await _getLocalPelangganList();
    final before = list.length;
    list.removeWhere((p) => p.id == id);
    if (list.length < before) {
      await _saveLocalPelangganList(list);
      debugPrint('deletePelanggan offline: removed locally id=$id');
      return true;
    }
    return false;
  }

  // Pesanan - with filter support
  static Future<List<Map<String, dynamic>>> getPesanan({
    String? cari,
    String? status,
    String? dari,
    String? sampai,
    int? minTotal,
    int? maxTotal,
    String? produk,
    bool? multiItem,
  }) async {
    try {
      final Map<String, String> queryParams = {};

      if (cari != null && cari.isNotEmpty) queryParams['cari'] = cari;
      if (status != null && status.isNotEmpty) queryParams['status'] = status;
      if (dari != null && dari.isNotEmpty) queryParams['dari'] = dari;
      if (sampai != null && sampai.isNotEmpty) queryParams['sampai'] = sampai;
      if (minTotal != null) queryParams['min_total'] = minTotal.toString();
      if (maxTotal != null) queryParams['max_total'] = maxTotal.toString();
      if (produk != null && produk.isNotEmpty) queryParams['produk'] = produk;
      if (multiItem == true) queryParams['multi_item'] = 'on';

      final Uri uri = Uri.parse(
        '$baseUrl/pesanan',
      ).replace(queryParameters: queryParams.isNotEmpty ? queryParams : null);

      final response = await http
          .get(uri, headers: _getHeaders())
          .timeout(
            const Duration(seconds: 5),
            onTimeout: () => http.Response('{"error":"timeout"}', 408),
          );
      if (response.statusCode == 200) {
        isOffline = false;
        final dynamic decoded = jsonDecode(response.body);

        // Handle both API response with "value" key and raw array
        List list = [];
        if (decoded is Map && decoded.containsKey('value')) {
          list = decoded['value'] ?? [];
        } else if (decoded is List) {
          list = decoded;
        }

        return list.map((item) => Map<String, dynamic>.from(item)).toList();
      } else {
        isOffline = true;
      }
    } catch (e) {
      isOffline = true;
      debugPrint('Get pesanan error: $e');
    }
    return [];
  }

  // ── Local-CRUD helpers (pesanan offline fallback) ─────────────────────────
  static Future<List<Map<String, dynamic>>> _getLocalPesananList() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final String? cached = prefs.getString('cached_pesanan');
      if (cached != null) {
        final List decoded = jsonDecode(cached);
        return decoded.map((item) => Map<String, dynamic>.from(item)).toList();
      }
    } catch (e) {
      debugPrint('_getLocalPesananList error: $e');
    }
    return [];
  }

  static Future<void> _saveLocalPesananList(
    List<Map<String, dynamic>> list,
  ) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('cached_pesanan', jsonEncode(list));
    } catch (e) {
      debugPrint('_saveLocalPesananList error: $e');
    }
  }

  /// Generate PO number in format PO-YYYYMMDD-XXX
  static String generatePoNumber(List<Map<String, dynamic>> existingList) {
    final now = DateTime.now();
    final datePart =
        '${now.year}${now.month.toString().padLeft(2, '0')}${now.day.toString().padLeft(2, '0')}';
    // Count how many POs exist for today
    final todayPrefix = 'PO-$datePart-';
    final todayCount = existingList
        .where((p) => (p['no'] ?? '').toString().startsWith(todayPrefix))
        .length;
    final seq = (todayCount + 1).toString().padLeft(3, '0');
    return 'PO-$datePart-$seq';
  }

  static Future<bool> updatePesananStatus(
    String id,
    String status, {
    String? catatan,
  }) async {
    if (!isOffline) {
      try {
        final response = await http
            .put(
              Uri.parse('$baseUrl/pesanan/$id/status'),
              headers: _getHeaders(),
              // ignore: use_null_aware_elements
              body: jsonEncode({
                'status': status,
                if (catatan != null) 'catatan': catatan,
              }),
            )
            .timeout(
              const Duration(seconds: 5),
              onTimeout: () => http.Response('{"error":"timeout"}', 408),
            );
        if (response.statusCode == 200) {
          // Also update local cache to stay in sync
          final list = await _getLocalPesananList();
          final idx = list.indexWhere((p) => p['id'].toString() == id);
          if (idx != -1) {
            list[idx]['status'] = status;
            final trail = List<Map<String, dynamic>>.from(
              (list[idx]['audit_trail'] as List? ?? []).map(
                (e) => Map<String, dynamic>.from(e),
              ),
            );
            trail.add({
              'status': status,
              'waktu': DateTime.now().toIso8601String(),
              'catatan': catatan ?? '',
            });
            list[idx]['audit_trail'] = trail;
            await _saveLocalPesananList(list);
          }
          return true;
        } else {
          isOffline = true;
        }
      } catch (e) {
        isOffline = true;
        debugPrint('Update pesanan status error: $e');
      }
    }
    // ── Offline fallback ──────────────────────────────────────────────
    final list = await _getLocalPesananList();
    final idx = list.indexWhere((p) => p['id'].toString() == id);
    if (idx != -1) {
      list[idx]['status'] = status;
      final trail = List<Map<String, dynamic>>.from(
        (list[idx]['audit_trail'] as List? ?? []).map(
          (e) => Map<String, dynamic>.from(e),
        ),
      );
      trail.add({
        'status': status,
        'waktu': DateTime.now().toIso8601String(),
        'catatan': catatan ?? '(offline)',
      });
      list[idx]['audit_trail'] = trail;
      await _saveLocalPesananList(list);
      debugPrint('updatePesananStatus offline: id=$id -> $status');
      return true;
    }
    return false;
  }

  static Future<bool> deletePesanan(String id) async {
    if (!isOffline) {
      try {
        final response = await http
            .delete(Uri.parse('$baseUrl/pesanan/$id'), headers: _getHeaders())
            .timeout(
              const Duration(seconds: 5),
              onTimeout: () => http.Response('{"error":"timeout"}', 408),
            );
        if (response.statusCode == 200) {
          // Also remove from local cache
          final list = await _getLocalPesananList();
          list.removeWhere((p) => p['id'].toString() == id);
          await _saveLocalPesananList(list);
          return true;
        }
        isOffline = true;
      } catch (e) {
        isOffline = true;
        debugPrint('Delete pesanan error: $e');
      }
    }
    // ── Offline fallback ──────────────────────────────────────────────
    final list = await _getLocalPesananList();
    final before = list.length;
    list.removeWhere((p) => p['id'].toString() == id);
    if (list.length < before) {
      await _saveLocalPesananList(list);
      debugPrint('deletePesanan offline: removed id=$id');
      return true;
    }
    return false;
  }

  // Arsip PDF
  static Future<List<Map<String, dynamic>>> getArsipPdf() async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/arsip-pdf'), headers: _getHeaders())
          .timeout(
            const Duration(seconds: 5),
            onTimeout: () => http.Response('{"error":"timeout"}', 408),
          );
      if (response.statusCode == 200) {
        isOffline = false;
        final List list = jsonDecode(response.body);
        return list.map((item) => Map<String, dynamic>.from(item)).toList();
      } else {
        isOffline = true;
      }
    } catch (e) {
      isOffline = true;
      debugPrint('Get arsip pdf error: $e');
    }
    return [];
  }

  // Produk
  static Future<List<Map<String, dynamic>>> getProduk() async {
    try {
      final response = await http
          .get(Uri.parse('$baseUrl/produk'), headers: _getHeaders())
          .timeout(
            const Duration(seconds: 5),
            onTimeout: () => http.Response('{"error":"timeout"}', 408),
          );
      if (response.statusCode == 200) {
        isOffline = false;
        final List list = jsonDecode(response.body);
        return list.map((item) => Map<String, dynamic>.from(item)).toList();
      } else {
        isOffline = true;
      }
    } catch (e) {
      isOffline = true;
      debugPrint('Get produk error: $e');
    }
    return [];
  }

  // Create Pesanan with improved structure
  static Future<bool> createPesanan(Map<String, dynamic> poData) async {
    if (!isOffline) {
      try {
        final response = await http
            .post(
              Uri.parse('$baseUrl/pesanan'),
              headers: _getHeaders(),
              body: jsonEncode(poData),
            )
            .timeout(
              const Duration(seconds: 5),
              onTimeout: () => http.Response('{"error":"timeout"}', 408),
            );
        if (response.statusCode == 201) {
          return true;
        } else {
          isOffline = true;
        }
      } catch (e) {
        isOffline = true;
        debugPrint('Create pesanan error: $e');
      }
    }
    // ── Offline fallback: save locally ─────────────────────────────────
    final list = await _getLocalPesananList();
    final now = DateTime.now();
    final newNo = generatePoNumber(list);
    final newId = 'local_${DateTime.now().millisecondsSinceEpoch}';
    const monthNames = [
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
      'Des',
    ];
    final tanggal = '${now.day} ${monthNames[now.month - 1]} ${now.year}';

    final newPesanan = <String, dynamic>{
      'id': newId,
      'no': newNo,
      'nomor_po': newNo,
      'pelanggan': poData['pelanggan_id'],
      'pelanggan_id': poData['pelanggan_id'],
      'tanggal_pengiriman': poData['tanggal_pengiriman'],
      'total_nilai': poData['total_nilai'],
      'catatan': poData['catatan'] ?? '',
      'status': poData['status'] ?? 'draft',
      'items': poData['items'] ?? [],
      'tanggal': tanggal,
    };
    list.add(newPesanan);
    await _saveLocalPesananList(list);
    debugPrint('createPesanan offline: saved with no=$newNo');
    return true;
  }

  static Future<bool> deleteArsipPdf(String id) async {
    try {
      final response = await http.delete(
        Uri.parse('$baseUrl/arsip-pdf/$id'),
        headers: _getHeaders(),
      );
      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Delete arsip pdf error: $e');
    }
    return false;
  }
}
