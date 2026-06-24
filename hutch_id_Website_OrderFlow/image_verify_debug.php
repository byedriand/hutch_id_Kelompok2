<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Produk;

echo "\n";
echo "╔════════════════════════════════════════════════╗\n";
echo "║  IMAGE DEBUGGING & VERIFICATION SCRIPT        ║\n";
echo "╚════════════════════════════════════════════════╝\n";

// Get all products
$products = Produk::all();
$imageDir = public_path('images');

echo "\n📊 SUMMARY:\n";
echo "- Total Produk: " . count($products) . "\n";
echo "- Image Directory: " . $imageDir . "\n";
echo "- Image Directory Exists: " . (is_dir($imageDir) ? "✅ YES" : "❌ NO") . "\n";

$filesOnDisk = is_dir($imageDir) ? count(glob($imageDir . '/*')) : 0;
echo "- Files on Disk: " . $filesOnDisk . "\n";

// Count products with foto
$withFoto = $products->filter(fn($p) => !empty($p->foto))->count();
echo "- Produk with Foto: " . $withFoto . " / " . count($products) . "\n";

// ============================================
echo "\n\n📋 DETAILED PRODUCT LIST:\n";
echo "─────────────────────────────────────────────────────────────────\n";

$issues = [];

foreach($products as $p) {
    $id = str_pad($p->id, 2, '0', STR_PAD_LEFT);
    $nama = str_pad(substr($p->nama, 0, 20), 20);
    
    if (empty($p->foto)) {
        echo "[$id] $nama | ❌ NO IMAGE\n";
        $issues[] = "Produk ID {$p->id} ({$p->nama}) - Tidak ada gambar";
        continue;
    }
    
    // Check if file exists on disk
    $filePath = $imageDir . '/' . basename($p->foto);
    $fileExists = file_exists($filePath);
    $fileSize = $fileExists ? filesize($filePath) : 0;
    $fileSizeKB = round($fileSize / 1024, 1);
    
    $status = $fileExists ? "✅" : "❌";
    $fotoShort = substr($p->foto, 0, 25);
    
    echo "[$id] $nama | $status | {$fotoShort}... ({$fileSizeKB}KB)\n";
    
    if (!$fileExists) {
        $issues[] = "Produk ID {$p->id} ({$p->nama}) - File tidak ada di disk: {$p->foto}";
    }
}

// ============================================
echo "\n\n🔗 API ENDPOINT TEST:\n";
echo "─────────────────────────────────────────────────────────────────\n";

$baseUrl = 'http://localhost:8082';
echo "Base URL: {$baseUrl}/api/produk\n";
echo "Image Base: {$baseUrl}/images/\n\n";

// Test sample URLs
foreach($products->take(3) as $p) {
    if (!empty($p->foto)) {
        $encodedFoto = urlencode(basename($p->foto));
        $url = "{$baseUrl}/images/{$encodedFoto}";
        echo "📸 " . substr($p->foto, 0, 20) . "...\n";
        echo "   → {$url}\n";
        
        // Try to access
        $headers = get_headers($url);
        $statusCode = substr($headers[0], 9, 3);
        $isOk = strpos($headers[0], '200') !== false;
        
        echo "   ✓ HTTP {$statusCode} " . ($isOk ? "✅" : "❌") . "\n\n";
    }
}

// ============================================
echo "\n\n⚠️  ISSUES FOUND:\n";
echo "─────────────────────────────────────────────────────────────────\n";

if (empty($issues)) {
    echo "✅ No issues found!\n";
} else {
    foreach($issues as $i => $issue) {
        echo ($i + 1) . ". " . $issue . "\n";
    }
}

// ============================================
echo "\n\n💡 RECOMMENDATIONS:\n";
echo "─────────────────────────────────────────────────────────────────\n";

if (!is_dir($imageDir)) {
    echo "1. ❌ /public/images directory tidak ada\n";
    echo "   → Buat: mkdir -p " . $imageDir . "\n";
} else {
    echo "1. ✅ /public/images directory sudah ada\n";
}

if ($withFoto < count($products)) {
    echo "2. ⚠️  " . (count($products) - $withFoto) . " produk tanpa gambar\n";
    echo "   → Upload gambar atau jalankan sync script\n";
} else {
    echo "2. ✅ Semua produk punya gambar\n";
}

echo "\n3. 📱 Mobile App Configuration:\n";
$deviceIp = exec("ipconfig | findstr IPv4");
echo "   Current IP: localhost atau 127.0.0.1\n";
echo "   For Physical Device, update AppConfig:\n";
echo "   → static const String imageBaseUrl = 'http://192.168.x.x:8082';\n";

// ============================================
echo "\n\n✅ NEXT STEPS:\n";
echo "─────────────────────────────────────────────────────────────────\n";
echo "1. If all checks pass:\n";
echo "   → Update mobile app AppConfig with correct IP\n";
echo "   → Run: flutter clean && flutter pub get && flutter run\n";
echo "\n2. If images are missing:\n";
echo "   → Copy images to /public/images/\n";
echo "   → Or run upload/sync scripts\n";
echo "\n3. Test URLs in browser:\n";
echo "   → http://localhost:8082/images/Tas%20gendong.jpeg\n";
echo "   → http://localhost:8082/api/produk\n";

echo "\n";
?>
