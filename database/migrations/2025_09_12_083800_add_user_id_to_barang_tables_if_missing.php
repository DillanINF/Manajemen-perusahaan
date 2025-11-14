<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // barang_masuks: tambah user_id kalau belum ada
        if (Schema::hasTable('barang_masuks')) {
            Schema::table('barang_masuks', function (Blueprint $table) {
                if (!Schema::hasColumn('barang_masuks', 'user_id')) {
                    $table->foreignId('user_id')
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();
                }
            });
        }

        // barang_keluars: tambah user_id kalau belum ada (fallback)
        if (Schema::hasTable('barang_keluars')) {
            Schema::table('barang_keluars', function (Blueprint $table) {
                if (!Schema::hasColumn('barang_keluars', 'user_id')) {
                    $table->foreignId('user_id')
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('barang_masuks') && Schema::hasColumn('barang_masuks', 'user_id')) {
            Schema::table('barang_masuks', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
        if (Schema::hasTable('barang_keluars') && Schema::hasColumn('barang_keluars', 'user_id')) {
            Schema::table('barang_keluars', function (Blueprint $table) {
                $table->dropConstrainedForeignId('user_id');
            });
        }
    }
};
