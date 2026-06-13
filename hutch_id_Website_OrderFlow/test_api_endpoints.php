<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
echo "=== DATABASE TEST ===\n";
$mysqli = new mysqli('127.0.0.1', 'hutch', 'secret', 'hutch', 3307);

if ($mysqli->connect_error) {
    echo "❌ Database Connection Error: " . $mysqli->connect_error . "\n";
    exit;
}

echo "✅ Database Connected\n";

// Check tables
$tables = ['users', 'pelanggan', 'pesanan', 'produk', 'notifikasis'];
foreach ($tables as $table) {
    $result = $mysqli->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "  - $table: " . $row['count'] . " records\n";
    } else {
        echo "  - $table: ❌ Error - " . $mysqli->error . "\n";
    }
}

// Show sample data
echo "\n=== SAMPLE DATA ===\n";

echo "\nPelanggan:\n";
$result = $mysqli->query("SELECT * FROM pelanggan LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "Query Error: " . $mysqli->error . "\n";
}

echo "\nPesanan:\n";
$result = $mysqli->query("SELECT * FROM pesanan LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "Query Error: " . $mysqli->error . "\n";
}

echo "\nProduk:\n";
$result = $mysqli->query("SELECT * FROM produk LIMIT 3");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo json_encode($row, JSON_PRETTY_PRINT) . "\n";
    }
} else {
    echo "Query Error: " . $mysqli->error . "\n";
}

$mysqli->close();
?>
