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
        // Tambahkan kolom employee_id yang hilang di tabel salaries
        if (!Schema::hasColumn('salaries', 'employee_id')) {
            DB::statement('ALTER TABLE `salaries` ADD COLUMN `employee_id` BIGINT UNSIGNED NULL AFTER `id`');
            
            // Tambahkan index untuk performa
            try {
                DB::statement('ALTER TABLE `salaries` ADD INDEX `salaries_employee_id_index` (`employee_id`)');
            } catch (\Throwable $e) {
                // Index mungkin sudah ada, abaikan error
            }
            
            // Tambahkan foreign key jika tabel employees ada
            try {
                DB::statement('ALTER TABLE `salaries` ADD CONSTRAINT `salaries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE SET NULL');
            } catch (\Throwable $e) {
                // FK mungkin gagal jika ada data tidak konsisten, biarkan tetap NULL tanpa FK
            }
        }
        
        // Tambahkan kolom lain yang hilang jika belum ada
        $columnsToAdd = [
            'tunjangan' => 'DECIMAL(15,2) DEFAULT 0 AFTER `gaji_pokok`',
            'bonus' => 'DECIMAL(15,2) DEFAULT 0 AFTER `tunjangan`',
            'lembur' => 'DECIMAL(15,2) DEFAULT 0 AFTER `bonus`',
            'potongan_pajak' => 'DECIMAL(15,2) DEFAULT 0 AFTER `lembur`',
            'potongan_bpjs' => 'DECIMAL(15,2) DEFAULT 0 AFTER `potongan_pajak`',
            'potongan_lain' => 'DECIMAL(15,2) DEFAULT 0 AFTER `potongan_bpjs`',
            'tanggal_bayar' => 'DATE NULL AFTER `status_pembayaran`',
            'keterangan' => 'TEXT NULL AFTER `tanggal_bayar`',
        ];
        
        foreach ($columnsToAdd as $column => $definition) {
            if (!Schema::hasColumn('salaries', $column)) {
                try {
                    DB::statement("ALTER TABLE `salaries` ADD COLUMN `$column` $definition");
                } catch (\Throwable $e) {
                    // Kolom mungkin sudah ada dengan nama/tipe berbeda, skip
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus foreign key
        try {
            DB::statement('ALTER TABLE `salaries` DROP FOREIGN KEY `salaries_employee_id_foreign`');
        } catch (\Throwable $e) {
            // Abaikan jika FK tidak ada
        }
        
        // Hapus index
        try {
            DB::statement('ALTER TABLE `salaries` DROP INDEX `salaries_employee_id_index`');
        } catch (\Throwable $e) {
            // Abaikan jika index tidak ada
        }
        
        // Hapus kolom employee_id
        if (Schema::hasColumn('salaries', 'employee_id')) {
            Schema::table('salaries', function (Blueprint $table) {
                $table->dropColumn('employee_id');
            });
        }
        
        // Hapus kolom lain yang ditambahkan
        Schema::table('salaries', function (Blueprint $table) {
            $columnsToRemove = ['tunjangan', 'bonus', 'lembur', 'potongan_pajak', 'potongan_bpjs', 'potongan_lain', 'tanggal_bayar', 'keterangan'];
            foreach ($columnsToRemove as $column) {
                if (Schema::hasColumn('salaries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
