<?php
// Quick script to check database size
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $dbName = env('DB_DATABASE');
    $result = DB::select("
        SELECT 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb,
            COUNT(*) as table_count
        FROM information_schema.TABLES 
        WHERE table_schema = ?
    ", [$dbName]);
    
    $sizeMB = $result[0]->size_mb ?? 0;
    $tableCount = $result[0]->table_count ?? 0;
    
    echo "<h2>Database Information</h2>";
    echo "<p><strong>Database Name:</strong> {$dbName}</p>";
    echo "<p><strong>Total Tables:</strong> {$tableCount}</p>";
    echo "<p><strong>Database Size:</strong> {$sizeMB} MB</p>";
    
    // Table sizes
    $tables = DB::select("
        SELECT 
            table_name AS name,
            ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
        FROM information_schema.TABLES 
        WHERE table_schema = ?
        ORDER BY (data_length + index_length) DESC
        LIMIT 10
    ", [$dbName]);
    
    echo "<h3>Top 10 Largest Tables:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Table Name</th><th>Size (MB)</th></tr>";
    foreach ($tables as $table) {
        echo "<tr><td>{$table->name}</td><td>{$table->size_mb}</td></tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
