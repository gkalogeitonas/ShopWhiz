<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create an admin user
        $admin = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@shopwhiz.com',
            'password' => bcrypt('password'),
        ]);

        // Create a demo tenant
        $tenant = \App\Models\Tenant::create([
            'name' => 'Demo Shop',
            'user_id' => $admin->id,
            'meilisearch_index' => 'products_demo_shop',
            'vector_namespace' => 'demo_shop',
            'api_token' => bin2hex(random_bytes(32)),
            'active' => true,
        ]);

        // Create a sample feed
        \App\Models\Feed::create([
            'tenant_id' => $tenant->id,
            'url' => 'https://example.com/products.xml',
            'format' => 'google_merchant',
            'sync_schedule' => 'daily',
            'next_sync_at' => now()->addDay(),
            'active' => true,
        ]);
    }
}
