<?php

use App\Models\User;
use App\Models\Tenant;
use App\Models\Feed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);





test('unauthenticated user cannot access merchant dashboard', function () {
    $response = $this->get(route('merchant.dashboard'));

    $response->assertRedirect(route('login'));
});

test('merchant dashboard uses correct component', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('merchant.dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($assert) => $assert
        ->component('Merchant/Dashboard')
    );
});


test('merchant dashboard works with inertia', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('merchant.dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($assert) => $assert
        ->component('Merchant/Dashboard')
        ->has('stats')
    );
});
