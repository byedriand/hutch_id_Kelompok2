import 'dart:convert';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
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
      return null;
    } catch (e) {
      debugPrint('Error creating pesanan: $e');
      return null;
    }
  }

  Future<bool> updatePesananStatus(int id, String status) async {
    try {
      final response = await http.patch(
        Uri.parse('${AppConfig.apiBaseUrl}/pesanan/$id/status'),
        headers: await _getHeaders(),
        body: jsonEncode({'status': status}),
      );

      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Error updating pesanan status: $e');
      return false;
    }
  }

  Future<bool> deletePesanan(int id) async {
    try {
      final response = await http.delete(
        Uri.parse('${AppConfig.apiBaseUrl}/pesanan/$id'),
        headers: await _getHeaders(),
      );

      return response.statusCode == 200;
    } catch (e) {
      debugPrint('Error deleting pesanan: $e');
      return false;
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

  // Helper Methods
  bool get isLoggedIn => _token != null;

  String? get token => _token;

  Future<void> clearToken() async {
    _token = null;
    await _prefs.remove(AppConfig.tokenKey);
  }
}
