<?php
// Script untuk cek error detail
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Salary;
use Illuminate\Support\Facades\DB;

echo "<h2>🔍 Cek Error Salary</h2><hr>";

try {
    // Cek struktur tabel
    echo "<h3>📋 Struktur Tabel Salaries:</h3>";
    $columns = DB::select("DESCRIBE salaries");
    
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th></tr>";
    foreach ($columns as $column) {
        echo "<tr><td><strong>{$column->Field}</strong></td><td>{$column->Type}</td></tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    
    // Cek data salary
    echo "<h3>📊 Data Salaries:</h3>";
    $salaries = Salary::all();
    
    if ($salaries->isEmpty()) {
        echo "<p>Tidak ada data gaji.</p>";
    } else {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Bulan</th><th>Tahun</th><th>Gaji Pokok</th><th>Total Gaji</th><th>Status</th></tr>";
        
        foreach ($salaries as $salary) {
            echo "<tr>";
            echo "<td>{$salary->id}</td>";
            echo "<td>{$salary->bulan}</td>";
            echo "<td>{$salary->tahun}</td>";
            echo "<td>Rp " . number_format($salary->gaji_pokok, 0, ',', '.') . "</td>";
            echo "<td>Rp " . number_format($salary->total_gaji, 0, ',', '.') . "</td>";
            echo "<td>{$salary->status_pembayaran}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr>";
    echo "<p style='color: green; font-weight: bold;'>✅ Tidak ada error! Model Salary berjalan normal.</p>";
    echo "<p><a href='/salary'>← Coba Buka Halaman Gaji</a></p>";
    
} catch (\Exception $e) {
    echo "<h3 style='color: red;'>❌ ERROR DITEMUKAN:</h3>";
    echo "<pre style='background: #ffe6e6; padding: 15px; border-radius: 5px;'>";
    echo "<strong>Message:</strong> " . $e->getMessage() . "\n\n";
    echo "<strong>File:</strong> " . $e->getFile() . "\n";
    echo "<strong>Line:</strong> " . $e->getLine() . "\n\n";
    echo "<strong>Stack Trace:</strong>\n" . $e->getTraceAsString();
    echo "</pre>";
}
?>
