import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:provider/provider.dart';
import '../../providers/auth_provider.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});
  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen>
    with TickerProviderStateMixin {
  late AnimationController _logoCtrl;
  late AnimationController _textCtrl;
  late AnimationController _pulseCtrl;
  late AnimationController _bgCtrl;

  late Animation<double> _logoScale;
  late Animation<double> _logoOpacity;
  late Animation<double> _textOpacity;
  late Animation<Offset> _textSlide;
  late Animation<double> _pulse;

  @override
  void initState() {
    super.initState();
    SystemChrome.setSystemUIOverlayStyle(SystemUiOverlayStyle.light);

    _bgCtrl = AnimationController(
        duration: const Duration(seconds: 6), vsync: this)
      ..repeat();

    _logoCtrl = AnimationController(
        duration: const Duration(milliseconds: 1200), vsync: this);
    _logoScale = Tween<double>(begin: 0.0, end: 1.0).animate(
        CurvedAnimation(parent: _logoCtrl, curve: Curves.elasticOut));
    _logoOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(
        CurvedAnimation(
            parent: _logoCtrl, curve: const Interval(0, 0.5)));

    _textCtrl = AnimationController(
        duration: const Duration(milliseconds: 800), vsync: this);
    _textOpacity = Tween<double>(begin: 0.0, end: 1.0).animate(
        CurvedAnimation(parent: _textCtrl, curve: Curves.easeOut));
    _textSlide =
        Tween<Offset>(begin: const Offset(0, 0.4), end: Offset.zero)
            .animate(CurvedAnimation(
                parent: _textCtrl, curve: Curves.easeOutCubic));

    _pulseCtrl = AnimationController(
        duration: const Duration(milliseconds: 1400), vsync: this)
      ..repeat(reverse: true);
    _pulse = Tween<double>(begin: 0.95, end: 1.05).animate(
        CurvedAnimation(parent: _pulseCtrl, curve: Curves.easeInOut));

    _startSequence();
  }

  Future<void> _startSequence() async {
    await Future.delayed(const Duration(milliseconds: 200));
    await _logoCtrl.forward();
    await Future.delayed(const Duration(milliseconds: 100));
    await _textCtrl.forward();
    await Future.delayed(const Duration(milliseconds: 1600));
    if (!mounted) return;

    final auth = Provider.of<AuthProvider>(context, listen: false);
    if (auth.isLoggedIn) {
      Navigator.pushReplacementNamed(context, '/home');
    } else {
      Navigator.pushReplacementNamed(context, '/landing');
    }
  }

  @override
  void dispose() {
    _logoCtrl.dispose();
    _textCtrl.dispose();
    _pulseCtrl.dispose();
    _bgCtrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: AnimatedBuilder(
        animation: _bgCtrl,
        builder: (_, __) => Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: const [
                Color(0xFF0f172a),
                Color(0xFF1e3a5f),
                Color(0xFF1d4ed8),
                Color(0xFF0f172a),
              ],
              stops: [
                0.0,
                0.3 + 0.2 * (_bgCtrl.value - 0.5).abs() * 2,
                0.7,
                1.0,
              ],
            ),
          ),
          child: Stack(
            children: [
              // Floating orbs background
              ..._buildOrbs(),
              // Center content
              Center(
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // Logo with glow ring
                    AnimatedBuilder(
                      animation: _pulse,
                      builder: (_, __) => Transform.scale(
                        scale: _pulse.value,
                        child: ScaleTransition(
                          scale: _logoScale,
                          child: FadeTransition(
                            opacity: _logoOpacity,
                            child: Stack(
                              alignment: Alignment.center,
                              children: [
                                // Glow rings
                                Container(
                                  width: 160,
                                  height: 160,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    border: Border.all(
                                        color: Colors.white.withValues(alpha: 0.08),
                                        width: 24),
                                  ),
                                ),
                                Container(
                                  width: 126,
                                  height: 126,
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    border: Border.all(
                                        color: Colors.white.withValues(alpha: 0.14),
                                        width: 16),
                                  ),
                                ),
                                // Logo container
                                Container(
                                  width: 100,
                                  height: 100,
                                  padding: const EdgeInsets.all(18),
                                  decoration: BoxDecoration(
                                    shape: BoxShape.circle,
                                    gradient: RadialGradient(
                                      colors: [
                                        Colors.white.withValues(alpha: 0.22),
                                        Colors.white.withValues(alpha: 0.06),
                                      ],
                                    ),
                                    border: Border.all(
                                        color: Colors.white.withValues(alpha: 0.35),
                                        width: 2),
                                    boxShadow: [
                                      BoxShadow(
                                        color: const Color(0xFF3b82f6)
                                            .withValues(alpha: 0.5),
                                        blurRadius: 40,
                                        spreadRadius: 8,
                                      ),
                                    ],
                                  ),
                                  child: Image.asset(
                                    'assets/images/hutch-logo.png',
                                    fit: BoxFit.contain,
                                    errorBuilder: (_, __, ___) => const Icon(
                                        Icons.business_rounded,
                                        color: Colors.white,
                                        size: 52),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ),
                    ),
                    const SizedBox(height: 36),
                    // Brand text
                    SlideTransition(
                      position: _textSlide,
                      child: FadeTransition(
                        opacity: _textOpacity,
                        child: Column(
                          children: [
                            const Text(
                              'HUTCH PRESTIGE',
                              style: TextStyle(
                                fontSize: 28,
                                fontWeight: FontWeight.w900,
                                color: Colors.white,
                                letterSpacing: 3,
                              ),
                            ),
                            const SizedBox(height: 8),
                            Container(
                              width: 80,
                              height: 2,
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  colors: [
                                    Colors.transparent,
                                    Colors.white.withValues(alpha: 0.8),
                                    Colors.transparent,
                                  ],
                                ),
                                borderRadius: BorderRadius.circular(1),
                              ),
                            ),
                            const SizedBox(height: 10),
                            Text(
                              'Sistem Manajemen Pesanan',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w400,
                                color: Colors.white.withValues(alpha: 0.7),
                                letterSpacing: 1.2,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                    const SizedBox(height: 60),
                    // Loading dots
                    FadeTransition(
                      opacity: _textOpacity,
                      child: _buildLoadingDots(),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  List<Widget> _buildOrbs() {
    return [
      _orb(top: -80, left: -60, size: 260, opacity: 0.08, speed: 1.0),
      _orb(bottom: -60, right: -40, size: 220, opacity: 0.06, speed: 0.7),
      _orb(top: 200, right: -80, size: 180, opacity: 0.05, speed: 1.3),
      _orb(bottom: 180, left: -50, size: 150, opacity: 0.07, speed: 0.9),
    ];
  }

  Widget _orb({
    double? top, double? bottom, double? left, double? right,
    required double size, required double opacity, required double speed,
  }) {
    return AnimatedBuilder(
      animation: _bgCtrl,
      builder: (_, __) {
        final t = (_bgCtrl.value * speed) % 1.0;
        return Positioned(
          top: top != null ? top + 20 * (t - 0.5).abs() * 2 : null,
          bottom: bottom != null ? bottom + 15 * (t - 0.5).abs() * 2 : null,
          left: left != null ? left + 10 * (t - 0.5).abs() * 2 : null,
          right: right != null ? right + 12 * (t - 0.5).abs() * 2 : null,
          child: Container(
            width: size,
            height: size,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: RadialGradient(
                colors: [
                  const Color(0xFF3b82f6).withValues(alpha: opacity),
                  Colors.transparent,
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _buildLoadingDots() {
    return AnimatedBuilder(
      animation: _pulseCtrl,
      builder: (_, __) => Row(
        mainAxisSize: MainAxisSize.min,
        children: List.generate(3, (i) {
          final delay = i * 0.33;
          final t = ((_pulseCtrl.value + delay) % 1.0);
          final scale = 0.6 + 0.4 * (t < 0.5 ? t * 2 : (1 - t) * 2);
          return Container(
            margin: const EdgeInsets.symmetric(horizontal: 5),
            child: Transform.scale(
              scale: scale,
              child: Container(
                width: 8,
                height: 8,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: Colors.white.withValues(alpha: 0.5 + 0.5 * scale),
                ),
              ),
            ),
          );
        }),
      ),
    );
  }
}
