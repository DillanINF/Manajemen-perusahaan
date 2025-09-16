<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        // Pastikan tidak ada UNIQUE index yang tersisa pada tabel jatuh_tempos (selain PRIMARY)
        $dbName = DB::getDatabaseName();
        $indexes = DB::select(
            "SELECT INDEX_NAME FROM information_schema.statistics WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'jatuh_tempos' AND NON_UNIQUE = 0 AND INDEX_NAME <> 'PRIMARY' GROUP BY INDEX_NAME",
            [$dbName]
        );

        Schema::table('jatuh_tempos', function (Blueprint $table) use ($indexes) {
            foreach ($indexes as $idx) {
                try {
                    $table->dropUnique($idx->INDEX_NAME);
                } catch (\Throwable $e) {
                    // Abaikan jika gagal, lanjut yang lain
                }
            }
        });
    }

    public function down(): void
    {
        // Tidak mengembalikan UNIQUE index secara otomatis
    }
};
