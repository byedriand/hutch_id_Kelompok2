import 'package:flutter/material.dart';

class WelcomeScreen extends StatelessWidget {
  const WelcomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFF0A111E), // Sleek dark slate
      body: SafeArea(
        child: Column(
          children: [
            // Top Header Bar
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
              color: const Color(0xFF070C15), // Even darker bar
              child: Row(
                children: [
                  Image.asset(
                    'assets/images/hutch-logo.png',
                    width: 24,
                    height: 24,
                  ),
                  const SizedBox(width: 12),
                  const Text(
                    'Hutch.id',
                    style: TextStyle(
                      color: Color(0xFF38BDF8), // Light blue / Cyan
                      fontWeight: FontWeight.bold,
                      fontSize: 18,
                    ),
                  ),
                ],
              ),
            ),
            
            // Scrollable Content
            Expanded(
              child: SingleChildScrollView(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 24.0, vertical: 32.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      // Middle Card Container
                      Container(
                        width: double.infinity,
                        padding: const EdgeInsets.symmetric(vertical: 48, horizontal: 24),
                        decoration: BoxDecoration(
                          color: const Color(0xFF13233C),
                          borderRadius: BorderRadius.circular(24),
                          border: Border.all(
                            color: const Color(0xFF1E3A5F),
                            width: 1.5,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.2),
                              blurRadius: 25,
                              offset: const Offset(0, 10),
                            ),
                          ],
                        ),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Container(
                              width: 110,
                              height: 110,
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: const Color(0xFF0F1B2F),
                                borderRadius: BorderRadius.circular(20),
                                border: Border.all(
                                  color: const Color(0xFF2E4E7A),
                                  width: 1.5,
                                ),
                              ),
                              child: Image.asset(
                                'assets/images/hutch-logo.png',
                                fit: BoxFit.contain,
                              ),
                            ),
                            const SizedBox(height: 24),
                            const Text(
                              'HUTCH',
                              style: TextStyle(
                                color: Color(0xFF93C5FD),
                                fontWeight: FontWeight.w900,
                                fontSize: 26,
                                letterSpacing: 3,
                              ),
                            ),
                            const SizedBox(height: 4),
                            const Text(
                              'PRESTIGE',
                              style: TextStyle(
                                color: Color(0xFF60A5FA),
                                fontWeight: FontWeight.bold,
                                fontSize: 13,
                                letterSpacing: 5,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 32),

                      // Badge
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        decoration: BoxDecoration(
                          color: const Color(0xFF0F2642),
                          borderRadius: BorderRadius.circular(30),
                          border: Border.all(
                            color: const Color(0xFF1E40AF),
                            width: 1.5,
                          ),
                        ),
                        child: const Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Text(
                              '⚡ Order Management & AI Terpadu',
                              style: TextStyle(
                                color: Color(0xFF38BDF8),
                                fontWeight: FontWeight.bold,
                                fontSize: 12,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 24),

                      // RichText Title
                      RichText(
                        textAlign: TextAlign.center,
                        text: const TextSpan(
                          style: TextStyle(
                            fontSize: 22,
                            fontWeight: FontWeight.w900,
                            height: 1.35,
                            fontFamily: 'Inter',
                          ),
                          children: [
                            TextSpan(
                              text: 'Platform Manajemen ',
                              style: TextStyle(color: Colors.white),
                            ),
                            TextSpan(
                              text: '\nPesanan & AI Chatbot',
                              style: TextStyle(color: Color(0xFF38BDF8)),
                            ),
                            TextSpan(
                              text: '\nuntuk UMKM Indonesia',
                              style: TextStyle(color: Colors.white),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),

                      // Description
                      const Text(
                        'Hutch.id adalah platform all-in-one yang dirancang khusus untuk UMKM. Kelola pesanan, inventori, pelanggan, dan dapatkan dukungan AI 24/7 dalam satu dashboard yang mudah digunakan. Tingkatkan efisiensi bisnis Anda dan fokus pada pertumbuhan.',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontSize: 13,
                          color: Color(0xFF94A3B8), // Slate 400
                          height: 1.6,
                        ),
                      ),
                      const SizedBox(height: 40),

                      // Login Button
                      Container(
                        width: double.infinity,
                        height: 54,
                        decoration: BoxDecoration(
                          gradient: const LinearGradient(
                            colors: [
                              Color(0xFF0EA5E9), // Cyan
                              Color(0xFF0284C7), // Blue
                            ],
                          ),
                          borderRadius: BorderRadius.circular(14),
                          boxShadow: [
                            BoxShadow(
                              color: const Color(0xFF0EA5E9).withValues(alpha: 0.3),
                              blurRadius: 15,
                              offset: const Offset(0, 5),
                            ),
                          ],
                        ),
                        child: ElevatedButton(
                          onPressed: () {
                            Navigator.pushNamed(context, '/login');
                          },
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.transparent,
                            shadowColor: Colors.transparent,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(14),
                            ),
                          ),
                          child: const Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.arrow_forward_rounded,
                                color: Colors.white,
                                size: 20,
                              ),
                              SizedBox(width: 8),
                              Text(
                                'Login',
                                style: TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
                                  color: Colors.white,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
