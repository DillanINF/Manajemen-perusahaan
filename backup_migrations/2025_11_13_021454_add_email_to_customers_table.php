public function up(): void
{
    Schema::table('customers', function (Blueprint $table) {
        $table->string('email')->nullable()->after('code_number');
    });
}

public function down(): void
{
    Schema::table('customers', function (Blueprint $table) {
        $table->dropColumn('email');
    });
}
