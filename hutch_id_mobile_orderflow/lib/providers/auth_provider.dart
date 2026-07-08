import 'package:flutter/material.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class AuthProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  User? _user;
  bool _isLoading = false;
  String? _errorMessage;
  bool _isLoggedIn = false;
  bool _justLoggedIn = false;   // flag untuk trigger welcome popup di HomeScreen
  String _loggedInRoleName = '';

  AuthProvider() {
    _initializeAuth();
  }

  Future<void> _initializeAuth() async {
    if (_apiService.token != null && _apiService.token!.isNotEmpty) {
      _isLoggedIn = true;
      notifyListeners();
      try {
        await getProfile();
      } catch (e) {
        _isLoggedIn = false;
        _user = null;
        notifyListeners();
      }
    }
  }

  // Getters
  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  bool get isLoggedIn => _isLoggedIn;
  String? get token => _apiService.token;
  bool get justLoggedIn => _justLoggedIn;
  String get loggedInRoleName => _loggedInRoleName;

  // Panggil dari HomeScreen setelah popup ditampilkan — reset flag
  void consumeJustLoggedIn() {
    _justLoggedIn = false;
    _loggedInRoleName = '';
    // tidak perlu notifyListeners — HomeScreen sudah tampil
  }

  // Login
  Future<bool> login(String email, String password, {String roleName = ''}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.login(email, password);

      if (result['success'] == true) {
        _user = result['user'];
        _isLoggedIn = true;
        _justLoggedIn = true;
        _loggedInRoleName = roleName;
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = result['message'] ?? 'Login failed';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Error: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Logout
  Future<void> logout() async {
    _isLoading = true;
    notifyListeners();

    await _apiService.logout();
    _user = null;
    _isLoggedIn = false;
    _errorMessage = null;
    _isLoading = false;
    notifyListeners();
  }

  // Get Profile
  Future<void> getProfile() async {
    try {
      final profile = await _apiService.getProfile();
      if (profile != null) {
        _user = profile;
        _isLoggedIn = true;
        notifyListeners();
      }
    } catch (e) {
      _errorMessage = 'Error loading profile: $e';
      notifyListeners();
    }
  }

  // Clear Error
  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}