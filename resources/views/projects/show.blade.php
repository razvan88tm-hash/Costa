@extends('layouts.app')

@section('content')
<div class="container my-5">

    {{-- 1. HEADER-UL PAGINII DE PROIECT --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div>
            {{-- Titlul proiectului --}}
            <h1 class="h3 mb-0 text-dark fw-light">
                <i class="bi bi-folder2-open me-2 text-primary"></i>
                {{ $project->name }}
            </h1>
            {{-- Sub-titlu cu ID și Client --}}
            <p class="text-muted mb-0">
                {{ $project->project_number }} / {{ $project->client_name }}
            </p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            {{-- Butoane de acțiune principale --}}
            <a href="{{ route('projects.edit', $project) }}" class="btn btn-light rounded-pill px-4 border">
                <i class="bi bi-pencil me-1"></i> Editare Proiect
            </a>
            <a href="{{ route('projects.index') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-arrow-left me-1"></i> Înapoi la Listă
            </a>
        </div>
    </div>

    {{-- STRUCTURA DASHBOARD (Coloană principală + Coloană laterală) --}}
    <div class="row g-4">

        {{-- ============================================= --}}
        {{-- COLOANA PRINCIPALĂ (Stânga) - Milestones, Costuri --}}
        {{-- ============================================= --}}
        <div class="col-lg-8">
            
  {{-- CARD: DETALII CHEIE --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold text-dark-emphasis mb-3">Detalii Cheie</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><small class="text-muted d-block text-uppercase">Project Manager</small><div class="fw-semibold">{{ $project->pm_name ?? '-' }}</div></li>
                        <li class="mb-2"><small class="text-muted d-block text-uppercase">Locație</small><div class="fw-semibold">{{ $project->location ?? '-' }}</div></li>
                        <li class="mb-2"><small class="text-muted d-block text-uppercase">Start Date</small><div class="fw-semibold">{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d M Y') : '-' }}</div></li>
                        <li class="mb-2"><small class="text-muted d-block text-uppercase">Target Handover</small><div class="fw-semibold">{{ $project->target_handover ? \Carbon\Carbon::parse($project->target_handover)->format('d M Y') : '-' }}</div></li>
                        <li class="mb-2"><small class="text-muted d-block text-uppercase">Real Handover</small><div class="fw-semibold">{{ $project->real_handover ? \Carbon\Carbon::parse($project->real_handover)->format('d M Y') : '-' }}</div></li>
                    </ul>
                </div>
            </div>

            {{-- CARD: MILESTONES --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                {{-- Antet card --}}
                <div class="card-header bg-white border-0 rounded-top-4 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold text-dark-emphasis mb-0">
                        <i class="bi bi-flag me-2 text-primary"></i>Milestones
                    </h5>
                    {{-- Folosește ruta 'project-milestones.create' --}}
                    <a href="{{ route('project-milestones.create', $project) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-plus-lg"></i> Adaugă
                    </a>
                </div>
                {{-- Corp card (tabel) --}}
                <div class="card-body p-0">
                    @if($project->milestones->isEmpty())
                        <p class="text-center text-muted p-4 mb-0">Nu există milestones definite pentru acest proiect.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Nume</th>
                                        <th>Status</th>
                                        <th>Dată Planificată</th>
                                        <th>Dată Realizată</th>
                                        <th class="text-end px-4">Acțiuni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->milestones->sortBy('sort_order') as $milestone)
                                        @php
                                            $statusClass = match($milestone->status) {
                                                'done' => 'bg-success-subtle text-success-emphasis',
                                                'in_progress' => 'bg-primary-subtle text-primary-emphasis',
                                                default => 'bg-secondary-subtle text-secondary-emphasis',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="px-4 fw-semibold">{{ $milestone->name }}</td>
                                            <td><span class="badge {{ $statusClass }} rounded-pill">{{ $milestone->status }}</span></td>
                                            <td>{{ $milestone->planned_date }}</td>
                                            <td>{{ $milestone->actual_date }}</td>
                                            <td class="text-end px-4">
                                                <a href="{{ route('project-milestones.edit', [$project, $milestone]) }}" class="btn btn-sm btn-light border" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- CARD: COSTURI MATERIALE --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 rounded-top-4 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold text-dark-emphasis mb-0">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Costuri Materiale
                    </h5>
                    {{-- Folosește ruta 'project-cost-items.create' --}}
                    <a href="{{ route('project-cost-items.create', $project) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-plus-lg"></i> Adaugă
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($project->costItems->isEmpty())
                        <p class="text-center text-muted p-4 mb-0">Nu există costuri materiale adăugate.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Articol</th>
                                        <th>Categorie</th>
                                        <th>Buget Planificat</th>
                                        <th>Cost Actual</th>
                                        <th class="text-end px-4">Acțiuni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->costItems as $item)
                                    <tr>
                                        <td class="px-4 fw-semibold">{{ $item->line_item }}</td>
                                        <td>{{ $item->category }}</td>
                                        <td>{{ number_format($item->planned_budget, 2) }} CHF</td>
                                        <td class="fw-bold">{{ number_format($item->actual_cost, 2) }} CHF</td>
                                        <td class="text-end px-4">
                                            <a href="{{ route('project-cost-items.edit', [$project, $item]) }}" class="btn btn-sm btn-light border" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- CARD: COSTURI MUNCITORI (Worker Logs) --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 rounded-top-4 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold text-dark-emphasis mb-0">
                        <i class="bi bi-person-gear me-2 text-primary"></i>Costuri Muncitori
                    </h5>
                    {{-- Folosește ruta 'project-worker-logs.create' --}}
                    <a href="{{ route('project-worker-logs.create', $project) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-plus-lg"></i> Adaugă
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($project->workerLogs->isEmpty())
                        <p class="text-center text-muted p-4 mb-0">Nu există log-uri de muncitori adăugate.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Nume</th>
                                        <th>Ore Lucrate</th>
                                        <th>Zile Șantier</th>
                                        <th>Km Transport</th>
                                        <th>Cost Total (Calculat)</th>
                                        <th class="text-end px-4">Acțiuni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->workerLogs as $log)
                                    <tr>
                                        <td class="px-4 fw-semibold">{{ $log->worker_name ?? 'N/A' }}</td>
                                        <td>{{ $log->hours_on_site }} ore</td>
                                        <td>{{ $log->days_on_site }} zile</td>
                                        <td>{{ $log->transport_km }} km</td>
                                        <td class="fw-bold">{{ number_format($log->calculated_total_cost, 2) }} CHF</td>
                                        <td class="text-end px-4">
                                            <a href="{{ route('project-worker-logs.edit', [$project, $log]) }}" class="btn btn-sm btn-light border" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- CARD: FACTURI --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 rounded-top-4 p-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-semibold text-dark-emphasis mb-0">
                        <i class="bi bi-receipt-cutoff me-2 text-primary"></i>Facturi
                    </h5>
                    {{-- CORECTAT: Folosește ruta 'projects.invoices.create' (așa cum e în fișierul tău) --}}
                    <a href="{{ route('projects.invoices.create', $project) }}" class="btn btn-primary btn-sm rounded-pill px-3">
                        <i class="bi bi-plus-lg"></i> Adaugă
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($project->invoices->isEmpty())
                        <p class="text-center text-muted p-4 mb-0">Nu există facturi asociate.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-borderless table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="px-4">Nume/Descriere</th>
                                        <th>Categorie</th>
                                        <th>Status</th>
                                        <th>Suma</th>
                                        <th class="text-end px-4">Acțiuni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->invoices as $invoice)
                                    <tr>
                                        <td class="px-4 fw-semibold">{{ $invoice->file_name }}</td>
                                        <td>{{ $invoice->category }}</td>
                                        <td>{{ $invoice->status }}</td>
                                        <td class="fw-bold">{{ number_format($invoice->amount, 2) }} {{ $invoice->currency }}</td>
                                        <td class="text-end px-4">
                                            {{-- TODO: Adaugă link-uri de edit/delete pentru facturi --}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ============================================= --}}
        {{-- COLOANA LATERALĂ (Dreapta) - Info Cheie, Status --}}
        {{-- ============================================= --}}
        <div class="col-lg-4">
            
            {{-- CARD: STATUS & PROGRES --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold text-dark-emphasis mb-3">Status General</h5>
                    {{-- Badge Status RAG --}}
                    @php 
                        $ragClass = 'bg-secondary-subtle text-secondary-emphasis';
                        if ($project->overall_rag == 'Green') $ragClass = 'bg-success-subtle text-success-emphasis';
                        if ($project->overall_rag == 'Red') $ragClass = 'bg-danger-subtle text-danger-emphasis';
                        if ($project->overall_rag == 'Orange') $ragClass = 'bg-warning-subtle text-warning-emphasis';
                    @endphp
                    <span class="badge {{ $ragClass }} fs-6 rounded-pill mb-3 px-3 py-2">{{ $project->overall_rag }}</span>
                    
                    <h6 class="fw-semibold small mt-2">Progres Proiect</h6>
                    @php $progress = $project->calculated_percent_complete ?? $project->percent_complete ?? 0; @endphp
                    <div class="progress rounded-pill" style="height: 10px;">
                        <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $progress }}%" aria-valuenow="{{ $progress }}"></div>
                    </div>
                    <p class="text-center fw-bold mt-1 mb-0">{{ number_format($progress, 1) }}%</p>
                </div>
            </div>

            {{-- CARD: SUMAR FINANCIAR --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold text-dark-emphasis mb-3">Sumar Financiar</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <small class="text-muted d-block">SELL PRICE</small>
                            <div class="fw-bold fs-5 text-success">{{ number_format($project->sell_price, 2) }} CHF</div>
                        </li>
                        <li class="mb-2">
                            <small class="text-muted d-block">PLANNED BUDGET</small>
                            <div class="fw-bold">{{ number_format($project->planned_budget, 2) }} CHF</div>
                        </li>
                        <li class="mb-2">
                            <small class="text-muted d-block">COST ACTUAL (Materiale)</small>
                            <div class="fw-bold text-danger">{{ number_format($project->costItems->sum('actual_cost'), 2) }} CHF</div>
                        </li>
                        <li class="mb-2">
                            <small class="text-muted d-block">COST ACTUAL (Muncitori)</small>
                            <div class="fw-bold text-danger">{{ number_format($project->workerLogs->sum('calculated_total_cost'), 2) }} CHF</div>
                        </li>
                         <li class="mb-2">
                            <small class="text-muted d-block">COST ACTUAL (Facturi)</small>
                            <div class="fw-bold text-danger">{{ number_format($project->invoices->sum('amount_chf'), 2) }} CHF</div>
                        </li>
                        <hr>
                        <li class="mb-2">
                            <small class="text-muted d-block">PROFIT ESTIMAT</small>
                            <div class="fw-bold fs-5">{{-- TODO: Logică Profit --}} ... CHF</div>
                        </li>
                    </ul>
                </div>
            </div>

        
            {{-- CARD: LINK-URI RAPIDE --}}
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-semibold text-dark-emphasis mb-3">Link-uri Rapide</h5>
                    <div class="d-grid gap-2">
                        @if($project->drive_folder_url)
                        <a href="{{ $project->drive_folder_url }}" class="btn btn-light border" target="_blank">
                            <i class="bi bi-google me-2"></i>Folder Proiect
                        </a>
                        @endif
                        @if($project->layout_file_url)
                        <a href="{{ $project->layout_file_url }}" class="btn btn-light border" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Vezi Layout
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            
            {{-- CARD: ZONA DE PERICOL --}}
            <div class="card bg-transparent border-danger border-2 rounded-4">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-semibold text-danger mb-3">Zonă de Pericol</h5>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Sigur ștergi acest proiect? Toate costurile și milestone-urile asociate vor fi șterse. Această acțiune este ireversibilă!');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4">
                            <i class="bi bi-trash me-1"></i> Șterge Proiectul
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection