<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Hanya buat tabel jika belum ada (idempotent)
        if (!Schema::hasTable('employees')) {
            Schema::create('employees', function (Blueprint $table) {
                $table->id();
                $table->string('nama_karyawan');
                $table->string('no_telepon');
                $table->text('alamat');
                $table->string('posisi');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('employees');
    }
};
