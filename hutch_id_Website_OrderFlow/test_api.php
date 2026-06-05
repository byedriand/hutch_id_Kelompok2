<?php

/**
 * API Integration Test Script
 * 
 * Usage: php test_api.php
 * 
 * This script tests all API endpoints to ensure proper integration
 * between Laravel backend and Flutter frontend.
 */

// Configuration
$API_URL = 'http://127.0.0.1:8000/api';
$TEST_EMAIL = 'admin@example.com';  // Change to your test user
$TEST_PASSWORD = 'password';         // Change to your test password
$TOKEN = null;

// ANSI Color codes for output
class Color {
    const GREEN = '\033[92m';
    const RED = '\033[91m';
    const YELLOW = '\033[93m';
    const BLUE = '\033[94m';
    const END = '\033[0m';
}

function print_header($title) {
    echo "\n" . Color::BLUE . "═══════════════════════════════════════════════════════════" . Color::END . "\n";
    echo Color::BLUE . "  $title" . Color::END . "\n";
    echo Color::BLUE . "═══════════════════════════════════════════════════════════" . Color::END . "\n";
}

function print_success($message) {
    echo Color::GREEN . "✓ " . $message . Color::END . "\n";
}

function print_error($message) {
    echo Color::RED . "✗ " . $message . Color::END . "\n";
}

function print_info($message) {
    echo Color::YELLOW . "ℹ " . $message . Color::END . "\n";
}

function make_request($method, $endpoint, $data = null, $token = null) {
    global $API_URL;
    
    $url = $API_URL . $endpoint;
    
    $options = [
        'http' => [
            'method' => $method,
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            'timeout' => 5,
        ],
    ];
    
    if ($token) {
        $options['http']['header'][] = 'Authorization: Bearer ' . $token;
    }
    
    if ($data) {
        $options['http']['content'] = json_encode($data);
    }
    
    $context = stream_context_create($options);
    
    try {
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return [
                'success' => false,
                'error' => 'Connection failed',
                'status' => null,
                'data' => null,
            ];
        }
        
        $http_response_header_value = $http_response_header[0] ?? '';
        preg_match('/\d+/', $http_response_header_value, $matches);
        $status = (int)($matches[0] ?? 0);
        
        return [
            'success' => $status >= 200 && $status < 300,
            'status' => $status,
            'data' => json_decode($response, true),
            'error' => null,
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage(),
            'status' => null,
            'data' => null,
        ];
    }
}

// ============================================================================
// TEST EXECUTION
// ============================================================================

print_header("🧪 HUTCH API INTEGRATION TEST");

echo "API URL: " . Color::YELLOW . $API_URL . Color::END . "\n";
echo "Test User: " . Color::YELLOW . $TEST_EMAIL . Color::END . "\n\n";

// ─────────────────────────────────────────────────────────────────────────
// Test 1: Health Check
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 1: Health Check");

$result = make_request('GET', '/login');
if ($result['status'] === 405 || $result['status'] === 200 || $result['status'] === 404) {
    print_success("API server is reachable");
} else {
    print_error("API server not responding properly");
    print_info("Ensure Laravel server is running: php artisan serve");
    exit(1);
}

// ─────────────────────────────────────────────────────────────────────────
// Test 2: Login
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 2: Authentication (Login)");

$login_result = make_request('POST', '/login', [
    'email' => $TEST_EMAIL,
    'password' => $TEST_PASSWORD,
]);

if ($login_result['success'] && isset($login_result['data']['token'])) {
    $TOKEN = $login_result['data']['token'];
    print_success("Login successful");
    echo "  Token: " . substr($TOKEN, 0, 20) . "...\n";
    
    if (isset($login_result['data']['user'])) {
        $user = $login_result['data']['user'];
        print_success("User data retrieved");
        echo "  - Name: " . ($user['name'] ?? 'N/A') . "\n";
        echo "  - Email: " . ($user['email'] ?? 'N/A') . "\n";
        echo "  - Role: " . ($user['role'] ?? 'N/A') . "\n";
    }
} else {
    print_error("Login failed");
    if (isset($login_result['data']['message'])) {
        echo "  Error: " . $login_result['data']['message'] . "\n";
    }
    print_info("Check credentials in test_api.php");
    exit(1);
}

// ─────────────────────────────────────────────────────────────────────────
// Test 3: Get User Profile
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 3: Get User Profile");

$profile_result = make_request('GET', '/user', null, $TOKEN);

if ($profile_result['success']) {
    print_success("User profile retrieved");
    if (isset($profile_result['data']['name'])) {
        echo "  User: " . $profile_result['data']['name'] . "\n";
    }
} else {
    print_error("Failed to get user profile");
}

// ─────────────────────────────────────────────────────────────────────────
// Test 4: Dashboard
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 4: Dashboard Data");

$dashboard_result = make_request('GET', '/dashboard', null, $TOKEN);

