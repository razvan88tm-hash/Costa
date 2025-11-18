<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Listă proiecte.
     */
    public function index(): View
    {
        $projects = Project::orderByDesc('id')->paginate(20);

        return view('projects.index', compact('projects'));
    }

    /**
     * Formular creare proiect.
     */
    public function create(): View
    {
        return view('projects.create');
    }

    /**
     * Salvează un proiect nou.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_number' => ['required', 'string', 'max:255', 'unique:projects,project_number'],
            'name'           => ['required', 'string', 'max:255'],
            'client_name'    => ['required', 'string', 'max:255'],
            'overall_rag'    => ['nullable', 'string', 'max:32'],
            'drive_folder_url' => ['nullable', 'string', 'max:500'],
            'layout_file_url'  => ['nullable', 'string', 'max:500'],
            'offers_folder_url'=> ['nullable', 'string', 'max:500'],
        ]);

        $data['overall_rag'] = $data['overall_rag'] ?? 'Green';

        $project = Project::create($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Proiect creat cu succes.');
    }

    /**
     * Afișează un proiect + facturi + costuri + milestone-uri.
     */
    public function show(Project $project): View
    {
        // preloadăm relațiile (evită N+1)
        $project->load([
            'invoices'      => fn ($q) => $q->orderByDesc('id'),
            'costItems',
            'milestones',
            'workerLogs',
        ]);

        return view('projects.show', compact('project'));
    }

    /**
     * Formular editare proiect.
     */
    public function edit(Project $project): View
    {
        return view('projects.edit', compact('project'));
    }

    /**
     * Actualizează datele proiectului.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $request->validate([
            'project_number' => ['required', 'string', 'max:255', 'unique:projects,project_number,' . $project->id],
            'name'           => ['required', 'string', 'max:255'],
            'client_name'    => ['required', 'string', 'max:255'],
            'overall_rag'    => ['nullable', 'string', 'max:32'],
            'drive_folder_url' => ['nullable', 'string', 'max:500'],
            'layout_file_url'  => ['nullable', 'string', 'max:500'],
            'offers_folder_url'=> ['nullable', 'string', 'max:500'],
        ]);

        $project->update($data);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Proiect actualizat cu succes.');
    }

    /**
     * Șterge proiectul (cascade delete pentru facturi, costuri, etc.).
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Proiect șters cu succes.');
    }
}
