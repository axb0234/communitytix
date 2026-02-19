<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PayPalSetting;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function index()
    {
        $tenant = app('current_tenant');
        $paypalSetting = PayPalSetting::where('tenant_id', $tenant->id)->first();
        return view('admin.settings.index', compact('tenant', 'paypalSetting'));
    }

    public function updateTenant(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string|max:100',
            'contact_email' => 'nullable|email|max:255',
            'logo_file' => 'nullable|image|max:2048',
        ]);

        $tenant = app('current_tenant');

        if ($request->hasFile('logo_file')) {
            $name = Str::uuid() . '.' . $request->file('logo_file')->getClientOriginalExtension();
            $path = "uploads/{$tenant->slug}/logo";
            $request->file('logo_file')->move(storage_path("app/public/{$path}"), $name);
            $data['logo_path'] = "{$path}/{$name}";
        }
        unset($data['logo_file']);

        $tenant->update($data);
        AuditLog::log('tenant_settings_updated');

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated.');
    }

    public function updatePaypal(Request $request)
    {
        $data = $request->validate([
            'mode' => 'required|in:sandbox,live',
            'client_id' => 'required|string|max:500',
            'client_secret' => 'required|string|max:500',
            'webhook_id' => 'nullable|string|max:255',
        ]);

        $tenant = app('current_tenant');

        PayPalSetting::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'mode' => $data['mode'],
                'client_id_enc' => $data['client_id'],
                'client_secret_enc' => $data['client_secret'],
                'webhook_id' => $data['webhook_id'],
            ]
        );

        AuditLog::log('paypal_settings_updated');
        return redirect()->route('admin.settings.index')->with('success', 'PayPal settings updated.');
    }
}
