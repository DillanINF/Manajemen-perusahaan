<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            if (!Schema::hasColumn('salaries', 'jenis_gaji')) {
                $table->enum('jenis_gaji', ['borongan', 'harian'])->default('borongan')->after('employee_id');
            }
            if (!Schema::hasColumn('salaries', 'jumlah_hari')) {
                $table->integer('jumlah_hari')->nullable()->after('jenis_gaji'); // untuk gaji harian
            }
            if (!Schema::hasColumn('salaries', 'tarif_harian')) {
                $table->integer('tarif_harian')->nullable()->after('jumlah_hari'); // untuk gaji harian
            }
            if (!Schema::hasColumn('salaries', 'jumlah_unit')) {
                $table->integer('jumlah_unit')->nullable()->after('tarif_harian'); // untuk gaji borongan
            }
            if (!Schema::hasColumn('salaries', 'tarif_per_unit')) {
                $table->integer('tarif_per_unit')->nullable()->after('jumlah_unit'); // untuk gaji borongan
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('salaries', 'jenis_gaji')) {
                $columnsToDrop[] = 'jenis_gaji';
            }
            if (Schema::hasColumn('salaries', 'jumlah_hari')) {
                $columnsToDrop[] = 'jumlah_hari';
            }
            if (Schema::hasColumn('salaries', 'tarif_harian')) {
                $columnsToDrop[] = 'tarif_harian';
            }
            if (Schema::hasColumn('salaries', 'jumlah_unit')) {
                $columnsToDrop[] = 'jumlah_unit';
            }
            if (Schema::hasColumn('salaries', 'tarif_per_unit')) {
                $columnsToDrop[] = 'tarif_per_unit';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
