<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hutch";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update nomor_whatsapp dengan format yang benar (tanpa spasi)
$nomor_baru = "628555555401";
$id = 7;

$sql = "UPDATE pelanggan SET nomor_whatsapp = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $nomor_baru, $id);

if ($stmt->execute()) {
    echo "✅ Update berhasil!\n";
    echo "ID: " . $id . "\n";
    echo "Nomor WhatsApp Baru: " . $nomor_baru . "\n";
    
    // Verifikasi
    $verify = $conn->query("SELECT nomor_whatsapp FROM pelanggan WHERE id = 7");
    $row = $verify->fetch_assoc();
    echo "Verifikasi: " . $row['nomor_whatsapp'] . "\n";
} else {
    echo "❌ Update gagal: " . $stmt->error . "\n";
}

$stmt->close();
$conn->close();
?>
