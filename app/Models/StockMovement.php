<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

class StockMovement extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = [
        'company_id',
        'product_id',
        'created_by',
        'type',
        'quantity',
        'before_quantity',
        'after_quantity',
        'reference_id',
        'reference_type',
        'notes',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
