@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-12"> {{-- Folosim o coloană mai largă pentru dashboard --}}

            @php
                // Logica KPI rămâne aceeași
                $totalProjects = $projects->count();
                $greenProjects = $projects->where('overall_rag', 'Green')->count();
                $redProjects = $projects->where('overall_rag', 'Red')->count();
                $totalSellPrice = $projects->sum('sell_price');
            @endphp

            {{-- 1. HEADER CU KPI (Indicatori Cheie) --}}
            <div class="row g-4 mb-4">
                
                {{-- Card Total Proiecte --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center">
                            <i class="bi bi-folder2-open fs-1 text-primary me-4"></i>
                            <div>
                                <h6 class="text-muted text-uppercase small mb-0">Total Proiecte</h6>
                                <h2 class="fw-bold mb-0">{{ $totalProjects }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Proiecte Green --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center">
                            <i class="bi bi-check-circle fs-1 text-success me-4"></i>
                            <div>
                                <h6 class="text-muted text-uppercase small mb-0">Status OK (Green)</h6>
                                <h2 class="fw-bold mb-0 text-success">{{ $greenProjects }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Proiecte Red --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle fs-1 text-danger me-4"></i>
                            <div>
                                <h6 class="text-muted text-uppercase small mb-0">Atenție (Red)</h6>
                                <h2 class="fw-bold mb-0 text-danger">{{ $redProjects }}</h2>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Valoare Totală --}}
                <div class="col-md-6 col-lg-3">
                    <div class="card shadow-sm border-0 rounded-4 h-100">
                        <div class="card-body p-4 d-flex align-items-center">
                            <i class="bi bi-cash-stack fs-1 text-dark-emphasis me-4"></i>
                            <div>
                                <h6 class="text-muted text-uppercase small mb-0">Valoare Contracte</h6>
                                <h3 class="fw-bold mb-0">
                                    {{ number_format($totalSellPrice, 0) }}
                                    <span class="text-muted fw-normal">CHF</span>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. ZONA DE ACȚIUNE --}}
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center my-4">
                <h1 class="h3 mb-2 mb-md-0 text-dark fw-light">
                    <i class="bi bi-list-task me-2"></i>
                    Dashboard Proiecte
                </h1>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('drive.sync') }}" class="btn btn-success rounded-pill px-3" 
                       onclick="return confirm('Ești sigur că vrei să scanezi Google Drive?\nAsta poate dura câteva secunde...');">
                        <i class="bi bi-google me-1"></i> Sincronizează Drive
                    </a>
                    <a href="{{ route('projects.create') }}" class="btn btn-primary rounded-pill px-3">
                        <i class="bi bi-plus-lg me-1"></i> Proiect Nou
                    </a>
                </div>
            </div>

            {{-- 3. TABELUL MODERNIZAT --}}
            <div class="card shadow border-0 rounded-4">
                @if($projects->isEmpty())
                    {{-- Starea "Empty" --}}
                    <div class="card-body text-center p-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <h4 class="mt-3 fw-light">Nu există proiecte momentan.</h4>
                        <p class="text-muted">Începe prin a crea un proiect nou sau sincronizează Google Drive.</p>
                        <a href="{{ route('projects.create') }}" class="btn btn-primary rounded-pill mt-3 px-4">
                            <i class="bi bi-plus-lg me-1"></i> Creează primul proiect
                        </a>
                    </div>
                @else
                    {{-- Container pentru tabel --}}
                    <div class="table-responsive">
                        <table class="table table-borderless table-hover align-middle mb-0">
                            {{-- Antetul tabelului --}}
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="px-4 py-3"># Proiect</th>
                                    <th scope="col" class="px-4 py-3">Client & Locație</th>
                                    <th scope="col" class="px-4 py-3">PM</th>
                                    <th scope="col" class="px-4 py-3">Progres</th>
                                    <th scope="col" class="px-4 py-3">Status</th>
                                    <th scope="col" class="px-4 py-3">Handover (T / R)</th>
                                    <th scope="col" class="px-4 py-3 text-end">Acțiuni</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                    @php
                                        // Folosim clasele moderne Bootstrap 5 pentru badges
                                        $rogClass = match($project->overall_rag) {
                                            'Red' => 'badge bg-danger-subtle text-danger-emphasis rounded-pill',
                                            'Orange' => 'badge bg-warning-subtle text-warning-emphasis rounded-pill',
                                            'Green' => 'badge bg-success-subtle text-success-emphasis rounded-pill',
                                            default => 'badge bg-secondary-subtle text-secondary-emphasis rounded-pill',
                                        };
                                        $progress = $project->calculated_percent_complete ?? $project->percent_complete ?? 0;
                                    @endphp
                                    <tr>
                                        {{-- # Proiect (Linkabil) --}}
                                        <td class="px-4">
                                            <a href="{{ route('projects.show', $project) }}" class="fw-semibold text-primary text-decoration-none">
                                                {{ $project->project_number ?? 'N/A' }}
                                            </a>
                                        </td>
                                        
                                        {{-- Client & Locație --}}
                                        <td class="px-4">
                                            <div class="fw-semibold">{{ $project->client_name ?? $project->name }}</div>
                                            <small class="text-muted">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $project->location ?? '-' }}
                                            </small>
                                        </td>
                                        
                                        {{-- PM --}}
                                        <td class="px-4 text-muted">{{ $project->pm_name ?? '-' }}</td>
                                        
                                        {{-- Progres --}}
                                        <td class="px-4" style="min-width: 150px;">
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted me-2" style="width: 35px;">{{ $progress }}%</small>
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar rounded-pill" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"></div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        {{-- Status --}}
                                        <td class="px-4">
                                            <span class="{{ $rogClass }}">{{ $project->overall_rag }}</span>
                                        </td>
                                        
                                        {{-- Handover --}}
                                        <td class="px-4">
                                            <small class="d-block text-muted">Target: {{ $project->target_handover ?? '-' }}</small>
                                            @if($project->real_handover)
                                                <small class="d-block text-success fw-semibold">Real: {{ $project->real_handover }}</small>
                                            @endif
                                        </td>
                                        
                                        {{-- Acțiuni --}}
                                        <td class="px-4 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                {{-- Butoane subtile, în stil Apple --}}
                                                <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-light border" title="Vezi detalii">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-light border" title="Editează">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                
                                                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline" onsubmit="return confirm('Sigur ștergi proiectul {{ $project->project_number }}?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-light border" title="Șterge">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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
</div>
@endsection