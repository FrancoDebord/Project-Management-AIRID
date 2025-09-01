<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Mon Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            max-width: 420px;
            width: 100%;
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, .1);
        }
    </style>
</head>

<body>
    <div class="card login-card p-4">
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock-fill text-danger fs-1"></i>
            <h4 class="fw-bold mt-2">Connexion</h4>
            <p class="text-muted small">Accédez à votre compte</p>
        </div>

        {{-- Formulaire de connexion --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="mb-3">
                <label for="email" class="form-label">Adresse email</label>
                <input id="email" type="email" name="email"
                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required
                    autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Mot de passe --}}
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input id="password" type="password" name="password"
                    class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember">Se souvenir de moi</label>
                </div>
                <a href="{{ route('password.request') }}" class="text-decoration-none small">Mot de passe oublié ?</a>
            </div>

            {{-- Bouton connexion --}}
            <div class="d-grid mb-3">
                <button type="submit" class="btn btn-danger">Se connecter</button>
            </div>

            {{-- Lien inscription --}}
            <p class="text-center small">
                Pas encore de compte ? <a href="{{ route('register') }}"
                    class="text-danger fw-semibold">Inscrivez-vous</a>
            </p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
