<?php

namespace App\Traits;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function($model) {
            // If tenant_id is already set, respect it
            if($model->tenant_id) {
                return;
            }

            // Try to get tenant_id from session
            if(session()->has('tenant_id')) {
                $model->tenant_id = session()->get('tenant_id');
                return;
            }

            // If user is authenticated, use their tenant
            if(Auth::check()) {
                $user = Auth::user();
                $tenant = $user->tenant;

                if($tenant) {
                    $model->tenant_id = $tenant->id;
                    session(['tenant_id' => $tenant->id]);
                }
            }
        });
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
