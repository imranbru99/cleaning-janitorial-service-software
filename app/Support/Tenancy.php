<?php

namespace App\Support;

use App\Models\Tenant;

class Tenancy
{
    protected static ?Tenant $tenant = null;

    public static function set(?Tenant $tenant): void
    {
        static::$tenant = $tenant;

        if ($tenant) {
            app()->instance('currentTenant', $tenant);
        } else {
            app()->forgetInstance('currentTenant');
        }
    }

    public static function tenant(): ?Tenant
    {
        return static::$tenant ?? (app()->bound('currentTenant') ? app('currentTenant') : null);
    }

    public static function id(): ?int
    {
        return static::tenant()?->id;
    }

    public static function check(): bool
    {
        return static::id() !== null;
    }

    public static function forget(): void
    {
        static::set(null);
    }

    public static function run(Tenant $tenant, callable $callback): mixed
    {
        $previous = static::tenant();
        static::set($tenant);

        try {
            return $callback($tenant);
        } finally {
            static::set($previous);
        }
    }
}
