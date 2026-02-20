<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $tenant->name ?? 'CommunityTix')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,400;0,600;0,700;1,400&family=Noto+Sans+Bengali:wght@400;600;700&family=Noto+Sans+Devanagari:wght@400;600;700&family=Noto+Sans+SC:wght@400;700&family=Noto+Sans+JP:wght@400;700&family=Noto+Sans+KR:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #18bc9c;
            --accent: #e74c3c;
        }
        body { font-family: 'Noto Sans', 'Noto Sans Bengali', 'Noto Sans Devanagari', 'Noto Sans SC', 'Noto Sans JP', 'Noto Sans KR', sans-serif; }
        .navbar-custom {
            background: var(--primary);
            padding: 1rem 0;
        }
        .navbar-custom .navbar-brand {
            color: #fff;
            font-weight: 700;
            font-size: 1.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 70vw;
        }
        .navbar-custom .nav-link {
            color: rgba(255,255,255,0.85);
            font-weight: 500;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        .navbar-custom .nav-link:hover, .navbar-custom .nav-link.active {
            color: var(--secondary);
        }
        .hero-section {
            background: linear-gradient(135deg, var(--primary) 0%, #1a252f 100%);
            color: #fff;
            padding: 4rem 0;
        }
        .section-heading {
            font-size: 2rem;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }
        .section-subheading {
            font-size: 1.1rem;
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card-hover { transition: all 0.3s ease; }
        .btn-primary { background: var(--secondary); border-color: var(--secondary); }
        .btn-primary:hover { background: #15a085; border-color: #15a085; }
        .btn-accent { background: var(--accent); border-color: var(--accent); color: #fff; }
        .btn-accent:hover { background: #c0392b; border-color: #c0392b; color: #fff; }
        footer {
            background: var(--primary);
            color: rgba(255,255,255,0.7);
            padding: 2rem 0;
        }
        footer a { color: var(--secondary); }
        .carousel-item img { height: 500px; object-fit: cover; }
        @media (max-width: 768px) {
            .carousel-item img { height: 300px; }
            .hero-section { padding: 2.5rem 0; }
            .hero-section .display-4 { font-size: 1.75rem; }
            .section-heading { font-size: 1.5rem; }
        }
        @media (max-width: 576px) {
            .navbar-custom .navbar-brand { font-size: 1.1rem; max-width: 65vw; }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                @if($tenant->logo_path ?? false)
                    <img src="{{ route('storage.local', $tenant->logo_path) }}" alt="{{ $tenant->name }}" height="40" class="me-2">
                @endif
                {{ $tenant->name ?? 'CommunityTix' }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('events.index') }}">Events</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('blog.index') }}">Blog</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('members.signup') }}">Join Us</a></li>
                    @auth
                        @if(auth()->user()->isGoverningIn($tenant->id ?? 0))
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="fas fa-cog"></i> Admin</a></li>
                        @endif
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="container mt-3"><div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
    @endif
    @if(session('error'))
        <div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
    @endif
    @if(session('info'))
        <div class="container mt-3"><div class="alert alert-info alert-dismissible fade show">{{ session('info') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
    @endif

    @yield('content')

    {{-- Footer --}}
    <footer class="mt-5">
        <div class="container text-center">
            <p>&copy; {{ date('Y') }} {{ $tenant->name ?? 'CommunityTix' }}. Powered by <a href="https://communitytix.org">CommunityTix</a>.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
