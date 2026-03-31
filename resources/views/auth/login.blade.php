<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign In — Project Tracking Sheet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: #f0f2f5;
        }

        /* ── Left panel ──────────────────────────────── */
        .left-panel {
            display: none;
            flex: 1;
            background: linear-gradient(160deg, #1a3a6b 0%, #c41230 100%);
            position: relative;
            overflow: hidden;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 60px 50px;
            color: #fff;
        }

        @media (min-width: 992px) {
            .left-panel { display: flex; }
        }

        .left-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 20% 20%, rgba(255,255,255,.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,.06) 0%, transparent 50%);
        }

        .left-panel .brand-logo {
            width: 220px;
            max-width: 80%;
            margin-bottom: 48px;
            position: relative;
            filter: brightness(0) invert(1);
        }

        .left-panel .tagline {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 16px;
            position: relative;
            text-align: center;
        }

        .left-panel .sub-tagline {
            font-size: .95rem;
            opacity: .75;
            text-align: center;
            max-width: 360px;
            position: relative;
        }

        .left-panel .feature-list {
            margin-top: 40px;
            list-style: none;
            padding: 0;
            position: relative;
        }

        .left-panel .feature-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: .9rem;
            opacity: .85;
            margin-bottom: 14px;
        }

        .left-panel .feature-list li i {
            font-size: 1.1rem;
            opacity: .9;
            flex-shrink: 0;
        }

        /* Floating circles decoration */
        .deco-circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
        }
        .deco-circle.c1 { width:320px; height:320px; bottom:-80px; right:-80px; }
        .deco-circle.c2 { width:180px; height:180px; top: 40px; right: 60px; }
        .deco-circle.c3 { width: 90px; height: 90px; top:200px; left: 30px; }

        /* ── Right panel ─────────────────────────────── */
        .right-panel {
            width: 100%;
            max-width: 480px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
            background: #fff;
        }

        @media (min-width: 992px) {
            .right-panel { min-height: 100vh; }
        }

        .login-box { width: 100%; }

        .login-box .top-logo {
            display: block;
            margin: 0 auto 32px;
            width: 160px;
        }

        .login-box h2 {
            font-size: 1.55rem;
            font-weight: 700;
            color: #1a3a6b;
            margin-bottom: 4px;
        }

        .login-box .subtitle {
            font-size: .875rem;
            color: #6c757d;
            margin-bottom: 32px;
        }

        .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 8px;
            border: 1.5px solid #dee2e6;
            padding: 10px 14px;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-control:focus {
            border-color: #1a3a6b;
            box-shadow: 0 0 0 3px rgba(26,58,107,.12);
        }

        .input-group-text {
            background: #f8f9fa;
            border: 1.5px solid #dee2e6;
            border-radius: 8px 0 0 8px;
            color: #6c757d;
        }

        .input-group .form-control {
            border-radius: 0 8px 8px 0;
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: none;
            box-shadow: none;
        }

        .btn-login {
            background: linear-gradient(90deg, #1a3a6b, #c41230);
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-size: .95rem;
            font-weight: 600;
            letter-spacing: .3px;
            color: #fff;
            width: 100%;
            transition: opacity .2s, transform .1s;
        }

        .btn-login:hover  { opacity: .92; color: #fff; }
        .btn-login:active { transform: scale(.98); }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #adb5bd;
            font-size: .78rem;
            margin: 20px 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e9ecef;
        }

        .toggle-password {
            cursor: pointer;
            border: 1.5px solid #dee2e6;
            border-left: none;
            background: #f8f9fa;
            border-radius: 0 8px 8px 0;
            padding: 0 14px;
            color: #6c757d;
            transition: color .2s;
        }
        .toggle-password:hover { color: #1a3a6b; }

        .footer-links {
            text-align: center;
            font-size: .8rem;
            color: #6c757d;
            margin-top: 28px;
        }
        .footer-links a {
            color: #1a3a6b;
            text-decoration: none;
            font-weight: 600;
        }
        .footer-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

    {{-- ── Left decorative panel ───────────────────── --}}
    <div class="left-panel">
        <div class="deco-circle c1"></div>
        <div class="deco-circle c2"></div>
        <div class="deco-circle c3"></div>

        <img src="{{ asset('storage/assets/logo/airid.jpg') }}"
             alt="AIRID" class="brand-logo"
             onerror="this.style.display='none'">

        <div class="tagline">Project Tracking Sheet</div>
        <p class="sub-tagline">
            Integrated platform for study tracking, quality assurance and archiving.
        </p>

        <ul class="feature-list">
            <li><i class="bi bi-kanban-fill"></i> Full project lifecycle management</li>
            <li><i class="bi bi-clipboard2-check-fill"></i> QA inspections & non-conformity tracking</li>
            <li><i class="bi bi-file-earmark-pdf-fill"></i> GLP controlled document generation</li>
            <li><i class="bi bi-shield-lock-fill"></i> Secure role-based access control</li>
        </ul>
    </div>

    {{-- ── Right login panel ───────────────────────── --}}
    <div class="right-panel">
        <div class="login-box">

            {{-- Mobile-only logo --}}
            <img src="{{ asset('storage/assets/logo/airid.jpg') }}"
                 alt="AIRID" class="top-logo d-lg-none"
                 onerror="this.style.display='none'">

            <h2>Welcome back</h2>
            <p class="subtitle">Sign in to access your workspace.</p>

            {{-- Error alert --}}
            @if($errors->any())
            <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-4" style="border-radius:8px;font-size:.85rem;">
                <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input id="email" type="email" name="email"
                               class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}"
                               placeholder="you@crec-lshtm.org"
                               required autofocus autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="password" class="form-label mb-0">Password</label>
                        <a href="{{ route('password.request') }}"
                           class="text-decoration-none" style="font-size:.78rem;color:#1a3a6b;">
                            Forgot password?
                        </a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input id="password" type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••"
                               required autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePwd()" tabindex="-1">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:.85rem;">
                        Remember me
                    </label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign in
                </button>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePwd() {
            const input = document.getElementById('password');
            const icon  = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
