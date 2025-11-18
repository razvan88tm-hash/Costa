@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-9">

            {{-- Cardul principal cu estetica Apple --}}
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">

                    {{-- 1. TITLUL PAGINII --}}
                    <h1 class="h3 mb-2 text-dark fw-light">
                        <i class="bi bi-pencil me-2 text-primary"></i>
                        Editează Cost Material
                    </h1>
                    <p class="text-muted mb-4">
                        Ajustezi valorile pentru: <strong>{{ $costItem->line_item ?? 'Articol Fără Nume' }}</strong>
                        <br>
                        <small>Proiect: {{ $project->name }}</small>
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
                    <form action="{{ route('project-cost-items.update', [$project, $costItem]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        {{-- SECȚIUNEA 1: DETALII ARTICOL --}}
                        <div class="form-section mb-4">
                            <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-tags me-2"></i>Detalii Articol</h5>
                            <p class="text-muted small mb-3">Modifică categoria și descrierea costului.</p>
                            <hr class="mt-0 mb-4 opacity-50">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label fw-semibold">Categorie</label>
                                    <input type="text" name="category" id="category" value="{{ old('category', $costItem->category) }}" class="form-control" placeholder="Ex: Robot, Safety, Logistics">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="line_item" class="form-label fw-semibold">Articol (Line item)</label>
                                    <input type="text" name="line_item" id="line_item" value="{{ old('line_item', $costItem->line_item) }}" class="form-control" placeholder="Ex: Senzor laser Sick">
                                </div>
                            </div>
                        </div>

                        {{-- SECȚIUNEA 2: SUME FINANCIARE --}}
                        <div class="form-section mb-4">
                            <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-calculator me-2"></i>Sumar Financiar (CHF)</h5>
                            <p class="text-muted small mb-3">Ajustează sumele alocate pentru acest articol.</p>
                            <hr class="mt-0 mb-4 opacity-50">

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="planned_budget" class="form-label fw-semibold">Planned Budget</label>
                                    <input type="number" step="0.01" min="0" name="planned_budget" id="planned_budget" value="{{ old('planned_budget', $costItem->planned_budget) }}" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="committed_po" class="form-label fw-semibold">Committed PO</label>
                                    <input type="number" step="0.01" min="0" name="committed_po" id="committed_po" value="{{ old('committed_po', $costItem->committed_po) }}" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="actual_cost" class="form-label fw-semibold">Actual Cost</label>
                                    <input type="number" step="0.01" min="0" name="actual_cost" id="actual_cost" value="{{ old('actual_cost', $costItem->actual_cost) }}" class="form-control" placeholder="0.00">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="forecast_to_complete" class="form-label fw-semibold">Forecast to complete</label>
                                    <input type="number" step="0.01" min="0" name="forecast_to_complete" id="forecast_to_complete" value="{{ old('forecast_to_complete', $costItem->forecast_to_complete) }}" class="form-control" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        {{-- SECȚIUNEA 3: NOTE --}}
                        <div class="form-section mb-4">
                             <h5 class="fw-semibold text-dark-emphasis mb-1"><i class="bi bi-pencil-square me-2"></i>Note</h5>
                            <p class="text-muted small mb-3">Orice observații suplimentare.</p>
                            <hr class="mt-0 mb-4 opacity-50">
                            
                            <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes', $costItem->notes) }}</textarea>
                        </div>

                        {{-- SECȚIUNEA 4: BUTOANE ACȚIUNE --}}
                        <hr class="mt-5 mb-4 opacity-50">
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-4">
                                <i class="bi bi-arrow-up-circle me-1"></i> Actualizează Cost
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