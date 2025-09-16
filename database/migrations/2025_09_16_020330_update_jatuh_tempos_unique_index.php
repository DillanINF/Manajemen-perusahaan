<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jatuh_tempos', function (Blueprint $table) {
            // Hapus unique tunggal pada no_invoice jika ada
            try { $table->dropUnique('jatuh_tempos_no_invoice_unique'); } catch (\Throwable $e) {}
            // Hapus unique gabungan jika sempat dibuat pada versi sebelumnya
            try { $table->dropUnique('jatuh_tempos_no_invoice_no_po_unique'); } catch (\Throwable $e) {}

            // Tambahkan index biasa (non-unique) untuk membantu pencarian
            try { $table->index('no_invoice', 'jatuh_tempos_no_invoice_index'); } catch (\Throwable $e) {}
            try { $table->index(['no_invoice', 'no_po'], 'jatuh_tempos_no_invoice_no_po_index'); } catch (\Throwable $e) {}
        });
    }

    public function down(): void
    {
        Schema::table('jatuh_tempos', function (Blueprint $table) {
            // Rollback: hapus index biasa
            try { $table->dropIndex('jatuh_tempos_no_invoice_no_po_index'); } catch (\Throwable $e) {}
            try { $table->dropIndex('jatuh_tempos_no_invoice_index'); } catch (\Throwable $e) {}

            // Kembalikan unique tunggal pada no_invoice (jika diinginkan saat rollback)
            try { $table->unique('no_invoice', 'jatuh_tempos_no_invoice_unique'); } catch (\Throwable $e) {}
        });
    }
};
