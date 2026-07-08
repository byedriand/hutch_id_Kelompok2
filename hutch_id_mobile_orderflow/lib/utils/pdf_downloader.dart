// Memilih implementasi savePdfBytes() yang tepat sesuai platform:
// - Flutter Web -> pdf_downloader_web.dart (pakai dart:html, trigger
//   download lewat browser, TIDAK pakai path_provider karena tidak
//   didukung di web — ini penyebab MissingPluginException sebelumnya).
// - Mobile/Desktop -> pdf_downloader_io.dart (simpan ke temp dir lalu
//   buka pakai open_filex, seperti semula).
export 'pdf_downloader_io.dart'
    if (dart.library.html) 'pdf_downloader_web.dart';
