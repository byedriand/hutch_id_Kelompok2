import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
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

  // Menyimpan respons mentah (message, whatsapp_sent, pdf_sent,
  // whatsapp_message) dari panggilan updatePesananStatus() terakhir yang
  // berhasil, supaya UI (mis. dialog "Status Diperbarui") bisa menampilkan
  // info notifikasi WhatsApp yang sama seperti popup di website.
  Map<String, dynamic>? _lastStatusUpdateResult;

  // Getters
  List<Pesanan> get pesananList => _pesananList;
  Pesanan? get selectedPesanan => _selectedPesanan;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  DateTime? get lastSyncTime => _lastSyncTime;
  Map<String, dynamic>? get lastStatusUpdateResult => _lastStatusUpdateResult;

  // Start auto-refresh polling (every 10 seconds). Pakai mode silent supaya
  // tidak memunculkan layar "Memuat..." berulang setiap 10 detik —
  // sebelumnya ini bikin tampilan kedip-kedip loading terus-terusan,
  // termasuk di halaman Detail Pesanan (karena _isLoading dipakai bersama).
  void startAutoRefresh() {
    _refreshTimer?.cancel();
    _refreshTimer = Timer.periodic(const Duration(seconds: 10), (_) {
      fetchPesanan(silent: true);
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

  // Fetch Pesanan. silent=true dipakai untuk polling background supaya
  // tidak menampilkan spinner/loading penuh layar setiap kali refresh,
  // hanya update data di belakang layar.
  Future<void> fetchPesanan({String? status, bool silent = false}) async {
    if (!silent) {
      _isLoading = true;
      _errorMessage = null;
      notifyListeners();
    }

    try {
      _pesananList = await _apiService.getPesanan(status: status);
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      // Untuk polling silent, jangan timpa data yang sudah tampil dengan
      // pesan error hanya karena satu kali polling gagal (mis. jaringan
      // sempat putus sebentar).
      if (!silent) {
        _errorMessage = 'Error fetching pesanan: $e';
      }
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
  Future<bool> updatePesananStatus(int id, String status, {String? alasanPembatalan}) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.updatePesananStatus(
        id,
        status,
        alasanPembatalan: alasanPembatalan,
      );
      _isLoading = false;
      _lastStatusUpdateResult = result;

      if (result['success'] == true) {
        // Update local list
        final index = _pesananList.indexWhere((p) => p.id == id);
        if (index != -1) {
          _pesananList[index] = _pesananList[index];
        }
        notifyListeners();
        return true;
      }

      _errorMessage = result['message'] as String? ?? 'Gagal mengubah status pesanan';
      notifyListeners();
      return false;
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