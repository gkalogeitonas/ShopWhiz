<?php

use App\Models\User;
use App\Models\Tenant;
use App\Models\Feed;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can view feed index', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('merchant.feeds.index'));

    $response->assertStatus(200);
});

test('authenticated user can view feed create page', function () {
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
        ->get(route('merchant.feeds.create', ['tenant_id' => $tenant->id]));

    $response->assertStatus(200);
});

test('authenticated user can create a feed', function () {
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
        ->post(route('merchant.feeds.store'), [
            'tenant_id' => $tenant->id,
            'url' => 'https://example.com/products.xml',
            'format' => 'google_merchant',
            'sync_schedule' => 'daily',
        ]);

    $feed = Feed::first();

    $response->assertRedirect(route('merchant.feeds.show', $feed->id));
    $this->assertDatabaseHas('feeds', [
        'tenant_id' => $tenant->id,
        'url' => 'https://example.com/products.xml',
        'format' => 'google_merchant',
        'sync_schedule' => 'daily',
    ]);
});

test('feed creation requires valid data', function () {
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
        ->post(route('merchant.feeds.store'), [
            'tenant_id' => $tenant->id,
            'url' => 'invalid-url',
            'format' => 'invalid-format',
            'sync_schedule' => 'invalid-schedule',
        ]);

    $response->assertSessionHasErrors(['url', 'format', 'sync_schedule']);
});

test('authenticated user can view feed details', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    $feed = Feed::create([
        'tenant_id' => $tenant->id,
        'url' => 'https://example.com/products.xml',
        'format' => 'google_merchant',
        'sync_schedule' => 'daily',
        'next_sync_at' => now()->addDay(),
        'active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('merchant.feeds.show', $feed->id));


    $response->assertStatus(200);
    $response->assertInertia(fn ($assert) => $assert
    ->has('feed')
    ->where('feed.url', 'https://example.com/products.xml')
     ); // Check the specific feed URL in the Inertia props
});

test('authenticated user can update a feed', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    $feed = Feed::create([
        'tenant_id' => $tenant->id,
        'url' => 'https://example.com/products.xml',
        'format' => 'google_merchant',
        'sync_schedule' => 'daily',
        'next_sync_at' => now()->addDay(),
        'active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('merchant.feeds.update', $feed->id), [
            'url' => 'https://example.com/updated-products.xml',
            'format' => 'skroutz_xml',
            'sync_schedule' => 'weekly',
            'active' => false,
        ]);

    $response->assertRedirect(route('merchant.feeds.show', $feed->id));
    $this->assertDatabaseHas('feeds', [
        'id' => $feed->id,
        'url' => 'https://example.com/updated-products.xml',
        'format' => 'skroutz_xml',
        'sync_schedule' => 'weekly',
        'active' => false,
    ]);
});

test('authenticated user can delete a feed', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    $feed = Feed::create([
        'tenant_id' => $tenant->id,
        'url' => 'https://example.com/products.xml',
        'format' => 'google_merchant',
        'sync_schedule' => 'daily',
        'next_sync_at' => now()->addDay(),
        'active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->delete(route('merchant.feeds.destroy', $feed->id));

    $response->assertRedirect(route('merchant.feeds.index', ['tenant_id' => $tenant->id]));
    $this->assertDatabaseMissing('feeds', [
        'id' => $feed->id,
    ]);
});

test('authenticated user can trigger manual feed sync', function () {
    $user = User::factory()->create();
    $tenant = Tenant::create([
        'name' => 'Test Tenant',
        'user_id' => $user->id,
        'meilisearch_index' => 'products_test_tenant',
        'vector_namespace' => 'test_tenant',
        'api_token' => bin2hex(random_bytes(32)),
        'active' => true,
    ]);

    $feed = Feed::create([
        'tenant_id' => $tenant->id,
        'url' => 'https://example.com/products.xml',
        'format' => 'google_merchant',
        'sync_schedule' => 'daily',
        'next_sync_at' => now()->addDay(),
        'active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('merchant.feeds.sync', $feed->id));

    $response->assertRedirect();

    $feed->refresh();
    $this->assertNotNull($feed->last_sync_at);
    $this->assertNotNull($feed->sync_status);
});
