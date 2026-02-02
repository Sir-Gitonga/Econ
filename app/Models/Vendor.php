<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'logo', 'cover_image',
        'slug', 'description', 'address', 'city', 'country',
        'is_active', 'is_verified'
    ];

    // 🛍️ One vendor has many products
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // 🎞️ One vendor has many slides
    public function slides()
    {
        return $this->hasMany(Slide::class);
    }

    // 📦 One vendor has many orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // 👥 One vendor has many customers (vendor_users)
    public function customers()
    {
        return $this->hasMany(Vendor_users::class);
    }
}
