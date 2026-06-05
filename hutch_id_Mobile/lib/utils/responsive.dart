import 'package:flutter/material.dart';

class Responsive {
  // Device Type Checks
  static bool isMobile(BuildContext context) =>
      MediaQuery.of(context).size.width < 600;

  static bool isTablet(BuildContext context) =>
      MediaQuery.of(context).size.width >= 600 &&
      MediaQuery.of(context).size.width < 1024;

  static bool isDesktop(BuildContext context) =>
      MediaQuery.of(context).size.width >= 1024;

  static bool isLandscape(BuildContext context) =>
      MediaQuery.of(context).orientation == Orientation.landscape;

  static bool isMicroScreen(BuildContext context) =>
      MediaQuery.of(context).size.width < 360;

  // Responsive Dimensions
  static double padding(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 12.0;
    if (width < 600) return 16.0;
    if (width < 1024) return 20.0;
    return 24.0;
  }

  static double horizontalPadding(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 8.0;
    if (width < 400) return 12.0;
    if (width < 600) return 16.0;
    if (width < 1024) return 20.0;
    return 28.0;
  }

  static double verticalPadding(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 8.0;
    if (width < 600) return 12.0;
    if (width < 1024) return 16.0;
    return 20.0;
  }

  // Responsive Font Sizes
  static double titleFontSize(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 16.0;
    if (width < 400) return 18.0;
    if (width < 600) return 20.0;
    if (width < 1024) return 24.0;
    return 28.0;
  }

  static double subtitleFontSize(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 10.0;
    if (width < 400) return 12.0;
    if (width < 600) return 13.0;
    if (width < 1024) return 14.0;
    return 16.0;
  }

  static double bodyFontSize(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 10.0;
    if (width < 400) return 11.0;
    if (width < 600) return 12.0;
    if (width < 1024) return 13.0;
    return 14.0;
  }

  static double smallFontSize(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 8.0;
    if (width < 400) return 9.0;
    if (width < 600) return 10.0;
    if (width < 1024) return 11.0;
    return 12.0;
  }

  // Grid Columns
  static int dashboardGridColumns(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 1;
    if (width < 400) return 1;
    if (width < 600) return 2;
    if (width < 1024) return 3;
    return 4;
  }

  static int listGridColumns(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 600) return 1;
    if (width < 1024) return 2;
    return 3;
  }

  // Responsive Heights
  static double buttonHeight(BuildContext context) =>
      isMobile(context) ? 44.0 : 48.0;

  static double appBarHeight(BuildContext context) =>
      isMobile(context) ? 56.0 : 64.0;

  static double cardHeight(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 120.0;
    if (width < 400) return 140.0;
    if (width < 600) return 150.0;
    return 160.0;
  }

  // Icon Sizes
  static double iconSize(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 18.0;
    if (width < 400) return 20.0;
    if (width < 600) return 24.0;
    return 28.0;
  }

  static double smallIconSize(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 14.0;
    if (width < 400) return 16.0;
    if (width < 600) return 18.0;
    return 20.0;
  }

  // Border Radius
  static double borderRadius(BuildContext context) {
    final width = MediaQuery.of(context).size.width;
    if (width < 360) return 8.0;
    if (width < 600) return 12.0;
    return 16.0;
  }

  // Get device width
  static double deviceWidth(BuildContext context) =>
      MediaQuery.of(context).size.width;

  static double deviceHeight(BuildContext context) =>
      MediaQuery.of(context).size.height;

  // Screen Dimensions
  static Size screenSize(BuildContext context) => MediaQuery.of(context).size;

  // Safe Area Padding
  static EdgeInsets safePadding(BuildContext context) =>
      MediaQuery.of(context).padding;
}
