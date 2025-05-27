<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // Get overall statistics
        $totalTenants = \App\Models\Tenant::count();
        $activeTenants = \App\Models\Tenant::where('active', true)->count();
        $totalFeeds = \App\Models\Feed::count();

        return inertia('Admin/Dashboard', [
            'stats' => [
                'totalTenants' => $totalTenants,
                'activeTenants' => $activeTenants,
                'totalFeeds' => $totalFeeds,
            ],
        ]);
    }
}
