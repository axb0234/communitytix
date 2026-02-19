<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureGoverning
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = app()->bound('current_tenant') ? app('current_tenant') : null;

        if (!$tenant) {
            abort(404);
        }

        if (!$tenant->tenant_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'This organisation account has been deactivated. Please contact support.');
        }

        $user = auth()->user();

        if (!$user || !$user->isGoverningIn($tenant->id)) {
            abort(403, 'Access denied. Governing role required.');
        }

        return $next($request);
    }
}
