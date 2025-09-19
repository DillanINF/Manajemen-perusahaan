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
            $table->enum('jenis_gaji', ['borongan', 'harian'])->default('borongan')->after('employee_id');
            $table->integer('jumlah_hari')->nullable()->after('jenis_gaji'); // untuk gaji harian
            $table->integer('tarif_harian')->nullable()->after('jumlah_hari'); // untuk gaji harian
            $table->integer('jumlah_unit')->nullable()->after('tarif_harian'); // untuk gaji borongan
            $table->integer('tarif_per_unit')->nullable()->after('jumlah_unit'); // untuk gaji borongan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['jenis_gaji', 'jumlah_hari', 'tarif_harian', 'jumlah_unit', 'tarif_per_unit']);
        });
    }
};
