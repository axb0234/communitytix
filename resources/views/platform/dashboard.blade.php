@extends('platform.layout')
@section('title', 'Platform Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-server me-2"></i>Tenants</h2>
    <a href="{{ route('platform.tenants.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>New Tenant</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Slug</th><th>Members</th><th>Currency</th><th>Active</th><th>Subscription</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($tenants as $tenant)
                <tr>
                    <td><strong>{{ $tenant->name }}</strong></td>
                    <td><code>{{ $tenant->slug }}.communitytix.org</code></td>
                    <td>{{ $tenant->members_count }}</td>
                    <td>{{ $tenant->currency }}</td>
                    <td><span class="badge bg-{{ $tenant->tenant_active ? 'success' : 'danger' }}">{{ $tenant->tenant_active ? 'Active' : 'Inactive' }}</span></td>
                    <td>{{ $tenant->sub_end_date_utc?->format('M j, Y') ?? 'N/A' }}</td>
                    <td><a href="{{ route('platform.tenants.edit', $tenant) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a></td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-3">No tenants yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
