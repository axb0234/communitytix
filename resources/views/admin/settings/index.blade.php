@extends('admin.layout')
@section('page-title', 'Settings')

@section('content')
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-building me-2"></i>Organisation Settings</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.tenant.update') }}" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Organisation Name *</label>
                        <input type="text" name="name" class="form-control" required value="{{ $tenant->name }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tagline</label>
                        <input type="text" name="tagline" class="form-control" value="{{ $tenant->tagline }}">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Currency *</label>
                            <select name="currency" class="form-select">
                                @foreach(['GBP','USD','EUR','INR','CAD','AUD'] as $c)
                                <option value="{{ $c }}" {{ $tenant->currency === $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Timezone *</label>
                            <input type="text" name="timezone" class="form-control" required value="{{ $tenant->timezone }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="{{ $tenant->contact_email }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo_file" class="form-control" accept="image/*">
                        @if($tenant->logo_path)<img src="{{ route('storage.local', $tenant->logo_path) }}" class="mt-2" style="max-height:60px;">@endif
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Settings</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fab fa-paypal me-2"></i>PayPal Settings</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.settings.paypal.update') }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Mode *</label>
                        <select name="mode" class="form-select">
                            <option value="sandbox" {{ ($paypalSetting?->mode ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                            <option value="live" {{ ($paypalSetting?->mode ?? '') === 'live' ? 'selected' : '' }}>Live</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Client ID *</label>
                        <input type="text" name="client_id" class="form-control" required value="{{ $paypalSetting ? '****' . substr($paypalSetting->client_id_decrypted, -8) : '' }}" placeholder="Enter PayPal Client ID">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Client Secret *</label>
                        <input type="password" name="client_secret" class="form-control" required value="" placeholder="Enter PayPal Client Secret">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Webhook ID</label>
                        <input type="text" name="webhook_id" class="form-control" value="{{ $paypalSetting?->webhook_id }}">
                        <small class="text-muted">Create a webhook in PayPal Developer Dashboard pointing to: <code>{{ url('/webhook/paypal/' . $tenant->slug) }}</code></small>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save PayPal Settings</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
