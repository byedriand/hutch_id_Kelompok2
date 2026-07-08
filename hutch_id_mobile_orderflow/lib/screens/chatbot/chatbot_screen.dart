import 'package:flutter/material.dart';
import 'package:flutter/gestures.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../services/api_service.dart';

/// Model pesan chat, sama fungsinya dengan elemen `.chat-message` pada
/// chatbot website (resources/views/layouts/app.blade.php).
class _ChatMessage {
  final String sender; // 'user' | 'bot'
  final String text;
  _ChatMessage({required this.sender, required this.text});
}

/// Layar Hutch AI Assistant untuk mobile.
///
/// Ini adalah versi mobile dari widget "ChatBot AI" pada website
/// (lihat tombol `.chatbot-btn` & modal `#chatbotModal` di
/// resources/views/layouts/app.blade.php). Fitur yang disamakan:
/// - Pesan sambutan awal
/// - Kirim pesan ke backend yang sama (POST /api/chatbot/message)
/// - Indikator "sedang mengetik" (typing indicator)
/// - Format pesan: **bold**, newline, dan [teks](url) link
/// - Tombol hapus riwayat chat (dengan konfirmasi)
/// - Bubble chat user (biru) vs bot (putih), skema warna sama seperti web
class ChatbotScreen extends StatefulWidget {
  const ChatbotScreen({super.key});

  @override
  State<ChatbotScreen> createState() => _ChatbotScreenState();
}

class _ChatbotScreenState extends State<ChatbotScreen> {
  static const _kPrimaryBlue = Color(0xFF2D7DD2);
  static const _kAccentCyan = Color(0xFF00D4FF);
  static const _kBubbleBorder = Color(0xFFE5ECF4);

  final ApiService _apiService = ApiService();
  final TextEditingController _inputController = TextEditingController();
  final ScrollController _scrollController = ScrollController();
  final FocusNode _inputFocusNode = FocusNode();

  final List<_ChatMessage> _messages = [];
  bool _isTyping = false;

  static const String _greeting =
      '👋 Halo! Saya adalah AI Assistant Hutch. Bagaimana saya bisa membantu Anda hari ini?';

  @override
  void initState() {
    super.initState();
    _messages.add(_ChatMessage(sender: 'bot', text: _greeting));
  }

