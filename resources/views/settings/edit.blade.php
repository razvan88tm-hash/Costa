@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-9">

            {{-- CARD PRINCIPAL "Apple-like" --}}
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">

                    {{-- 1. TITLUL PAGINII --}}
                    <h1 class="h3 mb-2 text-dark fw-light">
                        <i class="bi bi-gear-fill me-2 text-primary"></i>
                        Setări Globale
                    </h1>
                    <p class="text-muted mb-4">Ajustați tarifele de calcul și setările de integrare ale sistemului.</p>

                    {{-- ALERT SUCCES --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 mb-4" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- 3. FORMULARUL --}}
                    <form action="{{ route('settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- SECTIUNEA 1: COSTURI MUNCITORI --}}
                        <div class="form-section mb-5">
                            <h5 class="fw-semibold text-dark-emphasis mb-1">
                                <i class="bi bi-person-workspace me-2 text-primary"></i>
                                Costuri Muncitori (Pauschal)
                            </h5>
                            <p class="small text-muted mb-3">Tarife interne folosite pentru calcul automat.</p>
                            <hr class="mt-0 mb-4 opacity-50">

                            {{-- Salariu lunar --}}
                            <div class="mb-3">
                                <label for="monthly_salary" class="form-label fw-semibold">Base Cost (Salariu Lunar)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-bank"></i></span>
                                    <input type="number" step="0.01" name="monthly_salary" id="monthly_salary"
                                           class="form-control"
                                           value="{{ $settings->monthly_salary }}">
                                    <span class="input-group-text">CHF / lună</span>
                                </div>
                                <small class="text-muted">Folosit pentru a calcula tariful orar (160h/lună).</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="per_diem" class="form-label fw-semibold">Per Diem (Diurnă)</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-wallet2"></i></span>
                                        <input type="number" step="0.01" name="per_diem" id="per_diem"
                                               class="form-control"
                                               value="{{ $settings->per_diem }}">
                                        <span class="input-group-text">CHF / zi</span>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="hotel_night" class="form-label fw-semibold">Hotel Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-building"></i></span>
                                        <input type="number" step="0.01" name="hotel_night" id="hotel_night"
                                               class="form-control"
                                               value="{{ $settings->hotel_night }}">
                                        <span class="input-group-text">CHF / noapte</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Transport --}}
                            <div class="mb-3">
                                <label for="transport_per_km" class="form-label fw-semibold">Transport Cost per Km</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-truck"></i></span>
                                    <input type="number" step="0.01" name="transport_per_km" id="transport_per_km"
                                           class="form-control"
                                           value="{{ $settings->transport_per_km }}">
                                    <span class="input-group-text">CHF / km</span>
                                </div>
                            </div>
                        </div>


                        {{-- SECTIUNEA 2: GOOGLE DRIVE --}}
                        <div class="form-section mb-5">
                            <h5 class="fw-semibold text-dark-emphasis mb-1">
                                <i class="bi bi-cloud-arrow-down-fill me-2 text-primary"></i>
                                Integrare Google Drive
                            </h5>
                            <p class="small text-muted mb-3">Configurări necesare pentru sincronizarea automată.</p>
                            <hr class="mt-0 mb-4 opacity-50">

                            <div class="alert alert-info shadow-sm small border-0 rounded-3">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                Aici setezi ID-urile pentru conectarea la API-ul Google Drive și Document AI.
                            </div>

                            {{-- Folder proiecte --}}
                            <div class="mb-3">
                                <label for="drive_projects_folder_id" class="form-label fw-semibold">
                                    ID Folder Sursă Proiecte (Google Drive)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-folder2-open"></i></span>
                                    <input type="text" name="drive_projects_folder_id" id="drive_projects_folder_id"
                                           class="form-control"
                                           value="{{ $settings->drive_projects_folder_id }}"
                                           placeholder="1aBcD_..._eFgH">
                                </div>
                                <small class="text-muted">ID-ul după <code>/folders/</code> din URL-ul Drive.</small>
                            </div>

                            {{-- Document AI Processor --}}
                            <div class="mb-3">
                                <label for="doc_ai_processor_id" class="form-label fw-semibold">
                                    Google Document AI – Processor ID
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-cpu"></i></span>
                                    <input type="text" name="doc_ai_processor_id" id="doc_ai_processor_id"
                                           class="form-control"
                                           value="{{ $settings->doc_ai_processor_id }}"
                                           placeholder="a1b2c3d4e5f6...">
                                </div>
                                <small class="text-muted">Din Google Cloud → Document AI → Processors.</small>
                            </div>
                        </div>

                        {{-- BUTOANE ACȚIUNE --}}
                        <hr class="mt-5 mb-4 opacity-50">
                        <div class="d-flex gap-3 justify-content-end">
                            <a href="{{ route('projects.index') }}" class="btn btn-light btn-lg rounded-pill px-4 border">
                                <i class="bi bi-x-lg"></i> Anulează
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-save me-2"></i> Salvează Setările
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection