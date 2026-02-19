<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ $tenant->name ?? 'CommunityTix' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 420px; width: 100%; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <i class="fas fa-ticket-alt fa-2x text-primary mb-2"></i>
                    <h4 class="fw-bold">{{ $tenant->name ?? 'CommunityTix' }}</h4>
                    <p class="text-muted">Sign in to your account</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required autofocus value="{{ old('email') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-sign-in-alt me-2"></i>Sign In</button>
                </form>
            </div>
        </div>
        <p class="text-center mt-3 small text-muted">
            @if(isset($tenant))<a href="{{ route('home') }}">Back to {{ $tenant->name }}</a>@endif
        </p>
    </div>
</body>
</html>
