@extends('admin.layout')
@section('page-title', 'Member: ' . $member->full_name)

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Member Details</h5></div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><th style="width:30%">Name</th><td>{{ $member->full_name }}</td></tr>
                    <tr><th>Email</th><td>{{ $member->email }}</td></tr>
                    <tr><th>Phone</th><td>{{ $member->phone ?? '-' }}</td></tr>
                    <tr><th>Address</th><td>{{ $member->address ?? '-' }}</td></tr>
                    <tr><th>Type</th><td><span class="badge bg-info">{{ $member->member_type }}</span></td></tr>
                    <tr><th>Status</th><td><span class="badge bg-{{ $member->status === 'ACTIVE' ? 'success' : ($member->status === 'PENDING' ? 'warning' : 'danger') }}">{{ $member->status }}</span></td></tr>
                    <tr><th>Joined</th><td>{{ $member->created_at->format('M j, Y') }}</td></tr>
                    @if($member->notes)<tr><th>Notes</th><td>{{ $member->notes }}</td></tr>@endif
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Actions</h5></div>
            <div class="card-body d-flex flex-wrap gap-2">
                <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-outline-primary"><i class="fas fa-edit me-1"></i>Edit</a>

                @if($member->status === 'PENDING')
                <form method="POST" action="{{ route('admin.members.approve', $member) }}">
                    @csrf
                    <button class="btn btn-success"><i class="fas fa-check me-1"></i>Approve</button>
                </form>
                @endif

                @if($member->member_type === 'GUEST')
                <form method="POST" action="{{ route('admin.members.promote', $member) }}">
                    @csrf
                    <input type="hidden" name="member_type" value="ORDINARY">
                    <button class="btn btn-info text-white"><i class="fas fa-arrow-up me-1"></i>Promote to Ordinary</button>
                </form>
                @endif

                @if($member->member_type === 'ORDINARY')
                <form method="POST" action="{{ route('admin.members.promote', $member) }}">
                    @csrf
                    <input type="hidden" name="member_type" value="GOVERNING">
                    <button class="btn btn-warning"><i class="fas fa-arrow-up me-1"></i>Promote to Governing</button>
                </form>
                @endif

                @if($member->status === 'ACTIVE')
                <form method="POST" action="{{ route('admin.members.suspend', $member) }}" onsubmit="return confirm('Suspend this member?')">
                    @csrf
                    <button class="btn btn-danger"><i class="fas fa-ban me-1"></i>Suspend</button>
                </form>
                @endif

                @if($member->status === 'SUSPENDED')
                <form method="POST" action="{{ route('admin.members.activate', $member) }}">
                    @csrf
                    <button class="btn btn-success"><i class="fas fa-check me-1"></i>Activate</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
<a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary mt-3"><i class="fas fa-arrow-left me-1"></i>Back</a>
@endsection
