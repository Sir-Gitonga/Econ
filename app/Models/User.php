<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasCompany;
use App\Scopes\CompanyScope;

/**
 * User Model
 *
 * Represents a user within a company/tenant.
 * Users can have different roles: admin, staff, customer.
 * Always scoped to their company.
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable, HasCompany;

    // Prevent automatic relationship loading to avoid circular references
    protected $with = [];
    protected $withCount = [];

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'mobile',
        'password',
        'company_id',
        'role',
    ];

    /**
     * Boot the model.
     * Apply global scope for company isolation.
     */
    protected static function booted()
    {
        // Apply company scope to automatically filter by current tenant
        static::addGlobalScope(new CompanyScope());
    }

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user is a company admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is cashier.
     */
    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    /**
     * Check if user is a normal user.
     */
    public function isUser(): bool
    {
        return $this->role === 'user' || is_null($this->role);
    }
}
