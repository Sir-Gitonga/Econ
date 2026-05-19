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
        Schema::table('products', function (Blueprint $table) {
            // rename original quantity column to stock_quantity
            if (Schema::hasColumn('products', 'quantity')) {
                $table->renameColumn('quantity', 'stock_quantity');
            }

            // new threshold field to drive status transitions
            if (! Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->unsignedInteger('low_stock_threshold')->default(5)->after('stock_quantity');
            }

            // modify enum definition; raw statement ensures compatibility
            DB::statement("ALTER TABLE products MODIFY stock_status ENUM('instock','lowstock','outofstock') NOT NULL DEFAULT 'instock'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // revert enum back to original two values
            DB::statement("ALTER TABLE products MODIFY stock_status ENUM('instock','outofstock') NOT NULL");

            if (Schema::hasColumn('products', 'low_stock_threshold')) {
                $table->dropColumn('low_stock_threshold');
            }

            if (Schema::hasColumn('products', 'stock_quantity')) {
                $table->renameColumn('stock_quantity', 'quantity');
            }
        });
    }
};
