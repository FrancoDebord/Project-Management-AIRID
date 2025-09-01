{{-- resources/views/home.blade.php --}}
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accueil - Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

     <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- Thème Bootstrap 5 pour Select2 -->
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />


    <link rel="stylesheet" href="{{ asset('storage/assets/datatable/datatables.min.css') }}">

    <link rel="stylesheet" href="{{ asset('storage/assets/gigo-master/css/gijgo.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/assets/fileinput/css/fileinput.min.css') }}">

    {{-- <link rel="stylesheet" href="{{ asset('storage/assets/js/bootstrap-select/css/bootstrap-select.min.css') }}"> --}}

    <link rel="stylesheet" href="{{ asset('storage/assets/css/custom_style.css') }}" />

    <style>

    </style>
</head>

<body>
    {{-- Barre supérieure rouge --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger px-4 shadow-sm">
        <div class="container-fluid">
            {{-- Logo / Titre --}}
            <a class="navbar-brand fw-bold" href="/home">🌐 Project Tracking Sheet</a>

            {{-- Section droite --}}
            <div class="d-flex align-items-center ms-auto">
                @auth
                    {{-- Photo utilisateur --}}
                    {{-- <img src="{{ 'https://via.placeholder.com/40' }}" alt="Photo"
                        class="rounded-circle me-2" width="40" height="40"> --}}

                    {{-- Nom utilisateur --}}
                    <span class="text-white me-3 fw-semibold">Bonjour, {{ Auth::user()->prenom." ".Auth::user()->nom }}</span>

                    {{-- Bouton Déconnexion --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-light btn-sm">Déconnexion</button>
                    </form>
                @else
                    {{-- Boutons Connexion / Inscription --}}
                    <a href="{{ route('login') }}" class="btn btn-light btn-sm me-2">Connexion</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm">Inscription</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            {{-- Sidebar --}}
            <nav class="col-md-3 col-lg-2 d-md-block sidebar py-4 px-3">
                <ul class="nav flex-column">
                    <li class="nav-item"><a href="/" class="active"><i class="bi bi-house"></i> Accueil</a></li>
                    <li class="nav-item"><a href="{{ route("project.create") }}"><i class="bi bi-ui-checks"></i> Study Management</a></li>
                    <li class="nav-item"><a href="/dashboard"><i class="bi bi-bar-chart"></i> Dashboard</a></li>
                    <li class="nav-item"><a href="#"><i class="bi bi-gear"></i> Paramètres</a></li>
                </ul>
            </nav>

            {{-- Contenu principal --}}
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">
              


                @yield('content')


            </main>
        </div>

    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap-select-5/dist/js/bootstrap-select.min.js"></script> --}}
<!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    {{-- <script type="text/javascript" src="{{ asset('storage/assets_vendor3/js/jquery/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('storage/assets/js/bootstrap-select/js/bootstrap-select.min.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="{{ asset('storage/assets/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('storage/assets/alertifyjs/alertify.min.js') }}"></script>
    <script src="{{ asset('storage/assets/notify/notify.min.js') }}"></script>
    <script src="{{ asset('storage/assets/gigo-master/js/gijgo.min.js') }}"></script>
    <script src="{{ asset('storage/assets/fileinput/js/fileinput.min.js') }}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/gh/sumeetghimire/AlertJs/Alert.js"></script> --}}

    <script src="{{ asset('storage/assets/js/javascript-custom.js') }}"></script>
    <script src="{{ asset('storage/assets/js/javascript_ajax.js') }}"></script>
    {{-- <script>
        const ctx = document.getElementById('budgetChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Budget (€)',
                    data: @json($budgetsByMonth),
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    fill: true,
                    tension: 0.3,
                    borderWidth: 2,
                    pointRadius: 4,
                    pointBackgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script> --}}

    @yield('js')
</body>

</html>
