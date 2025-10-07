<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Gunakan SQL mentah agar tidak perlu doctrine/dbal
        // 1) Drop foreign key lama (nama default Laravel)
        try {
            DB::statement('ALTER TABLE `salaries` DROP FOREIGN KEY `salaries_employee_id_foreign`');
        } catch (\Throwable $e) {
            // Abaikan jika sudah tidak ada atau nama berbeda
        }

        // 2) Ubah kolom menjadi NULLABLE
        DB::statement('ALTER TABLE `salaries` MODIFY `employee_id` BIGINT UNSIGNED NULL');

        // 3) Tambahkan kembali FK dengan ON DELETE SET NULL
        try {
            DB::statement('ALTER TABLE `salaries` ADD CONSTRAINT `salaries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE SET NULL');
        } catch (\Throwable $e) {
            // Jika gagal, biarkan tanpa FK agar tidak menghambat penyimpanan simple
        }
    }

    public function down(): void
    {
        // Kembalikan ke NOT NULL dan ON DELETE CASCADE (default awal)
        try {
            DB::statement('ALTER TABLE `salaries` DROP FOREIGN KEY `salaries_employee_id_foreign`');
        } catch (\Throwable $e) {
        }

        // Hati-hati: jika ada baris employee_id NULL, perintah ini akan gagal.
        // Kita set nilai NULL menjadi 0 dulu (asumsi tidak ada employee id 0), lalu ubah menjadi NOT NULL.
        try {
            DB::statement('UPDATE `salaries` SET `employee_id` = 0 WHERE `employee_id` IS NULL');
        } catch (\Throwable $e) {
        }

        DB::statement('ALTER TABLE `salaries` MODIFY `employee_id` BIGINT UNSIGNED NOT NULL');

        try {
            DB::statement('ALTER TABLE `salaries` ADD CONSTRAINT `salaries_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE');
        } catch (\Throwable $e) {
        }
    }
};
