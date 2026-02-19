<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantExists
{
    public function handle(Request $request, Closure $next)
    {
        if (!app()->bound('current_tenant')) {
            abort(404, 'Organisation not found.');
        }

        return $next($request);
    }
}
