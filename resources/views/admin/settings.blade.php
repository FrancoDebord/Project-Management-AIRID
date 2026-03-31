@extends('index-new')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-0" style="color:#c20102;">
            <i class="bi bi-gear-fill me-2"></i>Paramètres de l'application
        </h4>
        <p class="text-muted small mb-0">Configurez les valeurs partagées par tous les documents et modules.</p>
    </div>
    <a href="{{ route('indexPage') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Retour
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 py-2 px-3" role="alert">
    <i class="bi bi-check-circle-fill"></i>
    {{ session('success') }}
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    @foreach($grouped as $groupKey => $settings)
    @php $meta = $groupLabels[$groupKey] ?? ['label' => ucfirst($groupKey), 'icon' => 'bi-sliders']; @endphp

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-0 d-flex align-items-center gap-2 py-3"
             style="background:linear-gradient(90deg,#c20102,#8b0001);color:#fff;border-radius:.5rem .5rem 0 0;">
            <i class="bi {{ $meta['icon'] }} fs-5"></i>
            <span class="fw-semibold">{{ $meta['label'] }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($settings as $setting)
                <div class="col-md-6">
                    <label class="form-label fw-semibold small mb-1" for="setting_{{ $setting->key }}">
                        {{ $setting->label }}
                    </label>
                    @if($setting->type === 'textarea')
                        <textarea class="form-control form-control-sm"
                                  id="setting_{{ $setting->key }}"
                                  name="settings[{{ $setting->key }}]"
                                  rows="3">{{ old('settings.'.$setting->key, $setting->value) }}</textarea>
                    @else
                        <input type="{{ $setting->type }}"
                               class="form-control form-control-sm"
                               id="setting_{{ $setting->key }}"
                               name="settings[{{ $setting->key }}]"
                               value="{{ old('settings.'.$setting->key, $setting->value) }}">
                    @endif
                    @if($setting->description)
                    <div class="form-text text-muted" style="font-size:.75rem;">{{ $setting->description }}</div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('indexPage') }}" class="btn btn-outline-secondary">Annuler</a>
        <button type="submit" class="btn px-4 fw-semibold" style="background:#c20102;color:#fff;border:none;">
            <i class="bi bi-save me-1"></i>Enregistrer les paramètres
        </button>
    </div>
</form>
@endsection
