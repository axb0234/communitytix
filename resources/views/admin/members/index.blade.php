@extends('admin.layout')
@section('page-title', 'Members')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form class="d-flex gap-2" method="GET">
        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
        <select name="member_type" class="form-select form-select-sm" style="width:130px;" onchange="this.form.submit()">
            <option value="">All Types</option>
            <option value="GUEST" {{ request('member_type') === 'GUEST' ? 'selected' : '' }}>Guest</option>
            <option value="ORDINARY" {{ request('member_type') === 'ORDINARY' ? 'selected' : '' }}>Ordinary</option>
            <option value="GOVERNING" {{ request('member_type') === 'GOVERNING' ? 'selected' : '' }}>Governing</option>
        </select>
        <select name="status" class="form-select form-select-sm" style="width:130px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
            <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
            <option value="SUSPENDED" {{ request('status') === 'SUSPENDED' ? 'selected' : '' }}>Suspended</option>
        </select>
        <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td><a href="{{ route('admin.members.show', $member) }}">{{ $member->full_name }}</a></td>
                    <td>{{ $member->email }}</td>
                    <td><span class="badge bg-info">{{ $member->member_type }}</span></td>
                    <td>
                        <span class="badge bg-{{ $member->status === 'ACTIVE' ? 'success' : ($member->status === 'PENDING' ? 'warning' : 'danger') }}">
                            {{ $member->status }}
                        </span>
                    </td>
                    <td class="d-flex gap-1">
                        @if($member->status === 'PENDING')
                        <form method="POST" action="{{ route('admin.members.approve', $member) }}">
                            @csrf
                            <button class="btn btn-sm btn-success" title="Approve"><i class="fas fa-check"></i></button>
                        </form>
                        @endif
                        <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-3">No members found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $members->links() }}</div>
@endsection
