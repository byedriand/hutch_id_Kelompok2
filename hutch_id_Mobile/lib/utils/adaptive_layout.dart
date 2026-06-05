import 'package:flutter/material.dart';
import 'responsive.dart';

/// Adaptive Layout - Helper untuk membuat layout yang responsif di berbagai ukuran
class AdaptiveLayout {
  /// Build responsive container dengan adaptive padding
  static Widget responsiveContainer({
    required BuildContext context,
    required Widget child,
    EdgeInsets? customPadding,
  }) {
    final padding =
        customPadding ??
        EdgeInsets.symmetric(
          horizontal: Responsive.horizontalPadding(context),
          vertical: Responsive.verticalPadding(context),
        );

    return SingleChildScrollView(
      child: Padding(padding: padding, child: child),
    );
  }

  /// Build responsive grid dengan auto-adjusting columns
  static Widget responsiveGrid({
    required BuildContext context,
    required List<Widget> children,
    int? overrideColumns,
    required Axis direction,
  }) {
    final columns = overrideColumns ?? Responsive.dashboardGridColumns(context);
    final crossAxisSpacing = Responsive.padding(context);
    final mainAxisSpacing = Responsive.padding(context);

    if (children.isEmpty) {
      return const SizedBox.shrink();
    }

    if (Responsive.isMobile(context) && direction == Axis.vertical) {
      // Untuk mobile, gunakan ListView untuk vertical scrolling yang smooth
      return ListView.separated(
        shrinkWrap: true,
        physics: const NeverScrollableScrollPhysics(),
        itemCount: children.length,
        separatorBuilder: (context, index) => SizedBox(height: mainAxisSpacing),
        itemBuilder: (context, index) => children[index],
      );
    }

    // Untuk tablet/desktop, gunakan GridView
    return GridView.count(
      crossAxisCount: columns,
      crossAxisSpacing: crossAxisSpacing,
      mainAxisSpacing: mainAxisSpacing,
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      children: children,
    );
  }

  /// Build adaptive AppBar dengan responsive styling
  static AppBar adaptiveAppBar({
    required BuildContext context,
    required String title,
    List<Widget>? actions,
    Widget? leading,
    bool centerTitle = false,
  }) {
    final titleSize = Responsive.titleFontSize(context);

    return AppBar(
      title: Text(
        title,
        style: TextStyle(fontSize: titleSize, fontWeight: FontWeight.bold),
      ),
      leading: leading,
      actions: actions,
      centerTitle: centerTitle,
      elevation: 0,
      backgroundColor: const Color(0xFF0066cc),
      foregroundColor: Colors.white,
    );
  }

  /// Build responsive button bar dengan wrapping support
  static Widget responsiveButtonBar({
    required BuildContext context,
    required List<Widget> buttons,
    MainAxisAlignment alignment = MainAxisAlignment.spaceEvenly,
  }) {
    final isMobile = Responsive.isMobile(context);
    final padding = Responsive.padding(context);

    if (isMobile) {
      return Column(spacing: padding * 0.5, children: buttons);
    }

    return Row(
      mainAxisAlignment: alignment,
      spacing: padding * 0.5,
      children: buttons,
    );
  }

