<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklists — {{ $inspection->inspection_name ?? $inspection->type_inspection }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --qa-brand: #C10202;
            --qa-brand-dark: #8b0001;
        }
        body { background: #f4f5f7; font-family: 'Segoe UI', sans-serif; }

        .page-header {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,.15);
        }
        .page-header .btn-back {
            background: rgba(255,255,255,.2);
            color: #fff;
            border: 1px solid rgba(255,255,255,.4);
            border-radius: 8px;
            font-size: .85rem;
            transition: background .2s;
        }
        .page-header .btn-back:hover { background: rgba(255,255,255,.35); color: #fff; }

        .checklist-card {
            border-radius: 14px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 14px rgba(0,0,0,.05);
            background: #fff;
            transition: box-shadow .2s, transform .15s;
            height: 100%;
        }
        .checklist-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,.1); transform: translateY(-2px); }

        .card-letter {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700;
            flex-shrink: 0;
        }
        .card-title-text {
            font-size: .92rem;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.3;
        }
        .card-qcount {
            font-size: .75rem;
            color: #888;
        }
        .badge-filled {
            background-color: #198754; color: #fff;
            border-radius: 999px; padding: .25rem .7rem; font-size: .75rem; font-weight: 600;
        }
        .badge-todo {
            background-color: #fd7e14; color: #fff;
            border-radius: 999px; padding: .25rem .7rem; font-size: .75rem; font-weight: 600;
        }
        .btn-open {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff; border: none; border-radius: 8px;
            font-size: .82rem; font-weight: 600; padding: .35rem .9rem;
            transition: opacity .2s;
        }
        .btn-open:hover { opacity: .88; color: #fff; }

        .inspection-meta {
            background: #fff7f7;
            border-left: 4px solid var(--qa-brand);
            border-radius: 10px;
            padding: 14px 18px;
        }
    </style>
</head>
<body>

{{-- ── Header ── --}}
<div class="page-header">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-clipboard2-check me-2"></i>
                Critical Phase Inspection Checklists
            </h4>
            <small class="opacity-75">
                QA-PR-1-003/05 — SANAS OECD GLP COMPLIANT FACILITY N° G0028
            </small>
        </div>
        @if ($inspection->project_id)
            <a href="/project/{{ $inspection->project_id }}/edit?project_id={{ $inspection->project_id }}"
               class="btn btn-back">
                <i class="bi bi-arrow-left me-1"></i>Retour au projet
            </a>
        @endif
    </div>
</div>

<div class="container-fluid py-4 px-4">

    {{-- Flash message --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Inspection meta --}}
    <div class="inspection-meta mb-4">
        <div class="row align-items-center g-3">
            <div class="col-auto">
                <i class="bi bi-shield-check fs-3" style="color:var(--qa-brand);"></i>
            </div>
            <div class="col">
                <div class="fw-bold" style="color:var(--qa-brand);">
                    {{ $inspection->inspection_name ?? $inspection->type_inspection }}
                </div>
                <div class="text-muted small">
                    @if ($inspection->inspector)
                        <i class="bi bi-person me-1"></i>{{ $inspection->inspector->prenom }} {{ $inspection->inspector->nom }}
                        &nbsp;|&nbsp;
                    @endif
                    @if ($inspection->date_scheduled)
                        <i class="bi bi-calendar3 me-1"></i>Programmée le {{ \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') }}
                    @endif
                    @if ($inspection->project)
                        &nbsp;|&nbsp;<i class="bi bi-folder2 me-1"></i>{{ $inspection->project->project_code }}
                    @endif
                </div>
            </div>
            <div class="col-auto">
                @php $filledCount = collect($statuses)->filter()->count(); @endphp
                <span class="fw-bold" style="font-size:1.1rem; color:var(--qa-brand);">
                    {{ $filledCount }} / {{ count($forms) }}
                </span>
                <div class="text-muted small">formulaires remplis</div>
            </div>
        </div>

        {{-- Progress bar --}}
        <div class="mt-3">
            <div class="progress" style="height:8px; border-radius:999px;">
                <div class="progress-bar"
                     style="width:{{ count($forms) > 0 ? ($filledCount / count($forms) * 100) : 0 }}%;
                            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
                            border-radius:999px;">
                </div>
            </div>
        </div>
    </div>

    {{-- Grille des 13 formulaires --}}
    <div class="row g-3">
        @foreach ($forms as $slug => $form)
            <div class="col-md-6 col-lg-4">
                <div class="checklist-card p-3 d-flex flex-column gap-2">
                    <div class="d-flex align-items-start gap-3">
                        <div class="card-letter">{{ $form['letter'] }}</div>
                        <div class="flex-grow-1">
                            <div class="card-title-text">{{ $form['title'] }}</div>
                            <div class="card-qcount">{{ count($form['questions']) }} questions</div>
                        </div>
                        <div>
                            @if ($statuses[$slug])
                                <span class="badge-filled">
                                    <i class="bi bi-check-lg me-1"></i>Rempli
                                </span>
                            @else
                                <span class="badge-todo">
                                    <i class="bi bi-clock me-1"></i>À compléter
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-auto text-end">
                        <a href="{{ route('checklist.show', [$inspection->id, $slug]) }}"
                           class="btn btn-open btn-sm">
                            <i class="bi bi-pencil-square me-1"></i>
                            {{ $statuses[$slug] ? 'Modifier' : 'Remplir' }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
