<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Only add the id column if it does not already exist (safe for existing databases)
        if (!Schema::hasColumn('products', 'id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->id()->first(); // Add auto-incrementing primary key
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('products', 'id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('id'); // Remove id if rolling back
            });
        }
    }
};
