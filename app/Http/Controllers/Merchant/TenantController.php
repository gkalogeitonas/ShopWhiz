<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = \App\Models\Tenant::with('user')
            ->withCount('feeds')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return inertia('Merchant/Tenants/Index', [
            'tenants' => $tenants,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return inertia('Merchant/Tenants/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $tenant = new \App\Models\Tenant();
        $tenant->name = $validated['name'];
        $tenant->user_id = auth()->id();
        $tenant->meilisearch_index = 'products_' . strtolower(str_replace(' ', '_', $validated['name']));
        $tenant->vector_namespace = strtolower(str_replace(' ', '_', $validated['name']));
        // Set a default token value to satisfy NOT NULL constraint
        $tenant->api_token = 'initial_' . bin2hex(random_bytes(16));
        $tenant->save();

        // Generate a proper API token after saving
        $tenant->generateApiToken();

        return redirect()->route('merchant.tenants.index')->with('success', 'Tenant created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tenant = \App\Models\Tenant::with(['feeds', 'user'])->findOrFail($id);

        // Check if the tenant belongs to the authenticated user
        if ($tenant->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        return inertia('Merchant/Tenants/Show', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $tenant = \App\Models\Tenant::findOrFail($id);

        return inertia('Merchant/Tenants/Edit', [
            'tenant' => $tenant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tenant = \App\Models\Tenant::findOrFail($id);

        // Check if the tenant belongs to the authenticated user
        if ($tenant->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'boolean',
        ]);

        $tenant->update([
            'name' => $validated['name'],
            'active' => $validated['active'] ?? $tenant->active,
        ]);

        return redirect()->route('merchant.tenants.show', $tenant)->with('success', 'Tenant updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tenant = \App\Models\Tenant::findOrFail($id);

        // Check if the tenant belongs to the authenticated user
        if ($tenant->user_id !== auth()->id() && !auth()->user()->is_admin) {
            abort(403, 'Unauthorized action.');
        }

        $tenant->delete();

        return redirect()->route('merchant.tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
