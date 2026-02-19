<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - {{ $tenant->name ?? 'CommunityTix' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; --primary-dark: #343a40; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; }
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--primary-dark);
            color: #c2c7d0;
            overflow-y: auto;
            z-index: 1000;
            transition: margin-left 0.3s;
        }
        .sidebar .brand {
            padding: 1rem;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-decoration: none;
            display: block;
        }
        .sidebar .nav-section {
            padding: 0.75rem 1rem 0.25rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0.5px;
        }
        .sidebar .nav-link {
            color: #c2c7d0;
            padding: 0.6rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            text-decoration: none;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar .nav-link i { width: 20px; text-align: center; }
        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .top-navbar {
            background: #fff;
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .content-body { padding: 1.5rem; }
        .card { border: none; box-shadow: 0 0 15px rgba(0,0,0,0.05); margin-bottom: 1.5rem; }
        .stat-card { text-align: center; padding: 1.5rem; }
        .stat-card .stat-number { font-size: 2rem; font-weight: 700; }
        .stat-card .stat-label { color: #6c757d; font-size: 0.85rem; text-transform: uppercase; }
        .table-responsive-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        @media (max-width: 768px) {
            .sidebar { margin-left: calc(-1 * var(--sidebar-width)); }
            .sidebar.show { margin-left: 0; }
            .content-wrapper { margin-left: 0; }
            .content-body { padding: 1rem; }
            .stat-card { padding: 1rem; }
            .stat-card .stat-number { font-size: 1.5rem; }
            .top-navbar { padding: 0.5rem 1rem; }
        }
        @media (max-width: 576px) {
            .filter-bar { flex-wrap: wrap !important; }
            .filter-bar .form-control, .filter-bar .form-select { min-width: 0; }
        }
    </style>
    @stack('styles')
    @hasSection('wysiwyg')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.css" rel="stylesheet">
    <style>
        .note-editor.note-frame { border: 1px solid #dee2e6; border-radius: 0.375rem; }
        .note-editor .note-toolbar { background: #f8f9fa; border-bottom: 1px solid #dee2e6; border-radius: 0.375rem 0.375rem 0 0; }
    </style>
    @endif
</head>
<body>
    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <a href="{{ route('admin.dashboard') }}" class="brand">
            <i class="fas fa-ticket-alt me-2"></i>{{ $tenant->name ?? 'Admin' }}
        </a>
        <nav>
            <div class="nav-section">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>

            <div class="nav-section">Content</div>
            <a href="{{ route('admin.home-content.index') }}" class="nav-link {{ request()->routeIs('admin.home-content.*') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Home Page
            </a>
            <a href="{{ route('admin.blog.index') }}" class="nav-link {{ request()->routeIs('admin.blog.*') ? 'active' : '' }}">
                <i class="fas fa-blog"></i> Blog Posts
            </a>

            <div class="nav-section">Events & Tickets</div>
            <a href="{{ route('admin.events.index') }}" class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Events
            </a>
            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.index', 'admin.orders.show') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>
            <a href="{{ route('admin.orders.rsvps') }}" class="nav-link {{ request()->routeIs('admin.orders.rsvps') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> RSVPs
            </a>
            <a href="{{ route('admin.orders.cash') }}" class="nav-link {{ request()->routeIs('admin.orders.cash') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i> Cash Collections
            </a>
            <a href="{{ route('admin.orders.pos') }}" class="nav-link {{ request()->routeIs('admin.orders.pos') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i> Card at Door
            </a>

            <div class="nav-section">People</div>
            <a href="{{ route('admin.members.index') }}" class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Members
            </a>

            <div class="nav-section">Settings</div>
            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <i class="fas fa-cogs"></i> Settings
            </a>

            <div class="nav-section mt-3"></div>
            <a href="{{ route('home') }}" class="nav-link" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
        </nav>
    </aside>

    {{-- Content --}}
    <div class="content-wrapper">
        <header class="top-navbar">
            <div>
                <button class="btn btn-sm btn-outline-secondary d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="ms-2 fw-bold">@yield('page-title', 'Dashboard')</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-sign-out-alt"></i></button>
                </form>
            </div>
        </header>

        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @hasSection('wysiwyg')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs5.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.wysiwyg-editor').forEach(function(el) {
                $(el).summernote({
                    height: 250,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'hr']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    styleTags: ['p', 'h2', 'h3', 'h4', 'h5', 'h6'],
                    callbacks: {
                        onImageUpload: function(files) {
                            alert('Please use the image upload fields or host images externally. Inline image paste is not supported.');
                        }
                    }
                });
            });
        });
    </script>
    @endif
    @stack('scripts')
</body>
</html>
