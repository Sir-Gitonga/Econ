<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyWhatsappSetting extends Model
{
    use HasFactory;

    protected $table = 'company_whatsapp_settings';

    protected $fillable = [
        'company_id',
        'gateway',
        'api_key',
        'instance_id',
        'base_url',
        'account_sid',
        'auth_token',
        'from_number',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
