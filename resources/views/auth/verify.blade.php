<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vérification de l'email — AIRID</title>
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
            text-align: center;
        }
        .brand { margin-bottom:1.8rem; }
        .brand-title { font-size:1.05rem; font-weight:700; color:#1a3a6b; margin-top:.5rem; margin-bottom:.15rem; }
        .brand-sub { font-size:.75rem; color:#9ca3af; }
        .icon-wrap {
            width:72px; height:72px;
            border-radius:50%;
            background:linear-gradient(135deg,#1a3a6b,#c41230);
            display:flex; align-items:center; justify-content:center;
            margin:0 auto 1.5rem;
        }
        .icon-wrap i { font-size:2rem; color:#fff; }
        .page-heading { font-size:1.1rem; font-weight:700; color:#1a3a6b; margin-bottom:.5rem; }
        .page-desc { font-size:.85rem; color:#6b7280; margin-bottom:1.5rem; line-height:1.6; }
        .btn-airid {
            background:linear-gradient(90deg,#1a3a6b,#c41230);
            border:none;
            border-radius:8px;
            padding:10px 24px;
            font-size:.9rem;
            font-weight:600;
            color:#fff;
            transition:opacity .2s;
        }
        .btn-airid:hover { opacity:.9; color:#fff; }
        .divider { border-top:1px solid #f3f4f6; margin:1.5rem 0; }
        .link-muted { font-size:.78rem; color:#6b7280; text-decoration:none; }
        .link-muted:hover { color:#1a3a6b; text-decoration:underline; }
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

    <div class="icon-wrap">
        <i class="bi bi-envelope-check"></i>
    </div>

    <div class="page-heading">Vérifiez votre adresse email</div>
    <div class="page-desc">
        Un lien de vérification a été envoyé à votre adresse email.<br>
        Cliquez sur ce lien pour activer votre compte et accéder à la plateforme.
    </div>

    @if(session('resent'))
    <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 mb-3" style="border-radius:8px;font-size:.82rem;text-align:left;">
        <i class="bi bi-check-circle-fill flex-shrink-0 text-success"></i>
        <span>Un nouveau lien de vérification a été envoyé à votre adresse email.</span>
    </div>
    @endif

    <form method="POST" action="{{ route('verification.resend') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn-airid btn">
            <i class="bi bi-arrow-repeat me-1"></i>Renvoyer le lien de vérification
        </button>
    </form>

    <div class="divider"></div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="link-muted btn btn-link p-0">
            <i class="bi bi-box-arrow-right me-1"></i>Se déconnecter
        </button>
    </form>

</div>
</body>
</html>
