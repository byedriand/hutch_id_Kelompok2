<?php
// Simple script to check images on disk without database

$imageDir = __DIR__ . '/public/images';
$baseUrl = 'http://localhost:8082';

echo "\n";
echo "╔════════════════════════════════════════════════╗\n";
echo "║  IMAGE DISK CHECK (No DB needed)              ║\n";
echo "╚════════════════════════════════════════════════╝\n";

// Check if directory exists
if (!is_dir($imageDir)) {
    echo "\n❌ ERROR: Directory does not exist: $imageDir\n";
    exit(1);
}

echo "\n📂 Image Directory: $imageDir\n";

// Get all files
$files = glob($imageDir . '/*');
$imageFiles = array_filter($files, function($file) {
    return is_file($file) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file);
});

sort($imageFiles);

echo "✅ Directory Found\n";
echo "\n📊 IMAGES FOUND: " . count($imageFiles) . " files\n";
echo "─────────────────────────────────────────────────────────────────\n\n";

if (empty($imageFiles)) {
    echo "❌ NO IMAGES FOUND!\n";
    echo "   Please upload images to: $imageDir\n";
    exit(1);
}

// List all images
foreach ($imageFiles as $i => $file) {
    $filename = basename($file);
    $filesize = filesize($file);
    $filesizeKB = round($filesize / 1024, 1);
    $encodedName = urlencode($filename);
    $url = "$baseUrl/images/$encodedName";
    
    echo ($i + 1) . ". ";
    echo "📸 " . substr($filename, 0, 30);
    if (strlen($filename) > 30) echo "...";
    echo "\n";
    echo "   Size: {$filesizeKB}KB\n";
    echo "   URL: $url\n\n";
}

echo "─────────────────────────────────────────────────────────────────\n";
echo "\n✅ Test di Browser:\n";
$testFile = reset($imageFiles);
$testFilename = basename($testFile);
$testEncoded = urlencode($testFilename);
echo "   Buka: http://localhost:8082/images/$testEncoded\n";
echo "   Harusnya muncul gambar (bukan 404 error)\n";

echo "\n✅ Test di Mobile:\n";
echo "   Pastikan App Config di mobile sudah correct:\n";
echo "   - Localhost: http://127.0.0.1:8082\n";
echo "   - WiFi Device: http://YOUR_IP:8082\n";

echo "\n✅ URL Encoding Examples:\n";
$samples = ['Tas gendong.jpeg', 'Key Chain - Black', 'File (1).jpg'];
foreach ($samples as $sample) {
    echo "   '$sample' → '" . urlencode($sample) . "'\n";
}

echo "\n";
?>
