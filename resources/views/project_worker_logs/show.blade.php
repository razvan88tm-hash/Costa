<!-- resources/views/project-worker-logs/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Log muncitor #{{ $workerLog->id ?? '' }}</h1>

    <table class="table table-striped">
        <tr><th>Nume muncitor</th><td>{{ $workerLog->worker_name }}</td></tr>
        <tr><th>Zile pe șantier</th><td>{{ $workerLog->days_on_site }}</td></tr>
        <tr><th>Ore pe zi / total</th><td>{{ $workerLog->hours_on_site }}</td></tr>
        <tr><th>Transport (km)</th><td>{{ $workerLog->transport_km }}</td></tr>
        <tr><th>Per diem total</th><td>{{ $workerLog->per_diem_total ?? '-' }}</td></tr>
        <tr><th>Hotel total</th><td>{{ $workerLog->hotel_total ?? '-' }}</td></tr>
        <tr><th>Labor cost</th><td>{{ $workerLog->labor_cost ?? '-' }}</td></tr>
        <tr><th>Transport cost</th><td>{{ $workerLog->transport_cost ?? '-' }}</td></tr>
        <tr><th>Total calculat</th><td>{{ $workerLog->calculated_total_cost ?? $workerLog->total_cost ?? '-' }}</td></tr>
        <tr><th>Observații</th><td>{{ $workerLog->notes }}</td></tr>
        <tr><th>Creat la</th><td>{{ $workerLog->created_at }}</td></tr>
    </table>

    <a class="btn btn-secondary" href="{{ route('projects.show', $project) }}">Înapoi la proiect</a>
    <a class="btn btn-primary" href="{{ route('project-worker-logs.edit', [$project, $workerLog]) }}">Editează</a>
</div>
@endsection