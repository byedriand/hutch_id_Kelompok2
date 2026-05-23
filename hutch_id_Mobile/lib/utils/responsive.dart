import 'package:flutter/material.dart';

class Responsive {
  static bool isMobile(BuildContext context) =>
      MediaQuery.of(context).size.width < 600;

  static bool isTablet(BuildContext context) =>
      MediaQuery.of(context).size.width >= 600 &&
      MediaQuery.of(context).size.width < 1024;

  static bool isDesktop(BuildContext context) =>
      MediaQuery.of(context).size.width >= 1024;

  static double padding(BuildContext context) =>
      isMobile(context) ? 16.0 : 24.0;

  static double titleFontSize(BuildContext context) =>
      isMobile(context) ? 20.0 : 28.0;
}
