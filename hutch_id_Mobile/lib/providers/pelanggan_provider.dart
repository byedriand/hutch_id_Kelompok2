import 'package:flutter/material.dart';
import 'dart:async';
import '../models/pelanggan.dart';
import '../services/api_service.dart';

class PelangganProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<Pelanggan> _pelangganList = [];
  Pelanggan? _selectedPelanggan;
  bool _isLoading = false;
  String? _errorMessage;
  Timer? _refreshTimer;
  DateTime? _lastSyncTime;

  // Getters
  List<Pelanggan> get pelangganList => _pelangganList;
  Pelanggan? get selectedPelanggan => _selectedPelanggan;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  DateTime? get lastSyncTime => _lastSyncTime;

  // Start auto-refresh polling (every 10 seconds)
  void startAutoRefresh() {
    _refreshTimer?.cancel();
    _refreshTimer = Timer.periodic(const Duration(seconds: 15), (_) {
      fetchPelanggan();
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

  // Fetch Pelanggan
  Future<void> fetchPelanggan() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _pelangganList = await _apiService.getPelanggan();
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Error fetching pelanggan: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Get Pelanggan Detail
  Future<void> getPelangganDetail(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _selectedPelanggan = await _apiService.getPelangganDetail(id);
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Error fetching pelanggan detail: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Create Pelanggan
  Future<bool> createPelanggan(Map<String, dynamic> data) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.createPelanggan(data);
      _isLoading = false;

      if (result != null) {
        _pelangganList.add(result);
        notifyListeners();
        return true;
      } else {
        _errorMessage = 'Failed to create pelanggan';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Error creating pelanggan: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Update Pelanggan
  Future<bool> updatePelanggan(int id, Map<String, dynamic> data) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.updatePelanggan(id, data);
      _isLoading = false;

      if (result != null) {
        // Update local list
        final index = _pelangganList.indexWhere((p) => p.id == id);
        if (index != -1) {
          _pelangganList[index] = result;
        }
        _selectedPelanggan = result;
        notifyListeners();
        return true;
      } else {
        _errorMessage = 'Failed to update pelanggan';
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Error updating pelanggan: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Delete Pelanggan
  Future<bool> deletePelanggan(int id) async {
    try {
      final success = await _apiService.deletePelanggan(id);
      if (success) {
        _pelangganList.removeWhere((p) => p.id == id);
        notifyListeners();
      }
      return success;
    } catch (e) {
      _errorMessage = 'Error deleting pelanggan: $e';
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
