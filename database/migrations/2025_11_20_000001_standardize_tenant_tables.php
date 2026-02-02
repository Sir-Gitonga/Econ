<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that require company_id for multi-tenancy isolation.
     * These are tenant-scoped tables where each company has isolated data.
     */
    private array $tenantTables = [
        'users',
        'slides',
        'brands',
        'categories',
        'coupons',
        'products',
        'orders',
        'order_items',
        'transactions',
        'addresses',
        'contacts',
    ];

    public function up(): void
    {
        foreach ($this->tenantTables as $table) {
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    // Only add company_id if it doesn't exist
                    if (!Schema::hasColumn($table, 'company_id')) {
                        $t->foreignId('company_id')
                            ->nullable()
                            ->after('id')
                            ->constrained('companies')
                            ->cascadeOnDelete();

                        // Add index for company_id to optimize queries
                        $t->index('company_id', 'idx_' . $table . '_company_id');
                    }
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tenantTables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropConstrainedForeignId('company_id');
                });
            }
        }
    }
};
