<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $key = 'login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Too many login attempts. Please try again in {$seconds} seconds.");
        }

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = auth()->user();
            $tenant = app()->bound('current_tenant') ? app('current_tenant') : null;

            // Check if user has access to this tenant's admin
            if ($tenant) {
                if (!$tenant->tenant_active && $user->isGoverningIn($tenant->id)) {
                    Auth::logout();
                    return back()->with('error', 'This organisation account has been deactivated.');
                }

                if ($user->isGoverningIn($tenant->id)) {
                    AuditLog::log('admin_login', 'User', $user->id);
                    return redirect()->intended(route('admin.dashboard'));
                }
            }

            // Platform admin login (on main domain)
            if (!$tenant && $user->isPlatformAdmin()) {
                return redirect()->intended(route('platform.dashboard'));
            }

            return redirect()->intended('/');
        }

        RateLimiter::hit($key, 300);
        return back()->with('error', 'Invalid credentials.')->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
