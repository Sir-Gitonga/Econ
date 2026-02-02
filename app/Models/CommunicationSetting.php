<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationSetting extends Model
{
    protected $fillable = [
        'company_id',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_from_address',
        'smtp_from_name',
        'smtp_encryption',
        'sms_api_key',
        'sms_provider',
        'email_notifications_enabled',
        'sms_notifications_enabled',
    ];

    protected $table = 'communication_settings';

    /**
     * Attributes that should be encrypted
     */
    protected $encrypted = [
        'smtp_password',
        'sms_api_key',
    ];

    protected $casts = [
        'email_notifications_enabled' => 'boolean',
        'sms_notifications_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get SMTP encryption options
     */
    public static function getEncryptionOptions()
    {
        return [
            'tls' => 'TLS',
            'ssl' => 'SSL',
        ];
    }

    /**
     * Get SMS providers
     */
    public static function getSmsProviders()
    {
        return [
            'twilio' => 'Twilio',
            'vonage' => 'Vonage (Nexmo)',
            'africastalking' => 'Africa\'s Talking',
        ];
    }
}
