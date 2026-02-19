<?php

namespace App\Http\Controllers\PublicSite;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\TenantUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function showSignupForm()
    {
        return view('public.members.signup');
    }

    public function signup(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Global email uniqueness
        if (User::where('email', $data['email'])->exists()) {
            return back()->with('error', 'This email is already registered.')->withInput();
        }

        $tenant = app('current_tenant');

        // Create user
        $user = User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Create tenant_user
        TenantUser::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'role_in_tenant' => 'MEMBER',
        ]);

        // Create member record (PENDING approval)
        Member::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'member_type' => 'ORDINARY',
            'status' => 'PENDING',
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        return redirect()->route('home')->with('success', 'Your membership application has been submitted! You will be notified once approved.');
    }
}
