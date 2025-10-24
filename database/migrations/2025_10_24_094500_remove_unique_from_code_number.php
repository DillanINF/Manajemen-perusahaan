<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cek dan hapus unique index pada code_number jika ada
        $indexes = DB::select("SHOW INDEX FROM customers WHERE Column_name = 'code_number' AND Non_unique = 0");
        
        foreach ($indexes as $index) {
            $indexName = $index->Key_name;
            if ($indexName !== 'PRIMARY') {
                DB::statement("ALTER TABLE customers DROP INDEX `{$indexName}`");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Jika ingin kembalikan unique constraint (opsional)
        // Schema::table('customers', function (Blueprint $table) {
        //     $table->unique('code_number');
        // });
    }
};
