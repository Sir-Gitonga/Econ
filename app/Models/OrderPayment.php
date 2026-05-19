<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

class OrderPayment extends Model
{
    use HasFactory, HasCompany;

    protected $table = 'payments';

    protected $fillable = ['order_id','amount','method','transaction_id','company_id'];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
