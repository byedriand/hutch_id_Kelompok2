import 'package:flutter/material.dart';
import '../models/notifikasi.dart';
import '../services/api_service.dart';

class NotifikasiProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<Notifikasi> _notifikasiList = [];
  bool _isLoading = false;
  String? _errorMessage;

  // Getters
  List<Notifikasi> get notifikasiList => _notifikasiList;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  // Fetch Notifikasi
  Future<void> fetchNotifikasi() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _notifikasiList = await _apiService.getNotifikasi();
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Error fetching notifikasi: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Clear Error
  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
