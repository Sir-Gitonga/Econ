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
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('canceled_date');
            $table->unsignedBigInteger('refunded_by')->nullable()->after('refunded_at');
            $table->foreign('refunded_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger('refunded_quantity')->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('refunded_quantity');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['refunded_by']);
            $table->dropColumn('refunded_by');
            $table->dropColumn('refunded_at');
        });
    }
};
