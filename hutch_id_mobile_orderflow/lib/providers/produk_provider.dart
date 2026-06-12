import 'package:flutter/material.dart';
import '../models/produk.dart';
import '../services/api_service.dart';

class ProdukProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<Produk> _produkList = [];
  Produk? _selectedProduk;
  bool _isLoading = false;
  String? _errorMessage;

  // Getters
  List<Produk> get produkList => _produkList;
  Produk? get selectedProduk => _selectedProduk;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  // Fetch Produk
  Future<void> fetchProduk() async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _produkList = await _apiService.getProduk();
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Error fetching produk: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  // Get Produk Detail
  Future<void> getProdukDetail(int id) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _selectedProduk = await _apiService.getProdukDetail(id);
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Error fetching produk detail: $e';
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
