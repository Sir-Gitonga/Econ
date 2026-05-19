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
        Schema::table('companies', function (Blueprint $table) {
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active')->after('updated_at');
            $table->foreignId('current_plan_id')->nullable()->constrained('subscription_plans')->onDelete('set null')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['current_plan_id']);
            $table->dropColumn(['status', 'current_plan_id']);
        });
    }
};