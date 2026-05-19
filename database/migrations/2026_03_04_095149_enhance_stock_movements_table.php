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
        Schema::table('stock_movements', function (Blueprint $table) {
            // add polymorphic reference fields
            if (! Schema::hasColumn('stock_movements', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('product_id');
                $table->string('reference_type')->nullable()->after('reference_id');
            }

            // record before/after quantities
            if (! Schema::hasColumn('stock_movements', 'before_quantity')) {
                $table->integer('before_quantity')->nullable()->after('quantity');
            }
            if (! Schema::hasColumn('stock_movements', 'after_quantity')) {
                $table->integer('after_quantity')->nullable()->after('before_quantity');
            }

            // rename user_id to created_by
            if (Schema::hasColumn('stock_movements', 'user_id') && ! Schema::hasColumn('stock_movements', 'created_by')) {
                $table->renameColumn('user_id', 'created_by');
            }

            // indexing for faster lookups
            $table->index(['company_id', 'product_id']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'product_id']);
            $table->dropIndex(['reference_type', 'reference_id']);

            if (Schema::hasColumn('stock_movements', 'created_by') && ! Schema::hasColumn('stock_movements', 'user_id')) {
                $table->renameColumn('created_by', 'user_id');
            }

            if (Schema::hasColumn('stock_movements', 'after_quantity')) {
                $table->dropColumn('after_quantity');
            }
            if (Schema::hasColumn('stock_movements', 'before_quantity')) {
                $table->dropColumn('before_quantity');
            }
            if (Schema::hasColumn('stock_movements', 'reference_type')) {
                $table->dropColumn('reference_type');
            }
            if (Schema::hasColumn('stock_movements', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
        });
    }
};
