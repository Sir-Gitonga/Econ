<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Creates payments table to track M-Pesa transactions and invoices
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('subscription_plan_id')->nullable()->constrained('subscription_plans')->onDelete('set null');
            
            // Payment Details
            $table->string('invoice_number')->unique()->comment('Unique invoice identifier');
            $table->decimal('amount', 12, 2)->comment('Amount in Ksh');
            $table->string('mpesa_phone')->nullable()->comment('M-Pesa phone number used for payment');
            
            // Status tracking
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['mpesa'])->default('mpesa')->comment('Payment method used');
            
            // M-Pesa tracking
            $table->string('mpesa_transaction_id')->nullable()->unique()->comment('M-Pesa CheckoutRequestID or transaction reference');
            $table->string('mpesa_receipt_number')->nullable()->unique()->comment('M-Pesa receipt after successful payment');
            
            // Period tracking
            $table->timestamp('payment_for_period_start')->nullable()->comment('Subscription period this payment covers');
            $table->timestamp('payment_for_period_end')->nullable()->comment('Subscription period end date');
            
            // Timestamps
            $table->timestamp('paid_at')->nullable()->comment('When payment was completed');
            $table->timestamps();
            
            // Indices
            $table->index('company_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
