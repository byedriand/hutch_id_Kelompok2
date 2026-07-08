import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_inappwebview/flutter_inappwebview.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:url_launcher/url_launcher.dart';
import 'config/environment.dart';

/// Domains that belong to other companies/services (Instagram, GitHub, etc).
/// Links to these should always open in the phone's real browser or in the
/// native app (e.g. tapping an Instagram link opens the Instagram app if
/// installed) — never inside our own in-app WebView. Loading them inside our
/// WebView is what causes the "stuck with no way back" error: those sites
/// expect a normal browser tab (with its own back/close controls, and the
/// ability to redirect to an app-only `instagram://` style link), which our
/// single WebView frame doesn't provide.
bool _isExternalDomain(Uri uri) {
  final host = uri.host.toLowerCase();
  final ownHost = Uri.tryParse(EnvironmentConfig.baseUrl)?.host.toLowerCase() ?? '';
  if (ownHost.isNotEmpty && (host == ownHost || host.endsWith('.$ownHost'))) {
    return false;
  }
  const externalHosts = [
    'instagram.com',
    'github.com',
    'wa.me',
    'whatsapp.com',
    'facebook.com',
    'twitter.com',
    'x.com',
    'tiktok.com',
    'youtube.com',
    'linkedin.com',
  ];
  return externalHosts.any((h) => host == h || host.endsWith('.$h'));
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  SystemChrome.setEnabledSystemUIMode(SystemUiMode.edgeToEdge);
  SystemChrome.setSystemUIOverlayStyle(
    const SystemUiOverlayStyle(
      statusBarColor: Colors.transparent,
      statusBarIconBrightness: Brightness.dark,
      statusBarBrightness: Brightness.light,
    ),
  );
  runApp(const HutchApp());
}

class HutchApp extends StatelessWidget {
  const HutchApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Hutch Indonesia',
      theme: ThemeData(primarySwatch: Colors.blue, useMaterial3: true),
      home: const MobileVersion(),
      debugShowCheckedModeBanner: false,
    );
  }
}

class MobileVersion extends StatefulWidget {
  const MobileVersion({super.key});

  @override
  State<MobileVersion> createState() => _MobileVersionState();
}

class _MobileVersionState extends State<MobileVersion> {
  InAppWebViewController? webViewController;
  bool isLoading = true;
  String? errorMessage;

  @override
  void initState() {
    super.initState();
    _requestMediaPermissions();
  }

  Future<void> _requestMediaPermissions() async {
    await [
      Permission.camera,
      Permission.photos,
      Permission.storage,
    ].request();
  }

