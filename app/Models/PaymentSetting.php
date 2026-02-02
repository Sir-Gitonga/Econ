<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentSetting extends Model
{
    protected $fillable = [
        'company_id',
        'gateway',
        'mpesa_paybill',
        'mpesa_consumer_key',
        'mpesa_consumer_secret',
        'mpesa_passkey',
        'mpesa_environment',
        'intasend_publishable_key',
        'intasend_secret_key',
        'intasend_mode',
    ];

    protected $table = 'payment_settings';

    /**
     * Attributes that should be encrypted
     */
    protected $encrypted = [
        'mpesa_consumer_secret',
        'mpesa_passkey',
        'intasend_secret_key',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get available payment gateways
     */
    public static function getGateways()
    {
        return [
            'mpesa' => 'M-PESA Only',
            'intasend' => 'IntaSend Only',
            'both' => 'M-PESA + IntaSend',
        ];
    }

    /**
     * Get M-PESA environments
     */
    public static function getMpesaEnvironments()
    {
        return [
            'sandbox' => 'Sandbox (Testing)',
            'live' => 'Live (Production)',
        ];
    }

    /**
     * Get IntaSend modes
     */
    public static function getIntasendModes()
    {
        return [
            'test' => 'Test Mode',
            'live' => 'Live Mode',
        ];
    }
}
