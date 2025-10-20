<?php
/**
 * Script untuk menambahkan kolom email ke tabel customers
 * Akses via browser: http://127.0.0.1:8000/fix_customer_email.php
 */

// Database configuration
$host = '127.0.0.1';
$port = '3306';
$dbname = 'manajemen_perusahaan';
$username = 'root';
$password = '';

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Perbaikan Tabel Customers - Tambah Kolom Email</h2>";
    echo "<hr>";
    
    // Check if email column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM customers LIKE 'email'");
    $emailExists = $stmt->rowCount() > 0;
    
    if ($emailExists) {
        echo "<p style='color: green;'>✅ Kolom 'email' sudah ada di tabel customers</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Kolom 'email' tidak ditemukan. Menambahkan kolom...</p>";
        
        // Add email column after name
        $pdo->exec("ALTER TABLE customers ADD COLUMN email VARCHAR(255) NULL AFTER name");
        
        echo "<p style='color: green;'>✅ Kolom 'email' berhasil ditambahkan!</p>";
    }
    
    // Show current table structure
    echo "<h3>Struktur Tabel Customers:</h3>";
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $stmt = $pdo->query("SHOW COLUMNS FROM customers");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td><strong>{$row['Field']}</strong></td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<p style='color: green; font-weight: bold;'>✅ SELESAI! Kolom email sudah tersedia.</p>";
    echo "<p><a href='/customer'>← Kembali ke Data Customer</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Pastikan database MySQL aktif dan konfigurasi sudah benar.</p>";
}
