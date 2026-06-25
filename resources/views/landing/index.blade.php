<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puskesmas Sancta Vita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sv-green: #1a6b3c; --sv-gold: #b8963e; }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d3320 0%, #1a6b3c 60%, #145530 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 24px;
        }

        .logo-wrap {
            text-align: center;
            margin-bottom: 36px;
            animation: fadeDown .5s ease both;
        }
        .logo-icon {
            width: 80px; height: 80px;
            background: rgba(255,255,255,.12);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            border: 2px solid rgba(255,255,255,.25);
        }
        .logo-icon i { font-size: 2.5rem; color: #fff; }
        .logo-title  { font-size: 1rem; color: rgba(255,255,255,.7); letter-spacing: 3px; text-transform: uppercase; }
        .logo-name   { font-size: 2rem; font-weight: 800; color: #fff; line-height: 1.1; }
        .logo-gold   { color: var(--sv-gold); }

        .card-choice {
            background: rgba(255,255,255,.97);
            border-radius: 20px;
            padding: 36px 28px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
            animation: fadeUp .5s ease .1s both;
        }
        .card-choice h6 {
            text-align: center;
            color: #555;
            margin-bottom: 24px;
            font-size: .85rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .btn-choice {
            display: flex;
            align-items: center;
            gap: 18px;
            padding: 20px 24px;
            border-radius: 14px;
            border: 2px solid transparent;
            text-decoration: none;
            transition: all .2s;
            margin-bottom: 14px;
            cursor: pointer;
            width: 100%;
        }
        .btn-choice:last-child { margin-bottom: 0; }

        .btn-pasien {
            background: linear-gradient(135deg, #1a6b3c, #22884e);
            color: #fff;
        }
        .btn-pasien:hover {
            background: linear-gradient(135deg, #145530, #1a6b3c);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26,107,60,.4);
        }

        .btn-admin {
            background: #fff;
            color: #1a3d25;
            border-color: #d1e8da;
        }
        .btn-admin:hover {
            background: #f0f9f3;
            border-color: #1a6b3c;
            color: #1a6b3c;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,.08);
        }

        .choice-icon {
            width: 52px; height: 52px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .btn-pasien .choice-icon { background: rgba(255,255,255,.2); }
        .btn-admin  .choice-icon { background: #e8f5ed; }
        .btn-admin  .choice-icon i { color: #1a6b3c; }

        .choice-icon i { font-size: 1.6rem; color: #fff; }

        .choice-label { font-weight: 700; font-size: 1.05rem; line-height: 1.2; }
        .choice-sub   { font-size: .78rem; opacity: .75; margin-top: 2px; }
        .btn-admin .choice-sub { color: #555; }

        .divider {
            text-align: center;
            color: #aaa;
            font-size: .78rem;
            margin: 6px 0 14px;
            position: relative;
        }
        .divider::before, .divider::after {
            content: '';
            position: absolute;
            top: 50%; width: 38%; height: 1px;
            background: #e0e0e0;
        }
        .divider::before { left: 0; }
        .divider::after  { right: 0; }

        .footer-text {
            color: rgba(255,255,255,.45);
            font-size: .75rem;
            margin-top: 24px;
            text-align: center;
            animation: fadeUp .5s ease .2s both;
        }

        @keyframes fadeDown { from { opacity:0; transform:translateY(-20px) } to { opacity:1; transform:none } }
        @keyframes fadeUp   { from { opacity:0; transform:translateY(20px)  } to { opacity:1; transform:none } }
    </style>
</head>
<body>

<div class="logo-wrap">
    <div class="logo-icon"><i class="bi bi-hospital-fill"></i></div>
    <div class="logo-title">Sistem Antrian Digital</div>
    <div class="logo-name">Puskesmas <span class="logo-gold">Sancta Vita</span></div>
</div>

<div class="card-choice">
    <h6>Silakan pilih akses</h6>

    {{-- Tombol Pasien --}}
    <a href="{{ route('pasien.daftar') }}" class="btn-choice btn-pasien">
        <div class="choice-icon"><i class="bi bi-person-fill"></i></div>
        <div>
            <div class="choice-label">Daftar Antrian</div>
            <div class="choice-sub">Pasien baru & registrasi antrian</div>
        </div>
        <i class="bi bi-chevron-right ms-auto"></i>
    </a>

    <div class="divider">atau</div>

    {{-- Tombol Admin --}}
    <a href="{{ route('login') }}" class="btn-choice btn-admin">
        <div class="choice-icon"><i class="bi bi-shield-lock-fill"></i></div>
        <div>
            <div class="choice-label">Admin / Petugas</div>
            <div class="choice-sub">Kelola antrian & panel admin</div>
        </div>
        <i class="bi bi-chevron-right ms-auto" style="color:#1a6b3c;"></i>
    </a>
</div>

<div class="footer-text">
    Daftar antrian langsung dari HP atau komputer
</div>

</body>
</html>