  /// Build responsive form field dengan adaptive sizing
  static Widget responsiveFormField({
    required BuildContext context,
    required TextEditingController controller,
    required String label,
    String? hint,
    TextInputType keyboardType = TextInputType.text,
    IconData? prefixIcon,
    Widget? suffixIcon,
    int maxLines = 1,
    String? Function(String?)? validator,
  }) {
    final padding = Responsive.padding(context);

    return Padding(
      padding: EdgeInsets.symmetric(vertical: padding * 0.3),
      child: TextFormField(
        controller: controller,
        keyboardType: keyboardType,
        maxLines: maxLines,
        minLines: maxLines == 1 ? 1 : null,
        decoration: InputDecoration(
          labelText: label,
          hintText: hint,
          prefixIcon: prefixIcon != null ? Icon(prefixIcon) : null,
          suffixIcon: suffixIcon,
          contentPadding: EdgeInsets.symmetric(
            horizontal: padding * 0.75,
            vertical: padding * 0.5,
          ),
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(
              Responsive.borderRadius(context),
            ),
          ),
        ),
        validator: validator,
      ),
    );
  }

  /// Build responsive list item dengan adaptive spacing
  static Widget responsiveListItem({
    required BuildContext context,
    required Widget child,
    EdgeInsets? customPadding,
    Color backgroundColor = Colors.white,
    bool showDivider = true,
  }) {
    final padding =
        customPadding ??
        EdgeInsets.symmetric(
          horizontal: Responsive.padding(context),
          vertical: Responsive.padding(context) * 0.6,
        );

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(color: backgroundColor, padding: padding, child: child),
        if (showDivider) Divider(height: 1, color: Colors.grey.shade200),
      ],
    );
  }

  /// Build responsive section header
  static Widget responsiveSectionHeader({
    required BuildContext context,
    required String title,
    VoidCallback? onViewAll,
    IconData? icon,
  }) {
    final titleSize = Responsive.subtitleFontSize(context);
    final padding = Responsive.padding(context);

    return Padding(
      padding: EdgeInsets.symmetric(
        horizontal: Responsive.horizontalPadding(context),
        vertical: padding * 0.6,
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Row(
            children: [
              if (icon != null) ...[
                Icon(
                  icon,
                  size: Responsive.iconSize(context),
                  color: const Color(0xFF0066cc),
                ),
                SizedBox(width: padding * 0.4),
              ],
              Text(
                title,
                style: TextStyle(
                  fontSize: titleSize,
                  fontWeight: FontWeight.bold,
                  color: const Color(0xFF0F172A),
                ),
              ),
            ],
          ),
          if (onViewAll != null)
            GestureDetector(
              onTap: onViewAll,
              child: Text(
                'Lihat Semua',
                style: TextStyle(
                  fontSize: Responsive.smallFontSize(context),
                  color: const Color(0xFF0066cc),
                  fontWeight: FontWeight.w600,
                ),
              ),
            ),
        ],
      ),
    );
  }

  /// Build responsive card dengan adaptive sizing
  static Widget responsiveCard({
    required BuildContext context,
    required Widget child,
    EdgeInsets? padding,
    Color? backgroundColor,
    double? elevation,
    BorderRadius? borderRadius,
    VoidCallback? onTap,
  }) {
    final cardPadding =
        padding ?? EdgeInsets.all(Responsive.padding(context) * 0.75);

    return GestureDetector(
      onTap: onTap,
      child: Card(
        elevation: elevation ?? 2,
        color: backgroundColor,
        shape: RoundedRectangleBorder(
          borderRadius:
              borderRadius ??
              BorderRadius.circular(Responsive.borderRadius(context)),
        ),
        child: Padding(padding: cardPadding, child: child),
      ),
    );
  }

  /// Build adaptive single/multi-column layout based on screen size
  static Widget responsiveLayout({
    required BuildContext context,
    required Widget mobileLayout,
    Widget? tabletLayout,
    Widget? desktopLayout,
  }) {
    if (Responsive.isDesktop(context) && desktopLayout != null) {
      return desktopLayout;
    }
    if (Responsive.isTablet(context) && tabletLayout != null) {
      return tabletLayout;
    }
    return mobileLayout;
  }

  /// Build responsive FAB with adaptive positioning
  static FloatingActionButton? adaptiveFAB({
    required BuildContext context,
    required VoidCallback onPressed,
    required IconData icon,
    String? tooltip,
    Color? backgroundColor,
  }) {
    return FloatingActionButton(
      onPressed: onPressed,
      tooltip: tooltip,
      backgroundColor: backgroundColor ?? const Color(0xFF0066cc),
      elevation: Responsive.isMobile(context) ? 4 : 6,
      child: Icon(icon),
    );
  }
}
