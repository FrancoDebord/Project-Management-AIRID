<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $form['letter'] }}. {{ $form['title'] }} — Inspection #{{ $inspection->id }}</title>
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

        .btn-print {
            background: rgba(255,255,255,.15);
            color: #fff;
            border: 1px solid rgba(255,255,255,.4);
            border-radius: 8px;
            font-size: .85rem;
            transition: background .2s;
        }
        .btn-print:hover { background: rgba(255,255,255,.3); color: #fff; }

        .form-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 14px rgba(0,0,0,.06);
            overflow: hidden;
        }

        .form-card-header {
            background: #fff7f7;
            border-bottom: 2px solid #f0d0d0;
            padding: 16px 24px;
        }

        .letter-badge {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem; font-weight: 700;
            flex-shrink: 0;
        }

        .questions-table th {
            background: #f8f8f8;
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #555;
            border-bottom: 2px solid #e0e0e0;
        }
        .questions-table td {
            vertical-align: middle;
            font-size: .9rem;
            border-color: #f0f0f0;
        }
        .questions-table tr:hover td { background: #fffaf9; }

        .q-num {
            width: 36px; height: 36px;
            background: var(--qa-brand);
            color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .78rem; font-weight: 700;
            flex-shrink: 0;
        }

        /* Radio YES/NO/NA custom */
        .radio-group {
            display: flex; gap: 6px; justify-content: center;
        }
        .radio-opt input[type="radio"] { display: none; }
        .radio-opt label {
            display: inline-flex; align-items: center; justify-content: center;
            width: 52px; height: 32px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: .78rem; font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            color: #888;
            background: #fff;
            user-select: none;
        }
        /* YES */
        .radio-opt input[value="yes"]:checked + label {
            background: #198754; border-color: #198754; color: #fff;
        }
        .radio-opt label:has(~ input[value="yes"]) { }
        input[value="yes"] + label:hover { border-color: #198754; color: #198754; }
        /* NO */
        .radio-opt input[value="no"]:checked + label {
            background: #dc3545; border-color: #dc3545; color: #fff;
        }
        input[value="no"] + label:hover { border-color: #dc3545; color: #dc3545; }
        /* NA */
        .radio-opt input[value="na"]:checked + label {
            background: #6c757d; border-color: #6c757d; color: #fff;
        }
        input[value="na"] + label:hover { border-color: #6c757d; color: #6c757d; }

        .btn-save {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff; border: none; border-radius: 10px;
            font-size: 1rem; font-weight: 700; padding: .65rem 2.2rem;
            transition: opacity .2s;
        }
        .btn-save:hover { opacity: .88; color: #fff; }

        .inspection-info {
            background: #fff7f7;
            border-left: 4px solid var(--qa-brand);
            border-radius: 8px;
            padding: 10px 16px;
            font-size: .85rem;
        }

        /* ── Print styles ── */
        @media print {
            body { background: #fff !important; }
            .page-header, .no-print { display: none !important; }
            .form-card { box-shadow: none; border: 1px solid #ccc; }
            .questions-table tr:hover td { background: transparent; }
            .radio-opt label {
                border-width: 1px;
                width: 40px; height: 24px;
                font-size: .7rem;
            }
            .radio-opt input[value="yes"]:checked + label { background: #198754 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .radio-opt input[value="no"]:checked + label { background: #dc3545 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .radio-opt input[value="na"]:checked + label { background: #6c757d !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .btn-save { display: none; }
            textarea { border: 1px solid #ccc !important; }
        }
    </style>
</head>
<body>

{{-- ── Header ── --}}
<div class="page-header no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h5 class="mb-1 fw-bold">
                <i class="bi bi-clipboard2-check me-2"></i>
                Critical Phase Inspection Checklist — {{ $form['letter'] }}. {{ $form['title'] }}
            </h5>
            <small class="opacity-75">QA-PR-1-003/05 — SANAS OECD GLP COMPLIANT FACILITY N° G0028</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('checklist.index', $inspection->id) }}" class="btn btn-back">
                <i class="bi bi-arrow-left me-1"></i>Retour aux checklists
            </a>
            <button type="button" class="btn btn-print" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Imprimer
            </button>
        </div>
    </div>
</div>

<div class="container-fluid py-4 px-4" style="max-width:1100px;">

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 no-print" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Inspection info bar --}}
    <div class="inspection-info mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div>
                <i class="bi bi-shield-check me-1" style="color:var(--qa-brand);"></i>
                <strong style="color:var(--qa-brand);">{{ $inspection->inspection_name ?? $inspection->type_inspection }}</strong>
            </div>
            @if ($inspection->inspector)
                <div class="text-muted">
                    <i class="bi bi-person me-1"></i>{{ $inspection->inspector->prenom }} {{ $inspection->inspector->nom }}
                </div>
            @endif
            @if ($inspection->date_scheduled)
                <div class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') }}
                </div>
            @endif
            @if ($inspection->project)
                <div class="text-muted">
                    <i class="bi bi-folder2 me-1"></i>{{ $inspection->project->project_code }}
                </div>
            @endif
            <div class="ms-auto text-muted" style="font-size:.8rem;">
                {{ count($form['questions']) }} questions
            </div>
        </div>
    </div>

    {{-- Form card --}}
    <div class="form-card">

        {{-- Card header --}}
        <div class="form-card-header">
            <div class="d-flex align-items-center gap-3">
                <div class="letter-badge">{{ $form['letter'] }}</div>
                <div>
                    <div class="fw-bold fs-6" style="color:var(--qa-brand);">{{ $form['title'] }}</div>
                    <div class="text-muted small">
                        @if ($record)
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            Formulaire rempli — dernière mise à jour {{ $record->updated_at->format('d/m/Y H:i') }}
                        @else
                            <i class="bi bi-clock text-warning me-1"></i>
                            Formulaire vide — veuillez compléter les réponses ci-dessous
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- The form --}}
        <form method="POST" action="{{ route('checklist.save', [$inspection->id, $slug]) }}" class="p-4">
            @csrf

            {{-- Hidden fields --}}
            <input type="hidden" name="project_id"   value="{{ $inspection->project_id }}">
            <input type="hidden" name="project_code" value="{{ $inspection->project->project_code ?? '' }}">

            {{-- Questions table --}}
            <div class="table-responsive">
                <table class="table questions-table align-middle mb-4">
                    <thead>
                        <tr>
                            <th style="width:42px;">#</th>
                            <th>Question</th>
                            <th class="text-center" style="width:180px;">Réponse</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($form['questions'] as $n => $question)
                            @php $val = $record ? $record->{"q$n"} : null; @endphp
                            <tr>
                                <td>
                                    <div class="q-num">{{ $n }}</div>
                                </td>
                                <td class="py-3">{{ $question }}</td>
                                <td>
                                    <div class="radio-group">
                                        {{-- YES --}}
                                        <div class="radio-opt">
                                            <input type="radio"
                                                   id="q{{ $n }}_yes"
                                                   name="q{{ $n }}"
                                                   value="yes"
                                                   {{ $val === 'yes' ? 'checked' : '' }}>
                                            <label for="q{{ $n }}_yes">YES</label>
                                        </div>
                                        {{-- NO --}}
                                        <div class="radio-opt">
                                            <input type="radio"
                                                   id="q{{ $n }}_no"
                                                   name="q{{ $n }}"
                                                   value="no"
                                                   {{ $val === 'no' ? 'checked' : '' }}>
                                            <label for="q{{ $n }}_no">NO</label>
                                        </div>
                                        {{-- N/A --}}
                                        <div class="radio-opt">
                                            <input type="radio"
                                                   id="q{{ $n }}_na"
                                                   name="q{{ $n }}"
                                                   value="na"
                                                   {{ $val === 'na' ? 'checked' : '' }}>
                                            <label for="q{{ $n }}_na">N/A</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Comments --}}
            <div class="mb-4">
                <label for="comments" class="form-label fw-semibold" style="color:var(--qa-brand);">
                    <i class="bi bi-chat-text me-1"></i>Comments / Observations
                </label>
                <textarea id="comments" name="comments" rows="4"
                          class="form-control"
                          placeholder="Observations, remarques, non-conformités…">{{ old('comments', $record->comments ?? '') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="d-flex justify-content-between align-items-center no-print">
                <a href="{{ route('checklist.index', $inspection->id) }}"
                   class="btn btn-outline-secondary rounded-3">
                    <i class="bi bi-arrow-left me-1"></i>Annuler
                </a>
                <button type="submit" class="btn btn-save">
                    <i class="bi bi-floppy me-1"></i>Enregistrer le formulaire
                </button>
            </div>
        </form>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
