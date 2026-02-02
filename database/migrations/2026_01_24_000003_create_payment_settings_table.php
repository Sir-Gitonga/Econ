<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            // Gateway Selection
            $table->enum('gateway', ['mpesa', 'intasend', 'both'])->default('mpesa');

            // M-PESA Settings (Encrypted)
            $table->string('mpesa_paybill')->nullable();
            $table->text('mpesa_consumer_key')->nullable();
            $table->text('mpesa_consumer_secret')->nullable(); // Will be encrypted
            $table->text('mpesa_passkey')->nullable(); // Will be encrypted
            $table->enum('mpesa_environment', ['sandbox', 'live'])->default('sandbox');

            // IntaSend Settings (Encrypted)
            $table->text('intasend_publishable_key')->nullable();
            $table->text('intasend_secret_key')->nullable(); // Will be encrypted
            $table->enum('intasend_mode', ['test', 'live'])->default('test');

            $table->timestamps();

            $table->unique('company_id');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_settings');
    }
};
