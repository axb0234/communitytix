<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Member;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::orderBy('last_name');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('last_name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('email', 'ilike', '%' . $request->search . '%');
            });
        }
        if ($request->filled('member_type')) {
            $query->where('member_type', $request->member_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $members = $query->paginate(20)->withQueryString();
        return view('admin.members.index', compact('members'));
    }

    public function show(Member $member)
    {
        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('admin.members.form', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $member->update($data);
        return redirect()->route('admin.members.show', $member)->with('success', 'Member updated.');
    }

    public function approve(Member $member)
    {
        $member->update(['status' => 'ACTIVE']);
        AuditLog::log('member_approved', 'Member', $member->id, [
            'email' => $member->email,
        ]);
        return redirect()->back()->with('success', 'Member approved.');
    }

    public function promote(Request $request, Member $member)
    {
        $request->validate([
            'member_type' => 'required|in:ORDINARY,GOVERNING',
        ]);

        $oldType = $member->member_type;
        $member->update(['member_type' => $request->member_type]);

        // If promoting to GOVERNING, also set up tenant_user role
        if ($request->member_type === 'GOVERNING' && $member->user_id) {
            $tenant = app('current_tenant');
            $tu = \App\Models\TenantUser::firstOrCreate(
                ['tenant_id' => $tenant->id, 'user_id' => $member->user_id],
                ['role_in_tenant' => 'TENANT_GOVERNING']
            );
            $tu->update(['role_in_tenant' => 'TENANT_GOVERNING']);
        }

        AuditLog::log('member_promoted', 'Member', $member->id, [
            'from' => $oldType,
            'to' => $request->member_type,
        ]);

        return redirect()->back()->with('success', "Member promoted to {$request->member_type}.");
    }

    public function suspend(Member $member)
    {
        $member->update(['status' => 'SUSPENDED']);
        AuditLog::log('member_suspended', 'Member', $member->id);
        return redirect()->back()->with('success', 'Member suspended.');
    }

    public function activate(Member $member)
    {
        $member->update(['status' => 'ACTIVE']);
        return redirect()->back()->with('success', 'Member activated.');
    }
}