  @override
  void dispose() {
    _inputController.dispose();
    _scrollController.dispose();
    _inputFocusNode.dispose();
    super.dispose();
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (_scrollController.hasClients) {
        _scrollController.animateTo(
          _scrollController.position.maxScrollExtent,
          duration: const Duration(milliseconds: 250),
          curve: Curves.easeOut,
        );
      }
    });
  }

  Future<void> _sendMessage() async {
    final message = _inputController.text.trim();
    if (message.isEmpty) return;

    setState(() {
      _messages.add(_ChatMessage(sender: 'user', text: message));
      _inputController.clear();
      _isTyping = true;
    });
    _scrollToBottom();

    final result = await _apiService.sendChatbotMessage(message);

    if (!mounted) return;
    setState(() {
      _isTyping = false;
      final reply = result['reply'] as String? ??
          'Maaf, saya tidak dapat memproses pesan Anda saat ini. Silakan coba lagi.';
      _messages.add(_ChatMessage(sender: 'bot', text: reply));
    });
    _scrollToBottom();
  }

  void _clearChatHistory() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
        title: const Text('Hapus Riwayat Chat?'),
        content: const Text(
          'Semua percakapan yang tersimpan pada sesi ini akan dihapus.',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('Batal'),
          ),
          FilledButton(
            style: FilledButton.styleFrom(backgroundColor: Colors.red[700]),
            onPressed: () {
              Navigator.pop(context);
              setState(() {
                _messages
                  ..clear()
                  ..add(
                    _ChatMessage(
                      sender: 'bot',
                      text:
                          '👋 Chat telah dihapus. Bagaimana saya bisa membantu Anda sekarang?',
                    ),
                  );
              });
            },
            child: const Text('Hapus'),
          ),
        ],
      ),
    );
  }

  Future<void> _openLink(String url) async {
    final uri = Uri.tryParse(url);
    if (uri == null) return;
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }

  /// Format pesan menyerupai `formatChatMessage()` pada website:
  /// mendukung **bold** dan [teks](url), serta baris baru.
  List<InlineSpan> _buildFormattedSpans(String text, {required bool isUser}) {
    final baseColor = isUser ? Colors.white : const Color(0xFF17233D);
    final linkColor = isUser ? Colors.white : _kPrimaryBlue;
    final boldColor = isUser ? Colors.white : _kPrimaryBlue;

    final spans = <InlineSpan>[];
    final pattern = RegExp(r'\*\*(.+?)\*\*|\[(.+?)\]\((.+?)\)');
    int lastEnd = 0;

    for (final match in pattern.allMatches(text)) {
      if (match.start > lastEnd) {
        spans.add(
          TextSpan(
            text: text.substring(lastEnd, match.start),
            style: TextStyle(color: baseColor),
          ),
        );
      }

      if (match.group(1) != null) {
        // **bold**
        spans.add(
          TextSpan(
            text: match.group(1),
            style: TextStyle(color: boldColor, fontWeight: FontWeight.w700),
          ),
        );
      } else if (match.group(2) != null) {
        // [label](url)
        final label = match.group(2)!;
        final url = match.group(3)!;
        spans.add(
          TextSpan(
            text: label,
            style: TextStyle(
              color: linkColor,
              decoration: TextDecoration.underline,
              fontWeight: FontWeight.w600,
            ),
            recognizer: TapGestureRecognizer()
              ..onTap = () => _openLink(url),
          ),
        );
      }

      lastEnd = match.end;
    }

    if (lastEnd < text.length) {
      spans.add(
        TextSpan(
          text: text.substring(lastEnd),
          style: TextStyle(color: baseColor),
        ),
      );
    }

    if (spans.isEmpty) {
      spans.add(TextSpan(text: text, style: TextStyle(color: baseColor)));
    }

    return spans;
  }

  Widget _buildMessageBubble(_ChatMessage message) {
    final isUser = message.sender == 'user';
    final lines = message.text.split('\n');

    final richTextSpans = <InlineSpan>[];
    for (var i = 0; i < lines.length; i++) {
      richTextSpans.addAll(_buildFormattedSpans(lines[i], isUser: isUser));
      if (i != lines.length - 1) {
        richTextSpans.add(const TextSpan(text: '\n'));
      }
    }

    return Align(
      alignment: isUser ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        constraints: BoxConstraints(
          maxWidth: MediaQuery.of(context).size.width * 0.78,
        ),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
        decoration: BoxDecoration(
          gradient: isUser
              ? const LinearGradient(
                  colors: [_kPrimaryBlue, Color(0xFF1F6BB8)],
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                )
              : null,
          color: isUser ? null : Colors.white,
          borderRadius: BorderRadius.circular(14),
          border: isUser ? null : Border.all(color: _kBubbleBorder),
          boxShadow: [
            BoxShadow(
              color: (isUser ? _kPrimaryBlue : _kPrimaryBlue).withValues(
                alpha: isUser ? 0.28 : 0.06,
              ),
              blurRadius: isUser ? 12 : 8,
              offset: const Offset(0, 3),
            ),
          ],
        ),
        child: RichText(
          text: TextSpan(
            style: const TextStyle(fontSize: 14.5, height: 1.45),
            children: richTextSpans,
          ),
        ),
      ),
    );
  }

  Widget _buildTypingIndicator() {
    return Align(
      alignment: Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.only(bottom: 10),
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(color: _kBubbleBorder),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: const _TypingDots(),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFF3F8FF),
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(64),
        child: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [_kPrimaryBlue, _kAccentCyan],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
          ),
          child: SafeArea(
            bottom: false,
            child: Padding(
              padding: const EdgeInsets.symmetric(horizontal: 8),
              child: Row(
                children: [
                  IconButton(
                    icon: const Icon(Icons.arrow_back, color: Colors.white),
                    onPressed: () => Navigator.pop(context),
                  ),
                  const Icon(Icons.smart_toy_rounded, color: Colors.white),
                  const SizedBox(width: 8),
                  const Expanded(
                    child: Text(
                      'Hutch AI Assistant',
                      style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.w700,
                        fontSize: 16.5,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  IconButton(
                    tooltip: 'Hapus chat',
                    icon: const Icon(
                      Icons.delete_outline_rounded,
                      color: Colors.white,
                    ),
                    onPressed: _clearChatHistory,
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
      body: SafeArea(
        child: Column(
          children: [
            Expanded(
              child: ListView.builder(
                controller: _scrollController,
                padding: const EdgeInsets.all(16),
                itemCount: _messages.length + (_isTyping ? 1 : 0),
                itemBuilder: (context, index) {
                  if (index == _messages.length) {
                    return _buildTypingIndicator();
                  }
                  return _buildMessageBubble(_messages[index]);
                },
              ),
            ),
            _buildInputBar(),
          ],
        ),
      ),
    );
  }

  Widget _buildInputBar() {
    return Container(
      padding: const EdgeInsets.fromLTRB(12, 10, 12, 12),
      decoration: BoxDecoration(
        color: Colors.white,
        border: Border(top: BorderSide(color: _kBubbleBorder)),
      ),
      child: Row(
        children: [
          Expanded(
            child: TextField(
              controller: _inputController,
              focusNode: _inputFocusNode,
              textInputAction: TextInputAction.send,
              onSubmitted: (_) => _sendMessage(),
              decoration: InputDecoration(
                hintText: 'Ketik pesan Anda...',
                filled: true,
                fillColor: const Color(0xFFF9FBFF),
                contentPadding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 12,
                ),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFDBE5F1)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: Color(0xFFDBE5F1)),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(10),
                  borderSide: const BorderSide(color: _kPrimaryBlue, width: 1.4),
                ),
              ),
            ),
          ),
          const SizedBox(width: 8),
          Material(
            color: _kPrimaryBlue,
            borderRadius: BorderRadius.circular(10),
            child: InkWell(
              borderRadius: BorderRadius.circular(10),
              onTap: _sendMessage,
              child: const Padding(
                padding: EdgeInsets.all(12),
                child: Icon(Icons.send_rounded, color: Colors.white, size: 22),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// Titik-titik animasi "sedang mengetik", versi mobile dari `.chat-typing`
/// pada CSS website.
class _TypingDots extends StatefulWidget {
  const _TypingDots();

  @override
  State<_TypingDots> createState() => _TypingDotsState();
}

class _TypingDotsState extends State<_TypingDots>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1400),
    )..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: 40,
      height: 12,
      child: AnimatedBuilder(
        animation: _controller,
        builder: (context, _) {
          return Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: List.generate(3, (i) {
              final t = (_controller.value + (i * 0.2)) % 1.0;
              final offset = (t < 0.3) ? -6 * (1 - (t / 0.3 - 1).abs()) : 0.0;
              return Transform.translate(
                offset: Offset(0, offset),
                child: Container(
                  width: 8,
                  height: 8,
                  decoration: const BoxDecoration(
                    color: Color(0xFF2D7DD2),
                    shape: BoxShape.circle,
                  ),
                ),
              );
            }),
          );
        },
      ),
    );
  }
}