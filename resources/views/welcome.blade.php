@extends('layouts.app')

@push('styles')
{{-- Adaugă acest @push('styles') în layouts.app în <head> dacă vrei, 
    altfel, lasă-l aici și va funcționa. --}}
<style>
    /* * Animația "Fade In Up" 
     
     */
    .fade-in-up {
        /* Inițial, elementul este invizibil și mutat în jos */
        opacity: 0;
        transform: translateY(30px);
        
        /* Definirea animației */
        animation: fadeInUp 1s ease-out forwards;
        animation-delay: 0.2s; /* O mică întârziere pentru un efect mai bun */
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
{{-- 
  Container principal care centrează vertical și orizontal.
  min-vh-75 înseamnă "minimum 75% din înălțimea ecranului".
--}}
<div classa="container">
    <div class="row min-vh-75 d-flex align-items-center justify-content-center text-center">
        <div class="col-lg-9 col-xl-8">
            
            {{-- Adăugăm clasa de animație aici --}}
            <div class="fade-in-up">

                {{-- 
                    TITLUL HERO 
                    Mare, subțire (fw-light) și cu accent de culoare.
                --}}
                <h1 class="display-2 fw-light text-dark mb-3">
                    Bun venit în
                    <br>
                    <span class="fw-semibold text-primary">PM Dashboard</span>
                </h1>

                {{-- 
                    TEXTUL DE INTRODUCERE 
                    Curat și la obiect.
                --}}
                <p class="lead text-muted mb-5 px-lg-5">
                    Sistemul tău de Project Management este gata. 
                    Începe să preiei controlul asupra proiectelor tale, să automatizezi costurile și să obții claritatea de care ai nevoie.
                </p>

                {{-- 
                    BUTOANELE DE ACȚIUNE
                    Stil "Pill" (pastilă), conform cu estetica modernă.
                --}}
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('projects.index') }}" class="btn btn-lg btn-primary rounded-pill shadow-sm px-4">
                        <i class="bi bi-list-task me-2"></i> Mergi la Proiecte
                    </a>
                    <a href="{{ route('settings.edit') }}" class="btn btn-lg btn-light rounded-pill px-4 border">
                        <i class="bi bi-gear-fill me-2"></i> Configurează Sistemul
                    </a>
                </div>
                
                {{-- 
                    FOOTER-UL TEHNIC
                    L-am făcut dinamic pentru un aspect mai profesionist.
                --}}
                <p class="mt-5 text-muted" style="font-size: 0.8rem;">
                    Rulând pe: PHP {{ PHP_VERSION }} / Laravel {{ app()->version() }}
                </p>
            </div>

        </div>
    </div>
</div>
@endsection