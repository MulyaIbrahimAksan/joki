<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Puskesmas Sancta Vita')</title>

    {{-- CDN langsung — tidak perlu npm run build --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root { --sv-green: #1a6b3c; --sv-gold: #b8963e; }

        body { background: #f4f7f5; font-family: 'Segoe UI', sans-serif; }

        /* Navbar */
        .navbar-sv { background: var(--sv-green); border-bottom: 3px solid var(--sv-gold); }
        .navbar-sv .navbar-brand { color: #fff; font-weight: 700; }
        .navbar-sv .navbar-brand span { color: var(--sv-gold); }

        /* Sidebar */
        .sidebar {
            width: 220px;
            min-height: calc(100vh - 57px);
            background: #fff;
            border-right: 1px solid #e0ede5;
            position: sticky; top: 57px;
            flex-shrink: 0;
        }
        .sidebar .nav-link {
            color: #444; border-radius: 8px;
            margin: 2px 10px; padding: 9px 14px;
            font-size: .88rem; display: flex; align-items: center; gap: 8px;
        }
        .sidebar .nav-link:hover  { background: #eaf3ed; color: var(--sv-green); }
        .sidebar .nav-link.active { background: #d4eddb; color: var(--sv-green); font-weight: 600; }
        .sidebar .nav-link i { width: 18px; font-size: 1rem; }
        .sidebar-label {
            font-size: .68rem; text-transform: uppercase; letter-spacing: 1.5px;
            color: #aaa; padding: 10px 24px 4px; font-weight: 600;
        }

        /* Main content */
        .main-content { flex: 1; padding: 28px; min-width: 0; }

        /* Cards */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,.07); }
        .card-header.sv { background: var(--sv-green); color: #fff; border-radius: 12px 12px 0 0 !important; }

        /* Buttons */
        .btn-sv       { background: var(--sv-green); color: #fff; border: none; }
        .btn-sv:hover { background: #145530; color: #fff; }

        /* Status badges */
        .badge-menunggu  { background: #fff3cd; color: #856404; }
        .badge-dipanggil { background: #cff4fc; color: #055160; }
        .badge-dilayani  { background: #cfe2ff; color: #084298; }
        .badge-selesai   { background: #d1e7dd; color: #0a3622; }
        .badge-batal     { background: #f8d7da; color: #842029; }

        /* Table */
        .table th { font-size: .8rem; text-transform: uppercase; letter-spacing: .5px; color: #666; }
        .table td { vertical-align: middle; }
    </style>
    @stack('styles')
</head>
<body>

{{-- Navbar --}}
<nav class="navbar navbar-sv py-2 px-3 sticky-top">
    <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('admin.dashboard') }}">
        <i class="bi bi-hospital-fill fs-5" style="color:var(--sv-gold);"></i>
        Puskesmas <span class="ms-1">Sancta Vita</span>
    </a>
    @auth
    <div class="d-flex align-items-center gap-3">
        <span class="text-white-50 small d-none d-md-inline">
            <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
        </span>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button class="btn btn-sm btn-outline-light px-3">
                <i class="bi bi-box-arrow-right me-1"></i>Keluar
            </button>
        </form>
    </div>
    @endauth
</nav>

@auth
<div class="d-flex" style="min-height: calc(100vh - 57px);">

    {{-- Sidebar --}}
    <aside class="sidebar d-none d-md-flex flex-column py-3">
        <div class="sidebar-label">Menu Utama</div>
        <nav>
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
               href="{{ route('admin.dashboard') }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.antrian.index') ? 'active' : '' }}"
               href="{{ route('admin.antrian.index') }}">
                <i class="bi bi-list-ol"></i> Antrian
            </a>
            <a class="nav-link" href="{{ route('display') }}" target="_blank">
                <i class="bi bi-display"></i> Display TV
                <i class="bi bi-box-arrow-up-right ms-auto small opacity-50"></i>
            </a>
        </nav>

        <div class="sidebar-label mt-2">Pengaturan</div>
        <nav>
            <a class="nav-link {{ request()->routeIs('admin.poli.*') ? 'active' : '' }}"
               href="{{ route('admin.poli.index') }}">
                <i class="bi bi-building"></i> Kelola Poli
            </a>
            <a class="nav-link {{ request()->routeIs('admin.dokter.*') ? 'active' : '' }}"
               href="{{ route('admin.dokter.index') }}">
                <i class="bi bi-person-badge"></i> Kelola Dokter
            </a>
        </nav>
    </aside>

    {{-- Content --}}
    <main class="main-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 mb-3" role="alert" style="border-radius:10px; font-size:.88rem;">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2 mb-3" role="alert" style="border-radius:10px; font-size:.88rem;">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @yield('content')
    </main>
</div>
@else
<div class="container py-4">
    @yield('content')
</div>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
