<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facility Inspection Checklist — Inspection #{{ $inspection->id }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        :root {
            --qa-brand: #C10202;
            --qa-brand-dark: #8b0001;
        }
        body { background: #f4f5f7; font-family: 'Segoe UI', sans-serif; }

        /* ── Page header ── */
        .page-header {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff;
            padding: 16px 28px;
            box-shadow: 0 2px 10px rgba(0,0,0,.15);
            position: sticky; top: 0; z-index: 1030;
        }
        .page-header .btn-back {
            background: rgba(255,255,255,.2); color: #fff;
            border: 1px solid rgba(255,255,255,.4); border-radius: 8px;
            font-size: .82rem; transition: background .2s;
        }
        .page-header .btn-back:hover { background: rgba(255,255,255,.35); color: #fff; }
        .btn-print {
            background: rgba(255,255,255,.15); color: #fff;
            border: 1px solid rgba(255,255,255,.4); border-radius: 8px;
            font-size: .82rem; transition: background .2s;
        }
        .btn-print:hover { background: rgba(255,255,255,.3); color: #fff; }

        /* ── Section nav pills ── */
        .section-nav {
            background: #fff;
            border-bottom: 1px solid #e8e8e8;
            padding: 8px 20px;
            display: flex; gap: 6px; flex-wrap: wrap; align-items: center;
            position: sticky; top: 61px; z-index: 1020;
            box-shadow: 0 2px 6px rgba(0,0,0,.04);
        }
        .sec-pill {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px;
            border-radius: 50%;
            font-size: .78rem; font-weight: 700;
            cursor: pointer; text-decoration: none;
            background: #f0f0f0; color: #555;
            border: 2px solid transparent;
            transition: all .15s;
        }
        .sec-pill:hover { background: #fce8e8; border-color: var(--qa-brand); color: var(--qa-brand); }
        .sec-pill.active { background: var(--qa-brand); color: #fff; border-color: var(--qa-brand); }
        .sec-pill.filled { background: #d4edda; color: #155724; border-color: #28a745; }

        /* ── Section cards ── */
        .section-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 3px 12px rgba(0,0,0,.05);
            overflow: hidden;
            margin-bottom: 24px;
        }
        .section-card-header {
            background: #fff7f7;
            border-bottom: 2px solid #f0d0d0;
            padding: 14px 22px;
            display: flex; align-items: center; gap: 14px;
        }
        .sec-badge {
            width: 46px; height: 46px;
            background: linear-gradient(135deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; font-weight: 700; flex-shrink: 0;
        }

        /* ── Questions table ── */
        .questions-table th {
            background: #f8f8f8; font-size: .78rem;
            font-weight: 700; text-transform: uppercase;
            letter-spacing: .04em; color: #555;
            border-bottom: 2px solid #e0e0e0;
        }
        .questions-table td { vertical-align: middle; font-size: .88rem; border-color: #f0f0f0; }
        .questions-table tr:hover td { background: #fffaf9; }

        .q-num {
            width: 32px; height: 32px;
            background: var(--qa-brand); color: #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 700; flex-shrink: 0;
        }

        /* ── YES / NO / N/A radio ── */
        .radio-group { display: flex; gap: 5px; justify-content: center; }
        .radio-opt input[type="radio"] { display: none; }
        .radio-opt label {
            display: inline-flex; align-items: center; justify-content: center;
            width: 48px; height: 30px;
            border: 2px solid #ddd; border-radius: 6px;
            font-size: .75rem; font-weight: 700;
            cursor: pointer; transition: all .15s;
            color: #888; background: #fff; user-select: none;
        }
        .radio-opt input[value="yes"]:checked + label { background: #198754; border-color: #198754; color: #fff; }
        .radio-opt input[value="no"]:checked  + label { background: #dc3545; border-color: #dc3545; color: #fff; }
        .radio-opt input[value="na"]:checked  + label { background: #6c757d; border-color: #6c757d; color: #fff; }
        input[value="yes"] + label:hover { border-color: #198754; color: #198754; }
        input[value="no"]  + label:hover { border-color: #dc3545; color: #dc3545; }
        input[value="na"]  + label:hover { border-color: #6c757d; color: #6c757d; }

        /* ── Save bar ── */
        .save-bar {
            position: sticky; bottom: 0; z-index: 1020;
            background: #fff; border-top: 1px solid #e0e0e0;
            padding: 12px 28px; box-shadow: 0 -3px 10px rgba(0,0,0,.07);
        }
        .btn-save {
            background: linear-gradient(90deg, var(--qa-brand), var(--qa-brand-dark));
            color: #fff; border: none; border-radius: 10px;
            font-size: .95rem; font-weight: 700; padding: .6rem 2rem;
            transition: opacity .2s;
        }
        .btn-save:hover { opacity: .88; color: #fff; }

        .inspection-info {
            background: #fff7f7; border-left: 4px solid var(--qa-brand);
            border-radius: 8px; padding: 10px 16px; font-size: .84rem;
        }

        /* ── Print ── */
        @media print {
            body { background: #fff !important; }
            .page-header, .section-nav, .save-bar, .no-print { display: none !important; }
            .section-card { box-shadow: none; border: 1px solid #ccc; page-break-inside: avoid; }
            .questions-table tr:hover td { background: transparent; }
            .radio-opt label { border-width: 1px; width: 38px; height: 22px; font-size: .68rem; }
            .radio-opt input[value="yes"]:checked + label { background: #198754 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .radio-opt input[value="no"]:checked  + label { background: #dc3545 !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .radio-opt input[value="na"]:checked  + label { background: #6c757d !important; color: #fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

{{-- ── Sticky header ── --}}
<div class="page-header no-print">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-building-check me-2"></i>Facility Inspection Checklist (Main Facility)
            </h5>
            <small class="opacity-75">{{ $form['doc_ref'] }} — SANAS OECD GLP COMPLIANT FACILITY N° G0028</small>
        </div>
        <div class="d-flex gap-2">
            @if ($inspection->project_id)
                <a href="/project/create?project_id={{ $inspection->project_id }}#step6" class="btn btn-back">
                    <i class="bi bi-arrow-left me-1"></i>Retour au projet
                </a>
            @endif
            <button type="button" class="btn btn-print" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>Imprimer
            </button>
        </div>
    </div>
</div>

{{-- ── Section navigation pills ── --}}
@php
    $sectionLetters = array_keys($form['sections']);
    $sectionLabels  = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O'];
@endphp
<div class="section-nav no-print">
    <small class="text-muted me-1" style="font-size:.75rem;">Sections :</small>
    @foreach ($form['sections'] as $sec => $section)
        @php $label = strtoupper($sec); @endphp
        <a href="#section-{{ $sec }}" class="sec-pill" id="pill-{{ $sec }}" title="{{ $section['title'] }}">
            {{ $label }}
        </a>
    @endforeach
</div>

<div class="container-fluid py-4 px-4" style="max-width:1200px;">

    {{-- Flash --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 no-print" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Inspection meta --}}
    <div class="inspection-info mb-4">
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <div>
                <i class="bi bi-shield-check me-1" style="color:var(--qa-brand);"></i>
                <strong style="color:var(--qa-brand);">
                    {{ $inspection->inspection_name ?? $inspection->type_inspection }}
                </strong>
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
            @if ($record)
                <div class="ms-auto">
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle-fill me-1"></i>
                        Formulaire rempli — dernière MAJ {{ $record->updated_at->format('d/m/Y H:i') }}
                    </span>
                </div>
            @else
                <div class="ms-auto">
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock me-1"></i>Formulaire vide
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Units/Sections inspected summary (print only) --}}
    <div class="d-none d-print-block mb-4">
        <h5 class="fw-bold">Units / Sections Inspected:</h5>
        <div class="row">
            @foreach ($form['sections'] as $sec => $section)
                <div class="col-6">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div style="width:14px;height:14px;border:1px solid #333;flex-shrink:0;"></div>
                        <span style="font-size:.85rem;">{{ $section['title'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        <hr>
        <div class="row mb-3">
            <div class="col-6">
                <strong>Inspection Date:</strong> ___________________________
            </div>
            <div class="col-6">
                <strong>Next Inspection Date:</strong> ________________________
            </div>
        </div>
        <strong>Quality Assurance Inspector:</strong>
        @if ($inspection->inspector)
            {{ $inspection->inspector->prenom }} {{ $inspection->inspector->nom }}
        @endif
        <hr>
    </div>

    {{-- ═══════════════════════════════════════
         THE FORM (all 15 sections)
    ═══════════════════════════════════════ --}}
    <form method="POST" action="{{ route('checklist.save', [$inspection->id, 'facility-inspection']) }}">
        @csrf

        <input type="hidden" name="project_id"   value="{{ $inspection->project_id }}">
        <input type="hidden" name="project_code" value="{{ $inspection->project->project_code ?? '' }}">

        @foreach ($form['sections'] as $sec => $section)
            @php $letter = strtoupper($sec); @endphp

            <div class="section-card" id="section-{{ $sec }}">

                {{-- Section header --}}
                <div class="section-card-header">
                    <div class="sec-badge">{{ $letter }}</div>
                    <div>
                        <div class="fw-bold fs-6" style="color:var(--qa-brand);">
                            {{ $letter }}. {{ $section['title'] }}
                        </div>
                        <div class="text-muted small">{{ count($section['questions']) }} questions</div>
                    </div>
                </div>

                {{-- Questions --}}
                <div class="table-responsive px-3 pt-3">
                    <table class="table questions-table align-middle mb-3">
                        <thead>
                            <tr>
                                <th style="width:40px;">#</th>
                                <th>Question</th>
                                <th class="text-center" style="width:170px;">YES / NO / N/A</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($section['questions'] as $n => $question)
                                @php $val = $record ? $record->{"{$sec}_q{$n}"} : null; @endphp
                                <tr>
                                    <td><div class="q-num">{{ $n }}</div></td>
                                    <td class="py-2">{{ $question }}</td>
                                    <td>
                                        <div class="radio-group">
                                            <div class="radio-opt">
                                                <input type="radio"
                                                       id="{{ $sec }}_q{{ $n }}_yes"
                                                       name="{{ $sec }}_q{{ $n }}"
                                                       value="yes"
                                                       {{ $val === 'yes' ? 'checked' : '' }}>
                                                <label for="{{ $sec }}_q{{ $n }}_yes">YES</label>
                                            </div>
                                            <div class="radio-opt">
                                                <input type="radio"
                                                       id="{{ $sec }}_q{{ $n }}_no"
                                                       name="{{ $sec }}_q{{ $n }}"
                                                       value="no"
                                                       {{ $val === 'no' ? 'checked' : '' }}>
                                                <label for="{{ $sec }}_q{{ $n }}_no">NO</label>
                                            </div>
                                            <div class="radio-opt">
                                                <input type="radio"
                                                       id="{{ $sec }}_q{{ $n }}_na"
                                                       name="{{ $sec }}_q{{ $n }}"
                                                       value="na"
                                                       {{ $val === 'na' ? 'checked' : '' }}>
                                                <label for="{{ $sec }}_q{{ $n }}_na">N/A</label>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Comments --}}
                <div class="px-3 pb-3">
                    <label for="{{ $sec }}_comments" class="form-label fw-semibold small" style="color:var(--qa-brand);">
                        <i class="bi bi-chat-text me-1"></i>Comments
                    </label>
                    <textarea id="{{ $sec }}_comments"
                              name="{{ $sec }}_comments"
                              rows="2"
                              class="form-control form-control-sm"
                              placeholder="Observations pour la section {{ $letter }}…">{{ old("{$sec}_comments", $record ? $record->{"{$sec}_comments"} : '') }}</textarea>
                </div>

            </div>{{-- /.section-card --}}
        @endforeach

        {{-- ── Sticky save bar ── --}}
        <div class="save-bar no-print">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    15 sections · 203 questions au total
                </span>
                <button type="submit" class="btn btn-save">
                    <i class="bi bi-floppy me-1"></i>Enregistrer le formulaire
                </button>
            </div>
        </div>

    </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Highlight active section pill based on scroll position
    const sections = document.querySelectorAll('[id^="section-"]');
    const pills    = document.querySelectorAll('.sec-pill');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const sec = entry.target.id.replace('section-', '');
                pills.forEach(p => p.classList.remove('active'));
                const pill = document.getElementById('pill-' + sec);
                if (pill) pill.classList.add('active');
            }
        });
    }, { rootMargin: '-30% 0px -65% 0px' });

    sections.forEach(s => observer.observe(s));

    // Smooth scroll for pill clicks
    pills.forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                const offset = 110; // height of sticky header + nav
                const top = target.getBoundingClientRect().top + window.scrollY - offset;
                window.scrollTo({ top, behavior: 'smooth' });
            }
        });
    });
</script>
</body>
</html>
