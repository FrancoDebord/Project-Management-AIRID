<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réinitialiser le mot de passe — AIRID</title>
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
            max-width: 420px;
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
        .password-hint { font-size:.74rem; color:#9ca3af; margin-top:.3rem; }
        .strength-bar { height:4px; border-radius:2px; margin-top:6px; background:#e5e7eb; overflow:hidden; }
        .strength-fill { height:100%; border-radius:2px; transition:width .3s, background .3s; width:0; }
        .divider { border-top:1px solid #f3f4f6; margin:1.5rem 0; }
        .back-link { font-size:.78rem; color:#6b7280; text-decoration:none; }
        .back-link:hover { color:#1a3a6b; text-decoration:underline; }
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

    <div class="page-heading"><i class="bi bi-shield-lock me-2" style="color:#c41230;"></i>Nouveau mot de passe</div>
    <div class="page-desc">Choisissez un mot de passe fort d'au moins 8 caractères.</div>

    @if($errors->any())
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3" style="border-radius:8px;font-size:.82rem;">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="mb-3">
            <label for="email" class="form-label">Adresse email</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ $email ?? old('email') }}"
                   placeholder="votre@email.com"
                   required autocomplete="email" autofocus>
            @error('email')
                <div class="invalid-feedback" style="font-size:.78rem;">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nouveau mot de passe</label>
            <div class="input-group">
                <input id="password" type="password" name="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="••••••••"
                       required autocomplete="new-password"
                       oninput="checkStrength(this.value)">
                <button type="button" class="toggle-password" onclick="togglePwd('password','icon1')" tabindex="-1">
                    <i class="bi bi-eye" id="icon1"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback" style="font-size:.78rem;">{{ $message }}</div>
                @enderror
            </div>
            <div class="strength-bar"><div class="strength-fill" id="strengthBar"></div></div>
            <div class="password-hint" id="strengthLabel">Saisissez votre nouveau mot de passe</div>
        </div>

        <div class="mb-4">
            <label for="password-confirm" class="form-label">Confirmer le mot de passe</label>
            <div class="input-group">
                <input id="password-confirm" type="password" name="password_confirmation"
                       class="form-control"
                       placeholder="••••••••"
                       required autocomplete="new-password">
                <button type="button" class="toggle-password" onclick="togglePwd('password-confirm','icon2')" tabindex="-1">
                    <i class="bi bi-eye" id="icon2"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-airid">
            <i class="bi bi-check2-circle me-1"></i>Réinitialiser le mot de passe
        </button>
    </form>

    <div class="divider"></div>
    <div class="text-center">
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left me-1"></i>Retour à la connexion
        </a>
    </div>

</div>

<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    input.type  = input.type === 'password' ? 'text' : 'password';
    icon.className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}

function checkStrength(val) {
    const bar   = document.getElementById('strengthBar');
    const label = document.getElementById('strengthLabel');
    let score = 0;
    if (val.length >= 8)  score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;
    const levels = [
        { pct:'15%', bg:'#dc3545', text:'Trop faible' },
        { pct:'35%', bg:'#fd7e14', text:'Faible' },
        { pct:'65%', bg:'#ffc107', text:'Moyen' },
        { pct:'85%', bg:'#20c997', text:'Bon' },
        { pct:'100%',bg:'#198754', text:'Fort' },
    ];
    const lvl = levels[score] || levels[0];
    bar.style.width  = val.length ? lvl.pct : '0';
    bar.style.background = lvl.bg;
    label.textContent = val.length ? lvl.text : 'Saisissez votre nouveau mot de passe';
    label.style.color = val.length ? lvl.bg : '#9ca3af';
}
</script>
</body>
</html>
