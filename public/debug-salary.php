<?php
// Debug script untuk cek data salary
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "<h2>🔍 Debug Data Salary</h2><hr>";

try {
    // Cek data langsung dari database
    echo "<h3>📊 Data dari Database (Raw Query):</h3>";
    $salaries = DB::select("SELECT * FROM salaries ORDER BY id DESC LIMIT 10");
    
    if (empty($salaries)) {
        echo "<p style='color: red; font-weight: bold;'>❌ Tidak ada data di tabel salaries!</p>";
    } else {
        echo "<p style='color: green;'>✅ Ditemukan " . count($salaries) . " data</p>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Bulan</th><th>Tahun</th><th>Gaji Pokok</th><th>Total Gaji</th><th>Status</th><th>Created At</th>";
        echo "</tr>";
        
        foreach ($salaries as $salary) {
            echo "<tr>";
            echo "<td>{$salary->id}</td>";
            echo "<td>{$salary->bulan}</td>";
            echo "<td>{$salary->tahun}</td>";
            echo "<td>Rp " . number_format($salary->gaji_pokok, 0, ',', '.') . "</td>";
            echo "<td>Rp " . number_format($salary->total_gaji, 0, ',', '.') . "</td>";
            echo "<td>{$salary->status_pembayaran}</td>";
            echo "<td>{$salary->created_at}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    
    // Cek struktur tabel
    echo "<h3>📋 Struktur Tabel Salaries:</h3>";
    $columns = DB::select("DESCRIBE salaries");
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>{$column->Field}</strong></td>";
        echo "<td>{$column->Type}</td>";
        echo "<td>{$column->Null}</td>";
        echo "<td>{$column->Key}</td>";
        echo "<td>" . ($column->Default ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    
    // Test insert manual
    echo "<h3>🧪 Test Insert Data:</h3>";
    
    $testData = [
        'bulan' => date('n'),
        'tahun' => date('Y'),
        'gaji_pokok' => 5000000,
        'total_gaji' => 5000000,
        'status_pembayaran' => 'dibayar',
        'created_at' => now(),
        'updated_at' => now()
    ];
    
    echo "<p>Mencoba insert data test...</p>";
    echo "<pre>" . json_encode($testData, JSON_PRETTY_PRINT) . "</pre>";
    
    $inserted = DB::table('salaries')->insert($testData);
    
    if ($inserted) {
        echo "<p style='color: green; font-weight: bold;'>✅ Insert berhasil!</p>";
        
        // Cek data terakhir
        $lastData = DB::select("SELECT * FROM salaries ORDER BY id DESC LIMIT 1");
        echo "<p>Data terakhir yang masuk:</p>";
        echo "<pre>" . json_encode($lastData[0], JSON_PRETTY_PRINT) . "</pre>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Insert gagal!</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='/salary'>← Kembali ke Halaman Gaji</a></p>";
    
} catch (\Exception $e) {
    echo "<h3 style='color: red;'>❌ ERROR:</h3>";
    echo "<pre style='background: #ffe6e6; padding: 15px;'>";
    echo $e->getMessage() . "\n\n";
    echo $e->getTraceAsString();
    echo "</pre>";
}
?>
