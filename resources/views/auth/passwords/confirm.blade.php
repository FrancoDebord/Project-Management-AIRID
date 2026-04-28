<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmer le mot de passe — AIRID</title>
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
        .auth-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,.08);
            padding: 2.5rem 2rem;
            width: 100%;
            max-width: 400px;
        }
        .brand { text-align:center; margin-bottom:1.8rem; }
        .brand-title { font-size:1.05rem; font-weight:700; color:#1a3a6b; margin-top:.5rem; margin-bottom:.15rem; }
        .brand-sub { font-size:.75rem; color:#9ca3af; }
        .page-heading { font-size:1rem; font-weight:700; color:#1a3a6b; margin-bottom:.3rem; }
        .page-desc { font-size:.82rem; color:#6b7280; margin-bottom:1.5rem; }
        .form-label { font-size:.8rem; font-weight:600; color:#374151; margin-bottom:4px; }
        .form-control {
            border-radius:8px;
            border:1.5px solid #e5e7eb;
            padding:9px 12px;
            font-size:.88rem;
        }
        .form-control:focus {
            border-color:#1a3a6b;
            box-shadow:0 0 0 3px rgba(26,58,107,.1);
        }
        .toggle-password {
            cursor:pointer;
            background:#f9fafb;
            border:1.5px solid #e5e7eb;
            border-left:none;
            border-radius:0 8px 8px 0;
            padding:0 12px;
            color:#9ca3af;
        }
        .toggle-password:hover { color:#1a3a6b; }
        .input-group .form-control { border-radius:8px 0 0 8px; border-right:none; }
        .input-group .form-control:focus { box-shadow:none; border-color:#1a3a6b; }
        .input-group:focus-within .toggle-password { border-color:#1a3a6b; }
        .btn-airid {
            background:linear-gradient(90deg,#1a3a6b,#c41230);
            border:none;
            border-radius:8px;
            padding:10px;
            font-size:.9rem;
            font-weight:600;
            color:#fff;
            width:100%;
            transition:opacity .2s;
        }
        .btn-airid:hover { opacity:.9; color:#fff; }
        .divider { border-top:1px solid #f3f4f6; margin:1.5rem 0; }
        .link-muted { font-size:.78rem; color:#6b7280; text-decoration:none; }
        .link-muted:hover { color:#1a3a6b; text-decoration:underline; }
        .info-box {
            background:#f0f5ff;
            border:1px solid #d0dff5;
            border-radius:8px;
            padding:.75rem 1rem;
            font-size:.82rem;
            color:#374151;
            margin-bottom:1.4rem;
            display:flex;
            align-items:flex-start;
            gap:.5rem;
        }
        .info-box i { color:#1a3a6b; margin-top:.1rem; flex-shrink:0; }
    </style>
</head>
<body>
<div class="auth-card">

    <div class="brand">
        <img src="{{ asset('storage/assets/logo/airid.jpg') }}"
             alt="AIRID" style="height:48px;object-fit:contain;"
             onerror="this.style.display='none'">
        <div class="brand-title">AIRID — Project Management</div>
        <div class="brand-sub">African Institute for Research in Infectious Diseases</div>
    </div>

    <div class="page-heading"><i class="bi bi-lock-fill me-2" style="color:#c41230;"></i>Confirmer votre identité</div>

    <div class="info-box">
        <i class="bi bi-info-circle-fill"></i>
        <span>Cette zone est protégée. Veuillez confirmer votre mot de passe avant de continuer.</span>
    </div>

    @if($errors->any())
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3" style="border-radius:8px;font-size:.82rem;">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div class="mb-4">
            <label for="password" class="form-label">Mot de passe actuel</label>
            <div class="input-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       required autocomplete="current-password" autofocus>
                <button type="button" class="toggle-password" onclick="togglePwd()" tabindex="-1">
                    <i class="bi bi-eye" id="toggleIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback" style="font-size:.78rem;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn-airid">
            <i class="bi bi-shield-check me-1"></i>Confirmer et continuer
        </button>
    </form>

    @if(Route::has('password.request'))
    <div class="divider"></div>
    <div class="text-center">
        <a href="{{ route('password.request') }}" class="link-muted">
            <i class="bi bi-key me-1"></i>Mot de passe oublié ?
        </a>
    </div>
    @endif

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
