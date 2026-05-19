<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

class Shift extends Model
{
    use HasFactory, HasCompany;

    protected $fillable = ['company_id','user_id','opening_cash','closing_cash','opened_at','closed_at','variance'];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrent($query)
    {
        return $query->whereNull('closed_at')->where('user_id', auth()->id());
    }
}
