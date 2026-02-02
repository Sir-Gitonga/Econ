<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Add company_id if missing
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
            }

            // Add role column if missing
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'cashier', 'user'])->default('user')->after('company_id');
            }

            // Add status column if missing
            if (!Schema::hasColumn('users', 'status')) {
                $table->boolean('status')->default(1)->after('role');
            }

            // Add last_login if missing
            if (!Schema::hasColumn('users', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('status');
            }
        });

        // Normalize existing role values (staff/customer -> cashier/user)
        DB::table('users')->where('role', 'staff')->update(['role' => 'cashier']);
        DB::table('users')->where('role', 'customer')->update(['role' => 'user']);
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'last_login')) {
                $table->dropColumn('last_login');
            }
            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
            // keep company_id - important for tenant model, do not drop
        });
    }
};
