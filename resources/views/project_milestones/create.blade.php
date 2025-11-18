@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8"> {{-- Un col-md-8 este suficient aici --}}

            {{-- Cardul principal cu estetica Apple --}}
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">

                    {{-- 1. TITLUL PAGINII --}}
                    <h1 class="h3 mb-2 text-dark fw-light">
                        <i class="bi bi-flag me-2 text-primary"></i>
                        Adaugă Milestone
                    </h1>
                    <p class="text-muted mb-4">
                        Pentru proiectul: <strong>{{ $project->name }}</strong>
                    </p>

                    {{-- 2. BLOCUL DE ERORI --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <h5 class="alert-heading fw-semibold mb-1">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                A apărut o problemă
                            </h5>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- 3. FORMULARUL --}}
                    <form action="{{ route('project-milestones.store', $project) }}" method="POST">
                        @csrf
                        
                        {{-- SECȚIUNEA 1: DETALII MILESTONE --}}
                        <div class="form-section mb-4">
                            <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-info-circle me-2"></i>Detalii Milestone</h5>
                            <p class="text-muted small mb-3">Alegeți tipul și denumirea milestone-ului.</p>
                            <hr class="mt-0 mb-4 opacity-50">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="type" class="form-label fw-semibold">Tip milestone *</label>
                                    <select name="type" id="type" class="form-select" required>
                                        <option value="pm" {{ old('type') == 'pm' ? 'selected' : '' }}>PM milestone (Managerial)</option>
                                        <option value="installation" {{ old('type') == 'installation' ? 'selected' : '' }}>Installation milestone (Site)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="preset_select" class="form-label fw-semibold">Alege Preset (opțional)</label>
                                    <select id="preset_select" class="form-select">
                                        {{-- Populat de JS --}}
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Nume milestone *</label>
                                <input type="text" name="name" id="name" class="form-control"
                                       value="{{ old('name') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label fw-semibold">Descriere</label>
                                <textarea name="description" id="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        {{-- SECȚIUNEA 2: PLANIFICARE & STATUS --}}
                        <div class="form-section mb-4">
                            <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-calendar-check me-2"></i>Planificare & Status</h5>
                            <p class="text-muted small mb-3">Setați datele și starea curentă.</p>
                            <hr class="mt-0 mb-4 opacity-50">

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="planned_date" class="form-label fw-semibold">Data planificată</label>
                                    <input type="date" name="planned_date" id="planned_date" class="form-control"
                                           value="{{ old('planned_date') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="actual_date" class="form-label fw-semibold">Data realizată</label>
                                    <input type="date" name="actual_date" id="actual_date" class="form-control"
                                           value="{{ old('actual_date') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="status" class="form-label fw-semibold">Status *</label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="not_started" {{ old('status', 'not_started') == 'not_started' ? 'selected' : '' }}>Not started</option>
                                        <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In progress</option>
                                        <option value="done" {{ old('status') == 'done' ? 'selected' : '' }}>Done</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- SECȚIUNEA 3: ORDINE --}}
                        <div class="form-section mb-4">
                            <label for="sort_order" class="form-label fw-semibold">Ordine de afișare</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control" min="0"
                                   value="{{ old('sort_order', 0) }}" style="max-width: 120px;">
                            <small class="text-muted">Un număr mai mic apare primul (ex: 0, 1, 2...).</small>
                        </div>

                        {{-- SECȚIUNEA 4: BUTOANE ACȚIUNE --}}
                        <hr class="mt-5 mb-4 opacity-50">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-save me-1"></i> Salvează Milestone
                            </button>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-light btn-lg rounded-pill px-4 border">
                                <i class="bi bi-x-lg"></i> Renunță
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- 
    IMPORTANT: 
    Am mutat scriptul aici, folosind @push. 
    Layout-ul (layouts.app) trebuie să aibă @stack('scripts') înainte de </body>
--}}
@push('scripts')
<script>
    // Așteptăm ca documentul să fie gata
    document.addEventListener("DOMContentLoaded", function() {

        const presets = {
            pm: [
                'Make BOM',
                'Cumpără materialele',
                'Organizare transport (robot / echipamente)',
                'Informează transportul',
                'Planifică instalarea și timpul ei',
                'Site readiness check',
                'After installation management'
            ],
            installation: [
                'Mechanical installation',
                'Electrical installation',
                'Școlarizare client',
                'Însoțirea producției',
                'Predare proiect'
            ]
        };

        const typeSelect   = document.getElementById('type');
        const presetSelect = document.getElementById('preset_select');
        const nameInput    = document.getElementById('name');

        function reloadPresets() {
            if (!typeSelect || !presetSelect) return; // Verificare siguranță

            const type = typeSelect.value || 'pm';
            const options = presets[type] || [];

            // curățăm lista
            presetSelect.innerHTML = '';
            const emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = '-- Alege din listă sau scrie manual --';
            presetSelect.appendChild(emptyOpt);

            // adăugăm opțiunile
            options.forEach(label => {
                const opt = document.createElement('option');
                opt.value = label;
                opt.textContent = label;
                presetSelect.appendChild(opt);
            });
        }

        if (typeSelect && presetSelect && nameInput) {
            // Încărcăm presetările inițiale
            reloadPresets();

            // Adăugăm event listener pentru schimbarea tipului
            typeSelect.addEventListener('change', () => {
                reloadPresets();
                presetSelect.value = ''; // Resetăm selecția preset
            });

            // Adăugăm event listener pentru selectarea unui preset
            presetSelect.addEventListener('change', () => {
                if (presetSelect.value) {
                    nameInput.value = presetSelect.value;
                }
            });
        }
    });
</script>
@endpush