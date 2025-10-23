<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (!Schema::hasColumn('customers', 'phone')) {
                    // Letakkan setelah email jika kolom email ada
                    if (Schema::hasColumn('customers', 'email')) {
                        $table->string('phone', 50)->nullable()->after('email');
                    } else {
                        $table->string('phone', 50)->nullable();
                    }
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'phone')) {
                    $table->dropColumn('phone');
                }
            });
        }
    }
};
