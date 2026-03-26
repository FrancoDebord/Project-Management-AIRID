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

        body {
            background: #f4f5f7;
            font-family: 'Segoe UI', sans-serif;
        }

        .page-header {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .15);
        }

        .page-header .btn-back {
            background: rgba(255, 255, 255, .2);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .4);
            border-radius: 8px;
            font-size: .85rem;
            transition: background .2s;
        }

        .page-header .btn-back:hover {
            background: rgba(255, 255, 255, .35);
            color: #fff;
        }

        .btn-print {
            background: rgba(255, 255, 255, .15);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .4);
            border-radius: 8px;
            font-size: .85rem;
            transition: background .2s;
        }

        .btn-print:hover {
            background: rgba(255, 255, 255, .3);
            color: #fff;
        }

        .form-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 14px rgba(0, 0, 0, .06);
            overflow: hidden;
        }

        .form-card-header {
            background: #fff7f7;
            border-bottom: 2px solid #f0d0d0;
            padding: 16px 24px;
        }

        .letter-badge {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 700;
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

        .questions-table tr:hover td {
            background: #fffaf9;
        }

        .q-num {
            width: 36px;
            height: 36px;
            background: var(--qa-brand);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .78rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Radio YES/NO/NA custom */
        .radio-group {
            display: flex;
            gap: 6px;
            justify-content: center;
        }

        .radio-opt input[type="radio"] {
            display: none;
        }

        .radio-opt label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 32px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: .78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            color: #888;
            background: #fff;
            user-select: none;
        }

        /* YES */
        .radio-opt input[value="yes"]:checked+label {
            background: #198754;
            border-color: #198754;
            color: #fff;
        }

        .radio-opt label:has(~ input[value="yes"]) {}

        input[value="yes"]+label:hover {
            border-color: #198754;
            color: #198754;
        }

        /* NO */
        .radio-opt input[value="no"]:checked+label {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }

        input[value="no"]+label:hover {
            border-color: #dc3545;
            color: #dc3545;
        }

        /* NA */
        .radio-opt input[value="na"]:checked+label {
            background: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        input[value="na"]+label:hover {
            border-color: #6c757d;
            color: #6c757d;
        }

        .btn-save {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            padding: .65rem 2.2rem;
            transition: opacity .2s;
        }

        .btn-save:hover {
            opacity: .88;
            color: #fff;
        }

        .inspection-info {
            background: #fff7f7;
            border-left: 4px solid var(--qa-brand);
            border-radius: 8px;
            padding: 10px 16px;
            font-size: .85rem;
        }

        /* ── Print styles ── */
        @media print {
            body {
                background: #fff !important;
            }

            .page-header,
            .no-print {
                display: none !important;
            }

            .form-card {
                box-shadow: none;
                border: 1px solid #ccc;
            }

            .questions-table tr:hover td {
                background: transparent;
            }

            .radio-opt label {
                border-width: 1px;
                width: 40px;
                height: 24px;
                font-size: .7rem;
            }

            .radio-opt input[value="yes"]:checked+label {
                background: #198754 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .radio-opt input[value="no"]:checked+label {
                background: #dc3545 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .radio-opt input[value="na"]:checked+label {
                background: #6c757d !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .btn-save {
                display: none;
            }

            textarea {
                border: 1px solid #ccc !important;
            }
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
                    @if (isset($fieldPrefix))
                        @if ($inspection->type_inspection === 'Study Protocol Inspection')
                            Study Protocol Inspection Checklist
                        @elseif($inspection->type_inspection === 'Process Inspection')
                            Process Inspection Checklist
                        @elseif($inspection->type_inspection === 'Study Report Inspection')
                            Study Report Inspection Checklist
                        @elseif($inspection->type_inspection === 'Data Quality Inspection')
                            Data Quality Inspection Checklist
                        @else
                            Facility Inspection Checklist
                        @endif
                    @elseif(in_array($inspection->type_inspection, [
                            'Study Protocol Amendment/Deviation Inspection',
                            'Study Report Amendment Inspection',
                        ]))
                        Amendment/Deviation Inspection Checklist
                    @else
                        Critical Phase Inspection Checklist
                    @endif
                    — {{ $form['letter'] }}. {{ $form['title'] }}
                </h5>
                <small class="opacity-75">
                    @if (isset($fieldPrefix))
                        @if ($inspection->type_inspection === 'Study Protocol Inspection')
                            QA-PR-1-002/06
                        @elseif($inspection->type_inspection === 'Process Inspection')
                            QA-PR-1-008/05
                        @elseif($inspection->type_inspection === 'Study Report Inspection')
                            QA-PR-1-005/06
                        @elseif($inspection->type_inspection === 'Data Quality Inspection')
                            {{ $form['doc_ref'] ?? 'QA-PR-1-018/03' }}
                        @else
                            {{ $inspection->facility_location === 'cove' ? 'QA-PR-1-001B/06' : 'QA-PR-1-001A/06' }}
                        @endif
                    @elseif(in_array($inspection->type_inspection, [
                            'Study Protocol Amendment/Deviation Inspection',
                            'Study Report Amendment Inspection',
                        ]))
                        QA-PR-1-004/06
                    @else
                        QA-PR-1-003/05
                    @endif
                    — SANAS OECD GLP COMPLIANT FACILITY N° G0028
                </small>
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
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 no-print" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Bannière statut inspection --}}
        @if ($inspection->completed_at)
            <div class="alert mb-4 no-print d-flex align-items-center gap-3"
                 style="background:#fff3cd;border:2px solid #ffc107;border-radius:12px;padding:14px 18px;">
                <i class="bi bi-lock-fill fs-4" style="color:#856404;flex-shrink:0;"></i>
                <div>
                    <div class="fw-bold" style="color:#856404;font-size:.95rem;">
                        Inspection clôturée — lecture seule
                    </div>
                    <div class="text-muted" style="font-size:.82rem;">
                        Cette inspection a été marquée comme terminée le
                        <strong>{{ \Carbon\Carbon::parse($inspection->completed_at)->format('d/m/Y à H:i') }}</strong>.
                        Aucune modification n'est possible.
                    </div>
                </div>
            </div>
        @endif

        {{-- Project info banner (shown when inspection is linked to a project) --}}
        @if ($inspection->project)
        @php
            $proj    = $inspection->project;
            $projDir = null;
            if ($proj->study_director) {
                $projDir = \App\Models\Pro_Personnel::find($proj->study_director);
            }
        @endphp
        <div class="mb-4 rounded-3 no-print" style="background:#fff8f0;border:1.5px solid #fde8c8;padding:14px 18px;">
            <div class="d-flex flex-wrap gap-4 align-items-center">
                <div>
                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;font-weight:600;">Code Projet</div>
                    <div class="fw-bold" style="font-size:1rem;color:#b45309;">{{ $proj->project_code }}</div>
                </div>
                <div style="flex:1;min-width:200px;">
                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;font-weight:600;">Titre du projet</div>
                    <div class="fw-semibold" style="font-size:.9rem;color:#1a1a1a;">{{ $proj->project_title ?? '—' }}</div>
                </div>
                @if ($projDir)
                <div>
                    <div class="text-muted" style="font-size:.7rem;text-transform:uppercase;letter-spacing:.06em;font-weight:600;">Study Director</div>
                    <div class="fw-semibold" style="font-size:.9rem;color:#1a1a1a;">
                        <i class="bi bi-person-badge me-1" style="color:#b45309;"></i>{{ $projDir->prenom }} {{ $projDir->nom }}
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Inspection info bar --}}
        <div class="inspection-info mb-4">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <div>
                    <i class="bi bi-shield-check me-1" style="color:var(--qa-brand);"></i>
                    <strong
                        style="color:var(--qa-brand);">{{ $inspection->inspection_name ?? $inspection->type_inspection }}</strong>
                </div>
                @if ($inspection->inspector)
                    <div class="text-muted">
                        <i class="bi bi-person me-1"></i>{{ $inspection->inspector->prenom }}
                        {{ $inspection->inspector->nom }}
                    </div>
                @endif
                @if ($inspection->date_scheduled)
                    <div class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($inspection->date_scheduled)->format('d/m/Y') }}
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
                @if ($inspection->completed_at)
                <fieldset disabled style="opacity:.75;">
                @endif

                {{-- Hidden fields --}}
                <input type="hidden" name="project_id" value="{{ $inspection->project_id }}">
                <input type="hidden" name="project_code" value="{{ $inspection->project->project_code ?? '' }}">

                {{-- Amendment/Deviation extra header fields --}}
                @if (in_array($inspection->type_inspection, [
                        'Study Protocol Amendment/Deviation Inspection',
                        'Study Report Amendment Inspection',
                    ]))
                    <div class="mb-4 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e9ecef;">
                        <h6 class="fw-bold mb-3" style="color:var(--qa-brand);">
                            <i class="bi bi-file-earmark-text me-2"></i>Document Information
                        </h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small">Document Type</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="document_type"
                                            id="doc_type_protocol" value="Study Protocol"
                                            {{ old('document_type', $record->document_type ?? '') === 'Study Protocol' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="doc_type_protocol">Study Protocol</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="document_type"
                                            id="doc_type_report" value="Study Report"
                                            {{ old('document_type', $record->document_type ?? '') === 'Study Report' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="doc_type_report">Study Report</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="deviation_number" class="form-label fw-semibold small">Deviation N°</label>
                                <input type="text" id="deviation_number" name="deviation_number"
                                    class="form-control form-control-sm"
                                    value="{{ old('deviation_number', $record->deviation_number ?? '') }}"
                                    placeholder="e.g. DEV-001">
                            </div>
                            <div class="col-md-6">
                                <label for="amendment_number" class="form-label fw-semibold small">Amendment
                                    N°</label>
                                <input type="text" id="amendment_number" name="amendment_number"
                                    class="form-control form-control-sm"
                                    value="{{ old('amendment_number', $record->amendment_number ?? '') }}"
                                    placeholder="e.g. AMD-001">
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Questions table --}}
                @php
                    $fp = $fieldPrefix ?? '';
                    $formType = $form['form_type'] ?? 'standard';
                    $dqAnswers = $dqAnswers ?? [];
                    $dqV1Answers = $dqV1Answers ?? [];
                    $dqV2Answers = $dqV2Answers ?? [];
                    $dqSection = $form['section'] ?? '';
                @endphp

                {{-- ══ DQ Standard Section (A, B) ══ --}}
                @if ($formType === 'dq_standard')
                    {{-- Header info block for Section A --}}
                    @if ($form['has_header'] ?? false)
                        <div class="mb-4 p-3 rounded-3" style="background:#f8f9fa; border:1px solid #e9ecef;">
                            <h6 class="fw-bold mb-3" style="color:var(--qa-brand);"><i
                                    class="bi bi-info-circle me-2"></i>Informations générales</h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Aspect(s) Inspecté(s)</label>
                                <div class="d-flex flex-wrap gap-3">
                                    @foreach (['Staff Training', 'Computerised Systems and Softwares validation', 'Data Validity', 'Data Sheet Information', 'Study Box'] as $asp)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                name="aspects_inspected[]" value="{{ $asp }}"
                                                id="asp_{{ Str::slug($asp) }}"
                                                {{ in_array($asp, $record?->aspects_inspected ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label small"
                                                for="asp_{{ Str::slug($asp) }}">{{ $asp }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @php
                                // Defaults from project and QA Manager (used only when record not yet saved)
                                $dqProject   = $dqProject   ?? null;
                                $dqQaManager = $dqQaManager ?? null;
                                $defaultStartDate = $record?->study_start_date?->format('Y-m-d')
                                    ?? ($dqProject?->date_debut_effective ?? $dqProject?->date_debut_previsionnelle);
                                $defaultEndDate = $record?->study_end_date?->format('Y-m-d')
                                    ?? ($dqProject?->date_fin_effective ?? $dqProject?->date_fin_previsionnelle);
                                $sdirector = $dqProject?->studyDirector;
                                $defaultStudyDirector = $record?->study_director_name
                                    ?? ($sdirector ? $sdirector->prenom . ' ' . $sdirector->nom : '');
                                $defaultPhone = $record?->qa_inspector_phone
                                    ?? $dqQaManager?->telephone_whatsapp ?? '';
                                $defaultEmail = $record?->qa_inspector_email
                                    ?? $dqQaManager?->email_professionnel ?? $dqQaManager?->email_personnel ?? '';
                            @endphp
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Study Start Date</label>
                                    <input type="date" name="study_start_date"
                                        class="form-control form-control-sm"
                                        max="{{ date('Y-m-d') }}"
                                        value="{{ old('study_start_date', $defaultStartDate) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold small">Study End Date</label>
                                    <input type="date" name="study_end_date" class="form-control form-control-sm"
                                        max="{{ date('Y-m-d') }}"
                                        value="{{ old('study_end_date', $defaultEndDate) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">Study Director</label>
                                    <input type="text" name="study_director_name"
                                        class="form-control form-control-sm"
                                        value="{{ old('study_director_name', $defaultStudyDirector) }}"
                                        placeholder="Nom du directeur d'étude">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">QA Inspector Phone</label>
                                    <input type="text" name="qa_inspector_phone"
                                        class="form-control form-control-sm"
                                        value="{{ old('qa_inspector_phone', $defaultPhone) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold small">QA Inspector Email</label>
                                    <input type="email" name="qa_inspector_email"
                                        class="form-control form-control-sm"
                                        value="{{ old('qa_inspector_email', $defaultEmail) }}">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label fw-semibold small">Personnel impliqué dans
                                    l'inspection</label>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0" style="font-size:.82rem;">
                                        <thead>
                                            <tr>
                                                <th style="width:32px;">#</th>
                                                <th>Nom</th>
                                                <th>Titre</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for ($pi = 0; $pi < 7; $pi++)
                                                @php $piRec = $record?->personnel_involved[$pi] ?? []; @endphp
                                                <tr>
                                                    <td class="text-center text-muted">{{ $pi + 1 }}</td>
                                                    <td><input type="text"
                                                            name="personnel_involved[{{ $pi }}][name]"
                                                            class="form-control form-control-sm border-0 p-0"
                                                            value="{{ old("personnel_involved.$pi.name", $piRec['name'] ?? '') }}">
                                                    </td>
                                                    <td><input type="text"
                                                            name="personnel_involved[{{ $pi }}][title]"
                                                            class="form-control form-control-sm border-0 p-0"
                                                            value="{{ old("personnel_involved.$pi.title", $piRec['title'] ?? '') }}">
                                                    </td>
                                                </tr>
                                            @endfor
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table questions-table align-middle mb-3">
                            <thead>
                                <tr>
                                    <th style="width:42px;">#</th>
                                    <th>Question</th>
                                    <th class="text-center" style="width:180px;">Réponse</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($form['questions'] as $n => $question)
                                    @php $val = $dqAnswers[(string)$n] ?? null; @endphp
                                    <tr>
                                        <td>
                                            <div class="q-num">{{ $n }}</div>
                                        </td>
                                        <td class="py-3">{{ $question }}</td>
                                        <td>
                                            <div class="radio-group">
                                                <div class="radio-opt">
                                                    <input type="radio" id="dq_q_{{ $n }}_yes"
                                                        name="q_{{ $n }}" value="yes"
                                                        {{ $val === 'yes' ? 'checked' : '' }}>
                                                    <label for="dq_q_{{ $n }}_yes">YES</label>
                                                </div>
                                                <div class="radio-opt">
                                                    <input type="radio" id="dq_q_{{ $n }}_no"
                                                        name="q_{{ $n }}" value="no"
                                                        {{ $val === 'no' ? 'checked' : '' }}>
                                                    <label for="dq_q_{{ $n }}_no">NO</label>
                                                </div>
                                                <div class="radio-opt">
                                                    <input type="radio" id="dq_q_{{ $n }}_na"
                                                        name="q_{{ $n }}" value="na"
                                                        {{ $val === 'na' ? 'checked' : '' }}>
                                                    <label for="dq_q_{{ $n }}_na">N/A</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Date Performed + QA Personnel --}}
                    <div class="row g-3 mb-4 p-3 rounded-3" style="background:#f8f9fa;border:1px solid #e9ecef;">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-0" style="color:var(--qa-brand);font-size:.85rem;"><i
                                    class="bi bi-calendar-check me-1"></i>Informations de vérification</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date Performed</label>
                            <input type="date" name="{{ $dqSection }}_date_performed"
                                class="form-control form-control-sm"
                                max="{{ date('Y-m-d') }}"
                                value="{{ old($dqSection . '_date_performed', $record?->{$dqSection . '_date_performed'}?->format('Y-m-d') ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">QA Personnel</label>
                            <select name="{{ $dqSection }}_qa_personnel_id" class="form-select form-select-sm">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($all_personnels ?? [] as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old($dqSection . '_qa_personnel_id', $record?->{$dqSection . '_qa_personnel_id'}) == $p->id ? 'selected' : '' }}>
                                        {{ $p->prenom }} {{ $p->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ══ DQ Dual Verification Section (C, D) ══ --}}
                @elseif ($formType === 'dual_verification')
                    <div class="table-responsive">
                        <table class="table questions-table align-middle mb-3" style="font-size:.85rem;">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="width:42px;">#</th>
                                    <th rowspan="2">Question</th>
                                    <th colspan="3" class="text-center"
                                        style="background:#e8f5e9;color:#1b5e20;border-bottom:2px solid #4caf50;">
                                        Vérification 1</th>
                                    <th colspan="3" class="text-center"
                                        style="background:#e3f2fd;color:#0d47a1;border-bottom:2px solid #2196f3;">
                                        Vérification 2</th>
                                </tr>
                                <tr>
                                    <th class="text-center" style="width:58px;background:#e8f5e9;">YES</th>
                                    <th class="text-center" style="width:44px;background:#e8f5e9;">NO</th>
                                    <th class="text-center" style="width:44px;background:#e8f5e9;">N/A</th>
                                    <th class="text-center" style="width:58px;background:#e3f2fd;">YES</th>
                                    <th class="text-center" style="width:44px;background:#e3f2fd;">NO</th>
                                    <th class="text-center" style="width:44px;background:#e3f2fd;">N/A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($form['questions'] as $n => $question)
                                    @php
                                        $v1 = $dqV1Answers[(string) $n] ?? null;
                                        $v2 = $dqV2Answers[(string) $n] ?? null;
                                        $isSub = in_array((string) $n, $form['sub_items'] ?? []);
                                    @endphp
                                    <tr class="{{ $isSub ? 'table-light' : '' }}">
                                        <td>
                                            <div class="q-num {{ $isSub ? 'ms-2' : '' }}"
                                                style="{{ $isSub ? 'font-size:.7rem;' : '' }}">{{ $n }}
                                            </div>
                                        </td>
                                        <td class="py-2 {{ $isSub ? 'ps-4 text-muted' : '' }}">{{ $question }}
                                        </td>
                                        <td class="text-center" style="background:#f9fef9;">
                                            <input type="radio" name="v1_q_{{ $n }}" value="yes"
                                                {{ $v1 === 'yes' ? 'checked' : '' }}
                                                style="accent-color:#198754;width:16px;height:16px;">
                                        </td>
                                        <td class="text-center" style="background:#f9fef9;">
                                            <input type="radio" name="v1_q_{{ $n }}" value="no"
                                                {{ $v1 === 'no' ? 'checked' : '' }}
                                                style="accent-color:#dc3545;width:16px;height:16px;">
                                        </td>
                                        <td class="text-center" style="background:#f9fef9;">
                                            <input type="radio" name="v1_q_{{ $n }}" value="na"
                                                {{ $v1 === 'na' ? 'checked' : '' }}
                                                style="accent-color:#6c757d;width:16px;height:16px;">
                                        </td>
                                        <td class="text-center" style="background:#f0f8ff;">
                                            <input type="radio" name="v2_q_{{ $n }}" value="yes"
                                                {{ $v2 === 'yes' ? 'checked' : '' }}
                                                style="accent-color:#198754;width:16px;height:16px;">
                                        </td>
                                        <td class="text-center" style="background:#f0f8ff;">
                                            <input type="radio" name="v2_q_{{ $n }}" value="no"
                                                {{ $v2 === 'no' ? 'checked' : '' }}
                                                style="accent-color:#dc3545;width:16px;height:16px;">
                                        </td>
                                        <td class="text-center" style="background:#f0f8ff;">
                                            <input type="radio" name="v2_q_{{ $n }}" value="na"
                                                {{ $v2 === 'na' ? 'checked' : '' }}
                                                style="accent-color:#6c757d;width:16px;height:16px;">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- V1 + V2 Date/Personnel --}}
                    <div class="row g-3 mb-4 p-3 rounded-3" style="background:#f8f9fa;border:1px solid #e9ecef;">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-1" style="color:var(--qa-brand);font-size:.85rem;"><i
                                    class="bi bi-calendar-check me-1"></i>Informations de vérification</h6>
                        </div>
                        <div class="col-12"><small class="text-muted fw-bold"
                                style="color:#1b5e20 !important;">Vérification 1</small></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date Performed</label>
                            <input type="date" name="{{ $dqSection }}_v1_date_performed"
                                class="form-control form-control-sm"
                                max="{{ date('Y-m-d') }}"
                                value="{{ old($dqSection . '_v1_date_performed', $record?->{$dqSection . '_v1_date_performed'}?->format('Y-m-d') ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">QA Personnel</label>
                            <select name="{{ $dqSection }}_v1_qa_personnel_id" class="form-select form-select-sm">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($all_personnels ?? [] as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old($dqSection . '_v1_qa_personnel_id', $record?->{$dqSection . '_v1_qa_personnel_id'}) == $p->id ? 'selected' : '' }}>
                                        {{ $p->prenom }} {{ $p->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-1"><small class="text-muted fw-bold"
                                style="color:#0d47a1 !important;">Vérification 2</small></div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date Performed</label>
                            <input type="date" name="{{ $dqSection }}_v2_date_performed"
                                class="form-control form-control-sm"
                                max="{{ date('Y-m-d') }}"
                                value="{{ old($dqSection . '_v2_date_performed', $record?->{$dqSection . '_v2_date_performed'}?->format('Y-m-d') ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">QA Personnel</label>
                            <select name="{{ $dqSection }}_v2_qa_personnel_id" class="form-select form-select-sm">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($all_personnels ?? [] as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old($dqSection . '_v2_qa_personnel_id', $record?->{$dqSection . '_v2_qa_personnel_id'}) == $p->id ? 'selected' : '' }}>
                                        {{ $p->prenom }} {{ $p->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- ══ DQ Study Box Section (E) ══ --}}
                @elseif ($formType === 'study_box')
                    <div class="table-responsive">
                        <table class="table questions-table align-middle mb-3" style="font-size:.85rem;">
                            <thead>
                                <tr>
                                    <th style="width:42px;">#</th>
                                    <th>Question</th>
                                    <th class="text-center" style="width:180px;">Réponse</th>
                                    <th class="text-center" style="width:100px;">Documents signés?</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($form['questions'] as $n => $question)
                                    @php
                                        $entry = $dqAnswers[(string) $n] ?? [];
                                        $resp = is_array($entry) ? $entry['response'] ?? null : null;
                                        $signed = is_array($entry) ? $entry['signed'] ?? null : null;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="q-num">{{ $n }}</div>
                                        </td>
                                        <td class="py-2">{{ $question }}</td>
                                        <td>
                                            <div class="radio-group">
                                                <div class="radio-opt">
                                                    <input type="radio" id="sb_q{{ $n }}_yes"
                                                        name="q_{{ $n }}_response" value="yes"
                                                        class="sb-response" data-q="{{ $n }}"
                                                        {{ $resp === 'yes' ? 'checked' : '' }}>
                                                    <label for="sb_q{{ $n }}_yes">YES</label>
                                                </div>
                                                <div class="radio-opt">
                                                    <input type="radio" id="sb_q{{ $n }}_no"
                                                        name="q_{{ $n }}_response" value="no"
                                                        class="sb-response" data-q="{{ $n }}"
                                                        {{ $resp === 'no' ? 'checked' : '' }}>
                                                    <label for="sb_q{{ $n }}_no">NO</label>
                                                </div>
                                                <div class="radio-opt">
                                                    <input type="radio" id="sb_q{{ $n }}_na"
                                                        name="q_{{ $n }}_response" value="na"
                                                        class="sb-response" data-q="{{ $n }}"
                                                        {{ $resp === 'na' ? 'checked' : '' }}>
                                                    <label for="sb_q{{ $n }}_na">N/A</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center" id="signed-cell-{{ $n }}">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="q_{{ $n }}_signed" value="yes"
                                                        id="sg_q{{ $n }}_yes"
                                                        {{ $signed === 'yes' ? 'checked' : '' }}>
                                                    <label class="form-check-label small"
                                                        for="sg_q{{ $n }}_yes">Yes</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input" type="radio"
                                                        name="q_{{ $n }}_signed" value="no"
                                                        id="sg_q{{ $n }}_no"
                                                        {{ $signed === 'no' ? 'checked' : '' }}>
                                                    <label class="form-check-label small"
                                                        for="sg_q{{ $n }}_no">No</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <script>
                        (function () {
                            function sbUpdateSigned(qNum, responseValue) {
                                const signedYes = document.getElementById('sg_q' + qNum + '_yes');
                                const signedNo  = document.getElementById('sg_q' + qNum + '_no');
                                const cell      = document.getElementById('signed-cell-' + qNum);
                                if (!signedYes) return;
                                if (responseValue !== 'yes') {
                                    signedYes.disabled = true;
                                    if (signedYes.checked) {
                                        signedYes.checked = false;
                                        if (signedNo) signedNo.checked = true;
                                    }
                                    if (cell) cell.style.opacity = '0.35';
                                } else {
                                    signedYes.disabled = false;
                                    if (cell) cell.style.opacity = '';
                                }
                            }

                            document.querySelectorAll('.sb-response').forEach(function (radio) {
                                radio.addEventListener('change', function () {
                                    if (this.checked) sbUpdateSigned(this.dataset.q, this.value);
                                });
                            });

                            // Initialise state on page load
                            document.querySelectorAll('.sb-response').forEach(function (radio) {
                                if (radio.checked) sbUpdateSigned(radio.dataset.q, radio.value);
                            });
                        })();
                    </script>
                    {{-- Date Performed + QA Personnel --}}
                    <div class="row g-3 mb-4 p-3 rounded-3" style="background:#f8f9fa;border:1px solid #e9ecef;">
                        <div class="col-12">
                            <h6 class="fw-semibold mb-0" style="color:var(--qa-brand);font-size:.85rem;"><i
                                    class="bi bi-calendar-check me-1"></i>Informations de vérification</h6>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">Date Performed</label>
                            <input type="date" name="{{ $dqSection }}_date_performed"
                                class="form-control form-control-sm"
                                max="{{ date('Y-m-d') }}"
                                value="{{ old($dqSection . '_date_performed', $record?->{$dqSection . '_date_performed'}?->format('Y-m-d') ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold small">QA Personnel</label>
                            <select name="{{ $dqSection }}_qa_personnel_id" class="form-select form-select-sm">
                                <option value="">-- Sélectionner --</option>
                                @foreach ($all_personnels ?? [] as $p)
                                    <option value="{{ $p->id }}"
                                        {{ old($dqSection . '_qa_personnel_id', $record?->{$dqSection . '_qa_personnel_id'}) == $p->id ? 'selected' : '' }}>
                                        {{ $p->prenom }} {{ $p->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    {{-- ══ Standard rendering (Facility, Process, SP, SR, Critical Phase, Amendment) ══ --}}
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
                                    @php $val = $record ? $record->{"{$fp}q{$n}"} : null; @endphp
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
                                                        id="{{ $fp }}q{{ $n }}_yes"
                                                        name="{{ $fp }}q{{ $n }}"
                                                        value="yes" {{ $val === 'yes' ? 'checked' : '' }}>
                                                    <label
                                                        for="{{ $fp }}q{{ $n }}_yes">YES</label>
                                                </div>
                                                {{-- NO --}}
                                                <div class="radio-opt">
                                                    <input type="radio"
                                                        id="{{ $fp }}q{{ $n }}_no"
                                                        name="{{ $fp }}q{{ $n }}"
                                                        value="no" {{ $val === 'no' ? 'checked' : '' }}>
                                                    <label
                                                        for="{{ $fp }}q{{ $n }}_no">NO</label>
                                                </div>
                                                {{-- N/A --}}
                                                <div class="radio-opt">
                                                    <input type="radio"
                                                        id="{{ $fp }}q{{ $n }}_na"
                                                        name="{{ $fp }}q{{ $n }}"
                                                        value="na" {{ $val === 'na' ? 'checked' : '' }}>
                                                    <label
                                                        for="{{ $fp }}q{{ $n }}_na">N/A</label>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Section F (Study Personnel): staff training records table --}}
                @if (($form['type'] ?? '') === 'study_personnel')
                    @php $staffCount = $form['staff_count'] ?? 15; @endphp
                    <h6 class="fw-bold mb-3 mt-2" style="color:var(--qa-brand);">
                        <i class="bi bi-people me-2"></i>Study Personnel Training Records
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle" style="font-size:.85rem;">
                            <thead>
                                <tr style="background:#f8f8f8;">
                                    <th style="width:40px;">#</th>
                                    <th style="width:160px;">Name / Code</th>
                                    <th class="text-center" style="width:180px;">Training done?</th>
                                    <th>Level of Training</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 1; $i <= $staffCount; $i++)
                                    @php
                                        $staffResult = $record ? $record->{"f_staff_{$i}_result"} : null;
                                        $staffLevel = $record ? $record->{"f_staff_{$i}_level"} : null;
                                        $staffRemarks = $record ? $record->{"f_staff_{$i}_remarks"} : null;
                                    @endphp
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $i }}</td>
                                        <td class="text-muted fst-italic" style="font-size:.78rem;">Staff
                                            {{ $i }}</td>
                                        <td>
                                            <div class="radio-group">
                                                <div class="radio-opt">
                                                    <input type="radio" id="f_staff_{{ $i }}_yes"
                                                        name="f_staff_{{ $i }}_result" value="yes"
                                                        {{ $staffResult === 'yes' ? 'checked' : '' }}>
                                                    <label for="f_staff_{{ $i }}_yes">YES</label>
                                                </div>
                                                <div class="radio-opt">
                                                    <input type="radio" id="f_staff_{{ $i }}_no"
                                                        name="f_staff_{{ $i }}_result" value="no"
                                                        {{ $staffResult === 'no' ? 'checked' : '' }}>
                                                    <label for="f_staff_{{ $i }}_no">NO</label>
                                                </div>
                                                <div class="radio-opt">
                                                    <input type="radio" id="f_staff_{{ $i }}_na"
                                                        name="f_staff_{{ $i }}_result" value="na"
                                                        {{ $staffResult === 'na' ? 'checked' : '' }}>
                                                    <label for="f_staff_{{ $i }}_na">N/A</label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" name="f_staff_{{ $i }}_level"
                                                class="form-control form-control-sm"
                                                value="{{ old("f_staff_{$i}_level", $staffLevel) }}"
                                                placeholder="e.g. Basic, Advanced…">
                                        </td>
                                        <td>
                                            <input type="text" name="f_staff_{{ $i }}_remarks"
                                                class="form-control form-control-sm"
                                                value="{{ old("f_staff_{$i}_remarks", $staffRemarks) }}"
                                                placeholder="Remarks…">
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Comments --}}
                @php $commentsField = ($fieldPrefix ?? '') . 'comments'; @endphp
                <div class="mb-4">
                    <label for="{{ $commentsField }}" class="form-label fw-semibold"
                        style="color:var(--qa-brand);">
                        <i class="bi bi-chat-text me-1"></i>Comments / Observations
                    </label>
                    <textarea id="{{ $commentsField }}" name="{{ $commentsField }}" rows="4" class="form-control"
                        placeholder="Observations, remarques, non-conformités…">{{ old($commentsField, $record->{$commentsField} ?? '') }}</textarea>
                </div>

                {{-- Conclusion : Critical Phase, Amendment/Deviation, Study Protocol, Study Report --}}
                @php
                    $isAmendment = in_array($inspection->type_inspection, [
                        'Study Protocol Amendment/Deviation Inspection',
                        'Study Report Amendment Inspection',
                    ]);
                    $isSpSection =
                        isset($fieldPrefix) &&
                        in_array($inspection->type_inspection, [
                            'Study Protocol Inspection',
                            'Study Report Inspection',
                            'Data Quality Inspection',
                            'Process Inspection',
                            'Facility Inspection',
                        ]);
                    $showConclusion = !isset($fieldPrefix) || $isSpSection;
                @endphp
                @if ($showConclusion)
                    <div class="mb-4 p-4 rounded-3" style="background:#f8f9fa; border: 2px solid #e9ecef;">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div
                                style="width:36px;height:36px;background:linear-gradient(135deg,var(--qa-brand),var(--qa-brand-dark));border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <i class="bi bi-award-fill text-white" style="font-size:.9rem;"></i>
                            </div>
                            <span class="fw-bold" style="color:var(--qa-brand);font-size:1rem;">
                                Conclusion de l'inspection
                            </span>
                        </div>
                        <p class="text-muted small mb-3">
                            @if ($isAmendment)
                                Après examen de l'amendement / déviation, l'inspecteur QA donne son appréciation globale
                                sur sa conformité.
                            @elseif($isSpSection)
                                Après examen des réponses de cette section, l'inspecteur QA donne son appréciation sur
                                la conformité de cette partie du @if ($inspection->type_inspection === 'Study Report Inspection')
                                    rapport d'étude
                                @elseif($inspection->type_inspection === 'Data Quality Inspection')
                                    contrôle qualité des données
                                @elseif($inspection->type_inspection === 'Process Inspection')
                                    processus
                                @elseif($inspection->type_inspection === 'Facility Inspection')
                                    facility
                                @else
                                    protocole
                                @endif.
                            @else
                                Après examen de toutes les réponses ci-dessus, l'inspecteur QA donne son appréciation
                                globale sur la conformité de cette phase critique.
                            @endif
                        </p>
                        @php
                            $conformingField = $isSpSection ? ($fieldPrefix . 'is_conforming') : 'is_conforming';
                            $savedConf       = $record ? ($record->{$conformingField} ?? null) : null;
                            $conformingValue = old('is_conforming', $savedConf);
                            // Determine which button to pre-select
                            $preConf    = ($conformingValue == 1);
                            $preNonConf = ($conformingValue !== null && !$conformingValue);
                        @endphp
                        {{-- Hidden input qui sera soumis avec le formulaire --}}
                        <input type="hidden" name="is_conforming" id="conforming_value"
                            value="{{ $preConf ? '1' : ($preNonConf ? '0' : '') }}">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <button type="button" id="btnConf"
                                onclick="setConforming(1)"
                                style="cursor:pointer;border-radius:50px;padding:.45rem 1.1rem;font-size:.88rem;font-weight:700;display:inline-flex;align-items:center;gap:.4rem;transition:all .15s;border:none;{{ $preConf ? 'background:#198754;color:#fff;outline:2px solid #198754;' : 'background:#fff;color:#6c757d;outline:2px solid #dee2e6;' }}">
                                <i class="bi bi-check-circle-fill"></i>CONFORME
                            </button>
                            <button type="button" id="btnNonConf"
                                onclick="setConforming(0)"
                                style="cursor:pointer;border-radius:50px;padding:.45rem 1.1rem;font-size:.88rem;font-weight:700;display:inline-flex;align-items:center;gap:.4rem;transition:all .15s;border:none;{{ $preNonConf ? 'background:#dc3545;color:#fff;outline:2px solid #dc3545;' : 'background:#fff;color:#6c757d;outline:2px solid #dee2e6;' }}">
                                <i class="bi bi-x-circle-fill"></i>NON CONFORME
                            </button>
                        </div>
                    </div>
                    <script>
                        function setConforming(val) {
                            document.getElementById('conforming_value').value = val;
                            const btnConf    = document.getElementById('btnConf');
                            const btnNonConf = document.getElementById('btnNonConf');
                            const styleActive   = 'cursor:pointer;border-radius:50px;padding:.45rem 1.1rem;font-size:.88rem;font-weight:700;display:inline-flex;align-items:center;gap:.4rem;transition:all .15s;border:none;';
                            const styleInactive = 'cursor:pointer;border-radius:50px;padding:.45rem 1.1rem;font-size:.88rem;font-weight:700;display:inline-flex;align-items:center;gap:.4rem;transition:all .15s;border:none;background:#fff;color:#6c757d;outline:2px solid #dee2e6;';
                            if (val === 1) {
                                btnConf.style.cssText    = styleActive + 'background:#198754;color:#fff;outline:2px solid #198754;';
                                btnNonConf.style.cssText = styleInactive;
                            } else {
                                btnConf.style.cssText    = styleInactive;
                                btnNonConf.style.cssText = styleActive + 'background:#dc3545;color:#fff;outline:2px solid #dc3545;';
                            }
                        }
                    </script>
                @endif

                @if ($inspection->completed_at)
                </fieldset>
                @endif

                {{-- Submit --}}
                <div class="d-flex justify-content-between align-items-center no-print">
                    <a href="{{ route('checklist.index', $inspection->id) }}"
                        class="btn btn-outline-secondary rounded-3">
                        <i class="bi bi-arrow-left me-1"></i>Retour
                    </a>
                    @if (!$inspection->completed_at)
                        <button type="submit" class="btn btn-save">
                            <i class="bi bi-floppy me-1"></i>Enregistrer le formulaire
                        </button>
                    @else
                        <span class="text-muted small">
                            <i class="bi bi-lock me-1"></i>Modification impossible — inspection clôturée
                        </span>
                    @endif
                </div>
            </form>
        </div>

        {{-- ── Findings panel (facility sections only) ── --}}
        @isset($sectionFindings)
            <div class="form-card mt-4">
                <div class="form-card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-journal-text fs-5" style="color:var(--qa-brand);"></i>
                        <span class="fw-bold" style="color:var(--qa-brand);">
                            Findings — {{ $form['letter'] }}. {{ $form['title'] }}
                        </span>
                        <span class="badge rounded-pill" style="background:var(--qa-brand); font-size:.75rem;">
                            {{ $sectionFindings->count() }}
                        </span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-3 no-print"
                        onclick="document.getElementById('addFindingForm').style.display=document.getElementById('addFindingForm').style.display==='none'?'':'none'">
                        <i class="bi bi-plus me-1"></i>Ajouter un finding
                    </button>
                </div>

                <div class="p-4">
                    {{-- Add finding inline form --}}
                    <div id="addFindingForm" style="display:none;" class="mb-4 p-3 rounded-3 border no-print"
                        style="background:#fff8f8;">
                        <div class="row g-2">
                            <div class="col-12">
                                <textarea id="newFindingText" rows="2" class="form-control form-control-sm"
                                    placeholder="Décrivez la non-conformité ou l'observation…"></textarea>
                            </div>
                            <div class="col-md-6">
                                <select id="newFindingAssigned" class="form-select form-select-sm">
                                    <option value="">— Assigner à (optionnel) —</option>
                                    @foreach ($personnels as $p)
                                        <option value="{{ $p->id }}">{{ $p->prenom }} {{ $p->nom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="date" id="newFindingDeadline" class="form-control form-control-sm"
                                    placeholder="Deadline">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-danger w-100"
                                    onclick="submitSectionFinding('{{ $inspection->id }}', '{{ $slug }}')">
                                    <i class="bi bi-floppy"></i>
                                </button>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="newFindingConform">
                                    <label class="form-check-label small text-muted" for="newFindingConform">
                                        Marquer comme conformité (observation positive)
                                    </label>
                                </div>
                            </div>
                            <div id="findingSaveError" class="col-12 text-danger small" style="display:none;"></div>
                        </div>
                    </div>

                    {{-- Existing findings --}}
                    @if ($sectionFindings->isEmpty())
                        <p class="text-muted small text-center py-3 mb-0">
                            <i class="bi bi-check-circle me-1 text-success"></i>
                            Aucun finding enregistré pour cette section.
                        </p>
                    @else
                        <div class="d-flex flex-column gap-2" id="sectionFindingsList">
                            @foreach ($sectionFindings as $fi)
                                <div class="p-3 rounded-3 border"
                                    style="border-left: 4px solid {{ $fi->is_conformity ? '#198754' : ($fi->status === 'complete' ? '#6c757d' : '#dc3545') }} !important;">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <div class="flex-grow-1">
                                            <div class="small">{{ $fi->finding_text }}</div>
                                            @if ($fi->assignedTo)
                                                <div class="text-muted" style="font-size:.75rem;">
                                                    <i class="bi bi-person me-1"></i>{{ $fi->assignedTo->prenom }}
                                                    {{ $fi->assignedTo->nom }}
                                                    @if ($fi->deadline_date)
                                                        &nbsp;·&nbsp;<i
                                                            class="bi bi-calendar3 me-1"></i>{{ $fi->deadline_date }}
                                                    @endif
                                                </div>
                                            @endif
                                            @if ($fi->action_point)
                                                <div class="text-success mt-1" style="font-size:.75rem;">
                                                    <i class="bi bi-check2-circle me-1"></i>{{ $fi->action_point }}
                                                    @if ($fi->means_of_verification)
                                                        &nbsp;·&nbsp;<em>{{ $fi->means_of_verification }}</em>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column align-items-end gap-1 flex-shrink-0">
                                            @if ($fi->is_conformity)
                                                <span class="badge bg-secondary"
                                                    style="font-size:.7rem;">Conformité</span>
                                            @elseif($fi->status === 'complete')
                                                <span class="badge bg-success" style="font-size:.7rem;">Résolu</span>
                                            @else
                                                <span class="badge bg-danger" style="font-size:.7rem;">Pending</span>
                                                @if (!$inspection->completed_at)
                                                    <button type="button" class="btn btn-sm btn-outline-success no-print"
                                                        style="font-size:.72rem; padding:.2rem .55rem;"
                                                        onclick="toggleResolveForm({{ $fi->id }})">
                                                        <i class="bi bi-check2-circle me-1"></i>Résoudre
                                                    </button>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Resolve inline form (non-conformity pending only) --}}
                                    @if (!$fi->is_conformity && $fi->status !== 'complete' && !$inspection->completed_at)
                                        <div id="resolveForm{{ $fi->id }}" class="mt-3 p-3 rounded-2 no-print"
                                            style="display:none; background:#f0fdf4; border:1px solid #bbf7d0;">
                                            <div class="row g-2">
                                                <div class="col-12">
                                                    <label class="form-label fw-semibold small mb-1">Action corrective
                                                        <span class="text-danger">*</span></label>
                                                    <textarea id="resolveAction{{ $fi->id }}" rows="2" class="form-control form-control-sm"
                                                        placeholder="Décrivez l'action corrective entreprise…"></textarea>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label fw-semibold small mb-1">Moyen de
                                                        vérification</label>
                                                    <input type="text" id="resolveMov{{ $fi->id }}"
                                                        class="form-control form-control-sm"
                                                        placeholder="Ex: rapport, photo, signature…">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-semibold small mb-1">Date de résolution
                                                        <span class="text-danger">*</span></label>
                                                    <input type="date" id="resolveDate{{ $fi->id }}"
                                                        class="form-control form-control-sm"
                                                        max="{{ date('Y-m-d') }}"
                                                        value="{{ now()->toDateString() }}">
                                                </div>
                                                <div class="col-md-2 d-flex align-items-end gap-1">
                                                    <button type="button" class="btn btn-success btn-sm w-100"
                                                        onclick="submitResolve({{ $fi->id }})">
                                                        <i class="bi bi-floppy"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm"
                                                        onclick="toggleResolveForm({{ $fi->id }})">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </div>
                                                <div id="resolveError{{ $fi->id }}"
                                                    class="col-12 text-danger small" style="display:none;"></div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <script>
                // Auto-open the add-finding form if arriving via #addFindingForm anchor
                if (window.location.hash === '#addFindingForm') {
                    const form = document.getElementById('addFindingForm');
                    if (form) {
                        form.style.display = '';
                        setTimeout(() => form.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        }), 200);
                    }
                }

                function toggleResolveForm(findingId) {
                    const el = document.getElementById('resolveForm' + findingId);
                    el.style.display = el.style.display === 'none' ? '' : 'none';
                }

                function submitResolve(findingId) {
                    const action = document.getElementById('resolveAction' + findingId).value.trim();
                    const mov = document.getElementById('resolveMov' + findingId).value.trim();
                    const date = document.getElementById('resolveDate' + findingId).value;
                    const errDiv = document.getElementById('resolveError' + findingId);

                    if (!action || !date) {
                        errDiv.textContent = 'L\'action corrective et la date sont requises.';
                        errDiv.style.display = '';
                        return;
                    }
                    errDiv.style.display = 'none';

                    fetch('{{ route('resolveQaFinding') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                finding_id: findingId,
                                action_point: action,
                                means_of_verification: mov || null,
                                resolved_date: date,
                            }),
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                errDiv.textContent = data.message;
                                errDiv.style.display = '';
                            }
                        })
                        .catch(() => {
                            errDiv.textContent = 'Erreur réseau.';
                            errDiv.style.display = '';
                        });
                }

                function submitSectionFinding(inspectionId, sectionSlug) {
                    const text = document.getElementById('newFindingText').value.trim();
                    const errDiv = document.getElementById('findingSaveError');
                    if (!text) {
                        errDiv.textContent = 'Le texte du finding est requis.';
                        errDiv.style.display = '';
                        return;
                    }
                    errDiv.style.display = 'none';

                    @php $isSectionedType = in_array($inspection->type_inspection, ['Facility Inspection', 'Process Inspection', 'Study Protocol Inspection', 'Study Report Inspection', 'Data Quality Inspection']); @endphp
                    const isSectioned = @json($isSectionedType);
                    const projectId = @json($inspection->project_id);

                    const payload = {
                        inspection_id: inspectionId,
                        finding_text: text,
                        is_conformity: document.getElementById('newFindingConform').checked ? 1 : 0,
                        assigned_to: document.getElementById('newFindingAssigned').value || null,
                        deadline_date: document.getElementById('newFindingDeadline').value || null,
                    };
                    if (isSectioned) {
                        payload.facility_section = sectionSlug;
                    }
                    if (projectId) {
                        payload.project_id = projectId;
                    }

                    fetch('{{ route('saveQaFinding') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload),
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                errDiv.textContent = data.message;
                                errDiv.style.display = '';
                            }
                        })
                        .catch(() => {
                            errDiv.textContent = 'Erreur réseau.';
                            errDiv.style.display = '';
                        });
                }
            </script>
        @endisset

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
