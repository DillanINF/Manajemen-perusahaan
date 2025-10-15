<?php
// Test apakah data salary bisa diambil dan ditampilkan
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Salary;

echo "<h2>🧪 Test Salary View</h2><hr>";

try {
    // Ambil data dengan Eloquent
    echo "<h3>📊 Data dari Model Salary:</h3>";
    $salaries = Salary::latest()->get();
    
    echo "<p>Total data: <strong>" . $salaries->count() . "</strong></p>";
    
    if ($salaries->isEmpty()) {
        echo "<p style='color: red;'>❌ Tidak ada data!</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #4CAF50; color: white;'>";
        echo "<th>ID</th><th>Bulan/Tahun</th><th>Gaji Pokok</th><th>Total Gaji</th><th>Status</th>";
        echo "</tr>";
        
        foreach ($salaries as $salary) {
            $bulanNama = DateTime::createFromFormat('!m', $salary->bulan)->format('F');
            
            echo "<tr>";
            echo "<td>{$salary->id}</td>";
            echo "<td>{$bulanNama} {$salary->tahun}</td>";
            echo "<td>Rp " . number_format($salary->gaji_pokok, 0, ',', '.') . "</td>";
            echo "<td>Rp " . number_format($salary->total_gaji, 0, ',', '.') . "</td>";
            echo "<td><span style='background: " . ($salary->status_pembayaran === 'dibayar' ? '#4CAF50' : '#FFC107') . "; color: white; padding: 5px 10px; border-radius: 5px;'>{$salary->status_pembayaran}</span></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<hr>";
        echo "<p style='color: green; font-weight: bold;'>✅ Data berhasil diambil dari database!</p>";
        echo "<p>Jika data muncul di sini tapi tidak di halaman /salary, berarti masalahnya di view blade.</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='/salary' style='background: #2196F3; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Buka Halaman Salary →</a></p>";
    
} catch (\Exception $e) {
    echo "<h3 style='color: red;'>❌ ERROR:</h3>";
    echo "<pre style='background: #ffe6e6; padding: 15px;'>";
    echo $e->getMessage();
    echo "</pre>";
}
?>
