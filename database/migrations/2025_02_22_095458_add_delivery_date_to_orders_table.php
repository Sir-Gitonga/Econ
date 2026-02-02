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
        // Only add the column if it doesn't already exist (safe for existing DBs)
        if (!Schema::hasColumn('orders', 'delivered_date')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->timestamp('delivered_date')->nullable()->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('orders', 'delivered_date')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('delivered_date');
            });
        }
    }
};
