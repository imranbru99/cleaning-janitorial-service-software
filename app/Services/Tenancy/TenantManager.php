<?php

namespace App\Services\Tenancy;

use App\Models\Tenant;
use App\Models\TenantDomain;
use App\Support\Tenancy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantManager
{
    public function resolve(?Request $request = null): ?Tenant
    {
        $request ??= request();

        if ($header = $request->header('X-Tenant')) {
            $tenant = Tenant::query()->where('slug', $header)->orWhere('id', $header)->first();
            if ($tenant) {
                return $tenant;
            }
        }

        if ($request->hasSession() && $request->session()->has('tenant_id')) {
            $tenant = Tenant::query()->find($request->session()->get('tenant_id'));
            if ($tenant) {
                return $tenant;
            }
        }

        $host = $request->getHost();
        $domain = TenantDomain::query()->where('domain', $host)->first();
        if ($domain) {
            return $domain->tenant;
        }

        $parts = explode('.', $host);
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            if (! in_array($subdomain, ['www', 'app', 'api', 'admin'], true)) {
                return Tenant::query()->where('slug', $subdomain)->first();
            }
        }

        if ($user = $request->user()) {
            return $user->tenant;
        }

        return null;
    }

    public function initialize(?Request $request = null): ?Tenant
    {
        $tenant = $this->resolve($request);
        Tenancy::set($tenant);

        return $tenant;
    }

    public function create(array $data, ?int $ownerUserId = null): Tenant
    {
        $slug = $data['slug'] ?? Str::slug($data['name']);

        $tenant = Tenant::query()->create([
            'name' => $data['name'],
            'slug' => $slug,
            'legal_name' => $data['legal_name'] ?? $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'currency' => $data['currency'] ?? 'USD',
            'timezone' => $data['timezone'] ?? 'UTC',
            'locale' => $data['locale'] ?? 'en',
            'country' => $data['country'] ?? null,
            'status' => 'active',
            'owner_user_id' => $ownerUserId,
            'trial_ends_at' => now()->addDays(14),
            'settings' => $data['settings'] ?? [],
        ]);

        TenantDomain::query()->create([
            'tenant_id' => $tenant->id,
            'domain' => ($data['domain'] ?? $slug.'.localhost'),
            'is_primary' => true,
            'is_verified' => true,
        ]);

        return $tenant;
    }
}
