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
        Schema::create('sisa_po_items', function (Blueprint $table) {
            $table->id();
            $table->string('no_po');
            $table->unsignedBigInteger('produk_id');
            $table->integer('qty_diminta'); // Jumlah yang diminta di PO
            $table->integer('qty_tersedia'); // Stok yang tersedia saat itu
            $table->integer('qty_sisa'); // Sisa yang belum bisa dipenuhi
            $table->string('qty_jenis')->default('PCS'); // Satuan
            $table->decimal('harga', 15, 2)->default(0);
            $table->decimal('total_sisa', 15, 2)->default(0); // Total nilai sisa
            $table->string('customer')->nullable();
            $table->date('tanggal_po');
            $table->enum('status', ['pending', 'fulfilled', 'cancelled'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');
            
            // Indexes untuk performa
            $table->index(['no_po', 'status']);
            $table->index(['produk_id', 'status']);
            $table->index('tanggal_po');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sisa_po_items');
    }
};
