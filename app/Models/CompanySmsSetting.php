<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySmsSetting extends Model
{
    use HasFactory;

    protected $table = 'company_sms_settings';

    protected $fillable = [
        'company_id',
        'provider',
        'username',
        'api_key',
        'partner_id',
        'sender_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
