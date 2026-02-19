<?php

use App\Models\Tenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('tenants:check-subscriptions', function () {
    $expired = Tenant::where('tenant_active', true)
        ->whereNotNull('sub_end_date_utc')
        ->where('sub_end_date_utc', '<', now())
        ->get();

    foreach ($expired as $tenant) {
        $tenant->update(['tenant_active' => false]);
        Log::info("Tenant '{$tenant->slug}' deactivated due to expired subscription.");
        $this->info("Deactivated: {$tenant->slug}");
    }

    $this->info("Checked subscriptions. {$expired->count()} tenant(s) deactivated.");
})->purpose('Deactivate tenants with expired subscriptions');

Schedule::command('tenants:check-subscriptions')->dailyAt('02:00');