  /// Opens a link with the phone's own browser/app (e.g. the Instagram app
  /// if installed, otherwise Chrome/Safari) instead of our WebView. This is
  /// what gives the user a real, working back/close button on Instagram's
  /// or GitHub's own page — something our single in-app WebView frame can
  /// never provide.
  Future<void> _openExternally(Uri uri) async {
    try {
      final opened = await launchUrl(uri, mode: LaunchMode.externalApplication);
      if (!opened && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Tidak dapat membuka ${uri.host}')),
        );
      }
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Tidak dapat membuka ${uri.host}')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (errorMessage != null) {
      return Scaffold(
        body: SafeArea(
          child: Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.error_outline, size: 64, color: Colors.red),
                const SizedBox(height: 20),
                const Text('Connection Error', style: TextStyle(fontSize: 18)),
                const SizedBox(height: 10),
                Text(
                  errorMessage!,
                  textAlign: TextAlign.center,
                  style: const TextStyle(fontSize: 14, color: Colors.grey),
                ),
                const SizedBox(height: 30),
                ElevatedButton(
                  onPressed: () {
                    setState(() => errorMessage = null);
                    webViewController?.reload();
                  },
                  child: const Text('Retry'),
                ),
              ],
            ),
          ),
        ),
      );
    }

    return PopScope(
      // If the WebView has internal navigation history (e.g. the user
      // navigated from the landing page to login), the phone's back button
      // should step back through that first instead of closing the app —
      // this is the other half of "never feel stuck with no way back".
      canPop: false,
      onPopInvokedWithResult: (didPop, result) async {
        if (didPop) return;
        final controller = webViewController;
        if (controller != null && await controller.canGoBack()) {
          controller.goBack();
        } else {
          SystemNavigator.pop();
        }
      },
      child: Scaffold(
      extendBodyBehindAppBar: true,
      body: Stack(
        children: [
          InAppWebView(
            initialUrlRequest: URLRequest(url: WebUri(EnvironmentConfig.baseUrl)),
            initialSettings: InAppWebViewSettings(
              // JavaScript
              javaScriptEnabled: true,

              // Performa & rendering
              useHybridComposition: false,
              hardwareAcceleration: true,

              // Cache
              cacheEnabled: true,
              cacheMode: CacheMode.LOAD_DEFAULT,

              // File access
              allowFileAccess: true,
              allowFileAccessFromFileURLs: true,
              allowUniversalAccessFromFileURLs: true,

              // File chooser
              useOnShowFileChooser: true,

              // Media
              mediaPlaybackRequiresUserGesture: false,

              // Scroll & zoom
              supportZoom: false,
              builtInZoomControls: false,
              displayZoomControls: false,
              overScrollMode: OverScrollMode.NEVER,

              // Teks
              minimumFontSize: 12,
              defaultFontSize: 16,

              // Storage
              domStorageEnabled: true,
              databaseEnabled: true,
              geolocationEnabled: false,
              thirdPartyCookiesEnabled: true,

              // Let us decide what happens with target="_blank" links and
              // window.open() instead of the WebView opening a window we
              // don't control.
              supportMultipleWindows: true,
              javaScriptCanOpenWindowsAutomatically: true,
            ),
            onWebViewCreated: (controller) {
              webViewController = controller;
            },
            shouldOverrideUrlLoading: (controller, navigationAction) async {
              final uri = navigationAction.request.url;
              if (uri == null) {
                return NavigationActionPolicy.ALLOW;
              }

              // Anything that isn't a normal http/https page (custom app
              // schemes like instagram://, intent://, mailto:, tel:, etc.)
              // can never be loaded inside a WebView frame — that's exactly
              // what produced the net::ERR_UNKNOWN_URL_SCHEME error. Hand
              // those off to the OS, which knows how to route them.
              if (uri.scheme != 'http' && uri.scheme != 'https') {
                _openExternally(uri);
                return NavigationActionPolicy.CANCEL;
              }

              // Links to other companies' sites (Instagram, GitHub, ...)
              // open in the phone's normal browser/app instead of inside
              // our WebView, so the user keeps a real back button and can
              // never get stuck on someone else's page with no way out.
              if (_isExternalDomain(uri)) {
                _openExternally(uri);
                return NavigationActionPolicy.CANCEL;
              }

              return NavigationActionPolicy.ALLOW;
            },
            onCreateWindow: (controller, createWindowAction) async {
              // This fires for target="_blank" links / window.open(). Same
              // rule as above: our own site stays inside the app, anything
              // else goes to the real browser.
              final uri = createWindowAction.request.url;
              if (uri != null) {
                _openExternally(uri);
              }
              return false;
            },
            onLoadStart: (controller, url) {
              setState(() => isLoading = true);
            },
            onLoadStop: (controller, url) async {
              setState(() => isLoading = false);
              await controller.evaluateJavascript(source: """
                var meta = document.querySelector('meta[name="viewport"]');
                if (!meta) {
                  meta = document.createElement('meta');
                  meta.name = 'viewport';
                  document.head.appendChild(meta);
                }
                meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0';
              """);
            },
            onReceivedError: (controller, request, error) {
              if (request.isForMainFrame != true) {
                return;
              }
              // ERR_UNKNOWN_URL_SCHEME / "net::ERR_FAILED" with code -1 here
              // means a navigation we already redirected to the external
              // browser via shouldOverrideUrlLoading. Showing the red
              // "Connection Error" screen for that would be wrong — the
              // link did open successfully, just outside this WebView.
              final description = error.description.toLowerCase();
              if (description.contains('err_unknown_url_scheme') ||
                  description.contains('cancel')) {
                return;
              }
              setState(() {
                errorMessage = error.description;
              });
            },
            onShowFileChooser: (controller, fileChooserParams) async {
              return null;
            },
            onPermissionRequest: (controller, request) async {
              return PermissionResponse(
                resources: request.resources,
                action: PermissionResponseAction.GRANT,
              );
            },
          ),
          if (isLoading)
            const Center(
              child: CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Colors.blue),
              ),
            ),
        ],
      ),
      floatingActionButton: Padding(
        padding: const EdgeInsets.only(top: 40),
        child: FloatingActionButton(
          mini: true,
          backgroundColor: Colors.blue,
          onPressed: () => webViewController?.reload(),
          child: const Icon(Icons.refresh, color: Colors.white),
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.endTop,
      ),
    );
  }
}