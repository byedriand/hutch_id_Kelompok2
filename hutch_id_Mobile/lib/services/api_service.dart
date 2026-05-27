import 'dart:async';
import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../models/user_model.dart';
import '../models/pelanggan_model.dart';

class ApiService {
  static const String baseUrl = 'http://127.0.0.1:8000/api';

  static String? _token;

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
        final data = jsonDecode(response.body);
        _token = data['token'];

        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', _token!);

        return User.fromJson(data['user']);
      }
    } catch (e) {
      print('Login error (offline/timeout): $e');
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
      print('Logout error: $e');
    }
    return false;
  }

  // Dashboard
  static Future<Map<String, dynamic>?> getDashboard() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/dashboard'),
        headers: _getHeaders(),
      );
      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      }
    } catch (e) {
      print('Dashboard error: $e');
    }
    return null;
  }

  // Pelanggan
  static Future<List<Pelanggan>> getPelanggan() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/pelanggan'),
        headers: _getHeaders(),
      );
      if (response.statusCode == 200) {
        final List list = jsonDecode(response.body);
        return list.map((item) => Pelanggan.fromJson(item)).toList();
      }
    } catch (e) {
      print('Get pelanggan error: $e');
    }
    return [];
  }

  static Future<Pelanggan?> createPelanggan(
    String nama,
    String telepon,
    String alamat,
    String email,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/pelanggan'),
        headers: _getHeaders(),
        body: jsonEncode({
          'nama': nama,
          'telepon': telepon,
          'alamat': alamat,
          'email': email,
        }),
      );
      if (response.statusCode == 201) {
        return Pelanggan.fromJson(jsonDecode(response.body));
      }
    } catch (e) {
      print('Create pelanggan error: $e');
    }
    return null;
  }

  static Future<Pelanggan?> updatePelanggan(
    String id,
    String nama,
    String telepon,
    String alamat,
    String email,
  ) async {
    try {
      final response = await http.put(
        Uri.parse('$baseUrl/pelanggan/$id'),
        headers: _getHeaders(),
        body: jsonEncode({
          'nama': nama,
          'telepon': telepon,
          'alamat': alamat,
          'email': email,
        }),
      );
      if (response.statusCode == 200) {
        return Pelanggan.fromJson(jsonDecode(response.body));
      }
    } catch (e) {
      print('Update pelanggan error: $e');
    }
    return null;
  }

  static Future<bool> deletePelanggan(String id) async {
    try {
      final response = await http.delete(
        Uri.parse('$baseUrl/pelanggan/$id'),
        headers: _getHeaders(),
      );
      return response.statusCode == 200;
    } catch (e) {
      print('Delete pelanggan error: $e');
    }
    return false;
  }

  // Pesanan
  static Future<List<Map<String, dynamic>>> getPesanan() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/pesanan'),
        headers: _getHeaders(),
      );
      if (response.statusCode == 200) {
        final List list = jsonDecode(response.body);
        return list.map((item) => Map<String, dynamic>.from(item)).toList();
      }
    } catch (e) {
      print('Get pesanan error: $e');
    }
    return [];
  }

  static Future<Map<String, dynamic>?> createPesanan(
    String pelangganNama,
    String deskripsi,
    int jumlah,
    int harga,
    String status,
  ) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/pesanan'),
        headers: _getHeaders(),
        body: jsonEncode({
          'pelanggan': pelangganNama,
          'deskripsi': deskripsi,
          'jumlah': jumlah,
          'harga': harga,
          'status': status,
        }),
      );
      if (response.statusCode == 201) {
        return Map<String, dynamic>.from(jsonDecode(response.body));
      }
    } catch (e) {
      print('Create pesanan error: $e');
    }
    return null;
  }

  static Future<bool> updatePesananStatus(String id, String status) async {
    try {
      final response = await http.put(
        Uri.parse('$baseUrl/pesanan/$id/status'),
        headers: _getHeaders(),
        body: jsonEncode({'status': status}),
      );
      return response.statusCode == 200;
    } catch (e) {
      print('Update pesanan status error: $e');
    }
    return false;
  }

  static Future<bool> deletePesanan(String id) async {
    try {
      final response = await http.delete(
        Uri.parse('$baseUrl/pesanan/$id'),
        headers: _getHeaders(),
      );
      return response.statusCode == 200;
    } catch (e) {
      print('Delete pesanan error: $e');
    }
    return false;
  }

  // Arsip PDF
  static Future<List<Map<String, dynamic>>> getArsipPdf() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/arsip-pdf'),
        headers: _getHeaders(),
      );
      if (response.statusCode == 200) {
        final List list = jsonDecode(response.body);
        return list.map((item) => Map<String, dynamic>.from(item)).toList();
      }
    } catch (e) {
      print('Get arsip pdf error: $e');
    }
    return [];
  }

  static Future<bool> deleteArsipPdf(String id) async {
    try {
      final response = await http.delete(
        Uri.parse('$baseUrl/arsip-pdf/$id'),
        headers: _getHeaders(),
      );
      return response.statusCode == 200;
    } catch (e) {
      print('Delete arsip pdf error: $e');
    }
    return false;
  }
}
