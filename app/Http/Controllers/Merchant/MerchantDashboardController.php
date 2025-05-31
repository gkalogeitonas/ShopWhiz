<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Feed;

class MerchantDashboardController extends Controller
{
    /**
     * Display the merchant dashboard.
     */
    public function index()
    {
        // Get the current user's tenant
        $tenant = auth()->user()->tenant;

        // If no tenant exists yet for this user, stats are all zero
        if (!$tenant) {
            return inertia('Merchant/Dashboard', [
                'stats' => [
                    'tenant' => null,
                    'feeds' => 0,
                    'activeFeed' => 0,
                    'lastSync' => null,
                ],
                'recentSyncs' => [],
            ]);
        }

        // Get statistics for the current tenant
        $feedCount = Feed::where('tenant_id', $tenant->id)->count();
        $activeFeedCount = Feed::where('tenant_id', $tenant->id)
            ->where('active', true)
            ->count();

        // Get recent syncs
        $recentSyncs = Feed::where('tenant_id', $tenant->id)
            ->whereNotNull('last_sync_at')
            ->orderBy('last_sync_at', 'desc')
            ->limit(5)
            ->get();

        $lastSyncAt = Feed::where('tenant_id', $tenant->id)
            ->whereNotNull('last_sync_at')
            ->max('last_sync_at');

        return inertia('Merchant/Dashboard', [
            'stats' => [
                'tenant' => $tenant->name,
                'feeds' => $feedCount,
                'activeFeeds' => $activeFeedCount,
                'lastSync' => $lastSyncAt,
            ],
            'recentSyncs' => $recentSyncs,
        ]);
    }
}
