<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            // About Company
            $table->text('about_description')->nullable();
            $table->text('mission')->nullable();
            $table->text('vision')->nullable();
            $table->text('services')->nullable();

            // Invoice Settings
            $table->string('invoice_prefix')->default('INV');
            $table->decimal('tax_rate', 5, 2)->default(0); // VAT percentage
            $table->string('vat_pin')->nullable();
            $table->integer('invoice_number_counter')->default(1000);

            // Security & Session
            $table->integer('session_timeout_minutes')->default(30);
            $table->boolean('two_factor_enabled')->default(false);

            $table->timestamps();

            $table->unique('company_id');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_settings');
    }
};
