<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        // First try to get tenant_id from session
        if(session()->has('tenant_id')) {
            $builder->where('tenant_id', session()->get('tenant_id'));
            return;
        }

        // If not in session but user is logged in, get their tenant
        if(Auth::check()) {
            $user = Auth::user();
            $tenant = $user->tenant;

            if($tenant) {
                // Store in session for future queries
                session(['tenant_id' => $tenant->id]);
                $builder->where('tenant_id', $tenant->id);
            }
        }
    }
}