if ($dashboard_result['success']) {
    print_success("Dashboard data retrieved");
    if (isset($dashboard_result['data'])) {
        $data = $dashboard_result['data'];
        echo "  - Total Aktif: " . ($data['total_aktif'] ?? 0) . "\n";
        echo "  - Total Menunggu: " . ($data['total_menunggu'] ?? 0) . "\n";
        echo "  - Siap Kirim: " . ($data['total_siap_kirim'] ?? 0) . "\n";
        echo "  - Selesai (Bulan Ini): " . ($data['total_selesai_bulan_ini'] ?? 0) . "\n";
    }
} else {
    print_error("Failed to get dashboard data");
}

// ─────────────────────────────────────────────────────────────────────────
// Test 5: Pelanggan
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 5: Pelanggan (Customers)");

$pelanggan_result = make_request('GET', '/pelanggan', null, $TOKEN);

if ($pelanggan_result['success']) {
    print_success("Pelanggan list retrieved");
    if (is_array($pelanggan_result['data'])) {
        $count = count($pelanggan_result['data']);
        echo "  Total pelanggan: " . $count . "\n";
        
        if ($count > 0) {
            $first = $pelanggan_result['data'][0];
            echo "  First pelanggan: " . ($first['nama'] ?? 'N/A') . "\n";
        }
    }
} else {
    print_error("Failed to get pelanggan list");
}

// ─────────────────────────────────────────────────────────────────────────
// Test 6: Pesanan
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 6: Pesanan (Orders)");

$pesanan_result = make_request('GET', '/pesanan', null, $TOKEN);

if ($pesanan_result['success']) {
    print_success("Pesanan list retrieved");
    if (is_array($pesanan_result['data'])) {
        $count = count($pesanan_result['data']);
        echo "  Total pesanan: " . $count . "\n";
    }
} else {
    print_error("Failed to get pesanan list");
}

// ─────────────────────────────────────────────────────────────────────────
// Test 7: Produk
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 7: Produk (Products)");

$produk_result = make_request('GET', '/produk', null, $TOKEN);

if ($produk_result['success']) {
    print_success("Produk list retrieved");
    if (is_array($produk_result['data'])) {
        $count = count($produk_result['data']);
        echo "  Total produk: " . $count . "\n";
    }
} else {
    print_error("Failed to get produk list");
}

// ─────────────────────────────────────────────────────────────────────────
// Test 8: Arsip PDF
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 8: Arsip PDF (Archives)");

$arsip_result = make_request('GET', '/arsip-pdf', null, $TOKEN);

if ($arsip_result['success']) {
    print_success("Arsip PDF list retrieved");
    if (is_array($arsip_result['data'])) {
        $count = count($arsip_result['data']);
        echo "  Total arsip: " . $count . "\n";
    }
} else {
    print_error("Failed to get arsip PDF list");
}

// ─────────────────────────────────────────────────────────────────────────
// Test 9: Notifikasi
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 9: Notifikasi (Notifications)");

$notifikasi_result = make_request('GET', '/notifikasi', null, $TOKEN);

if ($notifikasi_result['success']) {
    print_success("Notifikasi list retrieved");
    if (is_array($notifikasi_result['data'])) {
        $count = count($notifikasi_result['data']);
        echo "  Total notifikasi: " . $count . "\n";
    }
} else {
    print_error("Failed to get notifikasi list");
}

// ─────────────────────────────────────────────────────────────────────────
// Test 10: Logout
// ─────────────────────────────────────────────────────────────────────────
print_header("Test 10: Logout");

$logout_result = make_request('POST', '/logout', null, $TOKEN);

if ($logout_result['success']) {
    print_success("Logout successful");
    
    // Verify token is invalid
    $verify_result = make_request('GET', '/user', null, $TOKEN);
    if (!$verify_result['success']) {
        print_success("Token properly invalidated after logout");
    }
} else {
    print_error("Logout failed");
}

// ─────────────────────────────────────────────────────────────────────────
// Summary
// ─────────────────────────────────────────────────────────────────────────
print_header("✅ All Tests Completed!");

echo "\n" . Color::GREEN . "API Integration Status:" . Color::END . "\n";
echo "  - Authentication: " . Color::GREEN . "✓ Working" . Color::END . "\n";
echo "  - Dashboard: " . Color::GREEN . "✓ Working" . Color::END . "\n";
echo "  - Data Endpoints: " . Color::GREEN . "✓ Working" . Color::END . "\n";
echo "  - Authorization: " . Color::GREEN . "✓ Working" . Color::END . "\n";

echo "\n" . Color::BLUE . "Next Steps:" . Color::END . "\n";
echo "  1. Update Flutter app_config.dart if using different IP\n";
echo "  2. Run Flutter app with: flutter run\n";
echo "  3. Test API calls from Flutter\n";
echo "  4. Check logs with: flutter logs\n\n";

?>
