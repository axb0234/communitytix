<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\PayPalSetting;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create platform admin
        $adminEmail = env('PLATFORM_ADMIN_EMAIL', 'anirban@aspirtek.com');
        $adminPassword = env('PLATFORM_ADMIN_PASSWORD', 'Welcome1');

        $platformAdmin = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => 'Platform Admin',
                'password' => Hash::make($adminPassword),
                'platform_role' => 'PLATFORM_ADMIN',
            ]
        );

        // Create first tenant: Moitree
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'moitree'],
            [
                'name' => 'Moitree – Bengali Association of Bristol',
                'tagline' => 'Connecting Bengali culture in Bristol',
                'currency' => 'GBP',
                'timezone' => 'Europe/London',
                'account_type' => 'free',
                'tenant_active' => true,
                'contact_email' => $adminEmail,
            ]
        );

        // Make platform admin also governing for Moitree
        TenantUser::firstOrCreate(
            ['tenant_id' => $tenant->id, 'user_id' => $platformAdmin->id],
            ['role_in_tenant' => 'TENANT_GOVERNING']
        );

        Member::withoutGlobalScopes()->firstOrCreate(
            ['tenant_id' => $tenant->id, 'email' => $adminEmail],
            [
                'user_id' => $platformAdmin->id,
                'member_type' => 'GOVERNING',
                'status' => 'ACTIVE',
                'first_name' => 'Platform',
                'last_name' => 'Admin',
            ]
        );

        // Set up PayPal sandbox for Moitree
        $paypalClientId = env('PAYPAL_CLIENT_ID');
        $paypalSecret = env('PAYPAL_SECRET_KEY');

        if ($paypalClientId && $paypalSecret) {
            PayPalSetting::updateOrCreate(
                ['tenant_id' => $tenant->id],
                [
                    'mode' => 'sandbox',
                    'client_id_enc' => $paypalClientId,
                    'client_secret_enc' => $paypalSecret,
                ]
            );
        }
    }
}
