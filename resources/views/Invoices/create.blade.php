@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            {{-- Cardul principal --}}
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">

                    {{-- 1. TITLUL PAGINII --}}
                    <h1 class="h3 mb-2 text-dark fw-light">
                        <i class="bi bi-receipt-cutoff me-2 text-primary"></i>
                        Adaugă Factură Manuală
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
                    {{-- Folosește ruta 'projects.invoices.store' conform fișierului tău de rute --}}
                    <form action="{{ route('projects.invoices.store', $project) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="file_name" class="form-label fw-semibold">Nume fișier / descriere *</label>
                            <input
                                type="text"
                                name="file_name"
                                id="file_name"
                                class="form-control"
                                placeholder="ex: Factură materiale Landefeld 18167141"
                                value="{{ old('file_name') }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="drive_file_id" class="form-label fw-semibold">ID fișier Google Drive (opțional)</label>
                            <input
                                type="text"
                                name="drive_file_id"
                                id="drive_file_id"
                                class="form-control"
                                placeholder="ex: 1xXt8WXA-zQ_yTMV4PpedQkHljl-Ae0Me"
                                value="{{ old('drive_file_id') }}"
                            >
                        </div>

                        <hr class="my-4 opacity-50">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label fw-semibold">Sumă *</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    name="amount"
                                    id="amount"
                                    class="form-control"
                                    value="{{ old('amount') }}"
                                    placeholder="0.00"
                                    required
                                >
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="currency" class="form-label fw-semibold">Monedă *</label>
                                <select name="currency" id="currency" class="form-select" required>
                                    <option value="CHF" {{ old('currency', 'CHF') === 'CHF' ? 'selected' : '' }}>CHF</option>
                                    <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="RON" {{ old('currency') === 'RON' ? 'selected' : '' }}>RON</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label fw-semibold">Categorie *</label>
                            <select name="category" id="category" class="form-select" required>
                                <option value="misc" {{ old('category') === 'misc' ? 'selected' : '' }}>Misc / Nedefinit</option>
                                <option value="safety" {{ old('category') === 'safety' ? 'selected' : '' }}>Safety Systems</option>
                                <option value="painting_equipment" {{ old('category') === 'painting_equipment' ? 'selected' : '' }}>Painting Equipment</option>
                                <option value="robot" {{ old('category') === 'robot' ? 'selected' : '' }}>Robot System</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Status *</label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="approved" {{ old('status', 'approved') === 'approved' ? 'selected' : '' }}>Aprobată</option>
                                <option value="pending" {{ old('status') === 'pending' ? 'selected' : '' }}>În așteptare</option>
                                <option value="rejected" {{ old('status') === 'rejected' ? 'selected' : '' }}>Respinsă</option>
                            </select>
                        </div>

                        <hr class="mt-4 mb-4 opacity-50">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-save me-1"></i> Salvează factura
                            </button>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-light btn-lg rounded-pill px-4 border">
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