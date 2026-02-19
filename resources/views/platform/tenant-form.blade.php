@extends('platform.layout')
@section('title', $tenant ? 'Edit Tenant' : 'New Tenant')

@section('content')
<h2 class="mb-4">{{ $tenant ? 'Edit Tenant: ' . $tenant->name : 'Create New Tenant' }}</h2>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ $tenant ? route('platform.tenants.update', $tenant) : route('platform.tenants.store') }}">
            @csrf
            @if($tenant) @method('PUT') @endif

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name', $tenant->name ?? '') }}">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Slug * <small class="text-muted">(subdomain)</small></label>
                    @if($tenant)
                    <input type="text" class="form-control" value="{{ $tenant->slug }}" disabled>
                    @else
                    <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" required value="{{ old('slug') }}" placeholder="e.g. moitree">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Tagline</label>
                <input type="text" name="tagline" class="form-control" value="{{ old('tagline', $tenant->tagline ?? '') }}">
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Currency *</label>
                    <select name="currency" class="form-select">
                        @foreach(['GBP','USD','EUR','INR','CAD','AUD'] as $c)
                        <option value="{{ $c }}" {{ old('currency', $tenant->currency ?? 'GBP') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Timezone *</label>
                    <input type="text" name="timezone" class="form-control" required value="{{ old('timezone', $tenant->timezone ?? 'Europe/London') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Account Type *</label>
                    <select name="account_type" class="form-select">
                        <option value="free" {{ old('account_type', $tenant->account_type ?? 'free') === 'free' ? 'selected' : '' }}>Free</option>
                        <option value="paid" {{ old('account_type', $tenant->account_type ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $tenant->contact_email ?? '') }}">
            </div>

            @if($tenant)
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-check mt-2">
                        <input type="hidden" name="tenant_active" value="0">
                        <input type="checkbox" name="tenant_active" value="1" class="form-check-input" {{ old('tenant_active', $tenant->tenant_active) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Subscription Start</label>
                    <input type="date" name="sub_start_date_utc" class="form-control" value="{{ old('sub_start_date_utc', $tenant->sub_start_date_utc?->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Subscription End</label>
                    <input type="date" name="sub_end_date_utc" class="form-control" value="{{ old('sub_end_date_utc', $tenant->sub_end_date_utc?->format('Y-m-d')) }}">
                </div>
            </div>
            @else
            <hr>
            <h5>Initial Admin User</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Admin Name *</label>
                    <input type="text" name="admin_name" class="form-control @error('admin_name') is-invalid @enderror" required value="{{ old('admin_name') }}">
                    @error('admin_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Admin Email *</label>
                    <input type="email" name="admin_email" class="form-control @error('admin_email') is-invalid @enderror" required value="{{ old('admin_email') }}">
                    @error('admin_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Admin Password *</label>
                    <input type="password" name="admin_password" class="form-control @error('admin_password') is-invalid @enderror" required>
                    @error('admin_password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            @endif

            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>{{ $tenant ? 'Update' : 'Create Tenant' }}</button>
            <a href="{{ route('platform.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
