import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';

class AppColors {
  static const navy = Color(0xFF0F2744);
  static const blue = Color(0xFF1A3F6F);
  static const accent = Color(0xFF2D7DD2);
  static const light = Color(0xFFE8F0FA);
  static const bg = Color(0xFFF0F4FA);
  static const white = Color(0xFFFFFFFF);
  static const border = Color(0xFFD1DCE8);
  static const gray = Color(0xFF64748B);
  static const green = Color(0xFF16A34A);
  static const red = Color(0xFFDC2626);
  static const yellow = Color(0xFFD97706);
  static const purple = Color(0xFF7C3AED);

  // Badge backgrounds
  static const waitBg = Color(0xFFFEF3C7);
  static const waitFg = Color(0xFF92400E);
  static const confBg = Color(0xFFDBEAFE);
  static const confFg = Color(0xFF1E40AF);
  static const prodBg = Color(0xFFF3E8FF);
  static const prodFg = Color(0xFF6B21A8);
  static const readyBg = Color(0xFFDCFCE7);
  static const readyFg = Color(0xFF166534);
  static const doneBg = Color(0xFFF0FDF4);
  static const doneFg = Color(0xFF15803D);
  static const cancelBg = Color(0xFFF1F5F9);
  static const cancelFg = Color(0xFF64748B);
}

class AppTheme {
  static ThemeData get theme => ThemeData(
        useMaterial3: true,
        colorScheme: ColorScheme.fromSeed(
          seedColor: AppColors.accent,
          primary: AppColors.accent,
          surface: AppColors.white,
        ),
        scaffoldBackgroundColor: AppColors.bg,
        textTheme: GoogleFonts.plusJakartaSansTextTheme().copyWith(
          displayLarge:
              GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800),
          displayMedium:
              GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w800),
          titleLarge: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700),
          titleMedium: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w700),
          bodyLarge: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w400),
          bodyMedium: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w400),
          labelLarge: GoogleFonts.plusJakartaSans(fontWeight: FontWeight.w600),
        ),
        appBarTheme: AppBarTheme(
          backgroundColor: AppColors.navy,
          foregroundColor: AppColors.white,
          elevation: 0,
          centerTitle: false,
          titleTextStyle: GoogleFonts.plusJakartaSans(
            fontSize: 17,
            fontWeight: FontWeight.w700,
            color: AppColors.white,
          ),
        ),
        bottomNavigationBarTheme: const BottomNavigationBarThemeData(
          backgroundColor: AppColors.navy,
          selectedItemColor: AppColors.white,
          unselectedItemColor: Color(0x7FFFFFFF),
          selectedIconTheme: IconThemeData(size: 22),
          unselectedIconTheme: IconThemeData(size: 22),
          showSelectedLabels: true,
          showUnselectedLabels: true,
          type: BottomNavigationBarType.fixed,
        ),
        elevatedButtonTheme: ElevatedButtonThemeData(
          style: ElevatedButton.styleFrom(
            backgroundColor: AppColors.accent,
            foregroundColor: AppColors.white,
            elevation: 0,
            shape:
                RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
            textStyle: GoogleFonts.plusJakartaSans(
                fontWeight: FontWeight.w700, fontSize: 13),
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
          ),
        ),
        inputDecorationTheme: InputDecorationTheme(
          filled: true,
          fillColor: AppColors.white,
          contentPadding:
              const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(10),
            borderSide: const BorderSide(color: AppColors.border),
          ),
          enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(10),
            borderSide: const BorderSide(color: AppColors.border, width: 1.5),
          ),
          focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(10),
            borderSide: const BorderSide(color: AppColors.accent, width: 1.5),
          ),
          labelStyle: GoogleFonts.plusJakartaSans(
            color: AppColors.gray,
            fontWeight: FontWeight.w500,
            fontSize: 13,
          ),
          hintStyle: GoogleFonts.plusJakartaSans(
            color: AppColors.gray,
            fontSize: 12,
          ),
        ),
        cardTheme: CardThemeData(
          color: AppColors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
            side: const BorderSide(color: AppColors.border),
          ),
          margin: const EdgeInsets.only(bottom: 14),
        ),
        dividerTheme: const DividerThemeData(
          color: AppColors.border,
          thickness: 1,
          space: 0,
        ),
      );
}
