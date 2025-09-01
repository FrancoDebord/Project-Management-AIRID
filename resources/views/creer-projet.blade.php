@extends('index-new')

@section('title', 'Creer Projet')


@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4 text-center">Créer un nouveau projet</h4>

                    {{-- Formulaire --}}
                    <form method="POST" action="{{ route('project.store') }}">
                        @csrf

                        {{-- Code projet --}}
                        <div class="mb-3">
                            <label for="code" class="form-label">Code du projet</label>
                            <input type="text" id="code" name="code"
                                class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}"
                                required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Titre projet --}}
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre du projet</label>
                            <input type="text" id="title" name="title"
                                class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}"
                                required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- GLP ? (Select au lieu de checkbox) --}}
                        <div class="mb-3">
                            <label for="is_glp" class="form-label">Projet GLP</label>
                            <select id="is_glp" name="is_glp" class="form-select @error('is_glp') is-invalid @enderror"
                                required>
                                <option value="0" {{ old('is_glp') == '0' ? 'selected' : '' }}>Non</option>
                                <option value="1" {{ old('is_glp') == '1' ? 'selected' : '' }}>Oui</option>
                            </select>
                            @error('is_glp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Boutons --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
