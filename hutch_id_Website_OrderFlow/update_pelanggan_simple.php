<?php
/**
 * Simple script to update customer WhatsApp number
 * Connect directly to MySQL database
 */

// Database connection
$host = 'localhost';
$db = 'hutch';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Check connection
    if ($conn->connect_error) {
        echo "❌ Connection failed: " . $conn->connect_error . "\n";
        exit;
    }
    
    echo "✅ Connected to database\n\n";
    
    // Find pelanggan by name containing "Sopyan"
    $sql = "SELECT * FROM pelanggan WHERE nama LIKE '%Sopyan%'";
    $result = $conn->query($sql);
    
    if ($result->num_rows === 0) {
        echo "❌ Pelanggan tidak ditemukan.\n\n";
        echo "📋 Daftar Pelanggan:\n";
        $allSql = "SELECT id, nama, telepon, nomor_whatsapp FROM pelanggan";
        $allResult = $conn->query($allSql);
        while ($row = $allResult->fetch_assoc()) {
            echo "   " . $row['id'] . ". " . $row['nama'] . 
                 " (Telepon: " . $row['telepon'] . 
                 ", WhatsApp: " . ($row['nomor_whatsapp'] ?? 'Belum terdaftar') . ")\n";
        }
        exit;
    }
    
    $pelanggan = $result->fetch_assoc();
    
    echo "✅ Pelanggan ditemukan:\n";
    echo "   ID: " . $pelanggan['id'] . "\n";
    echo "   Nama: " . $pelanggan['nama'] . "\n";
    echo "   Telepon: " . $pelanggan['telepon'] . "\n";
    echo "   WhatsApp Lama: " . ($pelanggan['nomor_whatsapp'] ?? 'Belum terdaftar') . "\n";
    echo "\n";
    
    // Update nomor WhatsApp
    $newPhone = '+62 855-5555-401';
    $updateSql = "UPDATE pelanggan SET nomor_whatsapp = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    
    if (!$stmt) {
        echo "❌ Prepare failed: " . $conn->error . "\n";
        exit;
    }
    
    $stmt->bind_param("si", $newPhone, $pelanggan['id']);
    
    if ($stmt->execute()) {
        echo "✅ Update Berhasil!\n";
        echo "   Nomor WhatsApp Baru: " . $newPhone . "\n";
        
        // Format for verification
        $formatted = preg_replace('/\D/', '', $newPhone);
        if (substr($formatted, 0, 1) === '0') {
            $formatted = '62' . substr($formatted, 1);
        }
        if (substr($formatted, 0, 2) !== '62') {
            $formatted = '62' . $formatted;
        }
        echo "   Format Terformat: " . $formatted . "\n\n";
        
        // Verify
        $verifySql = "SELECT id, nama, telepon, nomor_whatsapp FROM pelanggan WHERE id = ?";
        $verifyStmt = $conn->prepare($verifySql);
        $verifyStmt->bind_param("i", $pelanggan['id']);
        $verifyStmt->execute();
        $verified = $verifyStmt->get_result()->fetch_assoc();
        
        echo "📋 Verifikasi Data:\n";
        echo "   ID: " . $verified['id'] . "\n";
        echo "   Nama: " . $verified['nama'] . "\n";
        echo "   Telepon: " . $verified['telepon'] . "\n";
        echo "   WhatsApp: " . $verified['nomor_whatsapp'] . "\n";
        echo "\n✅ Data pelanggan berhasil diupdate! Sekarang coba kirim notifikasi WhatsApp.\n";
        
        $verifyStmt->close();
    } else {
        echo "❌ Update failed: " . $stmt->error . "\n";
    }
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
