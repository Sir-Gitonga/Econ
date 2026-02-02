<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appearance_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            // Theme Settings
            $table->string('primary_color')->default('#4F46E5'); // Indigo
            $table->string('secondary_color')->default('#06B6D4'); // Cyan
            $table->enum('theme', ['light', 'dark'])->default('light');
            $table->string('invoice_template')->default('default');
            $table->string('favicon')->nullable();

            $table->timestamps();

            $table->unique('company_id');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appearance_settings');
    }
};
