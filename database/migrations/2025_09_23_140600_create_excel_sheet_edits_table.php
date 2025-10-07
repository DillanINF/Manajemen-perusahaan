<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('excel_sheet_edits')) {
            Schema::create('excel_sheet_edits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('sheet_name');
                $table->unsignedSmallInteger('period_year')->nullable()->index();
                $table->unsignedTinyInteger('period_month')->nullable()->index();
                $table->json('cells'); // key: cell address (e.g., "A1"), value: input text
                $table->timestamps();
                $table->softDeletes();

                // Optional FK if users table exists
                // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excel_sheet_edits');
    }
};
