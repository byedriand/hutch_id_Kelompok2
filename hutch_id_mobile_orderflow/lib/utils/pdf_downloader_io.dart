import 'dart:io';
import 'dart:typed_data';
import 'package:path_provider/path_provider.dart';
import 'package:open_filex/open_filex.dart';

/// Simpan bytes PDF ke direktori temp lalu buka dengan aplikasi default
/// (PDF viewer) bawaan perangkat. Dipakai untuk Android/iOS/Desktop.
Future<void> savePdfBytes(Uint8List bytes, String filename) async {
  final dir = await getTemporaryDirectory();
  final file = File('${dir.path}/$filename');
  await file.writeAsBytes(bytes, flush: true);
  await OpenFilex.open(file.path);
}
