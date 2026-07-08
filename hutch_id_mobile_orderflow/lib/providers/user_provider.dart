import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import '../config/app_config.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class UserProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<User> _users = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<User> get users => _users;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<Map<String, String>> _getHeaders() async {
    return {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      if (_apiService.token != null)
        'Authorization': 'Bearer ${_apiService.token}',
    };
  }

  // ─── Fetch semua pengguna ───────────────────────────────────────────────────
  Future<void> fetchUsers() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final response = await http
          .get(
            Uri.parse('${AppConfig.apiBaseUrl}/users'),
            headers: await _getHeaders(),
          )
          .timeout(const Duration(seconds: 15));

      if (response.statusCode == 200) {
        final data = jsonDecode(response.body);
        final List<dynamic> list =
            data['data'] is List ? data['data'] : [];
        _users = list.map((e) => User.fromJson(e)).toList();
        _errorMessage = null;
      } else if (response.statusCode == 403) {
        _errorMessage = 'Akses ditolak. Hanya Administrator yang dapat mengakses fitur ini.';
      } else {
        final data = jsonDecode(response.body);
        _errorMessage = data['message'] ?? 'Gagal memuat daftar pengguna.';
      }
    } catch (e) {
      _errorMessage = 'Gagal terhubung ke server: $e';
    }

    _isLoading = false;
    notifyListeners();
  }

  // ─── Tambah pengguna ────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> createUser({
    required String email,
    required String role,
    required String password,
  }) async {
    try {
      final response = await http
          .post(
            Uri.parse('${AppConfig.apiBaseUrl}/users'),
            headers: await _getHeaders(),
            body: jsonEncode({
              'email': email,
              'role': role,
              'password': password,
            }),
          )
          .timeout(const Duration(seconds: 15));

      final data = jsonDecode(response.body);

      if (response.statusCode == 201 && data['success'] == true) {
        // Tambahkan user baru ke list lokal tanpa perlu re-fetch
        if (data['data'] != null) {
          _users.insert(0, User.fromJson(data['data']));
          notifyListeners();
        }
        return {'success': true, 'message': data['message'] ?? 'Pengguna berhasil ditambahkan.'};
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal menambahkan pengguna.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server: $e'};
    }
  }

  // ─── Update pengguna ────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> updateUser({
    required int userId,
    required String email,
    required String role,
    String? password,
  }) async {
    try {
      final body = <String, dynamic>{
        'email': email,
        'role': role,
      };
      if (password != null && password.isNotEmpty) {
        body['password'] = password;
      }

      final response = await http
          .put(
            Uri.parse('${AppConfig.apiBaseUrl}/users/$userId'),
            headers: await _getHeaders(),
            body: jsonEncode(body),
          )
          .timeout(const Duration(seconds: 15));

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        // Update user di list lokal
        if (data['data'] != null) {
          final updated = User.fromJson(data['data']);
          final idx = _users.indexWhere((u) => u.id == userId);
          if (idx != -1) {
            _users[idx] = updated;
            notifyListeners();
          }
        }
        return {'success': true, 'message': data['message'] ?? 'Pengguna berhasil diperbarui.'};
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal memperbarui pengguna.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server: $e'};
    }
  }

  // ─── Hapus pengguna ─────────────────────────────────────────────────────────
  Future<Map<String, dynamic>> deleteUser(int userId) async {
    try {
      final response = await http
          .delete(
            Uri.parse('${AppConfig.apiBaseUrl}/users/$userId'),
            headers: await _getHeaders(),
          )
          .timeout(const Duration(seconds: 15));

      final data = jsonDecode(response.body);

      if (response.statusCode == 200 && data['success'] == true) {
        _users.removeWhere((u) => u.id == userId);
        notifyListeners();
        return {'success': true, 'message': data['message'] ?? 'Pengguna berhasil dihapus.'};
      }

      return {
        'success': false,
        'message': data['message'] ?? 'Gagal menghapus pengguna.',
      };
    } catch (e) {
      return {'success': false, 'message': 'Gagal terhubung ke server: $e'};
    }
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}