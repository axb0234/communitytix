<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use App\Models\Tenant;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PlatformController extends Controller
{
    public function dashboard()
    {
        $tenants = Tenant::withCount('members')->orderBy('name')->get();
        return view('platform.dashboard', compact('tenants'));
    }

    public function createTenant()
    {
        return view('platform.tenant-form', ['tenant' => null]);
    }

    public function storeTenant(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:tenants,slug',
            'tagline' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string',
            'contact_email' => 'nullable|email',
            'account_type' => 'required|in:free,paid',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ]);

        $tenant = Tenant::create([
            'name' => $data['name'],
            'slug' => Str::lower($data['slug']),
            'tagline' => $data['tagline'],
            'currency' => $data['currency'],
            'timezone' => $data['timezone'],
            'contact_email' => $data['contact_email'],
            'account_type' => $data['account_type'],
            'tenant_active' => true,
        ]);

        // Create admin user
        $user = User::create([
            'name' => $data['admin_name'],
            'email' => $data['admin_email'],
            'password' => Hash::make($data['admin_password']),
        ]);

        TenantUser::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role_in_tenant' => 'TENANT_GOVERNING',
        ]);

        Member::withoutGlobalScopes()->create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'member_type' => 'GOVERNING',
            'status' => 'ACTIVE',
            'first_name' => explode(' ', $data['admin_name'])[0],
            'last_name' => explode(' ', $data['admin_name'], 2)[1] ?? '',
            'email' => $data['admin_email'],
        ]);

        AuditLog::log('tenant_created', 'Tenant', $tenant->id, ['slug' => $tenant->slug]);

        return redirect()->route('platform.dashboard')->with('success', "Tenant '{$tenant->name}' created.");
    }

    public function editTenant(Tenant $tenant)
    {
        return view('platform.tenant-form', compact('tenant'));
    }

    public function updateTenant(Request $request, Tenant $tenant)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'currency' => 'required|string|size:3',
            'timezone' => 'required|string',
            'contact_email' => 'nullable|email',
            'account_type' => 'required|in:free,paid',
            'tenant_active' => 'boolean',
            'sub_start_date_utc' => 'nullable|date',
            'sub_end_date_utc' => 'nullable|date',
        ]);

        $data['tenant_active'] = $request->boolean('tenant_active', true);
        $tenant->update($data);

        return redirect()->route('platform.dashboard')->with('success', 'Tenant updated.');
    }

    public function loginForm()
    {
        return view('platform.login');
    }
}
