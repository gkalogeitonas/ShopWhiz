<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FeedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenantId = $request->input('tenant_id');

        $query = \App\Models\Feed::query()
            ->with('tenant')
            ->latest();

        if ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }

        $feeds = $query->paginate(10);

        return inertia('Merchant/Feeds/Index', [
            'feeds' => $feeds,
            'tenantId' => $tenantId,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tenantId = $request->input('tenant_id');
        $tenant = null;

        if ($tenantId) {
            $tenant = \App\Models\Tenant::findOrFail($tenantId);
        } else {
            // If no tenant ID provided, get all tenants for dropdown
            $tenants = \App\Models\Tenant::all();
        }

        return inertia('Merchant/Feeds/Create', [
            'tenant' => $tenant ?? null,
            'tenants' => isset($tenants) ? $tenants : null,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'url' => 'required|url',
            'format' => 'required|in:google_merchant,skroutz_xml,json',
            'sync_schedule' => 'required|in:hourly,daily,weekly',
        ]);

        $feed = \App\Models\Feed::create($validated);

        // Calculate next sync time based on schedule
        $feed->next_sync_at = $feed->calculateNextSync();
        $feed->save();

        return redirect()->route('merchant.feeds.show', $feed)->with('success', 'Feed created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $feed = \App\Models\Feed::with('tenant')->findOrFail($id);

        return inertia('Merchant/Feeds/Show', [
            'feed' => $feed,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $feed = \App\Models\Feed::with('tenant')->findOrFail($id);

        return inertia('Merchant/Feeds/Edit', [
            'feed' => $feed,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $feed = \App\Models\Feed::findOrFail($id);

        $validated = $request->validate([
            'url' => 'required|url',
            'format' => 'required|in:google_merchant,skroutz_xml,json',
            'sync_schedule' => 'required|in:hourly,daily,weekly',
            'active' => 'boolean',
        ]);

        $feed->update($validated);

        // Recalculate next sync time if schedule changed
        if ($request->input('sync_schedule') !== $feed->sync_schedule) {
            $feed->next_sync_at = $feed->calculateNextSync();
            $feed->save();
        }

        return redirect()->route('merchant.feeds.show', $feed)->with('success', 'Feed updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $feed = \App\Models\Feed::findOrFail($id);
        $tenantId = $feed->tenant_id;
        $feed->delete();

        return redirect()->route('merchant.feeds.index', ['tenant_id' => $tenantId])->with('success', 'Feed deleted successfully.');
    }

    /**
     * Trigger manual feed synchronization.
     */
    public function sync(string $id)
    {
        $feed = \App\Models\Feed::findOrFail($id);

        // In a real implementation, we would queue a job to sync the feed
        // For now, we'll just update the status
        $feed->update([
            'last_sync_at' => now(),
            'next_sync_at' => $feed->calculateNextSync(),
            'sync_status' => ['status' => 'queued', 'message' => 'Feed sync queued'],
        ]);

        return back()->with('success', 'Feed sync initiated.');
    }
}
