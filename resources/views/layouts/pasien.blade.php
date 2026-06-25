<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Puskesmas Sancta Vita')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sv-green: #1a6b3c; --sv-gold: #b8963e; }
        body {
            min-height: 100vh;
            background: linear-gradient(160deg, #f0f7f3 0%, #e8f5ed 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .top-bar {
            background: var(--sv-green);
            border-bottom: 3px solid var(--sv-gold);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .top-bar i   { color: var(--sv-gold); font-size: 1.4rem; }
        .top-bar span { color: #fff; font-weight: 700; font-size: 1rem; }
        .top-bar small { color: rgba(255,255,255,.6); font-size: .75rem; display: block; line-height: 1; }
        .card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,.08); }
        .btn-sv       { background: var(--sv-green); color: #fff; border: none; }
        .btn-sv:hover { background: #145530; color: #fff; }
        .form-control:focus, .form-select:focus {
            border-color: var(--sv-green);
            box-shadow: 0 0 0 3px rgba(26,107,60,.12);
        }
    </style>
    @stack('styles')
</head>
<body>

<div class="top-bar">
    <i class="bi bi-hospital-fill"></i>
    <div>
        <span>Puskesmas Sancta Vita</span>
        <small>Sistem Antrian Digital</small>
    </div>
</div>

<div class="container py-4">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
