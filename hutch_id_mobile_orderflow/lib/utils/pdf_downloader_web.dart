import 'dart:typed_data';
import 'dart:html' as html;

/// Trigger download PDF langsung lewat browser (web). Tidak pakai
/// path_provider sama sekali karena package itu tidak punya implementasi
/// di platform web — itu sebabnya sebelumnya muncul
/// "MissingPluginException: getTemporaryDirectory".
Future<void> savePdfBytes(Uint8List bytes, String filename) async {
  final blob = html.Blob([bytes], 'application/pdf');
  final url = html.Url.createObjectUrlFromBlob(blob);
  final anchor = html.AnchorElement(href: url)
    ..setAttribute('download', filename)
    ..style.display = 'none';
  html.document.body?.children.add(anchor);
  anchor.click();
  anchor.remove();
  html.Url.revokeObjectUrl(url);
}
