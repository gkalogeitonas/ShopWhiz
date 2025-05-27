<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MerchantDashboardController extends Controller
{
    /**     * Display the merchant dashboard.
     */
    public function index()
    {
        // Get overall statistics
        $totalTenants = \App\Models\Tenant::count();
        $activeTenants = \App\Models\Tenant::where('active', true)->count();
        $totalFeeds = \App\Models\Feed::count();

        return inertia('Merchant/Dashboard', [
            'stats' => [
                'totalTenants' => $totalTenants,
                'activeTenants' => $activeTenants,
                'totalFeeds' => $totalFeeds,
            ],
        ]);
    }
}
