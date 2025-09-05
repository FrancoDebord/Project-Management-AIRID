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
    .custom-navbar {
      background: linear-gradient(90deg, #c20102, #8b0001);
      padding: 15px 30px;
    }
    .custom-navbar .nav-link {
      color: #fff !important;
      font-weight: 500;
      font-size: 1.1rem;
      margin: 0 12px;
      transition: all 0.3s ease;
    }
    .custom-navbar .nav-link:hover {
      color: #ffd4d4 !important;
      transform: translateY(-2px);
    }
    .navbar-brand {
      font-weight: bold;
      font-size: 1.4rem;
      color: #fff !important;
    }
    .btn-auth {
      background-color: #fff;
      color: #c20102;
      border: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .btn-auth:hover {
      background-color: #ffd4d4;
      color: #8b0001;
    }
    .dropdown-menu {
      border-radius: 12px;
      overflow: hidden;
    }
  </style>
</head>

<body>
    
    <!-- Navbar -->
  <nav class="navbar navbar-expand-lg custom-navbar">
    <div class="container-fluid">
      <!-- Logo -->
      <a class="navbar-brand" href="#">Project Tracking Sheet</a>

      <!-- Menu burger -->
      <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Liens -->
      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route("project.create") }}">Study Management</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Quality Assurance</a></li>
          <li class="nav-item"><a class="nav-link" href="#">Settings</a></li>
        </ul>
      </div>

      <!-- Auth -->
      <div class="d-flex align-items-center">
        <!-- 🔹 Si NON connecté -->
        <!--
        <button class="btn btn-auth me-2">Login</button>
        <button class="btn btn-light">Register</button>
        -->

        @auth
            
       <div class="dropdown">
          <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle fs-4 me-2"></i> Welcome, {{ Auth::user()->prenom." ".Auth::user()->nom }}
          </a>
          <ul class="dropdown-menu dropdown-menu-end shadow">
            <li><a class="dropdown-item" href="#">Profil</a></li>
            <li><a class="dropdown-item" href="#">Paramètres</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item text-danger" href="#">Déconnexion</a></li>
          </ul>
        </div>
        @endauth
        <!-- 🔹 Si connecté -->
        
      </div>
    </div>
  </nav>

    <div class="container-fluid">
        <div class="row">
            <main class="col-md-12 ms-sm-auto col-lg-12 px-md-5 py-4">


                @yield('content')


            </main>
        </div>

    </div>

    {{-- Chart.js --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
