<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();

            // SMTP Settings
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->default(587)->nullable();
            $table->string('smtp_username')->nullable();
            $table->text('smtp_password')->nullable(); // Will be encrypted
            $table->string('smtp_from_address')->nullable();
            $table->string('smtp_from_name')->nullable();
            $table->enum('smtp_encryption', ['tls', 'ssl'])->default('tls')->nullable();

            // SMS Settings
            $table->text('sms_api_key')->nullable(); // Will be encrypted
            $table->string('sms_provider')->nullable(); // e.g., Twilio, Vonage

            // Notifications
            $table->boolean('email_notifications_enabled')->default(true);
            $table->boolean('sms_notifications_enabled')->default(true);

            $table->timestamps();

            $table->unique('company_id');
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_settings');
    }
};
