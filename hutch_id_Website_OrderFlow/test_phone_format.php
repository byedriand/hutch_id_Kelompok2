<?php
$phone = "+62 855-5555-401";

echo "Input: " . $phone . "\n";
echo "---\n";

// Simulasi formatPhoneNumber
function formatPhoneNumber($phone)
{
    if (empty($phone)) {
        return null;
    }

    // Hapus karakter non-digit
    $phone = preg_replace('/\D/', '', $phone);
    echo "Setelah remove non-digit: " . $phone . "\n";

    // Jika dimulai dengan 0, ganti dengan 62
    if (substr($phone, 0, 1) === '0') {
        $phone = '62' . substr($phone, 1);
    }

    // Jika belum dimulai dengan 62, tambahkan
    if (substr($phone, 0, 2) !== '62') {
        $phone = '62' . $phone;
    }

    return $phone;
}

// Simulasi isValidPhoneNumber
function isValidPhoneNumber($phone)
{
    if (empty($phone)) {
        return false;
    }

    $formatted = formatPhoneNumber($phone);
    echo "Formatted: " . $formatted . "\n";
    echo "Length after 62: " . (strlen($formatted) - 2) . " digits\n";
    
    // Nomor Indonesia harus 62xxxxxxxxxxxxx (11-13 digit setelah 62)
    $valid = preg_match('/^62\d{9,11}$/', $formatted);
    echo "Regex /^62\d{9,11}\$/: " . ($valid ? "MATCH" : "NO MATCH") . "\n";

    if (preg_match('/^62\d{9,11}$/', $formatted)) {
        return true;
    }

    return false;
}

$result = isValidPhoneNumber($phone);
echo "\nValid: " . ($result ? "YES ✅" : "NO ❌") . "\n";
