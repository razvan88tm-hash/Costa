@extends('layouts.app')

@section('content')
<div class="container my-5"> {{-- Adaugă spațiu vertical (sus și jos) --}}
    <div class="row justify-content-center">
        <div class="col-md-9"> {{-- Am redus puțin lățimea pentru un focus mai bun --}}

            {{-- Cardul principal cu estetica Apple --}}
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5"> {{-- Padding mai generos --}}

                    {{-- 1. TITLUL PAGINII --}}
                    <h1 class="h3 mb-2 text-dark fw-light">
                        <i class="bi bi-plus-circle me-2 text-primary"></i>
                        Creare Proiect Nou
                    </h1>
                    <p class="text-muted mb-4">Completați detaliile de mai jos pentru a înregistra un nou proiect.</p>

                    {{-- 2. BLOCUL DE ERORI --}}
                    @if ($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <h5 class="alert-heading fw-semibold mb-1">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                A apărut o problemă
                            </h5>
                            <p class="mb-2">Vă rugăm să corectați erorile de mai jos:</p>
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li> {{-- Am scos cratima, e redundantă --}}
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- 3. FORMULARUL --}}
                    <form action="{{ route('projects.store') }}" method="POST">
                        @csrf
                        
                        {{-- SECȚIUNEA 1: DETALII --}}
                        <div class="form-section mb-5">
                            <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-folder2-open me-2"></i>Detalii Proiect</h5>
                            <p class="text-muted small mb-3">Informațiile de bază pentru identificarea proiectului.</p>
                            <hr class="mt-0 mb-4 opacity-50">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="project_number" class="form-label fw-semibold">Număr proiect</label>
                                    <input
                                        type="text"
                                        name="project_number"
                                        id="project_number"
                                        class="form-control"
                                        value="{{ old('project_number') }}"
                                        placeholder="ex: 2412"
                                    >
                                </div>

                                <div class="col-md-8 mb-3">
                                    <label for="client_name" class="form-label fw-semibold">Client</label>
                                    <input
                                        type="text"
                                        name="client_name"
                                        id="client_name"
                                        class="form-control"
                                        value="{{ old('client_name') }}"
                                        placeholder="ex: Kleymann"
                                    >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label fw-semibold">Project Name *</label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control"
                                    value="{{ old('name') }}"
                                    required
                                >
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="customer_site" class="form-label fw-semibold">Customer / Site</label>
                                    <input
                                        type="text"
                                        name="customer_site"
                                        id="customer_site"
                                        class="form-control"
                                        value="{{ old('customer_site') }}"
                                        placeholder="Descriere scurtă"
                                    >
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label fw-semibold">Location</label>
                                    <input
                                        type="text"
                                        name="location"
                                        id="location"
                                        class="form-control"
                                        value="{{ old('location') }}"
                                        placeholder="City, Country"
                                    >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="pm_name" class="form-label fw-semibold">Project Manager</label>
                                <input
                                    type="text"
                                    name="pm_name"
                                    id="pm_name"
                                    class="form-control"
                                    value="{{ old('pm_name') }}"
                                >
                            </div>
                        </div>

                        {{-- SECȚIUNEA 2: STATUS --}}
                        <div class="form-section mb-5">
                            <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-clipboard-data me-2"></i>Status & Progres</h5>
                            <p class="text-muted small mb-3">Definiți starea curentă și progresul proiectului.</p>
                            <hr class="mt-0 mb-4 opacity-50">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="overall_rag" class="form-label fw-semibold">ROG Status *</label>
                                    <select name="overall_rag" id="overall_rag" class="form-select" required>
                                        <option value="" disabled selected>-- Alege Status --</option>
                                        <option value="Red" {{ old('overall_rag') == 'Red' ? 'selected' : '' }}>Red (Problematic)</option>
                                        <option value="Orange" {{ old('overall_rag') == 'Orange' ? 'selected' : '' }}>Orange (Atenție)</option>
                                        <option value="Green" {{ old('overall_rag') == 'Green' ? 'selected' : '' }}>Green (OK)</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="percent_complete" class="form-label fw-semibold">% Complete (Manual)</label>
                                    <div class="input-group">
                                        <input
                                            type="number"
                                            name="percent_complete"
                                            id="percent_complete"
                                            class="form-control"
                                            value="{{ old('percent_complete') }}"
                                            min="0"
                                            max="100"
                                            step="0.01"
                                            placeholder="0"
                                        >
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <small class="text-muted">Se va actualiza automat din Milestones.</small>
                                </div>
                            </div>
                        </div>

                        {{-- SECȚIUNEA 3: DATE --}}
                        <div class="form-section mb-5">
                            <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-calendar-check me-2"></i>Date & Handover</h5>
                            <p class="text-muted small mb-3">Datele cheie ale proiectului.</p>
                            <hr class="mt-0 mb-4 opacity-50">
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="start_date" class="form-label fw-semibold">Start Date</label>
                                    <input
                                        type="date"
                                        name="start_date"
                                        id="start_date"
                                        class="form-control"
                                        value="{{ old('start_date') }}"
                                    >
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="target_handover" class="form-label fw-semibold">Target Handover</label>
                                    <input
                                        type="date"
                                        name="target_handover"
                                        id="target_handover"
                                        class="form-control"
                                        value="{{ old('target_handover') }}"
                                    >
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="real_handover" class="form-label fw-semibold">Real Handover</label>
                                    <input
                                        type="date"
                                        name="real_handover"
                                        id="real_handover"
                                        class="form-control"
                                        value="{{ old('real_handover') }}"
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- SECȚIUNEA 4: FINANȚE & DOCUMENTE --}}
                        <div class="form-section mb-5">
                            <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-archive me-2"></i>Finanțe & Documente</h5>
                            <p class="text-muted small mb-3">Informații financiare și link-uri către documentație.</p>
                            <hr class="mt-0 mb-4 opacity-50">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sell_price" class="form-label fw-semibold">Sell Price (CHF)</label>
                                    <input
                                        type="number"
                                        name="sell_price"
                                        id="sell_price"
                                        class="form-control"
                                        value="{{ old('sell_price') }}"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                    >
                                    <small class="text-muted">Poate fi extras automat din Drive.</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="planned_budget" class="form-label fw-semibold">Planned Budget (CHF)</label>
                                    <input
                                        type="number"
                                        name="planned_budget"
                                        id="planned_budget"
                                        class="form-control"
                                        value="{{ old('planned_budget') }}"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                    >
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="drive_folder_url" class="form-label fw-semibold">Link folder proiect (Google Drive)</label>
                                <input
                                    type="url"
                                    name="drive_folder_url"
                                    id="drive_folder_url"
                                    class="form-control"
                                    value="{{ old('drive_folder_url') }}"
                                    placeholder="https://drive.google.com/..."
                                >
                                <small class="text-muted">Link-ul către folderul '2412_Kleymann'.</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="layout_file_url" class="form-label fw-semibold">Link layout (PDF din 03_Layout)</label>
                                <input
                                    type="url"
                                    name="layout_file_url"
                                    id="layout_file_url"
                                    class="form-control"
                                    value="{{ old('layout_file_url') }}"
                                    placeholder="https://drive.google.com/..."
                                >
                            </div>
                        </div>

                        {{-- SECȚIUNEA 5: BUTOANE ACȚIUNE --}}
                        <hr class="mt-5 mb-4 opacity-50">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-save me-1"></i> Salvează Proiect
                            </button>
                            {{-- Buton de anulare mai subtil --}}
                            <a href="{{ route('projects.index') }}" class="btn btn-light btn-lg rounded-pill px-4 border">
                                <i class="bi bi-x-lg"></i> Anulează
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection