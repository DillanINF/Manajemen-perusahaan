<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    DB::statement("ALTER TABLE pos ADD COLUMN status_approval VARCHAR(255) DEFAULT 'Pending'");
    echo "Column status_approval added successfully!\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column status_approval already exists!\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
