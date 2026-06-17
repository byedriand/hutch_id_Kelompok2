import 'package:flutter/material.dart';
import 'dart:async';
import '../models/pesanan.dart';
import '../services/api_service.dart';

class PesananProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<Pesanan> _pesananList = [];
  Pesanan? _selectedPesanan;
  bool _isLoading = false;
  String? _errorMessage;
  Timer? _refreshTimer;
  DateTime? _lastSyncTime;

  // Getters
  List<Pesanan> get pesananList => _pesananList;
  Pesanan? get selectedPesanan => _selectedPesanan;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  DateTime? get lastSyncTime => _lastSyncTime;

  // Start auto-refresh polling (every 10 seconds)
  void startAutoRefresh() {
    _refreshTimer?.cancel();
    _refreshTimer = Timer.periodic(const Duration(seconds: 15), (_) {
      fetchPesanan();
    });
  }

  // Stop auto-refresh polling
  void stopAutoRefresh() {
    _refreshTimer?.cancel();
    _refreshTimer = null;
  }

  @override
  void dispose() {
    stopAutoRefresh();
    super.dispose();
  }

  // Fetch Pesanan
  Future<void> fetchPesanan({String? status}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _pesananList = await _apiService.getPesanan(status: status);
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Error fetching pesanan: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Get Pesanan Detail
  Future<void> getPesananDetail(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _selectedPesanan = await _apiService.getPesananDetail(id);
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Error fetching pesanan detail: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Create Pesanan
  Future<bool> createPesanan(Map<String, dynamic> data) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.createPesanan(data);
      _isLoading = false;

      if (result != null) {
        _pesananList.add(result);
        notifyListeners();
        return true;
      } else {
        _errorMessage = 'Failed to create pesanan';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Error creating pesanan: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Update Pesanan Status
  Future<bool> updatePesananStatus(int id, String status) async {
    _isLoading = true;
    notifyListeners();

    try {
      final success = await _apiService.updatePesananStatus(id, status);
      _isLoading = false;

      if (success) {
        // Update local list
        final index = _pesananList.indexWhere((p) => p.id == id);
        if (index != -1) {
          _pesananList[index] = _pesananList[index];
        }
        notifyListeners();
      }
      return success;
    } catch (e) {
      _errorMessage = 'Error updating pesanan: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Delete Pesanan
  Future<bool> deletePesanan(int id) async {
    try {
      final success = await _apiService.deletePesanan(id);
      if (success) {
        _pesananList.removeWhere((p) => p.id == id);
        notifyListeners();
      }
      return success;
    } catch (e) {
      _errorMessage = 'Error deleting pesanan: $e';
      notifyListeners();
      return false;
    }
  }

  // Clear Error
  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
