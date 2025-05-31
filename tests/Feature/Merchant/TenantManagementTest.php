<?php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);



test('authenticated user can view tenant index', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('merchant.tenants.index'));

    $response->assertStatus(200);
});

test('authenticated user can create a tenant', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('merchant.tenants.store'), [
            'name' => 'Test Tenant',
        ]);

    $response->assertRedirect(route('merchant.tenants.index'));
    $this->assertDatabaseHas('tenants', [
        'name' => 'Test Tenant',
        'user_id' => $user->id,
    ]);
});

test('tenant requires a name', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('merchant.tenants.store'), [
            'name' => '',
        ]);

    $response->assertSessionHasErrors('name');
});

test('authenticated user can view tenant details', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('merchant.tenants.show', $tenant->id));

    $response->assertStatus(200);
    $response->assertSee('Test Tenant');
});

test('authenticated user can update a tenant', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('merchant.tenants.update', $tenant->id), [
            'name' => 'Updated Tenant',
            'active' => false,
        ]);

    $response->assertRedirect(route('merchant.tenants.show', $tenant->id));
    $this->assertDatabaseHas('tenants', [
        'id' => $tenant->id,
        'name' => 'Updated Tenant',
        'active' => false,
    ]);
});

test('authenticated user can delete a tenant', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('merchant.tenants.destroy', $tenant->id));

    $response->assertRedirect(route('merchant.tenants.index'));
    $this->assertDatabaseMissing('tenants', [
        'id' => $tenant->id,
    ]);
});

test('user can generate an api token for a tenant', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => 'initial_' . bin2hex(random_bytes(16)), // Set an initial token to satisfy NOT NULL constraint
        'active' => true,
    ]);

    $initialToken = $tenant->api_token;

    $response = $this
        ->actingAs($user)
        ->post(route('merchant.tenants.token.generate', $tenant->id));

    $tenant->refresh();
    $response->assertRedirect();
    $this->assertNotNull($tenant->api_token);
    $this->assertNotEquals($initialToken, $tenant->api_token);
});

test('user can revoke an api token for a tenant', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    $originalToken = $tenant->api_token;

    $response = $this
        ->actingAs($user)
        ->delete(route('merchant.tenants.token.revoke', $tenant->id));

    $tenant->refresh();
    $response->assertRedirect();

    // Instead of checking for null, verify the token has changed and contains 'revoked_'
    $this->assertNotEquals($originalToken, $tenant->api_token);
    $this->assertStringContainsString('revoked_', $tenant->api_token);
});
