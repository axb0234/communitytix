<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'tenant' => \App\Http\Middleware\ResolveTenant::class,
            'tenant.exists' => \App\Http\Middleware\EnsureTenantExists::class,
            'governing' => \App\Http\Middleware\EnsureGoverning::class,
            'platform.admin' => \App\Http\Middleware\EnsurePlatformAdmin::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\ResolveTenant::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'webhook/paypal/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
