@extends('platform.layout')
@section('title', 'Purge Tenant: ' . $tenant->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <a href="{{ route('platform.dashboard') }}" class="btn btn-outline-secondary mb-3"><i class="fas fa-arrow-left me-1"></i>Back to Dashboard</a>

        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Purge Tenant Data</h5>
            </div>
            <div class="card-body">
                <p class="fw-bold">You are about to purge all data for:</p>
                <p class="fs-5"><strong>{{ $tenant->name }}</strong> (<code>{{ $tenant->slug }}.communitytix.org</code>)</p>

                <div class="alert alert-warning">
                    <strong>This will permanently delete:</strong>
                    <ul class="mb-0 mt-2">
                        <li>All events, ticket types, and event images</li>
                        <li>All orders and order items</li>
                        <li>All RSVPs</li>
                        <li>All cash collections and POS payments</li>
                        <li>All blog posts</li>
                        <li>All members (except the governing admin)</li>
                        <li>All carousel items and content blocks</li>
                        <li>PayPal settings</li>
                        <li>All audit logs</li>
                        <li>All uploaded files</li>
                    </ul>
                </div>

                <p class="text-danger fw-bold">This action cannot be undone.</p>

                <form method="POST" action="{{ route('platform.tenants.purge.execute', $tenant) }}">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label class="form-label">Type <strong>{{ $tenant->slug }}</strong> to confirm:</label>
                        <input type="text" name="confirm_slug" class="form-control" required autocomplete="off" placeholder="Enter tenant slug">
                    </div>
                    <button type="submit" class="btn btn-danger w-100"><i class="fas fa-trash me-2"></i>Purge All Tenant Data</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
