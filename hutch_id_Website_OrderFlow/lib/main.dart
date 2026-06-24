import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_inappwebview/flutter_inappwebview.dart';
import 'package:permission_handler/permission_handler.dart';
import 'config/environment.dart';

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

    return Scaffold(
      extendBodyBehindAppBar: true,
      body: Stack(
        children: [
          InAppWebView(
            initialUrlRequest: URLRequest(url: WebUri(EnvironmentConfig.baseUrl)),
            initialSettings: InAppWebViewSettings(
              // JavaScript
              javaScriptEnabled: true,
              javaScriptCanOpenWindowsAutomatically: false,

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
            ),
            onWebViewCreated: (controller) {
              webViewController = controller;
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
              if (request.isForMainFrame == true) {
                setState(() {
                  errorMessage = error.description;
                });
              }
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
      floatingActionButton: FloatingActionButton(
        mini: true,
        backgroundColor: Colors.blue,
        onPressed: () => webViewController?.reload(),
        child: const Icon(Icons.refresh, color: Colors.white),
      ),
    );
  }
}