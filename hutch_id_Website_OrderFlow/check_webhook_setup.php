<?php
/**
 * Simple N8N Webhook Test - No Environment Issues
 */

echo "\n═══════════════════════════════════════════════════════════\n";
echo "🔍 N8N WEBHOOK TEST - SETUP GUIDE\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// ===== STEP 1: Verify .env Configuration =====
echo "📋 STEP 1: Verify .env Configuration\n";
echo "────────────────────────────────────────────────────────────\n";

$envFile = '.env';
if (!file_exists($envFile)) {
    echo "❌ ERROR: .env file not found\n";
    exit(1);
}

$envContent = file_get_contents($envFile);
echo "✅ .env file found\n\n";

// Check for N8N_CHATBOT_WEBHOOK_URL
if (preg_match('/N8N_CHATBOT_WEBHOOK_URL=(.*)/i', $envContent, $matches)) {
    $webhookUrl = trim($matches[1]);
    echo "✅ N8N_CHATBOT_WEBHOOK_URL configured:\n";
    echo "   URL: {$webhookUrl}\n\n";
} else {
    echo "❌ N8N_CHATBOT_WEBHOOK_URL not found in .env\n";
    echo "   Please add: N8N_CHATBOT_WEBHOOK_URL=http://localhost:5678/webhook/hutch-chatbot\n";
    exit(1);
}

// ===== STEP 2: Test Connection =====
echo "🔌 STEP 2: Test Webhook Connection\n";
echo "────────────────────────────────────────────────────────────\n";

// Parse URL
$urlParts = parse_url($webhookUrl);
$host = $urlParts['host'];
$port = $urlParts['port'] ?? 80;
$path = $urlParts['path'] ?? '/';

echo "Host: {$host}\n";
echo "Port: {$port}\n";
echo "Path: {$path}\n\n";

// Simple TCP connection test
echo "🧪 Testing connection to {$host}:{$port}...\n";

$timeout = 5;
if (@fsockopen($host, $port, $errno, $errstr, $timeout)) {
    echo "✅ CONNECTION SUCCESSFUL!\n";
    echo "   Host {$host} is reachable on port {$port}\n\n";
} else {
    echo "❌ CONNECTION FAILED!\n";
    echo "   Error: {$errstr} ({$errno})\n";
    echo "   Host {$host} is NOT reachable on port {$port}\n\n";
    echo "📌 Troubleshooting:\n";
    echo "   1. Is N8N running? Check: docker-compose ps\n";
    echo "   2. If not running, start: docker-compose up -d n8n\n";
    echo "   3. Wait 30 seconds for N8N to fully start\n";
    echo "   4. Check Docker logs: docker-compose logs n8n\n\n";
}

// ===== STEP 3: Show Setup Instructions =====
echo "📚 STEP 3: Setup Instructions\n";
echo "────────────────────────────────────────────────────────────\n";

echo "\n🚀 TO START N8N:\n";
echo "   cd c:\\xampp\\htdocs\\hutch-web\\hutch_id_Website_OrderFlow\n";
echo "   docker-compose up -d n8n\n";
echo "   Wait 30 seconds for startup...\n";

echo "\n📊 TO CHECK N8N:\n";
echo "   1. Open browser: http://localhost:5678\n";
echo "   2. Login with: adrianronald99@gmail.com / Drian11099\n";
echo "   3. Create/edit workflow with webhook node\n";
echo "   4. Get webhook URL from N8N UI\n";

echo "\n🔗 WEBHOOK URL USAGE:\n";
echo "   Laravel will use: {$webhookUrl}\n";
echo "   This is configured in: .env\n";

echo "\n✅ FINAL TEST:\n";
echo "   Run: php test_n8n_webhook.php\n";
echo "   (Only after N8N is fully started!)\n";

echo "\n═══════════════════════════════════════════════════════════\n";
echo "Status: Setup Guide Complete ✅\n";
echo "═══════════════════════════════════════════════════════════\n\n";

?>
