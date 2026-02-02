<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    $tables = ['users', 'slides', 'brands', 'categories', 'coupons', 'products'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->foreignId('company_id')->nullable()->after('id')->constrained('companies')->onDelete('cascade');
                });
            }
        }
    }

    public function down(): void
    {
    $tables = ['users', 'slides', 'brands', 'categories', 'coupons', 'products'];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'company_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropConstrainedForeignId('company_id');
                });
            }
        }
    }
};
