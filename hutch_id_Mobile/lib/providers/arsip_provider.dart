import 'package:flutter/material.dart';
import '../models/arsip_pdf.dart';
import '../services/api_service.dart';

class ArsipProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<ArsipPdf> _arsipList = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<ArsipPdf> get arsipList => _arsipList;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> fetchArsip() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _arsipList = await _apiService.getArsipPdf();
    } catch (e) {
      _errorMessage = e.toString();
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<bool> deleteArsip(int arsipId) async {
    try {
      final success = await _apiService.deleteArsipPdf(arsipId);
      if (success) {
        _arsipList.removeWhere((arsip) => arsip.id == arsipId);
        notifyListeners();
      }
      return success;
    } catch (e) {
      _errorMessage = e.toString();
      notifyListeners();
      return false;
    }
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
