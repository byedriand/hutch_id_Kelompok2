import 'package:flutter/material.dart';
import '../models/dashboard.dart';
import '../services/api_service.dart';

class DashboardProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  DashboardData? _dashboardData;
  bool _isLoading = false;
  String? _errorMessage;

  // Getters
  DashboardData? get dashboardData => _dashboardData;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  // Fetch Dashboard
  Future<void> fetchDashboard() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _dashboardData = await _apiService.getDashboard();
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Error fetching dashboard: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Refresh
  Future<void> refresh() async {
    await fetchDashboard();
  }

  // Clear Error
  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
