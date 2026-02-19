<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f4f6f9; min-height: 100vh; display: flex; align-items: center; justify-content: center; }</style>
</head>
<body>
    <div style="max-width:420px;width:100%;">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="fw-bold mb-3">Reset Password</h4>
                <p class="text-muted">Enter your email and we'll send you a reset link.</p>
                @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
                @error('email')<div class="alert alert-danger">{{ $message }}</div>@enderror
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                </form>
                <p class="text-center mt-3"><a href="{{ route('login') }}">Back to login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
