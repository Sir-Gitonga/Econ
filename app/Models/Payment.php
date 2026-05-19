<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Payment Model
 * 
 * Tracks company payments and invoices for subscriptions
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'subscription_plan_id',
        'invoice_number',
        'amount',
        'mpesa_phone',
        'status',
        'payment_method',
        'mpesa_transaction_id',
        'mpesa_receipt_number',
        'payment_for_period_start',
        'payment_for_period_end',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_for_period_start' => 'datetime',
        'payment_for_period_end' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the company that owns this payment
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the subscription plan for this payment
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber(): string
    {
        $date = now()->format('Ymd');
        $random = str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
        return "INV-{$date}-{$random}";
    }

    /**
     * Check if payment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Mark payment as completed with receipt
     */
    public function markAsCompleted(string $receiptNumber): void
    {
        $this->update([
            'status' => 'completed',
            'mpesa_receipt_number' => $receiptNumber,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => 'failed',
        ]);
    }
}
