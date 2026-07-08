import 'package:flutter/material.dart';
import 'dart:async';
import '../models/notifikasi.dart';
import '../services/api_service.dart';

class NotifikasiProvider extends ChangeNotifier {
  final ApiService _apiService = ApiService();

  List<Notifikasi> _notifikasiList = [];
  bool _isLoading = false;
  String? _errorMessage;
  Timer? _refreshTimer;

  // Getters
  List<Notifikasi> get notifikasiList => _notifikasiList;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  int get unreadCount =>
      _notifikasiList.where((n) => !n.sudahDibaca).length;

  // Mulai polling notifikasi (tiap 10 detik) supaya titik merah di ikon
  // lonceng pada AppBar bisa update otomatis tanpa harus buka layar
  // Notifikasi dulu. Pakai mode silent supaya tidak memicu state loading.
  void startAutoRefresh() {
    _refreshTimer?.cancel();
    _refreshTimer = Timer.periodic(const Duration(seconds: 10), (_) {
      fetchNotifikasi(silent: true);
    });
  }

  void stopAutoRefresh() {
    _refreshTimer?.cancel();
    _refreshTimer = null;
  }

  @override
  void dispose() {
    stopAutoRefresh();
    super.dispose();
  }

  // Fetch Notifikasi
  Future<void> fetchNotifikasi({bool silent = false}) async {
    if (!silent) {
      _isLoading = true;
      _errorMessage = null;
      notifyListeners();
    }

    try {
      _notifikasiList = await _apiService.getNotifikasi();
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      if (!silent) {
        _errorMessage = 'Error fetching notifikasi: $e';
        _isLoading = false;
        notifyListeners();
      }
    }
  }

  // Tandai satu notifikasi sudah dibaca
  Future<bool> markAsRead(int id) async {
    final success = await _apiService.markNotifikasiAsRead(id);
    if (success) {
      final index = _notifikasiList.indexWhere((n) => n.id == id);
      if (index != -1) {
        final old = _notifikasiList[index];
        _notifikasiList[index] = Notifikasi(
          id: old.id,
          judul: old.judul,
          isi: old.isi,
          tipe: old.tipe,
          pesananId: old.pesananId,
          data: old.data,
          untukRoles: old.untukRoles,
          dibacaAt: DateTime.now(),
          createdAt: old.createdAt,
          updatedAt: old.updatedAt,
        );
        notifyListeners();
      }
    }
    return success;
  }

  // Tandai semua notifikasi sudah dibaca
  Future<bool> markAllAsRead() async {
    final success = await _apiService.markAllNotifikasiAsRead();
    if (success) {
      await fetchNotifikasi();
    }
    return success;
  }

  // Hapus satu notifikasi
  Future<bool> deleteNotifikasi(int id) async {
    final success = await _apiService.deleteNotifikasi(id);
    if (success) {
      _notifikasiList.removeWhere((n) => n.id == id);
      notifyListeners();
    }
    return success;
  }

  // Clear Error
  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}
