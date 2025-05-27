<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    /**
     * Generate a new API token for a tenant.
     */
    public function generate(Request $request, string $id)
    {
        $tenant = \App\Models\Tenant::findOrFail($id);
        $token = $tenant->generateApiToken();

        return back()->with('success', 'API token generated successfully.');
    }

    /**
     * Revoke an API token from a tenant.
     */
    public function revoke(Request $request, string $id)
    {
        $tenant = \App\Models\Tenant::findOrFail($id);
        $tenant->update(['api_token' => null]);

        return back()->with('success', 'API token revoked successfully.');
    }
}
