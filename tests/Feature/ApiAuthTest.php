<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(\Database\Seeders\DemoSeeder::class);
});

it('issues sanctum tokens for valid demo credentials', function () {
    $response = $this->postJson('/api/v1/login', [
        'email' => 'owner@cleaningdemo.test',
        'password' => 'Demo@12345',
        'device_name' => 'pest',
    ]);

    $response->assertOk()->assertJsonStructure(['token', 'user']);
});

it('rejects invalid credentials', function () {
    $this->postJson('/api/v1/login', [
        'email' => 'owner@cleaningdemo.test',
        'password' => 'wrong-password',
    ])->assertStatus(422);
});

it('returns the authenticated user profile', function () {
    $user = User::query()->where('email', 'owner@cleaningdemo.test')->firstOrFail();
    $token = $user->createToken('pest')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('email', 'owner@cleaningdemo.test');
});
