<!-- resources/views/project-worker-logs/_form.blade.php -->
<div class="form-group">
    <label for="worker_name">Nume muncitor</label>
    <input id="worker_name" name="worker_name" class="form-control" value="{{ old('worker_name', $workerLog->worker_name ?? '') }}">
</div>

<div class="form-group">
    <label for="days_on_site">Zile pe șantier</label>
    <input id="days_on_site" name="days_on_site" type="number" step="1" min="0" class="form-control" value="{{ old('days_on_site', $workerLog->days_on_site ?? 0) }}" required>
</div>

<div class="form-group">
    <label for="hours_on_site">Ore pe zi / total</label>
    <input id="hours_on_site" name="hours_on_site" type="number" step="0.01" min="0" class="form-control" value="{{ old('hours_on_site', $workerLog->hours_on_site ?? 0) }}" required>
</div>

<div class="form-group">
    <label for="transport_km">Transport (km)</label>
    <input id="transport_km" name="transport_km" type="number" step="0.01" min="0" class="form-control" value="{{ old('transport_km', $workerLog->transport_km ?? 0) }}">
</div>

<div class="form-group">
    <label for="notes">Observații</label>
    <textarea id="notes" name="notes" class="form-control">{{ old('notes', $workerLog->notes ?? '') }}</textarea>
</div>