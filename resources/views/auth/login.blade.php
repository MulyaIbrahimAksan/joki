<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Puskesmas Sancta Vita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root { --sv-green: #1a6b3c; --sv-gold: #b8963e; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d3320 0%, #1a6b3c 60%, #145530 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            padding: 24px;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 40px 36px;
            width: 100%; max-width: 400px;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
            animation: fadeUp .4s ease both;
        }
        .login-header { text-align: center; margin-bottom: 28px; }
        .login-header .icon-wrap {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #1a6b3c, #22884e);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 14px;
        }
        .login-header .icon-wrap i { font-size: 1.8rem; color: #fff; }
        .login-header h5 { font-weight: 700; color: #1a3d25; margin: 0; }
        .login-header p  { color: #888; font-size: .83rem; margin: 4px 0 0; }

        .form-label { font-weight: 600; color: #333; font-size: .88rem; }
        .form-control {
            border-radius: 10px; border: 1.5px solid #ddd;
            padding: 10px 14px; font-size: .93rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: var(--sv-green);
            box-shadow: 0 0 0 3px rgba(26,107,60,.12);
        }
        .input-icon { position: relative; }
        .input-icon i {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            color: #aaa; font-size: 1rem; pointer-events: none;
        }
        .input-icon .form-control { padding-left: 38px; }

        .btn-login {
            background: linear-gradient(135deg, #1a6b3c, #22884e);
            color: #fff; border: none; border-radius: 10px;
            padding: 12px; font-weight: 700; font-size: 1rem;
            width: 100%; transition: all .2s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #145530, #1a6b3c);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(26,107,60,.35);
        }
        .back-link {
            display: flex; align-items: center; justify-content: center; gap: 6px;
            color: rgba(255,255,255,.7); font-size: .82rem; text-decoration: none;
            margin-top: 20px; transition: color .2s;
        }
        .back-link:hover { color: #fff; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(20px) } to { opacity:1; transform:none } }
    </style>
</head>
<body>

<div>
    <div class="login-card">
        <div class="login-header">
            <div class="icon-wrap"><i class="bi bi-shield-lock-fill"></i></div>
            <h5>Login Admin</h5>
            <p>Puskesmas Sancta Vita</p>
        </div>

        @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3" style="border-radius:10px; font-size:.85rem;">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Email atau password salah.
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Email</label>
                <div class="input-icon">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="admin@sanctavita.com"
                           required autofocus autocomplete="email">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-icon">
                    <i class="bi bi-lock"></i>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
            </button>
        </form>
    </div>

    <a href="{{ route('landing') }}" class="back-link">
        <i class="bi bi-arrow-left"></i> Kembali ke halaman utama
    </a>
</div>

</body>
</html>
