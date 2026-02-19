@extends('public.layout')
@section('title', 'Join Us - ' . $tenant->name)

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="fw-bold text-center mb-4">Join {{ $tenant->name }}</h2>
                        <p class="text-muted text-center mb-4">Sign up as a member. Your application will be reviewed by the committee.</p>

                        <form method="POST" action="{{ route('members.signup.store') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">First Name *</label>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" required value="{{ old('first_name') }}">
                                    @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Last Name *</label>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" required value="{{ old('last_name') }}">
                                    @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" required value="{{ old('email') }}">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required minlength="8">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-user-plus me-2"></i>Submit Application</button>
                        </form>

                        <p class="text-center mt-3 small text-muted">
                            Already a member? <a href="{{ route('login') }}">Log in here</a>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
