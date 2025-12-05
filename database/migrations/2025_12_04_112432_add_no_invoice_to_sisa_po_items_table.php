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
        Schema::table('sisa_po_items', function (Blueprint $table) {
            $table->string('no_invoice')->nullable()->after('no_po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sisa_po_items', function (Blueprint $table) {
            $table->dropColumn('no_invoice');
        });
    }
};
