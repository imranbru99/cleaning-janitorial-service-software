<?php

use App\Models\Tenant;
use App\Services\Modules\ModuleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\DemoSeeder::class);
});

it('activates and deactivates modules without deleting historical data', function () {
    $tenant = Tenant::query()->where('slug', 'northline')->firstOrFail();
    $manager = app(ModuleManager::class);

    expect($manager->isActive('marketing', $tenant))->toBeTrue();

    $manager->deactivate('marketing', $tenant);
    expect($manager->isActive('marketing', $tenant))->toBeFalse();

    $manager->activate('marketing', $tenant);
    expect($manager->isActive('marketing', $tenant))->toBeTrue();
});

it('blocks deactivation of modules with active dependents', function () {
    $tenant = Tenant::query()->where('slug', 'northline')->firstOrFail();
    $manager = app(ModuleManager::class);

    $manager->deactivate('crm', $tenant);
})->throws(RuntimeException::class);

it('keeps core modules always active', function () {
    $tenant = Tenant::query()->where('slug', 'northline')->firstOrFail();
    $manager = app(ModuleManager::class);

    expect($manager->isActive('dashboard', $tenant))->toBeTrue();

    expect(fn () => $manager->deactivate('dashboard', $tenant))
        ->toThrow(RuntimeException::class);
});
