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
        // Cek keberadaan kolom via INFORMATION_SCHEMA agar akurat di MySQL
        $exists = DB::table('INFORMATION_SCHEMA.COLUMNS')
            ->whereRaw('TABLE_SCHEMA = DATABASE()')
            ->where('TABLE_NAME', 'pos')
            ->where('COLUMN_NAME', 'status_approval')
            ->exists();

        if (!$exists) {
            Schema::table('pos', function (Blueprint $table) {
                $table->string('status_approval')->default('Pending')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos', function (Blueprint $table) {
            $table->dropColumn('status_approval');
        });
    }
};
