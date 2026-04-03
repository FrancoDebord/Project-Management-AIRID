<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Project Tracking Sheet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 380px;
        }

        .login-card .brand {
            text-align: center;
            margin-bottom: 1.8rem;
        }

        .login-card .brand-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1a3a6b;
            margin-top: .5rem;
            margin-bottom: .15rem;
        }

        .login-card .brand-sub {
            font-size: .75rem;
            color: #9ca3af;
        }

        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #e5e7eb;
            padding: 9px 12px;
            font-size: .88rem;
        }

        .form-control:focus {
            border-color: #1a3a6b;
            box-shadow: 0 0 0 3px rgba(26,58,107,.1);
        }

        .toggle-password {
            cursor: pointer;
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 0 12px;
            color: #9ca3af;
        }

        .toggle-password:hover { color: #1a3a6b; }

        .input-group .form-control { border-radius: 8px 0 0 8px; border-right: none; }
        .input-group .form-control:focus { box-shadow: none; border-color: #1a3a6b; }
        .input-group:focus-within .toggle-password { border-color: #1a3a6b; }

        .btn-login {
            background: linear-gradient(90deg, #1a3a6b, #c41230);
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: .9rem;
            font-weight: 600;
            color: #fff;
            width: 100%;
            transition: opacity .2s;
        }

        .btn-login:hover { opacity: .9; color: #fff; }

        .forgot-link {
            font-size: .75rem;
            color: #6b7280;
            text-decoration: none;
        }

        .forgot-link:hover { color: #1a3a6b; text-decoration: underline; }
    </style>
</head>
<body>

    <div class="login-card">

        <div class="brand">
            <img src="{{ asset('storage/assets/logo/airid.jpg') }}"
                 alt="AIRID" style="height:48px;object-fit:contain;"
                 onerror="this.style.display='none'">
            <div class="brand-title">Project Tracking Sheet</div>
            <div class="brand-sub">AIRID — African Institute for Research in Infectious Diseases</div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3" style="border-radius:8px;font-size:.82rem;">
            <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       placeholder="votre@email.com"
                       required autofocus autocomplete="email">
                @error('email')
                    <div class="invalid-feedback" style="font-size:.78rem;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">Mot de passe</label>
                    <a href="{{ route('password.request') }}" class="forgot-link">Oublié ?</a>
                </div>
                <div class="input-group">
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="••••••••"
                           required autocomplete="current-password">
                    <button type="button" class="toggle-password" onclick="togglePwd()" tabindex="-1">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback" style="font-size:.78rem;">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember" style="font-size:.82rem;color:#6b7280;">
                    Se souvenir de moi
                </label>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
            </button>
        </form>

    </div>

    <script>
        function togglePwd() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('toggleIcon');
            input.type  = input.type === 'password' ? 'text' : 'password';
            icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        }
    </script>
</body>
</html>
