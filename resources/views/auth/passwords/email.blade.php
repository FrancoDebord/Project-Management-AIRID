<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mot de passe oublié — AIRID</title>
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
        .auth-card .brand { text-align:center; margin-bottom:1.8rem; }
        .brand-title { font-size:1.05rem; font-weight:700; color:#1a3a6b; margin-top:.5rem; margin-bottom:.15rem; }
        .brand-sub { font-size:.75rem; color:#9ca3af; }
        .page-heading {
            font-size:1rem;
            font-weight:700;
            color:#1a3a6b;
            margin-bottom:.3rem;
        }
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
        .back-link { font-size:.78rem; color:#6b7280; text-decoration:none; }
        .back-link:hover { color:#1a3a6b; text-decoration:underline; }
        .divider { border-top:1px solid #f3f4f6; margin:1.5rem 0; }
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

    <div class="page-heading"><i class="bi bi-envelope-open me-2" style="color:#c41230;"></i>Mot de passe oublié</div>
    <div class="page-desc">
        Saisissez votre adresse email. Nous vous enverrons un lien pour réinitialiser votre mot de passe.
    </div>

    @if(session('status'))
    <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 mb-3" style="border-radius:8px;font-size:.82rem;">
        <i class="bi bi-check-circle-fill flex-shrink-0 text-success"></i>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 mb-3" style="border-radius:8px;font-size:.82rem;">
        <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
        <span>{{ $errors->first() }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label">Adresse email</label>
            <input id="email" type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="votre@email.com"
                   required autofocus autocomplete="email">
            @error('email')
                <div class="invalid-feedback" style="font-size:.78rem;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-airid">
            <i class="bi bi-send me-1"></i>Envoyer le lien de réinitialisation
        </button>
    </form>

    <div class="divider"></div>

    <div class="text-center">
        <a href="{{ route('login') }}" class="back-link">
            <i class="bi bi-arrow-left me-1"></i>Retour à la connexion
        </a>
    </div>

</div>
</body>
</html>
