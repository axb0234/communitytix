<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;

class ResolveTenant
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $baseDomain = config('app.domain', 'communitytix.org');

        // Extract subdomain
        $slug = null;
        if ($host !== $baseDomain && str_ends_with($host, '.' . $baseDomain)) {
            $slug = str_replace('.' . $baseDomain, '', $host);
        }

        if (!$slug || $slug === 'www') {
            // Main domain - no tenant context (platform level)
            return $next($request);
        }

        $tenant = Tenant::where('slug', $slug)->first();

        if (!$tenant) {
            abort(404, 'Organisation not found.');
        }

        app()->instance('current_tenant', $tenant);
        view()->share('tenant', $tenant);

        return $next($request);
    }
}
