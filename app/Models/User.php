<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'role_id',
        'role',
        'two_factor_enabled',
        'two_factor_phone',
        'two_factor_code',
        'two_factor_code_expires_at',
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
        'two_factor_code',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login' => 'datetime',
        'two_factor_code_expires_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Get the role this user belongs to
     */
    public function roleModel(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Check if user is a company admin.
     */
    public function isAdmin(): bool
    {
        if ($this->roleModel) {
            return $this->roleModel->isAdmin();
        }

        return strtolower(trim((string)$this->role ?? '')) === 'admin';
    }

    /**
     * Check if user is cashier.
     */
    public function isCashier(): bool
    {
        if ($this->roleModel) {
            return $this->roleModel->isCashier();
        }

        return strtolower(trim((string)$this->role ?? '')) === 'cashier';
    }

    /**
     * Check if user is a normal user/customer.
     */
    public function isUser(): bool
    {
        if ($this->roleModel) {
            return $this->roleModel->isUser();
        }

        return strtolower(trim((string)$this->role ?? '')) !== 'admin' && strtolower(trim((string)$this->role ?? '')) !== 'cashier';
    }

    /**
     * Get user's role name
     */
    public function getRoleName(): string
    {
        // Prefer the new relationship, but fall back to legacy `role` string column
        $name = $this->roleModel?->name ?? $this->role ?? 'user';
        // ensure consistent lowercase/trimmed value to avoid mismatches
        return strtolower(trim((string) $name));
    }
}
