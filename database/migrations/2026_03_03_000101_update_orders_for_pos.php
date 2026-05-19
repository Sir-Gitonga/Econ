<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // tenant isolation
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete()->after('id');
            // unified reference for POS
            $table->string('reference')->nullable()->unique()->after('company_id');
            // track who created sale/cashier
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('user_id');
            // add payment details
            $table->enum('payment_method', ['cash','mpesa','card'])->nullable()->after('status');
            // add order type (web, pos, etc)
            $table->string('order_type')->default('web')->after('payment_method');
            // remove some ecommerce columns not used by POS? keep for compatibility
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['company_id','reference','created_by','payment_method','order_type']);
        });
    }
};