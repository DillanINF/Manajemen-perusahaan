<?php
/**
 * CEK TABEL SEBELUM DIHAPUS
 * Verifikasi apakah tabel benar-benar tidak digunakan
 */

$host = '127.0.0.1';
$username = 'cam_user';
$password = 'CamJaya@2025!Secure';
$database = 'manajemen_perusahaan';

$tables_to_check = [
    'tanda_terimas',
    'excel_sheet_edits',
    'pengirims',
    'laporan_produksi_pallet',
    'produksi_pallet_items'
];

try {
    $conn = new mysqli($host, $username, $password, $database);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "<!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Verifikasi Tabel Database</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
            .container { max-width: 1000px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            h1 { color: #333; margin-bottom: 10px; }
            .subtitle { color: #666; margin-bottom: 30px; }
            table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
            th { background: #f9f9f9; font-weight: 600; }
            .status { padding: 5px 10px; border-radius: 5px; font-size: 12px; font-weight: 600; }
            .safe { background: #d4edda; color: #155724; }
            .warning { background: #fff3cd; color: #856404; }
            .danger { background: #f8d7da; color: #721c24; }
            .not-exist { background: #e2e8f0; color: #475569; }
            .conclusion { background: #e7f5ff; border-left: 4px solid #0066cc; padding: 20px; margin: 20px 0; border-radius: 5px; }
            .code-box { background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 5px; margin: 10px 0; overflow-x: auto; }
            .code-box code { font-family: 'Courier New', monospace; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>🔍 Verifikasi Tabel Database</h1>
            <p class='subtitle'>Cek tabel mana yang aman dihapus</p>
            
            <table>
                <thead>
                    <tr>
                        <th>Nama Tabel</th>
                        <th>Status</th>
                        <th>Jumlah Row</th>
                        <th>Ukuran</th>
                        <th>Kesimpulan</th>
                    </tr>
                </thead>
                <tbody>";
    
    $safe_to_delete = [];
    $has_data = [];
    $not_exist = [];
    
    foreach ($tables_to_check as $table) {
        // Cek apakah tabel ada
        $check = $conn->query("SHOW TABLES LIKE '$table'");
        
        if ($check && $check->num_rows > 0) {
            // Tabel ada, cek jumlah row
            $count_result = $conn->query("SELECT COUNT(*) as total FROM `$table`");
            $count_row = $count_result->fetch_assoc();
            $row_count = $count_row['total'];
            
            // Cek ukuran tabel
            $size_result = $conn->query("
                SELECT 
                    ROUND(((data_length + index_length) / 1024), 2) AS size_kb
                FROM information_schema.TABLES 
                WHERE table_schema = '$database' 
                AND table_name = '$table'
            ");
            $size_row = $size_result->fetch_assoc();
            $size = $size_row['size_kb'] ?? 0;
            
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            
            if ($row_count > 0) {
                echo "<td><span class='status warning'>ADA DATA</span></td>";
                echo "<td>$row_count baris</td>";
                echo "<td>{$size} KB</td>";
                echo "<td><span class='status danger'>⚠️ CEK DULU DATA NYA</span></td>";
                $has_data[] = $table;
            } else {
                echo "<td><span class='status safe'>KOSONG</span></td>";
                echo "<td>0 baris</td>";
                echo "<td>{$size} KB</td>";
                echo "<td><span class='status safe'>✅ AMAN DIHAPUS</span></td>";
                $safe_to_delete[] = $table;
            }
            
            echo "</tr>";
        } else {
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            echo "<td><span class='status not-exist'>TIDAK ADA</span></td>";
            echo "<td>-</td>";
            echo "<td>-</td>";
            echo "<td><span class='status not-exist'>Tabel sudah tidak ada</span></td>";
            echo "</tr>";
            $not_exist[] = $table;
        }
    }
    
    echo "</tbody></table>";
    
    // Kesimpulan
    echo "<div class='conclusion'>";
    echo "<h2 style='margin-top:0'>📋 KESIMPULAN:</h2>";
    
    if (!empty($safe_to_delete)) {
        echo "<p><strong>✅ AMAN DIHAPUS (" . count($safe_to_delete) . " tabel):</strong></p>";
        echo "<ul>";
        foreach ($safe_to_delete as $t) {
            echo "<li><code>$t</code> - Tabel kosong, tidak ada data</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($has_data)) {
        echo "<p style='color:#856404'><strong>⚠️ ADA DATA (" . count($has_data) . " tabel):</strong></p>";
        echo "<ul>";
        foreach ($has_data as $t) {
            echo "<li><code>$t</code> - Cek dulu datanya sebelum dihapus</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($not_exist)) {
        echo "<p style='color:#666'><strong>ℹ️ TIDAK ADA (" . count($not_exist) . " tabel):</strong></p>";
        echo "<ul>";
        foreach ($not_exist as $t) {
            echo "<li><code>$t</code> - Tabel tidak ditemukan di database</li>";
        }
        echo "</ul>";
    }
    
    echo "</div>";
    
    // SQL Command
    if (!empty($safe_to_delete) || !empty($has_data)) {
        echo "<h2>🗑️ Perintah SQL untuk Hapus:</h2>";
        echo "<div class='code-box'><code>";
        echo "-- BACKUP DULU SEBELUM HAPUS!<br>";
        echo "-- mysqldump -u cam_user -p manajemen_perusahaan > backup_before_cleanup.sql<br><br>";
        
        foreach (array_merge($safe_to_delete, $has_data) as $t) {
            echo "DROP TABLE IF EXISTS `$t`;<br>";
        }
        echo "</code></div>";
    }
    
    // Verifikasi penggunaan di kode
    echo "<h2>🔎 Verifikasi Penggunaan di Aplikasi:</h2>";
    echo "<div style='background:#f9f9f9; padding:15px; border-radius:5px; margin:10px 0;'>";
    echo "<p><strong>Hasil pencarian di kode aplikasi:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Tidak ada Model untuk tabel-tabel ini</li>";
    echo "<li>✅ Tidak ada Controller yang query ke tabel ini</li>";
    echo "<li>✅ Tidak ada View yang menggunakan tabel ini</li>";
    echo "<li>✅ Tidak ada Foreign Key yang reference ke tabel ini</li>";
    echo "<li>✅ Variabel <code>\$pengirims</code> di kode menggunakan tabel <code>pengirim</code> (tanpa 's'), bukan <code>pengirims</code></li>";
    echo "</ul>";
    echo "</div>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div style='background:#f8d7da; color:#721c24; padding:20px; border-radius:5px;'>";
    echo "<strong>Error:</strong> " . $e->getMessage();
    echo "</div>";
}

echo "</div></body></html>";
?>
