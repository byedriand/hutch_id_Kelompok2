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

  // Create Produk
  Future<bool> createProduk(
    Map<String, dynamic> data,
    dynamic imageFile,
  ) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.createProduk(data, imageFile);
      final success = result['success'] == true;

      if (success) {
        // Refresh produk list after create
        await fetchProduk();
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = result['message'] as String? ?? 'Gagal membuat produk';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Error creating produk: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Update info produk (nama/harga/keterangan/foto) — untuk staf
  Future<bool> updateProduk(
    int produkId,
    Map<String, dynamic> data,
    dynamic imageFile,
  ) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.updateProduk(produkId, data, imageFile);
      final success = result['success'] == true;

      if (success) {
        await fetchProduk();
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage =
            result['message'] as String? ?? 'Gagal memperbarui produk';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Error updating produk: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Hapus produk — untuk staf/admin (bukan operator gudang)
  Future<bool> deleteProduk(int produkId) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final success = await _apiService.deleteProduk(produkId);

      if (success) {
        _produkList.removeWhere((p) => p.id == produkId);
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = 'Gagal menghapus produk';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Error deleting produk: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }

  // Kirim notifikasi stok kurang ke operator gudang
  Future<bool> sendStokKurangNotifikasi({
    String? nomorPo,
    required List<Map<String, dynamic>> detailKurang,
  }) async {
    try {
      return await _apiService.sendStokKurangNotifikasi(
        nomorPo: nomorPo,
        detailKurang: detailKurang,
      );
    } catch (e) {
      debugPrint('Error sending stok kurang notifikasi: $e');
      return false;
    }
  }

  Future<bool> updateProdukStok(
    int produkId,
    int stok, {
    String? keterangan,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final success = await _apiService.updateProdukStok(
        produkId,
        stok,
        keterangan: keterangan,
      );

      if (success) {
        _isLoading = false;
        notifyListeners();
        return true;
      } else {
        _errorMessage = 'Gagal memperbarui stok produk';
        _isLoading = false;
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Error updating stok: $e';
      _isLoading = false;
      notifyListeners();
      return false;
    }
  }
}
