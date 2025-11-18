@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        {{-- Folosim o coloană largă pentru log-uri --}}
        <div class="col-lg-12">

            {{-- 1. TITLUL PAGINII --}}
            <h1 class="h3 mb-2 text-dark fw-light">
                <i class="bi bi-cloud-arrow-down-fill me-2 text-primary"></i>
                Loguri Sincronizare Drive
            </h1>
            <p class="text-muted mb-4">Istoricul operațiunilor de sincronizare cu Google Drive.</p>

            {{-- 2. CARDUL PRINCIPAL --}}
            <div class="card shadow border-0 rounded-4 overflow-hidden">

                @if($logs->isEmpty())
                    {{-- 3A. STAREA "EMPTY" --}}
                    <div class="card-body text-center p-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <h4 class="mt-3 fw-light">Nu există loguri.</h4>
                        <p class="text-muted">Rulează o sincronizare pentru a vedea istoricul aici.</p>
                    </div>

                @else
                    {{-- 3B. TABELUL CU LOG-URI --}}
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover align-middle mb-0">
                            {{-- Antetul "Light" --}}
                            <thead class="table-light">
                                <tr>
                                    <th class_tx="px-3 py-2">Data</th>
                                    <th class="px-3 py-2">Nivel</th>
                                    <th class="px-3 py-2">Tip</th>
                                    <th class="px-3 py-2">Proiect</th>
                                    <th class="px-3 py-2">Fișier</th>
                                    <th class="px-3 py-2">Mesaj</th>
                                    <th class="px-3 py-2">Context</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $log)
                                    <tr>
                                        <td class="px-3 text-muted" style="white-space: nowrap;">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-3">
                                            {{-- Badges "Subtle" --}}
                                            @if ($log->level === 'error')
                                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">EROARE</span>
                                            @elseif ($log->level === 'warning')
                                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">ATENȚIE</span>
                                            @else
                                                <span class="badge bg-success-subtle text-success-emphasis rounded-pill">INFO</span>
                                            @endif
                                        </td>
                                        <td class="px-3">{{ $log->type }}</td>
                                        <td class="px-3">
                                            @if ($log->project)
                                                <span class="fw-bold">{{ $log->project->project_number ?? '' }}</span> – {{ $log->project->name ?? '' }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3">
                                            @if ($log->file_name)
                                                {{ $log->file_name }}
                                                <br>
                                                <small class="text-muted">{{ $log->file_id }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3">{{ $log->message }}</td>
                                        <td class="px-3">
                                            @if ($log->context)
                                                {{-- Stil curat pentru JSON --}}
                                                <pre style="white-space: pre-wrap; font-size: 11px; max-height: 100px; overflow: auto;" class="bg-light-subtle border rounded p-2 text-dark-emphasis mb-0">
{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
                                                </pre>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. PAGINARE --}}
                    @if ($logs->hasPages())
                        <div class="card-footer bg-white border-0 rounded-bottom-4">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif

            </div>
        </div>
    </div>
</div>
@endsection