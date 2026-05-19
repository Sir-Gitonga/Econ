<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds trial period and billing management to companies table
     */
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Trial period tracking
            if (!Schema::hasColumn('companies', 'trial_started_at')) {
                $table->timestamp('trial_started_at')->nullable()->after('current_plan_id')->comment('When 30-day free trial started');
            }
            if (!Schema::hasColumn('companies', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('trial_started_at')->comment('When 30-day free trial expires');
            }
            if (!Schema::hasColumn('companies', 'trial_notified_day_25')) {
                $table->boolean('trial_notified_day_25')->default(false)->after('trial_ends_at')->comment('Flag: Was notification sent on day 25?');
            }
            
            // Subscription tracking
            if (!Schema::hasColumn('companies', 'subscription_started_at')) {
                $table->timestamp('subscription_started_at')->nullable()->after('trial_notified_day_25')->comment('When paid subscription started');
            }
            // subscription_expires_at already exists in create_companies_table
            if (!Schema::hasColumn('companies', 'subscription_status')) {
                $table->enum('subscription_status', ['trial', 'active', 'expired', 'canceled'])->default('trial')->after('subscription_expires_at')->comment('Current subscription status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'trial_started_at',
                'trial_ends_at',
                'trial_notified_day_25',
                'subscription_started_at',
                'subscription_expires_at',
                'subscription_status'
            ]);
        });
    }
};
