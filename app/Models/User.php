<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'name',
        'email',
        'password',
        'user_type',
        'phone',
        'locale',
        'timezone',
        'is_platform_user',
        'is_active',
        'meta',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_user' => 'boolean',
            'is_active' => 'boolean',
            'meta' => 'array',
            'last_login_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($panel->getId() === 'admin') {
            return $this->is_platform_user
                || $this->hasAnyRole([
                    'super_admin',
                    'platform_admin',
                    'business_owner',
                    'company_admin',
                    'operations_manager',
                    'branch_manager',
                    'quality_inspector',
                    'cleaning_supervisor',
                    'accountant',
                    'hr_manager',
                    'dispatcher',
                    'scheduler',
                ])
                || $this->can('dashboard.view');
        }

        return false;
    }

    public function isPlatformAdmin(): bool
    {
        return $this->is_platform_user || $this->hasAnyRole(['super_admin', 'platform_admin']);
    }
}
