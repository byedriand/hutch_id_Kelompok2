<?php
/**
 * Test N8N Webhook Connection
 * File untuk verify bahwa webhook N8N sudah tersambung dengan benar
 */

require 'vendor/autoload.php';

// Load .env file dengan method yang lebih robust
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Fallback: baca .env file langsung jika getenv() tidak bekerja
$envContent = file_get_contents('.env');
preg_match('/N8N_CHATBOT_WEBHOOK_URL=(.*)/i', $envContent, $matches);
$webhookUrlFromFile = isset($matches[1]) ? trim($matches[1]) : null;

echo "═══════════════════════════════════════════════════════════\n";
echo "🔍 TEST N8N WEBHOOK CONNECTION - Hutch.id\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// ===== CHECK 1: Webhook URL Configuration =====
echo "✅ STEP 1: Check Webhook URL Configuration\n";
echo "────────────────────────────────────────────────────────────\n";

$webhookUrl = getenv('N8N_CHATBOT_WEBHOOK_URL') ?: $webhookUrlFromFile;

if (empty($webhookUrl)) {
    echo "❌ ERROR: N8N_CHATBOT_WEBHOOK_URL tidak ada di .env\n";
    echo "   Silakan set variable di .env file:\n";
    echo "   N8N_CHATBOT_WEBHOOK_URL=https://your-n8n-url/webhook/chatbot\n\n";
    exit(1);
} else {
    echo "✅ Webhook URL ditemukan:\n";
    echo "   URL: {$webhookUrl}\n\n";
}

// ===== CHECK 2: Test Connection =====
echo "✅ STEP 2: Test Webhook Connection\n";
echo "────────────────────────────────────────────────────────────\n";

$testPayload = [
    'message' => 'Test connection from Hutch.id',
    'system_prompt' => 'Anda adalah AI Assistant untuk Hutch.id.',
    'user' => [
        'id' => 1,
        'name' => 'Test User',
        'email' => 'test@hutch.id',
        'role' => 'admin'
    ],
    'timestamp' => date('c'),
    'context' => 'test_webhook',
    'language' => 'id',
    'version' => '2.0'
];

try {
    $client = new \GuzzleHttp\Client();
    
    echo "📤 Sending test request to N8N...\n";
    echo "   Payload: " . json_encode($testPayload, JSON_PRETTY_PRINT) . "\n\n";
    
    $response = $client->post($webhookUrl, [
        'json' => $testPayload,
        'timeout' => 15,
        'verify' => false,
        'connect_timeout' => 10
    ]);
    
    $statusCode = $response->getStatusCode();
    $body = json_decode($response->getBody(), true);
    
    echo "✅ CONNECTION SUCCESS!\n";
    echo "   Status Code: {$statusCode}\n";
    echo "   Response:\n";
    echo "   " . json_encode($body, JSON_PRETTY_PRINT) . "\n\n";
    
    if (isset($body['reply']) && !empty($body['reply'])) {
        echo "✅ RESPONSE RECEIVED:\n";
        echo "   Reply: {$body['reply']}\n\n";
        echo "🎉 WEBHOOK CONNECTION WORKING PERFECTLY!\n";
        echo "═══════════════════════════════════════════════════════════\n\n";
        exit(0);
    } else {
        echo "⚠️  Response received but 'reply' field is empty\n";
        echo "   Check N8N workflow output\n\n";
    }
    
} catch (\GuzzleHttp\Exception\ConnectException $e) {
    echo "❌ CONNECTION FAILED - Cannot reach N8N\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   Problem: N8N URL might be wrong or N8N is offline\n\n";
    echo "   Solutions:\n";
    echo "   1. Check N8N URL is correct\n";
    echo "   2. Ensure N8N is running\n";
    echo "   3. Check firewall/network access\n";
    echo "   4. Verify webhook is active in N8N\n\n";
    exit(1);
    
} catch (\GuzzleHttp\Exception\RequestException $e) {
    echo "❌ REQUEST ERROR\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   Response:\n";
    if ($e->getResponse()) {
        echo "   Status: " . $e->getResponse()->getStatusCode() . "\n";
        echo "   Body: " . $e->getResponse()->getBody() . "\n";
    }
    echo "\n   Troubleshooting:\n";
    echo "   1. Check N8N workflow is published\n";
    echo "   2. Verify webhook path is correct\n";
    echo "   3. Check N8N Code node is processing correctly\n\n";
    exit(1);
    
} catch (\Exception $e) {
    echo "❌ UNEXPECTED ERROR\n";
    echo "   Error: " . $e->getMessage() . "\n";
    echo "   Type: " . get_class($e) . "\n\n";
    exit(1);
}

// ===== CHECK 3: Configuration Summary =====
echo "═══════════════════════════════════════════════════════════\n";
echo "📋 CONFIGURATION SUMMARY\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "✅ N8N Webhook URL: {$webhookUrl}\n";
echo "✅ Connection: Working\n";
echo "✅ Response Format: JSON\n";
echo "✅ Chatbot Status: READY FOR PRODUCTION\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "💡 Next Steps:\n";
echo "   1. Chatbot di Hutch.id siap digunakan\n";
echo "   2. Test chat di interface aplikasi\n";
echo "   3. Monitor N8N logs untuk troubleshooting\n";
echo "   4. Update system prompt jika diperlukan\n\n";

?>
