<!-- resources/views/project-worker-logs/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Adaugă cost muncitor pentru proiectul: {{ $project->name ?? ('#'.$project->id) }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Au apărut erori:</strong>
            <ul>
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('project-worker-logs.store', $project) }}">
        @csrf

        @include('project-worker-logs._form')

        <button class="btn btn-primary" type="submit">Salvează</button>
        <a class="btn btn-secondary" href="{{ route('projects.show', $project) }}">Înapoi la proiect</a>
    </form>
</div>
@endsection