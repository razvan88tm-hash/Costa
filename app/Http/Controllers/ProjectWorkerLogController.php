<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectWorkerLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectWorkerLogController extends Controller
{
    protected function resolveWorkerLog(Project $project, ProjectWorkerLog $workerLog): ProjectWorkerLog
    {
        if ($workerLog->project_id !== $project->id) {
            abort(404);
        }
        return $workerLog;
    }

    public function create(Project $project): View
    {
        if (! view()->exists('project-worker-logs.create')) {
            abort(500, 'Missing view: resources/views/project-worker-logs/create.blade.php');
        }

        return view('project-worker-logs.create', [
            'project' => $project
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate([
            'worker_name' => 'nullable|string|max:255',
            'hours_on_site' => 'required|numeric|min:0',
            'days_on_site' => 'required|numeric|min:0',
            'transport_km' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $log = new ProjectWorkerLog();
        $log->project()->associate($project);
        $log->fillAndCalculate($validated);
        $log->save();

        return redirect()->route('projects.show', $project)
                         ->with('success', 'Costul de muncitor a fost adăugat.');
    }

    public function edit(Project $project, ProjectWorkerLog $workerLog): View
    {
        $workerLog = $this->resolveWorkerLog($project, $workerLog);

        if (! view()->exists('project-worker-logs.edit')) {
            abort(500, 'Missing view: resources/views/project-worker-logs/edit.blade.php');
        }

        return view('project-worker-logs.edit', [
            'project' => $project,
            'workerLog' => $workerLog
        ]);
    }

    public function update(Request $request, Project $project, ProjectWorkerLog $workerLog): RedirectResponse
    {
        $workerLog = $this->resolveWorkerLog($project, $workerLog);

        $validated = $request->validate([
            'worker_name' => 'nullable|string|max:255',
            'hours_on_site' => 'required|numeric|min:0',
            'days_on_site' => 'required|numeric|min:0',
            'transport_km' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $workerLog->fillAndCalculate($validated);
        $workerLog->save();

        return redirect()->route('projects.show', $project)
                         ->with('success', 'Costul de muncitor a fost actualizat.');
    }

    public function destroy(Project $project, ProjectWorkerLog $workerLog): RedirectResponse
    {
        $workerLog = $this->resolveWorkerLog($project, $workerLog);
        $workerLog->delete();

        return redirect()->route('projects.show', $project)
                         ->with('success', 'Costul de muncitor a fost șters.');
    }

    public function show(Project $project, ProjectWorkerLog $workerLog): View
    {
        $workerLog = $this->resolveWorkerLog($project, $workerLog);

        if (! view()->exists('project-worker-logs.show')) {
            abort(500, 'Missing view: resources/views/project-worker-logs/show.blade.php');
        }

        return view('project-worker-logs.show', [
            'project' => $project,
            'workerLog' => $workerLog
        ]);
    }

    public function export(Project $project): StreamedResponse
    {
        $fileName = 'worker_logs_project_' . $project->id . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $columns = [
            'worker_name',
            'days_on_site',
            'hours_on_site',
            'transport_km',
            'per_diem_total',
            'hotel_total',
            'labor_cost',
            'transport_cost',
            'calculated_total_cost',
            'notes',
            'created_at',
        ];

        $callback = function () use ($project, $columns) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
            fputcsv($handle, $columns);

            $project->workerLogs()
                ->orderBy('created_at')
                ->chunk(200, function ($logs) use ($handle, $columns) {
                    foreach ($logs as $log) {
                        $row = [];
                        foreach ($columns as $col) {
                            $row[] = $log->{$col} ?? '';
                        }
                        fputcsv($handle, $row);
                    }
                });

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    public function import(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $data['file'];
        $created = 0;

        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle);
            if (isset($header[0]) && str_starts_with($header[0], "\xEF\xBB\xBF")) {
                $header[0] = substr($header[0], 3);
            }
            $header = array_map(fn ($h) => trim(mb_strtolower($h)), $header);

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) === 1 && trim($row[0]) === '') {
                    continue;
                }
                if (count($row) > count($header)) {
                    continue;
                }

                $rowAssoc = [];
                foreach ($row as $i => $value) {
                    $key = $header[$i] ?? null;
                    if ($key) {
                        $rowAssoc[$key] = $value;
                    }
                }

                $payload = [
                    'worker_name'   => $rowAssoc['worker_name']  ?? null,
                    'days_on_site'  => $rowAssoc['days_on_site'] ?? 0,
                    'hours_on_site' => $rowAssoc['hours_on_site'] ?? 0,
                    'transport_km'  => $rowAssoc['transport_km'] ?? 0,
                    'notes'         => $rowAssoc['notes']        ?? null,
                ];

                $log = new ProjectWorkerLog();
                $log->project()->associate($project);
                $log->fillAndCalculate($payload);
                $log->save();

                $created++;
            }

            fclose($handle);
        }

        return redirect()
            ->route('projects.show', $project)
            ->with('success', "Import finalizat: {$created} loguri adăugate.");
    }
}