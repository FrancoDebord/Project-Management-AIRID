{{-- resources/views/home.blade.php --}}
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Accueil - Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app_url" content="{{ \Request::getSchemeAndHttpHost() }}/">

    {{-- ── PWA ── --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1a3a6b">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="AIRID Projects">
    <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icons/icon-512.png">
    <script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function () {});
        });
    }
    </script>

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Thème Bootstrap 5 pour Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('storage/assets/datatable/datatables.min.css') }}">

    <link rel="stylesheet" href="{{ asset('storage/assets/gigo-master/css/gijgo.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/assets/fileinput/css/fileinput.min.css') }}">

    {{-- <link rel="stylesheet" href="{{ asset('storage/assets/js/bootstrap-select/css/bootstrap-select.min.css') }}"> --}}

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css"
        rel="stylesheet">

    @yield('css_vendor')

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
            <a class="navbar-brand" href="#">PTS</a>

            <!-- Menu burger -->
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Liens -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="/">Home</a></li>
                    @auth
                        @if (Auth::user()?->canCreateProject())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('project.create') }}">Study Management</a>
                            </li>
                        @endif
                        @if (Auth::user()?->canManageQA())
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('qaDashboard') }}">Quality Assurance</a>
                            </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('projects.list') || request()->routeIs('masterSchedule') ? 'active' : '' }}"
                               href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-folder2 me-1"></i>Projects' Infos
                            </a>
                            <ul class="dropdown-menu shadow">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('projects.list') ? 'active' : '' }}"
                                       href="{{ route('projects.list') }}">
                                        <i class="bi bi-table me-2"></i>List of Projects
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('masterSchedule') ? 'active' : '' }}"
                                       href="{{ route('masterSchedule') }}">
                                        <i class="bi bi-calendar3-range me-2"></i>Master Schedule
                                    </a>
                                </li>
                            </ul>
                        </li>
                        {{-- Features --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('features.*') ? 'active' : '' }}"
                               href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-stars me-1"></i>Features
                            </a>
                            <ul class="dropdown-menu shadow">
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('features.search') ? 'active' : '' }}"
                                       href="{{ route('features.search') }}">
                                        <i class="bi bi-search me-2"></i>Moteur de recherche
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item {{ request()->routeIs('features.diagnostics') ? 'active' : '' }}"
                                       href="{{ route('features.diagnostics') }}">
                                        <i class="bi bi-clipboard-pulse me-2"></i>Diagnostic des projets
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @if (Auth::user()?->hasRole(['super_admin', 'facility_manager']))
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-badge me-1"></i>Facility Manager
                                </a>
                                <ul class="dropdown-menu shadow">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('fm.qa-review.index') }}">
                                            <i class="bi bi-clipboard2-check me-2"></i>QA Review Inspections
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                        @if (Auth::user()?->canManageQA() || Auth::user()?->canManageUsers())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    <i class="bi bi-gear me-1"></i>Settings
                                </a>
                                <ul class="dropdown-menu shadow">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.settings') }}">
                                            <i class="bi bi-sliders me-2"></i>Paramètres
                                        </a>
                                    </li>
                                    @if (Auth::user()?->canManageUsers())
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.users') }}">
                                                <i class="bi bi-people me-2"></i>Gestion utilisateurs
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.checklists.index') }}">
                                                <i class="bi bi-list-check me-2"></i>Questions Checklists
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.cpia.index') }}">
                                                <i class="bi bi-clipboard2-pulse me-2"></i>CPIA Sections
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                    @endauth
                </ul>
            </div>

            <!-- Auth -->
            <div class="d-flex align-items-center gap-2">
                @auth
                    {{-- Notification bell --}}
                    <div class="dropdown" id="notifDropdown">
                        <a href="#" class="text-white position-relative d-flex align-items-center" id="notifBell"
                            data-bs-toggle="dropdown" aria-expanded="false" style="font-size:1.25rem;text-decoration:none;">
                            <i class="bi bi-bell-fill"></i>
                            <span id="notifBadge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none"
                                style="font-size:.55rem;min-width:16px;padding:2px 5px;">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow p-0"
                            style="min-width:340px;border-radius:12px;overflow:hidden;">
                            <div class="d-flex align-items-center justify-content-between px-3 py-2 border-bottom"
                                style="background:#f8f9fa;">
                                <span class="fw-semibold small">Notifications</span>
                                <button class="btn btn-link btn-sm p-0 text-muted" id="markAllReadDropBtn"
                                    style="font-size:.75rem;">Mark all read</button>
                            </div>
                            <div id="notifList" style="max-height:320px;overflow-y:auto;">
                                <div class="text-center text-muted py-4 small" id="notifEmpty">
                                    <i class="bi bi-bell-slash d-block fs-4 mb-1 opacity-25"></i>No notifications
                                </div>
                            </div>
                            <div class="border-top text-center py-2" style="background:#f8f9fa;">
                                <a href="{{ route('notifications.index') }}" class="small fw-semibold text-decoration-none"
                                    style="color:#1a3a6b;">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth
                @php
                    $roleColors = [
                        'super_admin' => '#1a3a6b',
                        'facility_manager' => '#0d6efd',
                        'qa_manager' => '#198754',
                        'study_director' => '#6f42c1',
                        'project_manager' => '#0dcaf0',
                        'archivist' => '#fd7e14',
                        'read_only' => '#6c757d',
                    ];
                    $userRole = Auth::user()?->role ?? '';
                @endphp
                <span class="badge rounded-pill px-2 py-1 d-none d-lg-inline-flex align-items-center"
                    style="background:{{ $roleColors[$userRole] ?? '#6c757d' }};font-size:.7rem;opacity:.9;">
                    {{ Auth::user()?->roleLabel() ?? '' }}
                </span>
                <div class="dropdown">
                    <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                        href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        {{ Auth::user()?->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li>
                            <span class="dropdown-item-text small text-muted">
                                {{ Auth::user()?->roleLabel() ?? '' }}
                            </span>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @if (Auth::user()?->canManageUsers())
                            <li>
                                <a class="dropdown-item" href="{{ route('admin.settings') }}">
                                    <i class="bi bi-gear me-2"></i>Paramètres
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="{{ asset('docs/index.html') }}" target="_blank">
                                <i class="bi bi-question-circle me-2"></i>Aide &amp; Documentation
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </a>
                        </li>
                    </ul>
                </div>

                @auth

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <main class="col-md-12 ms-sm-auto col-lg-12 px-4 px-md-5 px-xl-5 py-4" style="max-width:1600px;margin-left:auto;margin-right:auto;">


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
    {{-- <script src="{{ asset('storage/assets/alertifyjs/alertify.min.js') }}"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="{{ asset('storage/assets/notify/notify.min.js') }}"></script>
    <script src="{{ asset('storage/assets/gigo-master/js/gijgo.min.js') }}"></script>
    <script src="{{ asset('storage/assets/fileinput/js/fileinput.min.js') }}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/gh/sumeetghimire/AlertJs/Alert.js"></script> --}}

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

    @yield('js_vendor')

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

    @auth
        {{-- ── Notification bell JS ─────────────────────────────── --}}
        <script>
            (function() {
                const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
                const badge = document.getElementById('notifBadge');
                const list = document.getElementById('notifList');
                const empty = document.getElementById('notifEmpty');
                const icons = {
                    project_assigned: 'bi-person-badge-fill text-primary',
                    findings_resolved: 'bi-check2-circle text-success',
                    signature_requested: 'bi-pen-fill text-warning',
                    report_signed: 'bi-check2-circle text-success',
                    default: 'bi-bell text-secondary',
                };

                function loadNotifs() {
                    fetch('/notifications/latest')
                        .then(r => r.json())
                        .then(items => {
                            const unread = items.filter(n => !n.read_at).length;
                            if (unread > 0) {
                                badge.textContent = unread > 9 ? '9+' : unread;
                                badge.classList.remove('d-none');
                            } else {
                                badge.classList.add('d-none');
                            }

                            if (!items.length) {
                                empty.style.display = '';
                                return;
                            }
                            empty.style.display = 'none';

                            list.innerHTML = items.map(n => {
                                const icon = icons[n.type] || icons.default;
                                const bg = n.read_at ? '' : 'background:#f0f4ff;';
                                const time = new Date(n.created_at).toLocaleDateString('en-GB', {
                                    day: '2-digit',
                                    month: 'short'
                                });
                                return `<a href="${n.url || '#'}" class="d-flex align-items-start gap-2 px-3 py-2 text-decoration-none text-dark border-bottom notif-item"
                                   data-id="${n.id}" style="${bg}">
                            <i class="bi ${icon} mt-1 flex-shrink-0"></i>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="small fw-semibold text-truncate">${n.title}</div>
                                ${n.body ? `<div class="text-muted" style="font-size:.72rem;line-height:1.3;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">${n.body}</div>` : ''}
                            </div>
                            <span class="text-muted flex-shrink-0" style="font-size:.68rem;">${time}</span>
                        </a>`;
                            }).join('');

                            list.querySelectorAll('.notif-item').forEach(a => {
                                a.addEventListener('click', () => {
                                    const id = a.dataset.id;
                                    fetch(`/notifications/${id}/read`, {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': CSRF
                                        }
                                    });
                                });
                            });
                        });
                }

                document.getElementById('markAllReadDropBtn')?.addEventListener('click', (e) => {
                    e.preventDefault();
                    fetch('/notifications/read-all', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': CSRF
                            }
                        })
                        .then(() => loadNotifs());
                });

                // Load on bell open
                document.getElementById('notifBell')?.addEventListener('click', loadNotifs);
                // Poll every 60 seconds
                loadNotifs();
                setInterval(loadNotifs, 60000);
            })
            ();
        </script>

        {{-- Signature modal (included globally so any page can use it) --}}
        @include('partials.signature-modal')
    @endauth

    {{-- ── PWA Install Banner ─────────────────────────────────────── --}}
    <div id="pwaInstallBanner" style="
        display: none;
        position: fixed;
        bottom: 1.25rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        box-shadow: 0 6px 28px rgba(26,58,107,.18);
        padding: .85rem 1.25rem;
        display: none;
        align-items: center;
        gap: .85rem;
        max-width: 420px;
        width: calc(100% - 2rem);
        font-family: 'Segoe UI', system-ui, sans-serif;
    ">
        <img src="/icons/icon-192.png" alt="AIRID" width="40" height="40"
             style="border-radius:10px;flex-shrink:0;">
        <div style="flex:1;min-width:0;">
            <div style="font-weight:700;font-size:.9rem;color:#1a3a6b;line-height:1.2;">
                Installer AIRID PMS
            </div>
            <div style="font-size:.77rem;color:#6b7280;margin-top:2px;">
                Accès rapide depuis votre écran d'accueil
            </div>
        </div>
        <button id="pwaInstallBtn" style="
            background: linear-gradient(135deg, #1a3a6b, #c41230);
            color:#fff;border:none;border-radius:8px;
            padding:.45rem .9rem;font-size:.82rem;font-weight:600;
            cursor:pointer;white-space:nowrap;flex-shrink:0;">
            Installer
        </button>
        <button id="pwaInstallDismiss" style="
            background:none;border:none;color:#9ca3af;
            cursor:pointer;font-size:1.1rem;line-height:1;
            padding:0 .2rem;flex-shrink:0;" aria-label="Fermer">
            &times;
        </button>
    </div>
    <script>
    (function () {
        let deferredPrompt = null;
        const banner   = document.getElementById('pwaInstallBanner');
        const installBtn = document.getElementById('pwaInstallBtn');
        const dismissBtn = document.getElementById('pwaInstallDismiss');

        // Show banner when browser fires beforeinstallprompt
        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            // Only show if not dismissed before
            if (!sessionStorage.getItem('pwaDismissed')) {
                banner.style.display = 'flex';
            }
        });

        installBtn?.addEventListener('click', function () {
            banner.style.display = 'none';
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function () {
                    deferredPrompt = null;
                });
            }
        });

        dismissBtn?.addEventListener('click', function () {
            banner.style.display = 'none';
            sessionStorage.setItem('pwaDismissed', '1');
        });

        // Hide banner after successful install
        window.addEventListener('appinstalled', function () {
            banner.style.display = 'none';
            deferredPrompt = null;
        });
    })();
    </script>
</body>

</html>
