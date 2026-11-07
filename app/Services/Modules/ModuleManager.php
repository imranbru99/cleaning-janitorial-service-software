<?php

namespace App\Services\Modules;

use App\Models\Module;
use App\Models\Tenant;
use App\Models\TenantModule;
use App\Support\Tenancy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use RuntimeException;

class ModuleManager
{
    public function definitions(): Collection
    {
        return collect(config('modules.modules', []))->map(
            fn (array $def, string $key) => array_merge(['key' => $key], $def)
        );
    }

    public function definition(string $key): ?array
    {
        $modules = config('modules.modules', []);

        if (! isset($modules[$key])) {
            return null;
        }

        return array_merge(['key' => $key], $modules[$key]);
    }

    public function syncRegistry(): void
    {
        foreach ($this->definitions() as $definition) {
            Module::query()->updateOrCreate(
                ['key' => $definition['key']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'] ?? null,
                    'version' => $definition['version'] ?? '1.0.0',
                    'category' => $definition['category'] ?? null,
                    'is_core' => (bool) ($definition['core'] ?? false),
                    'is_global_enabled' => true,
                    'dependencies' => $definition['dependencies'] ?? [],
                    'permissions' => $definition['permissions'] ?? [],
                ]
            );
        }
    }

    public function isActive(string $moduleKey, ?Tenant $tenant = null): bool
    {
        $definition = $this->definition($moduleKey);

        if (! $definition) {
            return false;
        }

        if ($definition['core'] ?? false) {
            return true;
        }

        $module = Module::query()->where('key', $moduleKey)->first();
        if ($module && ! $module->is_global_enabled) {
            return false;
        }

        $tenant ??= Tenancy::tenant();
        if (! $tenant) {
            return false;
        }

        return (bool) Cache::remember(
            $this->cacheKey($tenant->id, $moduleKey),
            now()->addMinutes(10),
            fn () => TenantModule::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('module_key', $moduleKey)
                ->where('is_active', true)
                ->exists()
        );
    }

    public function activate(string $moduleKey, Tenant $tenant): TenantModule
    {
        $definition = $this->definition($moduleKey);
        if (! $definition) {
            throw new InvalidArgumentException("Unknown module [{$moduleKey}].");
        }

        $this->assertNoCircularDependency($moduleKey, $definition['dependencies'] ?? []);
        $this->assertDependenciesMet($moduleKey, $tenant);

        $record = TenantModule::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'module_key' => $moduleKey],
            [
                'is_active' => true,
                'activated_at' => now(),
                'deactivated_at' => null,
            ]
        );

        $this->forgetCache($tenant->id, $moduleKey);

        return $record;
    }

    public function deactivate(string $moduleKey, Tenant $tenant): TenantModule
    {
        $definition = $this->definition($moduleKey);
        if (! $definition) {
            throw new InvalidArgumentException("Unknown module [{$moduleKey}].");
        }

        if ($definition['core'] ?? false) {
            throw new RuntimeException("Core module [{$moduleKey}] cannot be deactivated.");
        }

        $dependents = $this->activeDependents($moduleKey, $tenant);
        if ($dependents->isNotEmpty()) {
            throw new RuntimeException(
                "Cannot deactivate [{$moduleKey}] while dependent modules are active: ".$dependents->implode(', ')
            );
        }

        $record = TenantModule::withoutGlobalScopes()->updateOrCreate(
            ['tenant_id' => $tenant->id, 'module_key' => $moduleKey],
            [
                'is_active' => false,
                'deactivated_at' => now(),
            ]
        );

        $this->forgetCache($tenant->id, $moduleKey);

        return $record;
    }

    public function activateDefaults(Tenant $tenant, array $moduleKeys): void
    {
        foreach ($this->topologicalSort($moduleKeys) as $key) {
            if ($this->definition($key) && ! ($this->definition($key)['core'] ?? false)) {
                $this->activate($key, $tenant);
            }
        }
    }

    public function navigationFor(?Tenant $tenant = null): Collection
    {
        $tenant ??= Tenancy::tenant();

        return $this->definitions()
            ->filter(fn (array $def) => $this->isActive($def['key'], $tenant))
            ->flatMap(fn (array $def) => collect($def['navigation'] ?? [])->map(
                fn (array $item) => array_merge($item, ['module' => $def['key']])
            ))
            ->values();
    }

    public function assertDependenciesMet(string $moduleKey, Tenant $tenant): void
    {
        $definition = $this->definition($moduleKey);

        foreach ($definition['dependencies'] ?? [] as $dependency) {
            $depDef = $this->definition($dependency);
            if (($depDef['core'] ?? false) === true) {
                continue;
            }

            if (! $this->isActive($dependency, $tenant)) {
                throw new RuntimeException("Module [{$moduleKey}] requires [{$dependency}] to be active.");
            }
        }
    }

    protected function activeDependents(string $moduleKey, Tenant $tenant): Collection
    {
        return $this->definitions()
            ->filter(fn (array $def) => in_array($moduleKey, $def['dependencies'] ?? [], true)
                && $this->isActive($def['key'], $tenant))
            ->pluck('key');
    }

    public function assertNoCircularDependency(string $moduleKey, array $dependencies, array $stack = []): void
    {
        $stack[] = $moduleKey;

        foreach ($dependencies as $dependency) {
            if (in_array($dependency, $stack, true)) {
                throw new RuntimeException('Circular module dependency detected: '.implode(' -> ', [...$stack, $dependency]));
            }

            $child = $this->definition($dependency);
            if ($child) {
                $this->assertNoCircularDependency($dependency, $child['dependencies'] ?? [], $stack);
            }
        }
    }

    public function topologicalSort(array $moduleKeys): array
    {
        $resolved = [];
        $visiting = [];

        $visit = function (string $key) use (&$visit, &$resolved, &$visiting): void {
            if (isset($resolved[$key])) {
                return;
            }

            if (isset($visiting[$key])) {
                throw new RuntimeException("Circular dependency involving [{$key}].");
            }

            $visiting[$key] = true;
            $def = $this->definition($key) ?? ['dependencies' => []];

            foreach ($def['dependencies'] ?? [] as $dep) {
                $visit($dep);
            }

            unset($visiting[$key]);
            $resolved[$key] = true;
        };

        foreach ($moduleKeys as $key) {
            $visit($key);
        }

        return array_keys($resolved);
    }

    protected function cacheKey(int $tenantId, string $moduleKey): string
    {
        return "tenant:{$tenantId}:module:{$moduleKey}:active";
    }

    protected function forgetCache(int $tenantId, string $moduleKey): void
    {
        Cache::forget($this->cacheKey($tenantId, $moduleKey));
    }
}
