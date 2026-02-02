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
        // Only add the password column if it does not already exist
        if (!Schema::hasColumn('companies', 'password')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('password')->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the password column only if it exists
        if (Schema::hasColumn('companies', 'password')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropColumn('password');
            });
        }

    }
};
