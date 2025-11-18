@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        {{-- Folosim o coloană mai lată pentru a afișa JSON-ul --}}
        <div class="col-md-10">

            {{-- Cardul principal cu estetica Apple --}}
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">

                    {{-- 1. TITLUL PAGINII --}}
                    <h1 class="h3 mb-2 text-dark fw-light">
                        <i class="bi bi-robot me-2 text-primary"></i>
                        Document AI – Debug
                    </h1>
                    <p class="text-muted mb-4">
                        Afișare conținut JSON extras pentru: <strong>{{ $file }}</strong>
                    </p>

                    <hr class="mt-0 mb-4 opacity-50">

                    {{-- 2. BLOCUL JSON --}}
                    {{-- Am păstrat stilul dark pentru <pre> deoarece este standardul pentru cod --}}
                    <pre class="bg-dark text-light p-3 p-md-4 rounded-3" style="max-height: 70vh; overflow: auto; font-size: 0.8rem;">{{ $json }}</pre>

                    {{-- 3. ACȚIUNE "ÎNAPOI" --}}
                    <hr class="mt-5 mb-4 opacity-50">
                    <div class="d-flex">
                        <a href="{{ route('projects.index') }}" class="btn btn-light btn-lg rounded-pill px-4 border">
                            <i class="bi bi-arrow-left me-1"></i> Înapoi la Dashboard
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection