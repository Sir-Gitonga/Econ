<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessSetting extends Model
{
    protected $fillable = [
        'company_id',
        'about_description',
        'mission',
        'vision',
        'services',
        'invoice_prefix',
        'tax_rate',
        'vat_pin',
        'invoice_number_counter',
        'session_timeout_minutes',
        'two_factor_enabled',
    ];

    protected $table = 'business_settings';

    protected $casts = [
        'two_factor_enabled' => 'boolean',
        'tax_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Generate next invoice number
     */
    public function getNextInvoiceNumber(): string
    {
        $this->increment('invoice_number_counter');
        return $this->invoice_prefix . str_pad($this->invoice_number_counter, 6, '0', STR_PAD_LEFT);
    }
}
