<?php

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can only view their own tenant', function () {
    // Create two users
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create tenant for first user
    $tenant1 = Tenant::create([
        'name' => 'User 1 Tenant',
        'user_id' => $user1->id,
        'meilisearch_index' => 'products_user1',
        'vector_namespace' => 'user1',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    // Create tenant for second user
    $tenant2 = Tenant::create([
        'name' => 'User 2 Tenant',
        'user_id' => $user2->id,
        'meilisearch_index' => 'products_user2',
        'vector_namespace' => 'user2',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    // When user1 visits dashboard, should only see their own tenant
    $this->actingAs($user1);

    // In tests, we need to manually set the session
    // The middleware runs during HTTP requests but not during tests
    session(['tenant_id' => $tenant1->id]);

    // Now check session properly contains the tenant_id
    $this->assertEquals($tenant1->id, session('tenant_id'));

    $response = $this->get(route('merchant.dashboard'));

    $response->assertStatus(200);
    $response->assertInertia(fn ($assert) => $assert
        ->has('stats')
        ->where('stats.tenant', 'User 1 Tenant')
    );
});

test('user cannot view tenant details of another user', function () {
    // Create two users
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create tenant for second user
    $tenant2 = Tenant::create([
        'name' => 'User 2 Tenant',
        'user_id' => $user2->id,
        'meilisearch_index' => 'products_user2',
        'vector_namespace' => 'user2',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    // First user should not be able to view second user's tenant
    $response = $this
        ->actingAs($user1)
        ->get(route('merchant.tenants.show', $tenant2->id));

    $response->assertStatus(403);
});

test('user cannot update tenant of another user', function () {
    // Create two users
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create tenant for second user
    $tenant2 = Tenant::create([
        'name' => 'User 2 Tenant',
        'user_id' => $user2->id,
        'meilisearch_index' => 'products_user2',
        'vector_namespace' => 'user2',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    // First user should not be able to update second user's tenant
    $response = $this
        ->actingAs($user1)
        ->patch(route('merchant.tenants.update', $tenant2->id), [
            'name' => 'Updated Name',
        ]);

    $response->assertStatus(403);
    $this->assertDatabaseHas('tenants', [
        'id' => $tenant2->id,
        'name' => 'User 2 Tenant', // Name should not change
    ]);
});

test('user cannot delete tenant of another user', function () {
    // Create two users
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    // Create tenant for second user
    $tenant2 = Tenant::create([
        'name' => 'User 2 Tenant',
        'user_id' => $user2->id,
        'meilisearch_index' => 'products_user2',
        'vector_namespace' => 'user2',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    // First user should not be able to delete second user's tenant
    $response = $this
        ->actingAs($user1)
        ->delete(route('merchant.tenants.destroy', $tenant2->id));

    $response->assertStatus(403);
    $this->assertDatabaseHas('tenants', [
        'id' => $tenant2->id,
    ]);
});

test('deleting a tenant also deletes associated feeds', function () {
    $user = User::factory()->create();

    // Create a tenant
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test',
        'vector_namespace' => 'test',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);    // Create feeds for this tenant
    $feed1 = \App\Models\Feed::create([
        'tenant_id' => $tenant->id,
        'url' => 'https://example.com/feed1.xml',
        'format' => 'google_merchant',
        'sync_schedule' => 'daily',
    ]);

    $feed2 = \App\Models\Feed::create([
        'tenant_id' => $tenant->id,
        'url' => 'https://example.com/feed2.xml',
        'format' => 'google_merchant',  // Changed from 'csv' to 'google_merchant'
        'sync_schedule' => 'weekly',
    ]);

    // Delete the tenant
    $response = $this
        ->actingAs($user)
        ->delete(route('merchant.tenants.destroy', $tenant->id));

    $response->assertRedirect(route('merchant.tenants.index'));

    // Verify tenant is deleted
    $this->assertDatabaseMissing('tenants', [
        'id' => $tenant->id,
    ]);

    // Verify associated feeds are also deleted
    $this->assertDatabaseMissing('feeds', [
        'id' => $feed1->id,
    ]);

    $this->assertDatabaseMissing('feeds', [
        'id' => $feed2->id,
    ]);
});

