<?php

use App\Models\Customer;
use App\Models\Tenant;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\DemoSeeder::class);
});

it('scopes customers to the current tenant', function () {
    $tenantA = Tenant::query()->where('slug', 'northline')->firstOrFail();
    $tenantB = Tenant::query()->where('slug', 'brighthome')->firstOrFail();

    Tenancy::set($tenantA);
    $countA = Customer::query()->count();

    Tenancy::set($tenantB);
    $countB = Customer::query()->count();

    expect($countA)->toBeGreaterThan(0)
        ->and($countB)->toBeGreaterThan(0);

    $idsA = Tenancy::run($tenantA, fn () => Customer::query()->pluck('id'));
    $idsB = Tenancy::run($tenantB, fn () => Customer::query()->pluck('id'));

    expect($idsA->intersect($idsB))->toBeEmpty();
});

it('prevents cross-tenant customer visibility through the API', function () {
    $owner = User::query()->where('email', 'owner@cleaningdemo.test')->firstOrFail();
    $token = $owner->createToken('test')->plainTextToken;

    Tenancy::set($owner->tenant);
    $foreign = Customer::withoutGlobalScopes()
        ->where('tenant_id', '!=', $owner->tenant_id)
        ->firstOrFail();

    $this->withToken($token)
        ->getJson('/api/v1/customers/'.$foreign->id)
        ->assertNotFound();
});
