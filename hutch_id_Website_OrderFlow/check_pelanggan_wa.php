<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hutch";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, nama, telepon, nomor_whatsapp FROM pelanggan WHERE id = 7";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"] . "\n";
        echo "Nama: " . $row["nama"] . "\n";
        echo "Telepon: " . $row["telepon"] . "\n";
        echo "Nomor WhatsApp: " . ($row["nomor_whatsapp"] ?? "(kosong)") . "\n";
    }
} else {
    echo "No results found";
}

$conn->close();
?>
