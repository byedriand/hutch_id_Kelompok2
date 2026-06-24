<?php
// Simple test to check if database connection works and has pesanan data

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hutch_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT COUNT(*) as total FROM pesanan";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total = $row['total'];

echo "Total Pesanan dalam Database: " . $total . "\n\n";

if ($total > 0) {
    echo "=== SAMPLE PESANAN ===\n";
    $sql = "SELECT id, nomor_po, status, created_by, total_nilai FROM pesanan LIMIT 5";
    $result = $conn->query($sql);
    
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " | PO: " . $row['nomor_po'] . " | Status: " . $row['status'] . " | Created By: " . $row['created_by'] . " | Total: " . $row['total_nilai'] . "\n";
    }
} else {
    echo "⚠️  TIDAK ADA PESANAN DI DATABASE!\n";
}

// Check users
echo "\n=== USERS ===\n";
$sql = "SELECT id, name, role FROM users LIMIT 5";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Role: " . $row['role'] . "\n";
}

$conn->close();
?>
