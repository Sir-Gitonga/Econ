<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
    ];

    public function tenantSubscriptions()
    {
        return $this->hasMany(TenantSubscription::class, 'plan_id');
    }

    public function companies()
    {
        return $this->hasMany(Company::class, 'current_plan_id');
    }
}