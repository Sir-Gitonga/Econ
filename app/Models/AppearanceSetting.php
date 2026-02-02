<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppearanceSetting extends Model
{
    protected $fillable = [
        'company_id',
        'primary_color',
        'secondary_color',
        'theme',
        'invoice_template',
        'favicon',
    ];

    protected $table = 'appearance_settings';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get favicon URL
     */
    public function getFaviconUrlAttribute()
    {
        if ($this->favicon && file_exists(storage_path('app/public/' . $this->favicon))) {
            return asset('storage/' . $this->favicon);
        }
        return asset('images/favicon.ico');
    }

    /**
     * Get available invoice templates
     */
    public static function getInvoiceTemplates()
    {
        return [
            'default' => 'Default Template',
            'modern' => 'Modern Minimal',
            'professional' => 'Professional',
            'detailed' => 'Detailed with Items',
        ];
    }

    /**
     * Get available themes
     */
    public static function getThemes()
    {
        return [
            'light' => 'Light',
            'dark' => 'Dark',
        ];
    }
}
