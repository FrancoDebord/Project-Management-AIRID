{{-- resources/views/home.blade.php --}}
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Accueil - Application</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body { overflow-x: hidden; background-color: #f8f9fa; }
    .sidebar { min-height: 100vh; background: linear-gradient(180deg, #0d6efd, #0a58ca); color: #fff; }
    .sidebar a { color: #fff; text-decoration: none; display: flex; align-items: center; gap: .5rem; padding: .75rem 1rem; border-radius: .375rem; }
    .sidebar a.active, .sidebar a:hover { background-color: rgba(255,255,255,0.15); }
    .sidebar h2 { font-size: 1.2rem; font-weight: 600; }
    .kpi-card { border: none; border-radius: 1rem; box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,.05); }
    .kpi-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">

      {{-- Sidebar --}}
      <nav class="col-md-3 col-lg-2 d-md-block sidebar py-4 px-3">
        <div class="text-center mb-4">
          <h2>🌐 Mon Application</h2>
        </div>
        <ul class="nav flex-column">
          <li class="nav-item"><a href="/home" class="active"><i class="bi bi-house"></i> Accueil</a></li>
          <li class="nav-item"><a href="/wizard"><i class="bi bi-ui-checks"></i> Formulaire</a></li>
          <li class="nav-item"><a href="/dashboard"><i class="bi bi-bar-chart"></i> Dashboard</a></li>
          <li class="nav-item"><a href="#"><i class="bi bi-gear"></i> Paramètres</a></li>
        </ul>
      </nav>

      {{-- Contenu principal --}}
      <main class="col-md-9 ms-sm-auto col-lg-10 px-md-5 py-4">
        <h1 class="h3 fw-semibold mb-4">Tableau de bord</h1>

        <div class="row g-4">
          {{-- KPI 1 --}}
          <div class="col-md-6 col-xl-3">
            <div class="card kpi-card bg-white p-3">
              <div class="d-flex align-items-center">
                <div class="kpi-icon bg-primary text-white me-3"><i class="bi bi-graph-up"></i></div>
                <div>
                  <h6 class="text-muted mb-1">Projets soumis</h6>
                  <h3 class="fw-semibold mb-0">{{ $projectsCount }}</h3>
                </div>
              </div>
            </div>
          </div>

          {{-- KPI 2 --}}
          <div class="col-md-6 col-xl-3">
            <div class="card kpi-card bg-white p-3">
              <div class="d-flex align-items-center">
                <div class="kpi-icon bg-success text-white me-3"><i class="bi bi-people"></i></div>
                <div>
                  <h6 class="text-muted mb-1">Utilisateurs actifs</h6>
                  <h3 class="fw-semibold mb-0">{{ $activeUsers }}</h3>
                </div>
              </div>
            </div>
          </div>

          {{-- KPI 3 --}}
          <div class="col-md-6 col-xl-3">
            <div class="card kpi-card bg-white p-3">
              <div class="d-flex align-items-center">
                <div class="kpi-icon bg-warning text-white me-3"><i class="bi bi-cash-stack"></i></div>
                <div>
                  <h6 class="text-muted mb-1">Budget total (€)</h6>
                  <h3 class="fw-semibold mb-0">{{ number_format($totalBudget, 2, ',', ' ') }}</h3>
                </div>
              </div>
            </div>
          </div>

          {{-- KPI 4 --}}
          <div class="col-md-6 col-xl-3">
            <div class="card kpi-card bg-white p-3">
              <div class="d-flex align-items-center">
                <div class="kpi-icon bg-danger text-white me-3"><i class="bi bi-list-task"></i></div>
                <div>
                  <h6 class="text-muted mb-1">Tâches en cours</h6>
                  <h3 class="fw-semibold mb-0">{{ $tasksInProgress }}</h3>
                </div>
              </div>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
