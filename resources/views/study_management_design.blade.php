    @extends('index-new')
    @section('title', 'Gestion Projet')


    {{-- <style>
    /* Style des onglets trapèze */
    .nav-tabs {
      border-bottom: none;
      justify-content: center;
      gap: 10px;
    }
    .nav-tabs .nav-link {
      border: none;
      color: #fff;
      font-weight: 500;
      padding: 12px 25px;
      clip-path: polygon(15% 0%, 85% 0%, 100% 100%, 0% 100%);
      transition: all 0.3s ease-in-out;
    }
    /* Couleurs des onglets inactifs */
    .nav-tabs .nav-link:nth-child(1) { background: #6c757d; }
    .nav-tabs .nav-link:nth-child(2) { background: #198754; }
    .nav-tabs .nav-link:nth-child(3) { background: #0d6efd; }
    .nav-tabs .nav-link:nth-child(4) { background: #fd7e14; }
    .nav-tabs .nav-link:nth-child(5) { background: #6610f2; }

    /* Couleurs des onglets actifs */
    .nav-tabs .nav-link.active:nth-child(1) { background: #495057; }
    .nav-tabs .nav-link.active:nth-child(2) { background: #157347; }
    .nav-tabs .nav-link.active:nth-child(3) { background: #0b5ed7; }
    .nav-tabs .nav-link.active:nth-child(4) { background: #dc6e0e; }
    .nav-tabs .nav-link.active:nth-child(5) { background: #520dc2; }

    /* Contenu */
    .tab-content {
      margin-top: 20px;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
  </style> --}}
    {{-- <style>
        .step-tabs {
            display: flex;
            margin: 30px auto;
            padding: 0;
            list-style: none;
        }

        .step-tabs li {
            position: relative;
            flex: 1;
            text-align: center;
        }

        .step-tabs li a {
            display: block;
            padding: 15px 20px;
            background: #198754;
            /* couleur par défaut */
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            position: relative;
            clip-path: polygon(0 0, 90% 0, 100% 50%, 90% 100%, 0 100%);
            transition: background 0.3s ease;
        }

        .step-tabs li a.active {
            background: #0d6efd;
            /* couleur active */
        }

        .step-tabs li:last-child a {
            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
        }

        .tab-content {
            margin-top: 20px;
            padding: 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style> --}}

    {{-- <style>
        .step-tabs {
            display: flex;
            margin: 30px auto;
            padding: 0;
            list-style: none;
        }

        .step-tabs li {
            position: relative;
            flex: 1;
        }

        .step-tabs li a {
            display: block;
            padding: 15px 25px;
            background: #198754;
            /* vert par défaut */
            color: #fff;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            position: relative;
            transition: background 0.3s ease;
        }

        /* Pointe à droite */
        .step-tabs li a::after {
            content: "";
            position: absolute;
            top: 0;
            right: -20px;
            width: 0;
            height: 0;
            border-top: 35px solid transparent;
            border-bottom: 35px solid transparent;
            border-left: 20px solid #198754;
            z-index: 1;
        }

        /* Pointe à gauche (fente arrière) */
        .step-tabs li:not(:first-child) a::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-top: 35px solid transparent;
            border-bottom: 35px solid transparent;
            border-left: 20px solid #fff;
            /* couleur de fond de la page */
            z-index: 2;
        }

        /* Actif */
        .step-tabs li a.active {
            background: #0d6efd;
            /* bleu */
        }

        .step-tabs li a.active::after {
            border-left-color: #0d6efd;
        }

        /* Dernier élément sans pointe droite */
        .step-tabs li:last-child a::after {
            display: none;
        }

        /* Contenu */
        .tab-content {
            margin-top: 20px;
            padding: 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style> --}}

    <style>
        .wizard {
            display: flex;
            margin: 30px auto;
            padding: 0;
            list-style: none;
        }

        .wizard li {
            position: relative;
            flex: 1;
        }

        .wizard li a {
            display: block;
            padding: 12px 18px;
            background: #f5b5b5;
            /* couleur inactive */
            color: #333;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            text-decoration: none;
            position: relative;
            transition: background 0.3s ease, color 0.3s ease;
            border-radius: 0;
        }

        /* Pointe à droite */
        .wizard li a::after {
            content: "";
            position: absolute;
            top: 0;
            right: -20px;
            width: 0;
            height: 0;
            border-top: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-left: 20px solid #f5b5b5;
            z-index: 1;
            transition: border-left-color 0.3s ease;
        }

        /* Pointe à gauche (fente arrière) */
        .wizard li:not(:first-child) a::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 0;
            border-top: 30px solid transparent;
            border-bottom: 30px solid transparent;
            border-left: 20px solid #fff;
            /* couleur fond */
            z-index: 2;
        }

        /* Actif */
        .wizard li a.active {
            background: #c20102;
            color: #fff;
        }

        .wizard li a.active::after {
            border-left-color: #c20102;
        }

        /* Dernier élément sans pointe */
        .wizard li:last-child a::after {
            display: none;
        }

        /* Contenu */
        .tab-content {
            margin-top: 25px;
            padding: 25px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        }

        .tab-pane h4 {
            color: #c20102;
            font-weight: bold;
        }
    </style>

    @section('content')
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-11">

                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <h2 class="h5">Liste des projets</h2>

                        <form action="">
                            <div class="input-group mb-3">

                                @php
                                    $project_id = request()->get('project_id');
                                @endphp
                                <select name="project_id" id="project_id"
                                    class="form-select form-control selectpicker show-tick" data-live-search="true">
                                    <option value="">Sélectionner un projet</option>
                                    @foreach ($all_projects as $proj)
                                        <option value="{{ $proj->id }}"
                                            {{ $project_id == $proj->id ? 'selected' : '' }}>{{ $proj->project_code }}
                                        </option>
                                    @endforeach
                                </select>

                                <button class="btn btn-outline-secondary" type="submit" id="button-search">Charger ce
                                    projet </button>

                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 border-top"></div>
                                    <span class="mx-2">ou</span>
                                    <div class="flex-grow-1 border-top"></div>
                                </div>

                                <a href="#" id="creer-nouveau-projet" class="btn btn-danger ms-3">Créer un nouveau
                                    projet</a>
                            </div>

                        </form>

                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h2 class="h5" style="color :  #c20102">Project Management Design for :
                            <span class=" ">
                                {{ $project ? $project->project_code . '(' . $project->project_title . ' )' : 'No project selected' }}
                            </span>
                        </h2>
                        <p>Use the tabs below to navigate through the different sections of the study management.</p>
                    </div>
                </div>


                <ul class="wizard" id="myTab" role="tablist">
                    <li><a class="active" id="step1-tab" data-bs-toggle="tab" href="#step1" role="tab">1. Study
                            Creation</a></li>
                    <li><a id="step2-tab" data-bs-toggle="tab" href="#step2" role="tab">2. Protocol Details</a></li>
                    <li><a id="step3-tab" data-bs-toggle="tab" href="#step3" role="tab">3. Protocol Development</a>
                    </li>
                    <li><a id="step4-tab" data-bs-toggle="tab" href="#step4" role="tab">4. Planning Phase</a></li>
                    <li><a id="step5-tab" data-bs-toggle="tab" href="#step5" role="tab">5. Experimental Phase</a></li>
                    <li><a id="step6-tab" data-bs-toggle="tab" href="#step6" role="tab">6. Data Analysis</a></li>
                    <li><a id="step7-tab" data-bs-toggle="tab" href="#step7" role="tab">7. Report Phase</a></li>
                    <li><a id="step8-tab" data-bs-toggle="tab" href="#step8" role="tab">8. Archiving Phase</a></li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="step1" role="tabpanel">
                        @include('study_creation_step')
                    </div>
                    <div class="tab-pane fade" id="step2" role="tabpanel">
                        <h4>Protocol Details</h4>
                        <p>Contenu de la deuxième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step3" role="tabpanel">
                        <h4>Protocol Development</h4>
                        <p>Contenu de la troisième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step4" role="tabpanel">
                        <h4>Planning Phase</h4>
                        <p>Contenu de la quatrième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step5" role="tabpanel">
                        <h4>Experimental Phase</h4>
                        <p>Contenu de la cinquième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step6" role="tabpanel">
                        <h4>Data Analysis</h4>
                        <p>Contenu de la sixième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step7" role="tabpanel">
                        <h4>Report Phase</h4>
                        <p>Contenu de la septième étape.</p>
                    </div>
                    <div class="tab-pane fade" id="step8" role="tabpanel">
                        <h4>Archiving Phase</h4>
                        <p>Contenu de la huitième étape.</p>
                    </div>
                </div>

            </div>

            @include('partials.dialog-create-project')
        @endsection

        @section('js')
            {{-- <script>
                (function() {
                    const form = document.getElementById('wizardForm');
                    const steps = [...document.querySelectorAll('.step')];
                    const nextBtn = document.getElementById('nextBtn');
                    const prevBtn = document.getElementById('prevBtn');
                    const submitBtn = document.getElementById('submitBtn');
                    const progressBar = document.getElementById('progressBar');
                    const circles = document.querySelectorAll('[data-step-circle]');
                    const lines = document.querySelectorAll('[data-step-line]');
                    const review = document.getElementById('review');

                    let current = 0; // index 0..n-1

                    const total = steps.length;
                    updateUI();

                    nextBtn.addEventListener('click', () => {
                        // Valider les champs requis de l'étape courante
                        if (!validateStep(steps[current])) return;
                        if (current < total - 1) {
                            current++;
                            // Remplir la section récap à l'entrée de la dernière étape
                            if (current === total - 1) fillReview();
                            updateUI();
                        }
                    });

                    prevBtn.addEventListener('click', () => {
                        if (current > 0) {
                            current--;
                            updateUI();
                        }
                    });

                    form.addEventListener('submit', (e) => {
                        // Validation globale au submit
                        if (!form.checkValidity()) {
                            e.preventDefault();
                            e.stopPropagation();
                            form.classList.add('was-validated');
                        }
                    });

                    function validateStep(stepEl) {
                        const requiredFields = stepEl.querySelectorAll('[required]');
                        let valid = true;
                        requiredFields.forEach(input => {
                            if (input.type === 'checkbox') {
                                if (!input.checked) {
                                    valid = false;
                                }
                            } else if (!input.value) {
                                valid = false;
                            } else if (input.type === 'email' && !/^\S+@\S+\.\S+$/.test(input.value)) {
                                valid = false;
                            }
                            if (!input.checkValidity()) valid = false;
                        });
                        stepEl.querySelectorAll('input, select, textarea').forEach(el => el.reportValidity());
                        return valid;
                    }

                    function updateUI() {
                        steps.forEach((s, i) => s.classList.toggle('active', i === current));
                        prevBtn.disabled = current === 0;
                        nextBtn.classList.toggle('d-none', current === total - 1);
                        submitBtn.classList.toggle('d-none', current !== total - 1);
                        // Progression (0%, 50%, 100% pour 3 étapes)
                        const pct = Math.round((current) / (total - 1) * 100);
                        progressBar.style.width = pct + '%';
                        progressBar.setAttribute('aria-valuenow', pct);
                        // Indicateurs
                        circles.forEach((c, i) => {
                            c.classList.remove('active', 'done', 'bg-secondary-subtle', 'text-secondary');
                            if (i < current) {
                                c.classList.add('done');
                            } else if (i === current) {
                                c.classList.add('active');
                            } else {
                                c.classList.add('bg-secondary-subtle', 'text-secondary');
                            }
                        });
                        lines.forEach((l, i) => {
                            l.classList.toggle('filled', i < current);
                        });
                    }

                    function fillReview() {
                        const data = new FormData(form);
                        const entries = {};
                        for (const [k, v] of data.entries()) {
                            entries[k] = v;
                        }
                        review.innerHTML = `
          <div class="row g-3 small">
            <div class="col-md-6"><strong>Prénom:</strong> ${escapeHtml(entries.first_name || '')}</div>
            <div class="col-md-6"><strong>Nom:</strong> ${escapeHtml(entries.last_name || '')}</div>
            <div class="col-md-6"><strong>Email:</strong> ${escapeHtml(entries.email || '')}</div>
            <div class="col-md-6"><strong>Téléphone:</strong> ${escapeHtml(entries.phone || '')}</div>
            <div class="col-md-6"><strong>Projet:</strong> ${escapeHtml(entries.project_name || '')}</div>
            <div class="col-md-6"><strong>Catégorie:</strong> ${escapeHtml(entries.category || '')}</div>
            <div class="col-md-6"><strong>Budget:</strong> ${escapeHtml(entries.budget || '')}</div>
            <div class="col-12"><strong>Description:</strong><br>${escapeHtml(entries.description || '')}</div>
          </div>`;
                    }

                    function escapeHtml(str) {
                        return str.replace(/[&<>"](/g, "_");
                    }
                })();
            </script> --}}
        @endsection
