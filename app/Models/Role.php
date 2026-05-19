<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Role Model
 * 
 * Defines user roles for RBAC within a company context.
 * Each role is scoped to a company for multi-tenant isolation.
 */
class Role extends Model
{
    use HasFactory;

    /**
     * Fillable attributes
     */
    protected $fillable = [
        'company_id',
        'name',
        'label',
        'description',
    ];

    /**
     * Get the company this role belongs to
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get all users with this role
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this is the admin role
     */
    public function isAdmin(): bool
    {
        return $this->name === 'admin';
    }

    /**
     * Check if this is the cashier role
     */
    public function isCashier(): bool
    {
        return $this->name === 'cashier';
    }

    /**
     * Check if this is the user/customer role
     */
    public function isUser(): bool
    {
        return $this->name === 'user';
    }
}
